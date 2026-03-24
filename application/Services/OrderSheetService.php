<?php

namespace App\Services;

use Exception;
use App\Models\OrderSheetModel;
use App\Models\CalendarModel;
use App\Models\AdminModel;
use App\Core\AuthAdmin;
use App\Classes\UploadedFile;
use App\Services\AdminActionLogService;

class OrderSheetService
{


    /**
     * 주문서 목록 조회
     * @param array $criteria
     * @return array
     */
    public function getOrderSheetList($criteria)
    {

        $page = (int)($criteria['page'] ?? ($criteria['pn'] ?? 1));
        if ($page < 1) {
            $page = 1;
        }
        $perPage = (int)($criteria['per_page'] ?? 100);
        if ($perPage < 1) {
            $perPage = 100;
        }

        $ooImport = (string)($criteria['oo_import'] ?? 'all');
        $ooState = (string)($criteria['oo_state'] ?? 'ing');
        $ooFormIdx = (int)($criteria['oo_form_idx'] ?? 0);
        $searchValue = trim((string)($criteria['search_value'] ?? ''));

        $query = OrderSheetModel::query()
            ->select(['ona_order.*', 'ona_order_group.oog_name'])
            ->leftJoin('ona_order_group', 'ona_order_group.oog_idx', '=', 'ona_order.oo_form_idx')
            ->orderBy('ona_order.oo_idx', 'desc');

        if ($ooImport === '수입') {
            $query->whereRaw("ona_order.oo_import IN ('수입','구매대행')");
        } elseif ($ooImport === '국내') {
            $query->where('ona_order.oo_import', '=', '국내');
        }

        if( !empty($ooState) ){
            if ($ooState == 'ing') {
                $query->whereRaw("ona_order.oo_state IN ('1','2','4','5')");
            } else if ($ooState == 'all') {
            } else {
                $query->where('ona_order.oo_state', '=', $ooState);
            }
        }

        if ($ooFormIdx > 0) {
            $query->where('ona_order.oo_form_idx', '=', $ooFormIdx);
        }

        if ($searchValue !== '') {
            $searchEscaped = addslashes($searchValue);
            $query->whereRaw(
                "(INSTR(ona_order.oo_name, '{$searchEscaped}') > 0
                OR INSTR(ona_order.oo_express_data, '{$searchEscaped}') > 0
                OR INSTR(ona_order.oo_tex_data, '{$searchEscaped}') > 0)"
            );
        }

        $result = $query->paginate($perPage, $page);

        $stateTextMap = [
            1 => '작성중',
            2 => '주문전송',
            4 => '입금완료',
            5 => '입고완료',
            7 => '주문종료',
        ];

        foreach ($result['data'] as &$row) {
            $row['oo_price_data'] = json_decode($row['oo_price_data'] ?? '{}', true);
            if (!is_array($row['oo_price_data'])) {
                $row['oo_price_data'] = [];
            }

            /*
            $reg = json_decode($row['reg'] ?? '{}', true);
            if (!is_array($reg)) {
                $reg = [];
            }

            $showDate = '';
            $regDate = (string)($reg['reg']['date'] ?? '');
            $regName = (string)($reg['reg']['name'] ?? '');
            if ($regDate !== '') {
                $showDate = date('y.m.d H:i', strtotime($regDate)) . '<br>(' . $regName . ')';
            } else {
                $ooDate = (int)($row['oo_date'] ?? 0);
                if ($ooDate > 0) {
                    $showDate = date('Y.m.d H:i', $ooDate);
                }
            }
            $row['show_date'] = $showDate;
            */

            $state = (int)($row['oo_state'] ?? 0);
            if ($state === 2) {
                $row['tr_class'] = 'tr-2';
            } elseif ($state === 4) {
                $row['tr_class'] = 'tr-4';
            } elseif ($state === 7) {
                $row['tr_class'] = 'status_end';
            } else {
                $row['tr_class'] = 'tr-normal';
            }
            $row['oo_state_text'] = $stateTextMap[$state] ?? '';
        }
        unset($row);

        return $result;
    }


    /*
     * 주문서 정보 조회
     * @param int $idx
     * @return array
     */
    public function getOrderSheetInfo($idx)
    {

        $query = OrderSheetModel::find($idx);
        if (!$query) {
            throw new Exception("주문서 정보를 찾을 수 없습니다.");
        }

        $result = $query->toArray();

        $admin_query = AdminModel::query()
            ->select(['idx', 'ad_id', 'ad_name', 'ad_nick'])
            ->get()
            ->toArray();

        $adminMap = [];
        foreach ($admin_query as $row) {
            $adminMap[$row['ad_id']] = $row['ad_name'] ?? '';
        }

        $result['oo_price_data'] = json_decode($result['oo_price_data'] ?? '{}', true);
        $result['oo_date_data'] = json_decode($result['oo_date_data'] ?? '{}', true);
        $result['oo_upload_file'] = json_decode($result['oo_upload_file'] ?? '{}', true);
        $result['oo_express_data'] = json_decode($result['oo_express_data'] ?? '{}', true);
        $result['oo_approval_date'] = json_decode($result['oo_approval_date'] ?? '{}', true);
        $result['oo_tex_data'] = json_decode($result['oo_tex_data'] ?? '{}', true);
        $result['oo_json'] = json_decode($result['oo_json'] ?? '{}', true);
        $result['reg'] = json_decode($result['reg'] ?? '{}', true);
        $result['oo_stock'] = json_decode($result['oo_stock'] ?? '{}', true);

        if (!is_array($result['oo_upload_file'])) {
            $result['oo_upload_file'] = [];
        }
        if (!isset($result['oo_upload_file']['invoice']) || !is_array($result['oo_upload_file']['invoice'])) {
            $result['oo_upload_file']['invoice'] = [];
        }
        if (!isset($result['oo_upload_file']['import_declaration']) || !is_array($result['oo_upload_file']['import_declaration'])) {
            $result['oo_upload_file']['import_declaration'] = [];
        }

        // 결제 파일은 레거시(pay_file)와 신규(pay) 키를 모두 호환한다.
        $payFiles = [];
        if (isset($result['oo_upload_file']['pay']) && is_array($result['oo_upload_file']['pay'])) {
            $payFiles = $result['oo_upload_file']['pay'];
        }
        if (isset($result['oo_upload_file']['pay_file']) && is_array($result['oo_upload_file']['pay_file'])) {
            $payFiles = array_merge($payFiles, $result['oo_upload_file']['pay_file']);
        }
        if (!empty($payFiles)) {
            $seen = [];
            $deduped = [];
            foreach ($payFiles as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $dedupeKey = ($row['name'] ?? '') . '|' . ($row['date'] ?? '');
                if ($dedupeKey !== '|' && isset($seen[$dedupeKey])) {
                    continue;
                }
                $seen[$dedupeKey] = true;
                $deduped[] = $row;
            }
            $payFiles = $deduped;
        }
        $result['oo_upload_file']['pay'] = $payFiles;
        $result['oo_upload_file']['pay_file'] = $payFiles;

        //주문 관련 파일
        foreach( $result['oo_upload_file']['invoice'] as &$invoice ){
            $invoice['reg_name'] = $adminMap[$invoice['id']] ?? '';
        }
        unset($invoice);

        //결제 관련 파일
        foreach( $result['oo_upload_file']['pay'] as &$pay ){
            $pay['reg_name'] = $adminMap[$pay['id']] ?? '';
        }
        unset($pay);

        foreach( $result['oo_upload_file']['import_declaration'] as &$import_declaration ){
            $import_declaration['reg_name'] = $adminMap[$import_declaration['id']] ?? '';
        }
        unset($import_declaration);



        return $result;
    }


    /**
     * 주문서 생성
     * @param array $data
     * @return array
     */
    public function createOrderSheet($data)
    {
        $name = $data['oo_name'] ?? '';
        if (empty($name)) {
            throw new Exception("주문서명을 입력해주세요.");
        }

        $input_data = $this->buildOrderSheetInputData($data, []);

        $orderSheetModel = new OrderSheetModel();
        $orderSheetInsertResult = $orderSheetModel->insert($input_data);
        $afterData = $this->getOrderSheetForLog($orderSheetInsertResult);
        $this->writeOrderSheetActionLog(
            $orderSheetInsertResult,
            'create',
            '주문서 생성',
            [],
            $afterData
        );

        return [
            'status' => 'success',
            'message' => '주문서 저장 완료',
            'order_sheet_idx' => $orderSheetInsertResult,
        ];
    }

    /**
     * 주문서 저장
     * @param Request $request
     * @return array
     */
    public function saveOrderSheet($data)
    {
        $idx = $data['idx'] ?? null;
        if (empty($idx)) {
            throw new Exception("주문서 번호가 없습니다.");
        }

        $orderSheetInfo = OrderSheetModel::find($idx);
        if (!$orderSheetInfo) {
            throw new Exception("주문서를 찾을 수 없습니다.");
        }

        $date_data = json_decode($orderSheetInfo['oo_date_data'] ?? '{}', true);
        if (!is_array($date_data)) {
            $date_data = [];
        }

        $name = $data['oo_name'] ?? '';
        if (empty($name)) {
            throw new Exception("주문서명을 입력해주세요.");
        }

        $beforeData = $this->getOrderSheetForLog($idx);
        $input_data = $this->buildOrderSheetInputData($data, $date_data);

        $orderSheetModel = new OrderSheetModel();
        $orderSheetInsertResult = $orderSheetModel->update(['oo_idx' => $idx], $input_data);
        $afterData = $this->getOrderSheetForLog($idx);
        $this->writeOrderSheetActionLog(
            $idx,
            'update',
            '주문서 수정',
            $beforeData,
            $afterData
        );

        return [
            'status' => 'success',
            'message' => '주문서 저장 완료',
            'order_sheet_idx' => $orderSheetInsertResult,
        ];

    }

    /**
     * 주문서 입력 데이터 생성
     * @param array $data
     * @param array $dateData
     * @return array
     */
    private function buildOrderSheetInputData(array $data, array $dateData): array
    {
        $po_name = $data['oo_po_name'] ?? '';
        $form_idx = $data['oo_form_idx'] ?? 0;
        $import = $data['oo_import'] ?? '';
        $sort = $data['oo_sort'] ?? 0;
        $state = $data['oo_state'] ?? 1;
        $order_send_date = $data['order_send_date'] ?? '';
        $change_price_mode = $data['change_price_mode'] ?? []; //가격변동사항 모드
        $change_price_body = $data['change_price_body'] ?? []; //가격변동사항 내용
        $change_price_price = $data['change_price_price'] ?? []; //가격변동사항 금액
        $pay_mode = $data['pay_mode'] ?? []; //결제 모드
        $pay_price = $data['pay_price'] ?? []; //결제 금액
        $pay_date = $data['pay_date'] ?? []; //결제 일자
        $pay_memo = $data['pay_memo'] ?? []; //결제 메모

        if (!is_array($change_price_mode)) $change_price_mode = [];
        if (!is_array($change_price_body)) $change_price_body = [];
        if (!is_array($change_price_price)) $change_price_price = [];
        if (!is_array($pay_mode)) $pay_mode = [];
        if (!is_array($pay_price)) $pay_price = [];
        if (!is_array($pay_date)) $pay_date = [];
        if (!is_array($pay_memo)) $pay_memo = [];

        $prd_currency = $data['oo_prd_currency'] ?? ''; //상품 가격 화폐
        $prd_exchange_rate_raw = trim((string)($data['oo_prd_exchange_rate'] ?? ''));
        $prd_exchange_rate = $prd_exchange_rate_raw === '' ? 0 : (float)preg_replace('/[,\s]/', '', $prd_exchange_rate_raw); //상품 가격 환율

        $memo = $data['oo_memo'] ?? '';

        $sum_currency = $data['oo_sum_currency'] ?? ''; //주문 결제 가격 화폐
        $sum_exchange_rate_raw = trim((string)($data['oo_sum_exchange_rate'] ?? ''));
        $sum_exchange_rate = $sum_exchange_rate_raw === '' ? 0 : (float)preg_replace('/[,\s]/', '', $sum_exchange_rate_raw); //주문 결제 가격 환율

        $name = $data['oo_name'] ?? '';
        $prd_sum_price = (int)str_replace(',', '', (string)($data['prd_sum_price'] ?? '0')); // 결제가격
        $oo_sum_price = (int)str_replace(',', '', (string)($data['oo_sum_price'] ?? '0')); // 결제가격
        $oo_fn_price = (double)str_replace(',', '', $data['oo_fn_price'] ?? '0'); // 확정 주문 금액
        $pay_fee = (double)str_replace(',', '', $data['pay_fee'] ?? '0'); // 결제 수수료
        $in_date = trim((string)($data['in_date'] ?? '')); //입고일
        $inDateColumnValue = ($in_date === '') ? null : $in_date;
        $oo_price_kr = (double)str_replace(',', '', $data['oo_price_kr'] ?? '0'); // 최종 합계 결제액

        //가격변동사항
        $_change_price = [];
        foreach ($change_price_mode as $key => $value) {
            $_mode = $change_price_mode[$key] ?? "";
            $_body = $change_price_body[$key] ?? "";
            $_price = (double)str_replace(',', '', $change_price_price[$key] ?? '0');

            $_change_price[] = [
                'mode' => $_mode,
                'body' => $_body,
                'price' => $_price,
            ];
        }

        //결제처리
        $_add_pay_list = [];
        foreach ($pay_mode as $key => $value) {
            $_pay_mode = $pay_mode[$key] ?? "";
            $_pay_price = (double)str_replace(',', '', $pay_price[$key] ?? '0');
            $_pay_date = $pay_date[$key] ?? "";
            $_pay_memo = $pay_memo[$key] ?? "";

            $_add_pay_list[] = [
                'pay_mode' => $_pay_mode,
                'pay_price' => $_pay_price,
                'pay_date' => $_pay_date,
                'pay_memo' => $_pay_memo,
            ];
        }

        $price_data_json = [
            'prd_sum_price' => $prd_sum_price ?? 0,
            'price' => $oo_sum_price ?? 0,
            'currency' => $sum_currency,
            'change_price' => $_change_price ?? [],
            'pay_fee' => $pay_fee ?? 0,
            'pay_price' => $oo_price_kr ?? 0,
            'pay_list' => $_add_pay_list ?? [],
        ];
        $price_data = json_encode($price_data_json, JSON_UNESCAPED_UNICODE);

        $dateData['order_send_date'] = $order_send_date; //주문서 발송일
        $dateData['in_date'] = $in_date; //입고일
        $date_data = json_encode($dateData, JSON_UNESCAPED_UNICODE);

        $express_mode = $data['express_mode'] ?? '';
        $express_name = $data['express_name'] ?? '';
        $express_number = $data['express_number'] ?? '';
        $express_report_weight = $data['express_report_weight'] ?? '';
        $express_weight = $data['express_weight'] ?? '';
        $express_cbm = $data['express_cbm'] ?? '';
        $express_box = $data['express_box'] ?? '';
        $express_price_expected_date = trim((string)($data['express_price_expected_date'] ?? ''));
        $express_price_expected = (int)str_replace(',', '', (string)($data['express_price_expected'] ?? '0'));
        $express_price = (int)str_replace(',', '', (string)($data['express_price'] ?? '0'));
        $express_price_add = (int)str_replace(',', '', (string)($data['express_price_add'] ?? '0'));

        $express_data_json = [
            'mode' => $express_mode,
            'name' => $express_name,
            'number' => $express_number,
            'report_weight' => $express_report_weight,
            'weight' => $express_weight,
            'cbm' => $express_cbm,
            'box' => $express_box,
            'price_expected_date' => $express_price_expected_date,
            'price_expected' => $express_price_expected,
            'price' => $express_price,
            'price_add' => $express_price_add
        ];
        $express_data = json_encode($express_data_json, JSON_UNESCAPED_UNICODE);

        $tex_num = $data['tex_num'] ?? '';
        $tex_report_price = (int)str_replace(',', '', (string)($data['tex_report_price'] ?? '0'));
        $tex_duty_price = (int)str_replace(',', '', (string)($data['tex_duty_price'] ?? '0'));
        $tex_vat_price = (int)str_replace(',', '', (string)($data['tex_vat_price'] ?? '0'));
        $tex_commission = (int)str_replace(',', '', (string)($data['tex_commission'] ?? '0'));

        $tex_data_json = [
            'num' => $tex_num,
            'report_price' => $tex_report_price,
            'duty_price' => $tex_duty_price,
            'vat_price' => $tex_vat_price,
            'commission' => $tex_commission
        ];
        $tex_data = json_encode($tex_data_json, JSON_UNESCAPED_UNICODE);

        $reg_json = AuthAdmin::getConnectionInfo();
        $reg = json_encode($reg_json, JSON_UNESCAPED_UNICODE);

        // created_by 컬럼은 정수(PK) 컬럼이므로 sess_idx를 사용하고 정수로 강제 변환
        $created_by = (int)(AuthAdmin::getSession('sess_idx') ?? 0);
        $created_name = AuthAdmin::getSession('sess_name');

        return [
            'oo_name' => $name ?? '',
            'oo_po_name' => $po_name ?? '',
            'oo_form_idx' => $form_idx ?? 0,
            'oo_import' => $import ?? '', //수입형태
            'oo_sort' => $sort ?? 0,
            'oo_state' => $state ?? 0,
            'oo_prd_currency' => $prd_currency ?? '', //상품 가격 화폐
            'oo_prd_exchange_rate' => $prd_exchange_rate ?? 0, //상품 가격 환율
            'oo_memo' => $memo ?? '',
            'oo_price_data' => $price_data ?? '',
            'oo_fn_price' => $oo_fn_price ?? 0, //확정 주문 금액
            'oo_express_data' => $express_data ?? '',
            'oo_tex_data' => $tex_data ?? '',
            'oo_date_data' => $date_data ?? '',
            'oo_sum_currency' => $sum_currency ?? '', //주문 결제 가격 화폐
            'oo_sum_exchange_rate' => $sum_exchange_rate ?? 0, //주문 결제 가격 환율
            'oo_sum_price' => $oo_sum_price ?? 0, //주문 결제 가격
            'oo_in_date' => $inDateColumnValue, //입고일
            'reg' => $reg ?? '',
            'created_by' => $created_by,
            'created_name' => $created_name ?? '',
            'oo_price_kr' => $oo_price_kr ?? 0, //최종 합계 결제액
        ];
    }


    /**
     * 주문서 상태 변경 (레거시 processing.order_sheet.php 리팩토링)
     *
     * @param array $data
     * @return array
     */
    public function orderSheetState(array $data): array
    {
        $idx = $data['idx'] ?? null;
        $state = $data['state'] ?? null;
        $in_date = $data['in_date'] ?? '';

        if (empty($idx) || $state === null || $state === '') {
            throw new Exception('필수 값이 누락되었습니다.');
        }

        $row = OrderSheetModel::query()
            ->select(['oo_state', 'oo_date_data'])
            ->where('oo_idx', $idx)
            ->first();
        $row = $row ? $row->toArray() : null;

        if (empty($row)) {
            throw new Exception('주문서를 찾을 수 없습니다.');
        }

        $oo_state = $row['oo_state'] ?? '';
        $oo_date_data = json_decode($row['oo_date_data'] ?? '{}', true);
        if (!is_array($oo_date_data)) {
            $oo_date_data = [];
        }
        if (!isset($oo_date_data['state']) || !is_array($oo_date_data['state'])) {
            $oo_date_data['state'] = [];
        }

        if ($oo_state != $state) {
            $oo_date_data['in_date'] = $in_date;
            $oo_date_data['state'][] = [
                'state_before' => $oo_state,
                'state_after' => $state,
                'date' => date('Y-m-d H:i:s'),
                'id' => AuthAdmin::getSession('sess_id'),
                'name' => AuthAdmin::getSession('sess_name'),
            ];
        }

        $update_data = [
            'oo_state' => $state,
            'oo_date_data' => json_encode($oo_date_data, JSON_UNESCAPED_UNICODE),
        ];

        $beforeData = $this->getOrderSheetForLog($idx);
        OrderSheetModel::update(['oo_idx' => $idx], $update_data);
        $afterData = $this->getOrderSheetForLog($idx);
        $this->writeOrderSheetActionLog(
            $idx,
            'update_state',
            '주문서 상태 변경',
            $beforeData,
            $afterData
        );

        return [
            'success' => true,
            'msg' => '완료',
        ];
    }


    /**
     * 주문서 파일 등록
     * @param array $data
     * @param array $files
     * @return array
     */
    public function orderSheetFile(array $data, array $files): array
    {
        $idx = $data['idx'] ?? null;
        $smode = $data['smode'] ?? ($data['mode'] ?? '');
        if ($smode === 'pay_file') {
            $smode = 'pay';
        }
        $viewName = $data['sname'] ?? ($data['view_name'] ?? '');

        if (empty($idx) || empty($smode)) {
            throw new Exception('필수 값이 누락되었습니다.');
        }

        $fileKey = "upload_file_{$smode}";
        $file = $files['fileObj'] ?? ($files[$fileKey] ?? null);
        if (!$file && !empty($files)) {
            $firstFile = reset($files);
            $file = is_array($firstFile) ? $firstFile : null;
        }

        if (empty($file)) {
            throw new Exception('파일이 없습니다.');
        }

        $uploaded = new UploadedFile($file);

        $savePrefix = '';
        if ($smode === 'pay') {
            $savePrefix = "pay_file_{$idx}_";
        } elseif ($smode === 'import_declaration') {
            $savePrefix = "import_declaration_{$idx}_";
        } elseif ($smode === 'invoice') {
            $savePrefix = "invoice_{$idx}_";
        } else {
            throw new Exception('유효하지 않은 파일 구분입니다.');
        }

        $uploadDir = ($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__, 2)) . '/data/uploads';
        $extension = $uploaded->getClientOriginalExtension();
        $saveFilename = $savePrefix . time() . ($extension ? '.' . $extension : '');
        $uploaded->move($uploadDir, $saveFilename);

        $orderRow = OrderSheetModel::query()
            ->select(['oo_upload_file'])
            ->where('oo_idx', $idx)
            ->first();
        $orderRow = $orderRow ? $orderRow->toArray() : [];

        $uploadFile = json_decode($orderRow['oo_upload_file'] ?? '{}', true);
        if (!is_array($uploadFile)) {
            $uploadFile = [];
        }

        if (!isset($uploadFile[$smode]) || !is_array($uploadFile[$smode])) {
            $uploadFile[$smode] = [];
        }

        $sizeBytes = (int) $uploaded->getSize();
        $displaySize = $sizeBytes >= 1048576
            ? sprintf('%.2f MB', $sizeBytes / 1048576)
            : sprintf('%.0f KB', ceil($sizeBytes / 1024));

        $uploadRow = [
            'name' => $saveFilename,
            'view_name' => $viewName,
            'size' => $displaySize,
            'date' => date('Y-m-d H:i:s'),
            'id' => AuthAdmin::getSession('sess_id'),
            'reg_name' => AuthAdmin::getSession('sess_name'),
        ];
        $uploadFile[$smode][] = $uploadRow;

        // 결제 파일은 레거시 키(pay_file)와 동기화하여 양쪽 화면을 모두 지원한다.
        if ($smode === 'pay') {
            $uploadFile['pay_file'] = $uploadFile['pay'];
        }

        $beforeData = $this->getOrderSheetForLog($idx);
        OrderSheetModel::update(
            ['oo_idx' => $idx],
            ['oo_upload_file' => json_encode($uploadFile, JSON_UNESCAPED_UNICODE)]
        );
        $afterData = $this->getOrderSheetForLog($idx);
        $this->writeOrderSheetActionLog(
            $idx,
            'update_file_upload',
            '주문서 파일 업로드',
            $beforeData,
            $afterData
        );

        return [
            'success' => true,
            'msg' => '완료',
            'idx' => $idx,
            'filename' => $saveFilename,
            'view_name' => $viewName,
            'size' => $displaySize,
            'reg_id' => AuthAdmin::getSession('sess_id'),
            'reg_name' => AuthAdmin::getSession('sess_name'),
            'reg_date' => date('Y-m-d H:i:s'),
        ];
    }


    /**
     * 주문서 파일 삭제
     * @param array $data
     * @return array
     */
    public function orderSheetFileDelete(array $data): array
    {
        $idx = $data['idx'] ?? null;
        $smode = $data['smode'] ?? ($data['mode'] ?? '');
        if ($smode === 'pay_file') {
            $smode = 'pay';
        }
        $filename = $data['filename'] ?? '';

        if (empty($idx) || empty($smode) || empty($filename)) {
            throw new Exception('필수 값이 누락되었습니다.');
        }

        $uploadDir = ($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__, 2)) . '/data/uploads';
        $targetPath = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
        if (is_file($targetPath)) {
            @unlink($targetPath);
        }

        $orderRow = OrderSheetModel::query()
            ->select(['oo_upload_file'])
            ->where('oo_idx', $idx)
            ->first();
        $orderRow = $orderRow ? $orderRow->toArray() : [];

        $uploadFile = json_decode($orderRow['oo_upload_file'] ?? '{}', true);
        if (!is_array($uploadFile)) {
            $uploadFile = [];
        }
        if (!isset($uploadFile[$smode]) || !is_array($uploadFile[$smode])) {
            $uploadFile[$smode] = [];
        }

        $uploadFile[$smode] = array_values(array_filter(
            $uploadFile[$smode],
            function ($row) use ($filename) {
                return is_array($row) && ($row['name'] ?? '') !== $filename;
            }
        ));

        if ($smode === 'pay') {
            $legacyPayFile = isset($uploadFile['pay_file']) && is_array($uploadFile['pay_file']) ? $uploadFile['pay_file'] : [];
            $uploadFile['pay_file'] = array_values(array_filter(
                $legacyPayFile,
                function ($row) use ($filename) {
                    return is_array($row) && ($row['name'] ?? '') !== $filename;
                }
            ));
            $uploadFile['pay'] = $uploadFile['pay_file'];
        }

        $beforeData = $this->getOrderSheetForLog($idx);
        OrderSheetModel::update(
            ['oo_idx' => $idx],
            ['oo_upload_file' => json_encode($uploadFile, JSON_UNESCAPED_UNICODE)]
        );
        $afterData = $this->getOrderSheetForLog($idx);
        $this->writeOrderSheetActionLog(
            $idx,
            'update_file_delete',
            '주문서 파일 삭제',
            $beforeData,
            $afterData
        );

        return [
            'success' => true,
            'msg' => '완료',
            'idx' => $idx,
        ];
    }

    
    /**
     * 캘린더 결제기한 등록/수정
     * 
     * @param array $data
     * @return array
     */
    public function approvalPayment(array $data): array
    {
        $idx = $data['idx'] ?? null;
        $priceRaw = $data['price'] ?? '0';
        $apMode = $data['ap_mode'] ?? '';
        $date = $data['date'] ?? '';
        $memo = $data['memo'] ?? '';
        $calendarIdx = $data['calendar_idx'] ?? '';

        if (empty($idx) || empty($apMode) || empty($date)) {
            throw new Exception('필수 값이 누락되었습니다.');
        }

        $price = (int)str_replace(',', '', (string)$priceRaw);

        $orderRow = OrderSheetModel::query()
            ->select(['oo_name', 'oo_express_data', 'oo_approval_date'])
            ->where('oo_idx', $idx)
            ->first();
        $orderRow = $orderRow ? $orderRow->toArray() : [];

        $ooName = $orderRow['oo_name'] ?? '';
        $expressData = json_decode($orderRow['oo_express_data'] ?? '{}', true);
        $approvalDate = json_decode($orderRow['oo_approval_date'] ?? '{}', true);

        if (!is_array($expressData)) {
            $expressData = [];
        }
        if (!is_array($approvalDate)) {
            $approvalDate = [];
        }

        $subject = '';
        $mode = '결제기한';
        $kind = '';

        if ($apMode === 'express') {
            $subject = $ooName . ' - 배송비 결제기한';
            $kind = '배송비';

            if ((empty($expressData['price']) || ($expressData['price'] ?? 0) == 0) && $price > 0) {
                $expressData['price'] = $price;
            }
        } elseif ($apMode === 'tax') {
            $subject = $ooName . ' - 관/부가세 결제기한';
            $kind = '관/부가세';
        } else {
            throw new Exception('결제기한 모드가 올바르지 않습니다.');
        }

        $dataJson = json_encode(['oo_idx' => $idx, 'price' => $price], JSON_UNESCAPED_UNICODE);
        $regInfo = AuthAdmin::getConnectionInfo();
        $regJson = json_encode(['reg' => $regInfo], JSON_UNESCAPED_UNICODE);

        if (!empty($calendarIdx)) {
            $calendarStateRow = CalendarModel::query()
                ->select(['state'])
                ->where('idx', '=', $calendarIdx)
                ->first();
            $calendarStateRow = $calendarStateRow ? $calendarStateRow->toArray() : [];
            $calendarState = $calendarStateRow['state'] ?? '';

            if ($calendarState === 'E') {
                throw new Exception('완료된 결제기한은 수정할 수 없습니다.');
            }
            if ($calendarState === 'C') {
                throw new Exception('취소된 결제기한은 수정할 수 없습니다.');
            }

            if ($calendarState !== '' && $calendarState !== 'I') {
                throw new Exception('진행중 상태에서만 수정할 수 있습니다.');
            }

            CalendarModel::query()
                ->where('idx', '=', $calendarIdx)
                ->update([
                    'subject' => $subject,
                    'kind' => $kind,
                    'mode' => $mode,
                    'date_s' => $date,
                    'date_e' => $date,
                    'data' => $dataJson,
                    'targrt_idx' => $idx,
                    'memo' => $memo,
                    'reg' => $regJson,
                ]);
        } else {
            $calendarIdx = CalendarModel::query()->insertGetId([
                'subject' => $subject,
                'kind' => $kind,
                'mode' => $mode,
                'date_s' => $date,
                'date_e' => $date,
                'data' => $dataJson,
                'targrt_idx' => $idx,
                'memo' => $memo,
                'comment_count' => 0,
                'reg' => $regJson,
            ]);
        }

        if (!isset($approvalDate[$apMode]) || !is_array($approvalDate[$apMode])) {
            $approvalDate[$apMode] = [];
        }
        $approvalDate[$apMode]['price'] = $price;
        $approvalDate[$apMode]['approval'] = [
            'date' => $date,
            'calendar_idx' => $calendarIdx,
            'reg' => $regInfo,
        ];

        $beforeData = $this->getOrderSheetForLog($idx);
        OrderSheetModel::update(
            ['oo_idx' => $idx],
            [
                'oo_express_data' => json_encode($expressData, JSON_UNESCAPED_UNICODE),
                'oo_approval_date' => json_encode($approvalDate, JSON_UNESCAPED_UNICODE),
            ]
        );
        $afterData = $this->getOrderSheetForLog($idx);
        $this->writeOrderSheetActionLog(
            $idx,
            'update_approval_payment',
            '주문서 결제기한 등록/수정',
            $beforeData,
            $afterData
        );

        return [
            'success' => true,
            'msg' => '완료',
            'calendar_idx' => $calendarIdx,
        ];
    }

    /**
     * 결제기한 캘린더 완료처리
     * @param array $data
     * @return array
     */
    public function calendarOk(array $data): array
    {
        $idx = $data['idx'] ?? null;
        $calendarIdx = $data['calendar_idx'] ?? null;
        $apMode = $data['ap_mode'] ?? '';

        if (empty($idx) || empty($calendarIdx) || empty($apMode)) {
            throw new Exception('필수 값이 누락되었습니다.');
        }

        $regInfo = AuthAdmin::getConnectionInfo();

        $calendarRow = CalendarModel::query()
            ->select(['reg'])
            ->where('idx', $calendarIdx)
            ->first();
        $calendarRow = $calendarRow ? $calendarRow->toArray() : [];

        $regJson = json_decode($calendarRow['reg'] ?? '{}', true);
        if (!is_array($regJson)) {
            $regJson = [];
        }
        $regJson['mod'][] = $regInfo;

        CalendarModel::query()
            ->where('idx', '=', $calendarIdx)
            ->update([
                'state' => 'E',
                'reg' => json_encode($regJson, JSON_UNESCAPED_UNICODE),
            ]);

        $orderRow = OrderSheetModel::query()
            ->select(['oo_approval_date'])
            ->where('oo_idx', $idx)
            ->first();
        $orderRow = $orderRow ? $orderRow->toArray() : [];

        $approvalDate = json_decode($orderRow['oo_approval_date'] ?? '{}', true);
        if (!is_array($approvalDate)) {
            $approvalDate = [];
        }
        if (!isset($approvalDate[$apMode]) || !is_array($approvalDate[$apMode])) {
            $approvalDate[$apMode] = ['approval' => []];
        }
        if (!isset($approvalDate[$apMode]['approval']) || !is_array($approvalDate[$apMode]['approval'])) {
            $approvalDate[$apMode]['approval'] = [];
        }

        $approvalDate[$apMode]['approval']['calendar_state'] = 'E';
        $approvalDate[$apMode]['approval']['calendar_reg'] = $regInfo;

        $beforeData = $this->getOrderSheetForLog($idx);
        OrderSheetModel::update(
            ['oo_idx' => $idx],
            ['oo_approval_date' => json_encode($approvalDate, JSON_UNESCAPED_UNICODE)]
        );
        $afterData = $this->getOrderSheetForLog($idx);
        $this->writeOrderSheetActionLog(
            $idx,
            'update_calendar_ok',
            '주문서 결제기한 완료처리',
            $beforeData,
            $afterData
        );

        return [
            'success' => true,
            'msg' => '완료',
        ];
    }


    /**
     * 결제기한 캘린더 삭제
     * @param array $data
     * @return array
     */
    public function calendarDel(array $data): array
    {
        $idx = $data['idx'] ?? null;
        $calendarIdx = $data['calendar_idx'] ?? null;
        $apMode = $data['ap_mode'] ?? '';

        if (empty($idx) || empty($calendarIdx) || empty($apMode)) {
            throw new Exception('필수 값이 누락되었습니다.');
        }

        $orderRow = OrderSheetModel::query()
            ->select(['oo_approval_date'])
            ->where('oo_idx', $idx)
            ->first();
        $orderRow = $orderRow ? $orderRow->toArray() : [];

        $approvalDate = json_decode($orderRow['oo_approval_date'] ?? '{}', true);
        if (!is_array($approvalDate)) {
            $approvalDate = [];
        }
        if (!isset($approvalDate[$apMode]) || !is_array($approvalDate[$apMode])) {
            $approvalDate[$apMode] = ['approval' => []];
        }
        if (!isset($approvalDate[$apMode]['approval']) || !is_array($approvalDate[$apMode]['approval'])) {
            $approvalDate[$apMode]['approval'] = [];
        }

        $approvalDate[$apMode]['approval']['calendar_idx'] = '';

        $beforeData = $this->getOrderSheetForLog($idx);
        OrderSheetModel::update(
            ['oo_idx' => $idx],
            ['oo_approval_date' => json_encode($approvalDate, JSON_UNESCAPED_UNICODE)]
        );
        $afterData = $this->getOrderSheetForLog($idx);
        $this->writeOrderSheetActionLog(
            $idx,
            'update_calendar_delete',
            '주문서 결제기한 삭제',
            $beforeData,
            $afterData
        );

        CalendarModel::where('idx', $calendarIdx)->delete();

        return [
            'success' => true,
            'msg' => '완료',
        ];
    }

    /**
     * 주문서 로그용 현재 데이터 조회
     * @param int|string|null $idx
     * @return array
     */
    private function getOrderSheetForLog($idx): array
    {
        if (empty($idx)) {
            return [];
        }

        $row = OrderSheetModel::query()
            ->where('oo_idx', '=', $idx)
            ->first();

        return $row ? $row->toArray() : [];
    }

    /**
     * 주문서 액션 로그 저장
     * @param int|string|null $idx
     * @param string $actionMode
     * @param string $actionSummary
     * @param array $beforeData
     * @param array $afterData
     * @return void
     */
    private function writeOrderSheetActionLog($idx, string $actionMode, string $actionSummary, array $beforeData, array $afterData): void
    {
        if (empty($idx)) {
            return;
        }

        try {
            $adminActionLogService = new AdminActionLogService();
            $diff = $adminActionLogService->buildDiff($beforeData, $afterData);
            $actionUrl = (string)($_SERVER['REQUEST_URI'] ?? '');

            $adminActionLogService->log([
                'target_type' => 'orderSheet',
                'target_table' => 'ona_order',
                'target_pk' => (string)$idx,
                'action_mode' => $actionMode,
                'action_summary' => $actionSummary,
                'before_json' => $beforeData,
                'after_json' => $afterData,
                'diff_json' => $diff,
                'action_url' => $actionUrl !== '' ? $actionUrl : null,
            ]);
        } catch (\Throwable $e) {
            // 로그 저장 실패는 메인 동작에 영향을 주지 않도록 분리한다.
        }
    }

}