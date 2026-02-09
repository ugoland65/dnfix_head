<?php

namespace App\Services;

use Exception;
use App\Models\OrderSheetModel;
use App\Models\CalendarModel;
use App\Models\AdminModel;
use App\Core\AuthAdmin;
use App\Classes\UploadedFile;

class OrderSheetService{

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

        //주문 관련 파일
        foreach( $result['oo_upload_file']['invoice'] as &$invoice ){
            $invoice['reg_name'] = $adminMap[$invoice['id']] ?? '';
        }
        unset($invoice);

        //결제 관련 파일
        foreach( $result['oo_upload_file']['pay_file'] as &$pay_file ){
            $pay_file['reg_name'] = $adminMap[$pay_file['id']] ?? '';
        }
        unset($pay_file);

        foreach( $result['oo_upload_file']['import_declaration'] as &$import_declaration ){
            $import_declaration['reg_name'] = $adminMap[$import_declaration['id']] ?? '';
        }
        unset($import_declaration);



        return $result;
    }


    /**
     * 주문서 저장
     * @param Request $request
     * @return array
     */
    public function saveOrderSheet($data)
    {

        $mode = $data['mode'] ?? '';
        $idx = $data['idx'] ?? null;

        $date_data = [];
        if( $mode == 'modify' || !empty($idx) ){
            $orderSheetInfo = OrderSheetModel::find($idx);
            if( !$orderSheetInfo ){
                throw new Exception("주문서를 찾을 수 없습니다.");
            }

            $date_data = json_decode($orderSheetInfo['oo_date_data'] ?? '{}', true);
            if( !is_array($date_data) ){
                $date_data = [];
            }
        }

        $name = $data['oo_name'] ?? '';

        if( empty($name) ){
            throw new Exception("주문서명을 입력해주세요.");
        }

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

        $prd_currency = $data['oo_prd_currency'] ?? ''; //상품 가격 화폐
        $prd_exchange_rate_raw = trim((string)($data['oo_prd_exchange_rate'] ?? ''));
        $prd_exchange_rate = $prd_exchange_rate_raw === '' ? 0 : (float)preg_replace('/[,\s]/', '', $prd_exchange_rate_raw); //상품 가격 환율

        $memo = $data['oo_memo'] ?? '';

        $sum_currency = $data['oo_sum_currency'] ?? ''; //주문 결제 가격 화폐
        $sum_exchange_rate_raw = trim((string)($data['oo_sum_exchange_rate'] ?? ''));
        $sum_exchange_rate = $sum_exchange_rate_raw === '' ? 0 : (float)preg_replace('/[,\s]/', '', $sum_exchange_rate_raw); //주문 결제 가격 환율

        $prd_sum_price = (int)str_replace(',','', (string)($data['prd_sum_price'] ?? '0')); // 결제가격
        $oo_sum_price = (int)str_replace(',','', (string)($data['oo_sum_price'] ?? '0')); // 결제가격
        //$oo_fn_price = (int) preg_replace('/[^\d]/', '', (string)($data['oo_fn_price'] ?? '0')); // 확정 주문 금액
        $oo_fn_price = (double)str_replace(',','', $data['oo_fn_price'] ?? '0'); // 확정 주문 금액
        //$pay_fee = (int) preg_replace('/[^\d]/', '', (string)($data['pay_fee'] ?? '0')); // 결제 수수료
        $pay_fee = (double)str_replace(',','', $data['pay_fee'] ?? '0'); // 결제 수수료
        //$oo_price_kr = (int) preg_replace('/[^\d]/', '', (string)($data['oo_price_kr'] ?? '0')); // 최종 합계 결제액
        $in_date = $data['in_date'] ?? ''; //입고일
        $oo_price_kr = (double)str_replace(',','', $data['oo_price_kr'] ?? '0'); // 최종 합계 결제액
 
        //가격변동사항
        $_change_price = [];
        foreach( $change_price_mode as $key => $value ){

            $_mode = $change_price_mode[$key] ?? "";
            $_body = $change_price_body[$key] ?? "";
            $_price = (double)str_replace(',','', $change_price_price[$key] ?? '0');

            $_change_price[] = [
                'mode' => $_mode,
                'body' => $_body,
                'price' => $_price,
            ];
        }

        //결제처리
        $_add_pay_list = [];
        foreach( $pay_mode as $key => $value ){
            $_pay_mode = $pay_mode[$key] ?? "";
            $_pay_price = (double)str_replace(',','', $pay_price[$key] ?? '0');
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

        $date_data['order_send_date'] = $order_send_date; //주문서 발송일
        $date_data['in_date'] = $in_date; //입고일

        $date_data = json_encode($date_data, JSON_UNESCAPED_UNICODE);

        $express_mode = $data['express_mode'] ?? '';
        $express_name = $data['express_name'] ?? '';
        $express_number = $data['express_number'] ?? '';
        $express_report_weight = $data['express_report_weight'] ?? '';
        $express_weight = $data['express_weight'] ?? '';
        $express_cbm = $data['express_cbm'] ?? '';
        $express_box = $data['express_box'] ?? '';
        $express_price = (int)str_replace(',','', (string)($data['express_price'] ?? '0'));
        $express_price_add = (int)str_replace(',','', (string)($data['express_price_add'] ?? '0'));

        $express_data_json = [
            'mode' => $express_mode,
            'name' => $express_name,
            'number' => $express_number,
            'report_weight' => $express_report_weight,
            'weight' => $express_weight,
            'cbm' => $express_cbm,
            'box' => $express_box,
            'price' => $express_price,
            'price_add' => $express_price_add
        ];

        $express_data = json_encode($express_data_json, JSON_UNESCAPED_UNICODE);

        $tex_num = $data['tex_num'] ?? '';
        $tex_report_price = (int)str_replace(',','', (string)($data['tex_report_price'] ?? '0'));
        $tex_duty_price = (int)str_replace(',','', (string)($data['tex_duty_price'] ?? '0'));
        $tex_vat_price = (int)str_replace(',','', (string)($data['tex_vat_price'] ?? '0'));
        $tex_commission = (int)str_replace(',','', (string)($data['tex_commission'] ?? '0'));

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

        $input_data = [
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
            'oo_in_date' => $in_date ?? '', //입고일
            'reg' => $reg ?? '',

            'oo_price_kr' => $oo_price_kr ?? 0, //최종 합계 결제액
        ];

        $OrderSheetModel = new OrderSheetModel();

        if( $mode == 'create' ){
            $OrderSheetInsertResult = $OrderSheetModel->insert($input_data);
        }else{
            $OrderSheetInsertResult = $OrderSheetModel->update(['oo_idx' => $idx], $input_data);
        }

        return [
            'status' => 'success',
            'message' => '주문서 저장 완료',
            'order_sheet_idx' => $OrderSheetInsertResult,
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

        OrderSheetModel::update(['oo_idx' => $idx], $update_data);

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

        $uploadFile[$smode][] = [
            'name' => $saveFilename,
            'view_name' => $viewName,
            'size' => $displaySize,
            'date' => date('Y-m-d H:i:s'),
            'id' => AuthAdmin::getSession('sess_id'),
            'reg_name' => AuthAdmin::getSession('sess_name'),
        ];

        OrderSheetModel::update(
            ['oo_idx' => $idx],
            ['oo_upload_file' => json_encode($uploadFile, JSON_UNESCAPED_UNICODE)]
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

        OrderSheetModel::update(
            ['oo_idx' => $idx],
            ['oo_upload_file' => json_encode($uploadFile, JSON_UNESCAPED_UNICODE)]
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

        OrderSheetModel::update(
            ['oo_idx' => $idx],
            [
                'oo_express_data' => json_encode($expressData, JSON_UNESCAPED_UNICODE),
                'oo_approval_date' => json_encode($approvalDate, JSON_UNESCAPED_UNICODE),
            ]
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

        OrderSheetModel::update(
            ['oo_idx' => $idx],
            ['oo_approval_date' => json_encode($approvalDate, JSON_UNESCAPED_UNICODE)]
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

        OrderSheetModel::update(
            ['oo_idx' => $idx],
            ['oo_approval_date' => json_encode($approvalDate, JSON_UNESCAPED_UNICODE)]
        );

        CalendarModel::where('idx', $calendarIdx)->delete();

        return [
            'success' => true,
            'msg' => '완료',
        ];
    }

}