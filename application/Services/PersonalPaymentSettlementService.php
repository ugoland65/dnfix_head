<?php
namespace App\Services;

use Exception;
use App\Core\AuthAdmin;
use App\Models\AdminModel;
use App\Models\PersonalPaymentAccountModel;
use App\Models\PersonalPaymentSettlementFileModel;
use App\Models\PersonalPaymentSettlementModel;
use App\Models\PersonalPaymentStatusLogModel;
use App\Utils\TelegramUtils;

class PersonalPaymentSettlementService
{
    public const STATUSES = ['정산요청', '확인중', '지급예정', '지급완료', '보류', '반려'];
    public const PAY_METHODS = ['개인 신용카드', '체크카드', '계좌이체', '현금'];
    public const EDITABLE_STATUSES = ['정산요청', '보류', '반려'];

    private static $schemaReady = false;

    public function __construct()
    {
        $this->ensureSchema();
    }

    public function getAdminOptions(): array
    {
        $rows = AdminModel::query()
            ->select(['idx', 'ad_name', 'ad_department', 'ad_work_status'])
            ->where('ad_work_status', '=', '재직중')
            ->orderBy('ad_name', 'ASC')
            ->get()
            ->toArray();

        return is_array($rows) ? $rows : [];
    }

    public function getSavedAccount(int $adminIdx): array
    {
        if ($adminIdx <= 0) {
            return [];
        }

        $row = PersonalPaymentAccountModel::query()
            ->where('ad_pk', '=', $adminIdx)
            ->first();
        if (!$row) {
            return [];
        }

        return is_array($row) ? $row : $row->toArray();
    }

    public function getList(array $criteria): array
    {
        $page = max(1, (int)($criteria['page'] ?? 1));
        $perPage = max(1, (int)($criteria['per_page'] ?? 100));
        $status = trim((string)($criteria['status'] ?? ''));
        $keyword = trim((string)($criteria['keyword'] ?? ''));
        $dateStart = trim((string)($criteria['use_date_start'] ?? ''));
        $dateEnd = trim((string)($criteria['use_date_end'] ?? ''));

        $viewer = $this->viewer();
        $query = PersonalPaymentSettlementModel::query()->orderBy('idx', 'desc');
        $this->applyVisibility($query, $viewer);
        if ($status !== '' && in_array($status, self::STATUSES, true)) {
            $query->where('status', '=', $status);
        }
        if ($dateStart !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStart)) {
            $query->where('use_date', '>=', $dateStart);
        }
        if ($dateEnd !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateEnd)) {
            $query->where('use_date', '<=', $dateEnd);
        }
        if ($keyword !== '') {
            $query->where(function ($query) use ($keyword) {
                $query->whereRaw('INSTR(applicant_name, :personal_keyword_name)', ['personal_keyword_name' => $keyword])
                    ->orWhereRaw('INSTR(vendor, :personal_keyword_vendor)', ['personal_keyword_vendor' => $keyword])
                    ->orWhereRaw('INSTR(purpose, :personal_keyword_purpose)', ['personal_keyword_purpose' => $keyword])
                    ->orWhereRaw('INSTR(pay_method, :personal_keyword_method)', ['personal_keyword_method' => $keyword]);
            });
        }

        $result = $query->paginate($perPage, $page);
        $rows = $result['data'] ?? [];
        $ids = [];
        foreach ($rows as $row) {
            $ids[] = (int)($row['idx'] ?? 0);
        }
        $fileCounts = $this->fileCounts($ids);
        foreach ($rows as &$row) {
            $row['file_count'] = $fileCounts[(int)($row['idx'] ?? 0)] ?? 0;
            $row['bank_account_masked'] = $this->maskAccount((string)($row['bank_account'] ?? ''));
        }
        unset($row);
        $result['data'] = $rows;
        $result['summary'] = $this->summary($viewer);

        return $result;
    }

    public function getInfo(int $idx): array
    {
        $row = PersonalPaymentSettlementModel::find($idx);
        $row = $row ? (is_array($row) ? $row : $row->toArray()) : [];
        if (empty($row)) {
            throw new Exception('개인결제 정산요청을 찾을 수 없습니다.');
        }
        $this->assertVisible($row, $this->viewer());

        $files = PersonalPaymentSettlementFileModel::query()
            ->where('settlement_idx', '=', $idx)
            ->orderBy('idx', 'ASC')
            ->get()
            ->toArray();
        $logs = PersonalPaymentStatusLogModel::query()
            ->where('settlement_idx', '=', $idx)
            ->orderBy('idx', 'DESC')
            ->get()
            ->toArray();
        $row['files'] = is_array($files) ? $files : [];
        $row['logs'] = is_array($logs) ? $logs : [];

        return $row;
    }

    public function create(array $requestData): int
    {
        $input = $this->buildInput($requestData);
        $now = date('Y-m-d H:i:s');
        $actor = $this->actor();
        $input['status'] = '정산요청';
        $input['ad_pk'] = $actor['idx'];
        $input['ad_name'] = $actor['name'];
        $input['created_at'] = $now;
        $input['updated_at'] = $now;

        $connection = app('db');
        $ownsTransaction = $connection instanceof \PDO && !$connection->inTransaction();
        try {
            if ($ownsTransaction) {
                $connection->beginTransaction();
            }

            $settlementIdx = (int)PersonalPaymentSettlementModel::insert($input);
            if ($settlementIdx <= 0) {
                throw new Exception('개인결제 정산요청을 저장하지 못했습니다.');
            }
            $this->storeFiles($settlementIdx, $actor['idx']);
            $this->saveAccount($input['applicant_ad_pk'], $input['bank'], $input['bank_account'], $input['depositor']);
            $this->writeStatusLog($settlementIdx, '정산요청', '정산요청을 등록했습니다.', $actor);
            if ($ownsTransaction && $connection->inTransaction()) {
                $connection->commit();
            }
        } catch (\Throwable $e) {
            if ($ownsTransaction && $connection instanceof \PDO && $connection->inTransaction()) {
                $connection->rollBack();
            }
            throw $e instanceof Exception ? $e : new Exception($e->getMessage());
        }

        $this->notifyCreated($input, $actor);
        return $settlementIdx;
    }

    public function update(array $requestData): int
    {
        $idx = (int)($requestData['idx'] ?? 0);
        $existing = $this->getInfo($idx);
        if ((string)($existing['status'] ?? '') === '지급완료') {
            throw new Exception('지급완료된 정산요청은 수정할 수 없습니다.');
        }

        $actor = $this->actor();
        $nextStatus = trim((string)($requestData['status'] ?? $existing['status']));
        if (!in_array($nextStatus, self::STATUSES, true) || $nextStatus === '') {
            throw new Exception('정산 상태가 올바르지 않습니다.');
        }

        $editable = in_array((string)$existing['status'], self::EDITABLE_STATUSES, true);
        $update = [
            'account_subject' => trim((string)($requestData['account_subject'] ?? ($existing['account_subject'] ?? ''))),
            'status' => $nextStatus,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($editable) {
            $input = $this->buildInput($requestData);
            $update = array_merge($update, $input);
        }
        if ($nextStatus === '지급완료') {
            $paidAmount = $this->money((string)($requestData['paid_amount'] ?? ''), '실제 지급금액');
            $paidDate = trim((string)($requestData['paid_date'] ?? ''));
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $paidDate)) {
                throw new Exception('지급일자를 선택해 주세요.');
            }
            $update['paid_amount'] = $paidAmount;
            $update['paid_date'] = $paidDate;
            $update['paid_ad_pk'] = $actor['idx'];
            $update['paid_ad_name'] = $actor['name'];
            $update['paid_memo'] = trim((string)($requestData['paid_memo'] ?? ''));
        }

        $connection = app('db');
        $ownsTransaction = $connection instanceof \PDO && !$connection->inTransaction();
        try {
            if ($ownsTransaction) {
                $connection->beginTransaction();
            }
            PersonalPaymentSettlementModel::update(['idx' => $idx], $update);
            if ($editable) {
                $this->storeFiles($idx, $actor['idx']);
                $this->saveAccount(
                    (int)$update['applicant_ad_pk'],
                    (string)$update['bank'],
                    (string)$update['bank_account'],
                    (string)$update['depositor']
                );
            }
            if ($nextStatus !== (string)$existing['status']) {
                $memo = $nextStatus === '지급완료'
                    ? trim((string)($update['paid_memo'] ?? ''))
                    : trim((string)($requestData['status_memo'] ?? ''));
                $this->writeStatusLog($idx, $nextStatus, $memo, $actor);
            }
            if ($ownsTransaction && $connection->inTransaction()) {
                $connection->commit();
            }
        } catch (\Throwable $e) {
            if ($ownsTransaction && $connection instanceof \PDO && $connection->inTransaction()) {
                $connection->rollBack();
            }
            throw $e instanceof Exception ? $e : new Exception($e->getMessage());
        }

        return $idx;
    }

    private function buildInput(array $requestData): array
    {
        $applicantPk = (int)($requestData['applicant_ad_pk'] ?? 0);
        $applicant = AdminModel::query()->where('idx', '=', $applicantPk)->first();
        $applicant = $applicant ? (is_array($applicant) ? $applicant : $applicant->toArray()) : [];
        if (empty($applicant)) {
            throw new Exception('신청자를 선택해 주세요.');
        }

        $useDate = trim((string)($requestData['use_date'] ?? ''));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $useDate)) {
            throw new Exception('사용일자를 선택해 주세요.');
        }

        $vendor = trim((string)($requestData['vendor'] ?? ''));
        $purpose = trim((string)($requestData['purpose'] ?? ''));
        $payMethod = trim((string)($requestData['pay_method'] ?? ''));
        if ($vendor === '') {
            throw new Exception('사용처를 입력해 주세요.');
        }
        if ($purpose === '') {
            throw new Exception('사용내용 및 업무목적을 입력해 주세요.');
        }
        if (!in_array($payMethod, self::PAY_METHODS, true)) {
            throw new Exception('결제수단을 선택해 주세요.');
        }

        $bank = trim((string)($requestData['bank'] ?? ''));
        $bankAccount = trim((string)($requestData['bank_account'] ?? ''));
        $depositor = trim((string)($requestData['depositor'] ?? ''));
        if ($bank === '' || $bankAccount === '' || $depositor === '') {
            throw new Exception('지급받을 계좌의 은행, 계좌번호, 예금주를 입력해 주세요.');
        }

        return [
            'applicant_ad_pk' => $applicantPk,
            'applicant_name' => (string)($applicant['ad_name'] ?? ''),
            'use_date' => $useDate,
            'vendor' => $vendor,
            'purpose' => $purpose,
            'pay_method' => $payMethod,
            'payment_amount' => $this->money((string)($requestData['payment_amount'] ?? ''), '결제금액'),
            'request_amount' => $this->money((string)($requestData['request_amount'] ?? ''), '정산요청 금액'),
            'bank' => $bank,
            'bank_account' => $bankAccount,
            'depositor' => $depositor,
        ];
    }

    private function money(string $raw, string $label): string
    {
        $raw = str_replace(',', '', trim($raw));
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $raw) || (float)$raw <= 0) {
            throw new Exception($label . '을 입력해 주세요.');
        }

        return $raw;
    }

    private function actor(): array
    {
        $idx = (int)(AuthAdmin::getSession('sess_idx') ?? 0);
        $name = trim((string)(AuthAdmin::getSession('sess_name') ?? ''));
        if ($idx <= 0) {
            throw new Exception('로그인 정보를 확인할 수 없습니다.');
        }

        return ['idx' => $idx, 'name' => $name];
    }

    private function viewer(): array
    {
        $actor = $this->actor();
        $admin = AdminModel::query()
            ->select(['idx', 'ad_job_type'])
            ->where('idx', '=', $actor['idx'])
            ->first();
        $admin = $admin ? (is_array($admin) ? $admin : $admin->toArray()) : [];
        $actor['is_executive'] = (string)($admin['ad_job_type'] ?? '') === '임원';

        return $actor;
    }

    private function applyVisibility($query, array $viewer)
    {
        if (!empty($viewer['is_executive'])) {
            return $query;
        }

        $idx = (int)($viewer['idx'] ?? 0);
        return $query->where(function ($query) use ($idx) {
            $query->where('applicant_ad_pk', '=', $idx)
                ->orWhere('ad_pk', '=', $idx);
        });
    }

    private function assertVisible(array $row, array $viewer): void
    {
        if (!empty($viewer['is_executive'])) {
            return;
        }

        $idx = (int)($viewer['idx'] ?? 0);
        $applicantPk = (int)($row['applicant_ad_pk'] ?? 0);
        $writerPk = (int)($row['ad_pk'] ?? 0);
        if ($idx > 0 && ($applicantPk === $idx || $writerPk === $idx)) {
            return;
        }

        throw new Exception('본인이 등록한 개인결제 정산요청만 볼 수 있습니다.');
    }

    private function summary(array $viewer): array
    {
        $query = PersonalPaymentSettlementModel::query()
            ->select(['status', 'request_amount', 'paid_amount']);
        $this->applyVisibility($query, $viewer);
        $rows = $query->get()->toArray();
        $summary = [
            'request_total' => 0.0,
            'scheduled_total' => 0.0,
            'paid_total' => 0.0,
            'unpaid_total' => 0.0,
            'review_total' => 0.0,
        ];
        foreach ($rows as $row) {
            $status = (string)($row['status'] ?? '');
            $requestAmount = (float)($row['request_amount'] ?? 0);
            $paidAmount = (float)($row['paid_amount'] ?? 0);
            if ($status !== '반려') {
                $summary['request_total'] += $requestAmount;
            }
            if ($status === '지급예정') {
                $summary['scheduled_total'] += $requestAmount;
                $summary['unpaid_total'] += $requestAmount;
            }
            if ($status === '지급완료') {
                $summary['paid_total'] += $paidAmount > 0 ? $paidAmount : $requestAmount;
            }
            if (in_array($status, ['정산요청', '확인중', '보류'], true)) {
                $summary['review_total'] += $requestAmount;
            }
        }

        return $summary;
    }

    private function fileCounts(array $ids): array
    {
        $ids = array_values(array_filter(array_map('intval', $ids)));
        if ($ids === []) {
            return [];
        }

        $rows = PersonalPaymentSettlementFileModel::query()
            ->select(['settlement_idx'])
            ->whereIn('settlement_idx', $ids)
            ->get()
            ->toArray();
        $counts = [];
        foreach ($rows as $row) {
            $settlementIdx = (int)($row['settlement_idx'] ?? 0);
            $counts[$settlementIdx] = ($counts[$settlementIdx] ?? 0) + 1;
        }

        return $counts;
    }

    private function saveAccount(int $adminIdx, string $bank, string $account, string $depositor): void
    {
        $now = date('Y-m-d H:i:s');
        $existing = $this->getSavedAccount($adminIdx);
        $payload = [
            'bank' => $bank,
            'bank_account' => $account,
            'depositor' => $depositor,
            'updated_at' => $now,
        ];
        if (!empty($existing['idx'])) {
            PersonalPaymentAccountModel::update(['idx' => (int)$existing['idx']], $payload);
            return;
        }

        $payload['ad_pk'] = $adminIdx;
        PersonalPaymentAccountModel::insert($payload);
    }

    private function writeStatusLog(int $settlementIdx, string $status, string $memo, array $actor): void
    {
        PersonalPaymentStatusLogModel::insert([
            'settlement_idx' => $settlementIdx,
            'status' => $status,
            'memo' => $memo,
            'ad_pk' => $actor['idx'],
            'ad_name' => $actor['name'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function storeFiles(int $settlementIdx, int $adminIdx): void
    {
        $files = $_FILES['evidence_files'] ?? null;
        if (!is_array($files) || !isset($files['name']) || !is_array($files['name'])) {
            return;
        }

        $count = count($files['name']);
        for ($i = 0; $i < $count; $i++) {
            $error = (int)($files['error'][$i] ?? UPLOAD_ERR_NO_FILE);
            if ($error === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $file = [
                'name' => $files['name'][$i] ?? '',
                'type' => $files['type'][$i] ?? '',
                'tmp_name' => $files['tmp_name'][$i] ?? '',
                'error' => $error,
                'size' => $files['size'][$i] ?? 0,
            ];
            $stored = $this->storeOneFile($file, $adminIdx);
            PersonalPaymentSettlementFileModel::insert([
                'settlement_idx' => $settlementIdx,
                'file_path' => $stored['path'],
                'original_name' => $stored['name'],
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function storeOneFile(array $file, int $adminIdx): array
    {
        if ((int)($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new Exception('증빙자료를 업로드하지 못했습니다.');
        }
        if ((int)($file['size'] ?? 0) > 20 * 1024 * 1024) {
            throw new Exception('증빙자료는 파일당 20MB 이하만 업로드할 수 있습니다.');
        }

        $tmpName = (string)($file['tmp_name'] ?? '');
        $originalName = (string)($file['name'] ?? '');
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($extension, ['pdf', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'webp'], true) || !is_uploaded_file($tmpName)) {
            throw new Exception('PDF, Excel, 이미지 파일만 첨부할 수 있습니다.');
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($tmpName);
        $allowedMimesByExtension = [
            'pdf' => ['application/pdf'],
            'xls' => ['application/vnd.ms-excel', 'application/CDFV2', 'application/x-ole-storage'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'],
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'webp' => ['image/webp'],
        ];
        if (!in_array($mime, $allowedMimesByExtension[$extension], true)) {
            throw new Exception('증빙자료 파일 형식을 확인할 수 없습니다.');
        }

        $documentRoot = trim((string)($_SERVER['DOCUMENT_ROOT'] ?? ''));
        if ($documentRoot === '') {
            $documentRoot = dirname(__DIR__, 3);
        }
        $directory = rtrim($documentRoot, '/\\') . '/data/personal_payment';
        if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
            throw new Exception('증빙자료 저장 폴더를 만들 수 없습니다.');
        }

        $fileName = 'personal_payment_' . max(0, $adminIdx) . '_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        if (!move_uploaded_file($tmpName, $directory . '/' . $fileName)) {
            throw new Exception('증빙자료 저장에 실패했습니다.');
        }
        @chmod($directory . '/' . $fileName, 0640);

        return [
            'path' => '/data/personal_payment/' . $fileName,
            'name' => mb_substr($originalName, 0, 200),
        ];
    }

    private function notifyCreated(array $input, array $actor): void
    {
        try {
            $message = "🟣 개인결제 정산요청\n\n";
            $message .= "신청자 : " . $input['applicant_name'] . "\n";
            $message .= "사용일자 : " . $input['use_date'] . "\n";
            $message .= "사용처 : " . $input['vendor'] . "\n";
            $message .= "결제수단 : " . $input['pay_method'] . "\n";
            $message .= "결제금액 : " . number_format((float)$input['payment_amount']) . "원\n";
            $message .= "정산요청 금액 : " . number_format((float)$input['request_amount']) . "원\n";
            $message .= "등록자 : " . $actor['name'] . "\n";
            $message .= "요청일 : " . date('Y-m-d H:i:s');
            (new TelegramUtils())->sendMessage('-1003769602878', $message);
        } catch (\Throwable $e) {
            error_log('[PersonalPaymentSettlementService] notify failed: ' . $e->getMessage());
        }
    }

    private function maskAccount(string $account): string
    {
        $account = trim($account);
        $length = mb_strlen($account);
        if ($account === '' || $length <= 4) {
            return $account;
        }

        return str_repeat('*', $length - 4) . mb_substr($account, -4);
    }

    private function ensureSchema(): void
    {
        if (self::$schemaReady) {
            return;
        }

        $connection = app('db');
        if (!$connection instanceof \PDO) {
            throw new Exception('데이터베이스 연결을 확인할 수 없습니다.');
        }

        $connection->exec("CREATE TABLE IF NOT EXISTS personal_payment_settlement (
            idx INT UNSIGNED NOT NULL AUTO_INCREMENT,
            applicant_ad_pk INT UNSIGNED NOT NULL,
            applicant_name VARCHAR(100) NOT NULL DEFAULT '',
            use_date DATE NOT NULL,
            vendor VARCHAR(200) NOT NULL DEFAULT '',
            purpose TEXT NULL,
            pay_method VARCHAR(30) NOT NULL DEFAULT '',
            payment_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
            request_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
            bank VARCHAR(50) NOT NULL DEFAULT '',
            bank_account VARCHAR(100) NOT NULL DEFAULT '',
            depositor VARCHAR(100) NOT NULL DEFAULT '',
            account_subject VARCHAR(100) NOT NULL DEFAULT '',
            status VARCHAR(20) NOT NULL DEFAULT '정산요청',
            paid_amount DECIMAL(14,2) NULL,
            paid_date DATE NULL,
            paid_ad_pk INT UNSIGNED NULL,
            paid_ad_name VARCHAR(100) NOT NULL DEFAULT '',
            paid_memo TEXT NULL,
            payout_idx INT UNSIGNED NULL,
            ad_pk INT UNSIGNED NOT NULL DEFAULT 0,
            ad_name VARCHAR(100) NOT NULL DEFAULT '',
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY (idx),
            KEY idx_personal_payment_applicant (applicant_ad_pk),
            KEY idx_personal_payment_status (status),
            KEY idx_personal_payment_use_date (use_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $connection->exec("CREATE TABLE IF NOT EXISTS personal_payment_settlement_file (
            idx INT UNSIGNED NOT NULL AUTO_INCREMENT,
            settlement_idx INT UNSIGNED NOT NULL,
            file_path VARCHAR(255) NOT NULL DEFAULT '',
            original_name VARCHAR(200) NOT NULL DEFAULT '',
            created_at DATETIME NOT NULL,
            PRIMARY KEY (idx),
            KEY idx_personal_payment_file_settlement (settlement_idx)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $connection->exec("CREATE TABLE IF NOT EXISTS personal_payment_settlement_status_log (
            idx INT UNSIGNED NOT NULL AUTO_INCREMENT,
            settlement_idx INT UNSIGNED NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT '',
            memo TEXT NULL,
            ad_pk INT UNSIGNED NOT NULL DEFAULT 0,
            ad_name VARCHAR(100) NOT NULL DEFAULT '',
            created_at DATETIME NOT NULL,
            PRIMARY KEY (idx),
            KEY idx_personal_payment_log_settlement (settlement_idx)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $connection->exec("CREATE TABLE IF NOT EXISTS personal_payment_account (
            idx INT UNSIGNED NOT NULL AUTO_INCREMENT,
            ad_pk INT UNSIGNED NOT NULL,
            bank VARCHAR(50) NOT NULL DEFAULT '',
            bank_account VARCHAR(100) NOT NULL DEFAULT '',
            depositor VARCHAR(100) NOT NULL DEFAULT '',
            updated_at DATETIME NOT NULL,
            PRIMARY KEY (idx),
            UNIQUE KEY uq_personal_payment_account_admin (ad_pk)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        self::$schemaReady = true;
    }
}
