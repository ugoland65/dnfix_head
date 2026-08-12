<?php
namespace App\Services;

use Exception;
use App\Models\PaymentRequestModel;
use App\Core\AuthAdmin;
use App\Utils\TelegramUtils;

class PaymentRequestService
{

    /**
     * 결제요청 목록 조회
     * 
     * @return array
     */
    public function getPaymentRequestList($criteria)
    {

        $page = (int)($criteria['page'] ?? ($criteria['pn'] ?? 1));
        if ($page < 1) {
            $page = 1;
        }
        $perPage = (int)($criteria['per_page'] ?? 100);
        if ($perPage < 1) {
            $perPage = 100;
        }

        $status = $criteria['status'] ?? '요청';
        $keyword = trim((string)($criteria['keyword'] ?? ''));

        $query = PaymentRequestModel::query()
            ->when($status, function($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('idx', 'desc');

        if ($keyword !== '') {
            $query->where(function ($query) use ($keyword) {
                $query->whereRaw('INSTR(bank, :keyword_bank)', ['keyword_bank' => $keyword])
                    ->orWhereRaw('INSTR(bank_account, :keyword_account)', ['keyword_account' => $keyword])
                    ->orWhereRaw('INSTR(depositor, :keyword_depositor)', ['keyword_depositor' => $keyword])
                    ->orWhereRaw('INSTR(foreign_account, :keyword_foreign_account)', ['keyword_foreign_account' => $keyword])
                    ->orWhereRaw('INSTR(memo, :keyword_memo)', ['keyword_memo' => $keyword])
                    ->orWhereRaw('INSTR(meta_json, :keyword_meta)', ['keyword_meta' => $keyword]);
            });
        }

        //$result = $query->toArray();
        $result = $query->paginate($perPage, $page);

        foreach($result['data'] as &$row){
            $row['meta_json'] = json_decode($row['meta_json'], true);
        }
        unset($row);

        return $result;
    }


    /**
     * 결제요청 상세 조회
     * 
     * @param int $idx 결제요청 번호
     * @return array
     */
    public function getPaymentRequestInfo($idx)
    {

        $query = PaymentRequestModel::find($idx)->toArray();
        if( !$query ){
            throw new Exception("결제요청 정보를 찾을 수 없습니다.");
        }
        
        return $query;

    }


    /**
     * 결제요청 저장
     * 
     * @param array $requestData
     * @return array
     */
    public function createPaymentRequest($requestData)
    {

        $category = $requestData['category'] ?? null;
        $kind = $requestData['kind'] ?? null;
        $kind_idx_raw = $requestData['kind_idx'] ?? null;
        $kind_idx = (is_numeric($kind_idx_raw) && (string)$kind_idx_raw !== '') ? (int)$kind_idx_raw : null;
        $currency = $requestData['currency'] ?? 'KRW';
        $amount = str_replace(',', '', (string)($requestData['amount'] ?? ''));
        $memo = trim((string)($requestData['memo'] ?? ''));
        $foreign_account = trim((string)($requestData['foreign_account'] ?? ''));
        $is_vat = $requestData['is_vat'] ?? 'Y';
        $request_date = $requestData['request_date'] ?? date('Y-m-d');
        $depositorName = trim((string)($requestData['depositor_name'] ?? ''));
        $bank = trim((string)($requestData['bank'] ?? ''));
        $bankAccount = trim((string)($requestData['bank_account'] ?? ''));
        $depositor = trim((string)($requestData['depositor'] ?? ''));

        $ad_pk = (int)(AuthAdmin::getSession('sess_idx') ?? 0);
        $ad_name = AuthAdmin::getSession('sess_name');
        $invoiceFilePath = $this->storePaymentRequestInvoiceFile($ad_pk);

        $meta_json = [];
        if( $category == '환불' ){
            $godo_order_no = trim((string)($requestData['godo_order_no'] ?? ''));
            if ($godo_order_no === '') {
                throw new Exception("환불일 때 고도몰 주문번호는 필수입니다.");
            }
            $kind = 'godo_refund';
            $kind_idx = null;
            $meta_json['godo_order_no'] = $godo_order_no;
        }

        $meta_json = json_encode($meta_json, JSON_UNESCAPED_UNICODE);

        $inputData = [
            'kind' => $kind,
            'kind_idx' => $kind_idx,
            'category' => $category,
            'currency' => $currency,
            'amount' => $amount,
            'depositor_name' => $depositorName,
            'foreign_account' => $foreign_account,
            'is_vat' => $is_vat,
            'request_date' => $request_date,
            'bank' => $bank,
            'bank_account' => $bankAccount,
            'depositor' => $depositor,
            'memo' => $memo,
            'invoice_file_path' => $invoiceFilePath,
            'meta_json' => $meta_json,
            'ad_pk' => $ad_pk,
            'ad_name' => $ad_name,
        ];

        $result = PaymentRequestModel::insert($inputData);


        $telegram = new TelegramUtils();

        $message = "🟣 ".$category." 결제요청\n\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "분류 : " . ($category ?: '-') . "\n";
        $message .= "요청금액 : " . number_format($amount) . " " . $currency . "\n";
        $message .= "부가세 포함 여부 : " . ($is_vat === 'Y' ? '포함' : '미포함') . "\n";
        $message .= "무통장 입금자명 : " . ($depositorName !== '' ? $depositorName : '-') . "\n";
        $message .= "결제 희망일 : " . ($request_date ?: '-') . "\n";

        if ($kind !== null && $kind !== '') {
            $message .= "연결 kind : " . $kind . "\n";
        }
        if ($kind_idx !== null) {
            $message .= "연결 kind_idx : " . $kind_idx . "\n";
        }
        if (!empty($meta_json)) {
            $metaDecoded = json_decode($meta_json, true);
            if (is_array($metaDecoded) && !empty($metaDecoded['godo_order_no'])) {
                $message .= "고도몰 주문번호 : " . $metaDecoded['godo_order_no'] . "\n";
            }
        }

        $message .= "━━━━━━━━━━━━━━━━━━━━\n";

        if ($foreign_account !== '') {
            $message .= "해외계좌 : " . $foreign_account . "\n";
        } else {
            $message .= "결제계좌 : " . (($bank !== '') ? $bank : '-') . " " . (($bankAccount !== '') ? $bankAccount : '-') . "\n";
            $message .= "예금주 : " . (($depositor !== '') ? $depositor : '-') . "\n";
        }
        $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";
        $message .= "요청내용\n";
        $message .= ($memo !== '' ? $memo : '(요청내용 없음)') . "\n\n";
        $message .= "————————————\n\n";
        $message .= "요청일 : " . date('Y-m-d H:i:s') . "\n";
        $message .= "등록자 : " . $ad_name . "\n";
        $message .= "————————————\n\n";

        // parse_mode HTML 사용 시 memo 내 특수문자로 전송 실패할 수 있어 plain text로 보낸다.
        $chatId = "-1003769602878";
        $telegramResult = $telegram->sendMessage($chatId, $message);



        return $result;

    }


    /**
     * 결제요청 수정
     * 
     * @param array $requestData
     * @return array
     */
    public function updatePaymentRequest($requestData)
    {
        $idx = $requestData['idx'] ?? null;

        if( empty($idx) ){
            throw new Exception("결제요청 번호가 없습니다.");
        }
        $existingRequest = PaymentRequestModel::find($idx);
        $existingRequest = $existingRequest ? $existingRequest->toArray() : [];
        if (empty($existingRequest)) {
            throw new Exception("결제요청 정보를 찾을 수 없습니다.");
        }

        $category = $requestData['category'] ?? null;
        $kind = $requestData['kind'] ?? null;
        $kind_idx_raw = $requestData['kind_idx'] ?? null;
        $kind_idx = (is_numeric($kind_idx_raw) && (string)$kind_idx_raw !== '') ? (int)$kind_idx_raw : null;
        $currency = $requestData['currency'] ?? 'KRW';
        $amount = str_replace(',', '', (string)($requestData['amount'] ?? ''));
        $foreign_account = $requestData['foreign_account'] ?? null;
        $is_vat = $requestData['is_vat'] ?? 'Y';
        $request_date = $requestData['request_date'] ?? date('Y-m-d');

        $status = $requestData['status'] ?? null;
        $approved_ad_pk = 0;
        $approved_ad_name = '';
        $process_date = null;
        $process_memo = '';
        $invoiceFilePath = $this->storePaymentRequestInvoiceFile((int)(AuthAdmin::getSession('sess_idx') ?? 0));


        if( $status != '요청' ){
            $approved_ad_pk = (int)(AuthAdmin::getSession('sess_idx') ?? 0);
            $approved_ad_name = AuthAdmin::getSession('sess_name');
            $process_date = $requestData['process_date'] ?? date('Y-m-d');
            $process_memo = $requestData['process_memo'] ?? null;
        }

        $meta_json = [];
        if( $category == '환불' ){
            $godo_order_no = trim((string)($requestData['godo_order_no'] ?? ''));
            if ($godo_order_no === '') {
                throw new Exception("환불일 때 고도몰 주문번호는 필수입니다.");
            }
            $kind = 'godo_refund';
            $kind_idx = null;
            $meta_json['godo_order_no'] = $godo_order_no;
        }

        $meta_json = json_encode($meta_json, JSON_UNESCAPED_UNICODE);        

        $updateData = [
            'kind' => $kind,
            'kind_idx' => $kind_idx,
            'category' => $category,
            'currency' => $currency,
            'amount' => $amount,
            'depositor_name' => $requestData['depositor_name'],
            'foreign_account' => $foreign_account,
            'is_vat' => $is_vat,
            'request_date' => $request_date,
            'status' => $status,
            'bank' => $requestData['bank'],
            'bank_account' => $requestData['bank_account'],
            'depositor' => $requestData['depositor'],
            'memo' => $requestData['memo'],
            'invoice_file_path' => $invoiceFilePath ?? (string)($existingRequest['invoice_file_path'] ?? ''),
            'meta_json' => $meta_json,
            'approved_ad_pk' => $approved_ad_pk,
            'approved_ad_name' => $approved_ad_name,
            'process_date' => $process_date,
            'process_memo' => $process_memo ?? '',
        ];

        $result = PaymentRequestModel::update(['idx' => $idx], $updateData);
        if ($invoiceFilePath !== null && $result) {
            $this->deletePaymentRequestInvoiceFile((string)($existingRequest['invoice_file_path'] ?? ''));
        }

        if ($status === '처리완료') {
            try {
                $adminServices = new AdminServices();
                $paymentRequestInfo = PaymentRequestModel::find($idx);
                $target_mb_idx = 0;
                if (is_array($paymentRequestInfo)) {
                    $target_mb_idx = (int)($paymentRequestInfo['ad_pk'] ?? 0);
                } elseif (is_object($paymentRequestInfo)) {
                    $target_mb_idx = (int)($paymentRequestInfo->ad_pk ?? 0);
                }
                if ($target_mb_idx <= 0) {
                    return $result;
                }

                $mentionTargetTelegramIds = $adminServices->getMentionTargetTelegramId($target_mb_idx);

                if (!empty($mentionTargetTelegramIds)) {
                    $telegram = new TelegramUtils();
                    $message = "🟢 결제요청 처리완료\n";
                    $message .= "분류 : " . ($category ?: '-') . "\n";
                    $message .= "요청금액 : " . $amount . " " . $currency . "\n";
                    $message .= "결제요청이 처리완료 되었습니다.";

                    foreach ($mentionTargetTelegramIds as $mentionTargetTelegramId) {
                        $token = trim((string)($mentionTargetTelegramId['ad_telegram_token'] ?? ''));
                        if ($token === '') {
                            continue;
                        }
                        $telegram->sendMessage($token, $message);
                    }
                }
            } catch (\Throwable $e) {
                error_log('[PaymentRequestService] complete notify failed: ' . $e->getMessage());
            }
        }

        return $result;



    }

    /**
     * 청구서(PDF/XLS/XLSX) 파일을 저장하고 웹 경로를 반환한다.
     */
    private function storePaymentRequestInvoiceFile(int $adminIdx): ?string
    {
        $file = $_FILES['invoice_file'] ?? null;
        if (!is_array($file) || (int)($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ((int)($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new Exception('청구서 파일을 업로드하지 못했습니다.');
        }
        if ((int)($file['size'] ?? 0) > 20 * 1024 * 1024) {
            throw new Exception('청구서 파일은 20MB 이하만 업로드할 수 있습니다.');
        }

        $tmpName = (string)($file['tmp_name'] ?? '');
        $extension = strtolower(pathinfo((string)($file['name'] ?? ''), PATHINFO_EXTENSION));
        if (!in_array($extension, ['pdf', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'webp'], true) || !is_uploaded_file($tmpName)) {
            throw new Exception('PDF, XLS, XLSX 또는 이미지 파일만 첨부할 수 있습니다.');
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($tmpName);
        $allowedMimesByExtension = [
            'pdf' => ['application/pdf'],
            'xls' => [
                'application/vnd.ms-excel',
                'application/CDFV2',
                'application/x-ole-storage',
            ],
            'xlsx' => [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/zip',
            ],
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'webp' => ['image/webp'],
        ];
        if (!in_array($mime, $allowedMimesByExtension[$extension], true)) {
            throw new Exception('청구서 파일 형식을 확인할 수 없습니다.');
        }

        $documentRoot = trim((string)($_SERVER['DOCUMENT_ROOT'] ?? ''));
        if ($documentRoot === '') {
            $documentRoot = dirname(__DIR__, 3);
        }
        $directory = rtrim($documentRoot, '/\\') . '/data/payment_request';
        if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
            throw new Exception('청구서 파일 저장 폴더를 만들 수 없습니다.');
        }
        if (!is_writable($directory)) {
            throw new Exception('청구서 파일 저장 폴더에 쓰기 권한이 없습니다. 서버 권한을 확인해주세요: ' . $directory);
        }

        $fileName = 'payment_request_' . max(0, $adminIdx) . '_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        if (!move_uploaded_file($tmpName, $directory . '/' . $fileName)) {
            throw new Exception('청구서 파일 저장에 실패했습니다.');
        }
        @chmod($directory . '/' . $fileName, 0640);

        return '/data/payment_request/' . $fileName;
    }

    private function deletePaymentRequestInvoiceFile(string $publicPath): void
    {
        if (strpos($publicPath, '/data/payment_request/') !== 0) {
            return;
        }

        $fileName = basename($publicPath);
        $documentRoot = trim((string)($_SERVER['DOCUMENT_ROOT'] ?? ''));
        if ($documentRoot === '') {
            $documentRoot = dirname(__DIR__, 3);
        }
        $filePath = rtrim($documentRoot, '/\\') . '/data/payment_request/' . $fileName;
        if (is_file($filePath)) {
            @unlink($filePath);
        }
    }


}