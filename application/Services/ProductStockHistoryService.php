<?php

namespace App\Services;

use Exception;
use Throwable;
use App\Models\ProductStockHistoryModel;
use App\Models\ProductModel;
use App\Models\BrandModel;
use App\Models\ProductStockModel;
use App\Models\ProductStockUnitModel;
use App\Models\AdminModel;
use App\Core\AuthAdmin;
use App\Utils\TelegramUtils;

use App\Services\ProductService;
use App\Services\GodoApiService;

class ProductStockHistoryService
{

    /**
     * 상품 재고 이력 조회
     * 
     * @param int $idx
     * @return array
     */
    public function getProductStockHistoryList($criteria)
    {

        $start_date = $criteria['start_date'] ?? date('Y-m-d');
        $end_date = $criteria['end_date'] ?? date('Y-m-d');
        // 동일한 날짜 범위도 포함되도록 하루의 시작~끝까지 범위를 확장
        $startDateTime = $start_date . ' 00:00:00';
        $endDateTime = $end_date . ' 23:59:59';

        $admin_query = AdminModel::query()
            ->select(['idx', 'ad_id', 'ad_name', 'ad_nick'])
            ->get()
            ->toArray();

        $adminMap = [];
        foreach ($admin_query as $row) {
            $adminMap[$row['ad_id']] = $row['ad_name'] ?? '';
        }

        $query = ProductStockHistoryModel::query()
            ->select(['uid', 'file_name', 'reg_time', 'end_time', 'reg_id', 'step', 'info', 'error', 'source_type'])
            ->whereBetween('reg_time', [$startDateTime, $endDateTime])
            ->orderBy('uid', 'desc')
            ->get()
            ->toArray();

        foreach ($query as &$row) {
            $info = json_decode($row['info'] ?? '{}', true);
            $error = json_decode($row['error'] ?? '{}', true);
            if (!is_array($info)) {
                $info = [];
            }
            if (!is_array($error)) {
                $error = [];
            }

            $row['info'] = $info;
            $row['error'] = $error;
            $row['reg_name'] = $adminMap[$row['reg_id']] ?? '';
            $row['source_type_name'] = (($row['source_type'] ?? '') === 'fetch') ? 'API' : '엑셀';
            $row['is_temp'] = ((string)($row['step'] ?? '') === '1');
        }
        unset($row);

        return $query;

    }


    /**
     * 일일 재고관리 상세(우측 패널) 데이터
     *
     * @param int|string $idx
     * @param string $sort qty|brand
     * @return array
     */
    public function getStockExcelView($idx, $sort = 'qty'): array
    {
        $uid = (int)$idx;
        if ($uid <= 0) {
            throw new Exception('idx is required');
        }

        $history = ProductStockHistoryModel::find($uid);
        if (!$history) {
            throw new Exception('재고 이력을 찾을 수 없습니다.');
        }

        $historyArray = is_array($history) ? $history : $history->toArray();
        $step = (string)($historyArray['step'] ?? '');

        $metaData = json_decode($historyArray['meta_data'] ?? '[]', true);
        $jsonData = json_decode($historyArray['data'] ?? '[]', true);
        $errorData = json_decode($historyArray['error'] ?? '{"result":[]}', true);

        if (!is_array($metaData)) {
            $metaData = [];
        }
        if (!is_array($jsonData)) {
            $jsonData = [];
        }
        if (!is_array($errorData)) {
            $errorData = ['result' => []];
        }
        if (!isset($errorData['result']) || !is_array($errorData['result'])) {
            $errorData['result'] = [];
        }

        if ($sort === 'brand') {
            usort($jsonData, function ($a, $b) {
                return (int)($b['brand_idx'] ?? 0) <=> (int)($a['brand_idx'] ?? 0);
            });
        } else {
            usort($jsonData, function ($a, $b) {
                return (float)($b['qty'] ?? 0) <=> (float)($a['qty'] ?? 0);
            });
        }

        $psIdxList = [];
        foreach ($jsonData as $val) {
            if (!is_array($val)) {
                continue;
            }
            if (($val['ps_idx'] ?? '') !== '') {
                $psIdxList[] = $val['ps_idx'];
            }
        }
        $stockMap = $this->loadStockProductMap($psIdxList);

        $alertRows = [];
        $basicRows = [];

        foreach ($jsonData as $val) {
            if (!is_array($val)) {
                continue;
            }

            $psIdx = $val['ps_idx'] ?? '';
            $prdData = $stockMap[(string)$psIdx] ?? [];
            $currentStock = (int)($prdData['ps_stock'] ?? 0);
            $qty = (float)($val['qty'] ?? 0);
            $stockSum = ($step === '2') ? $currentStock : ($currentStock - $qty);

            $oneOrders = is_array($val['one']['order_num'] ?? null) ? $val['one']['order_num'] : [];
            $setOrders = is_array($val['set']['order_num'] ?? null) ? $val['set']['order_num'] : [];

            $row = [
                'mode' => 'basic',
                'ps_idx' => $psIdx,
                'cd_idx' => $val['cd_idx'] ?? ($prdData['CD_IDX'] ?? ''),
                'brand_name' => $prdData['BD_NAME'] ?? '',
                'prd_name' => $prdData['CD_NAME'] ?? '',
                'packageOut' => (int)($val['packageOut'] ?? 0),
                'one_qty' => (float)($val['one']['qty'] ?? 0),
                'set_qty' => (float)($val['set']['qty'] ?? 0),
                'qty' => $qty,
                'ps_stock' => $currentStock,
                'stock_sum' => $stockSum,
                'ps_stock_object' => $prdData['ps_stock_object'] ?? '',
                'order_num' => array_merge($oneOrders, $setOrders),
            ];

            if ($stockSum < 1 && (($prdData['ps_stock_object'] ?? '') === 'Y')) {
                $row['mode'] = ($stockSum < 0) ? 'stock_over' : 'stock_zero';
                $alertRows[] = $row;
            } else {
                $basicRows[] = $row;
            }
        }

        usort($alertRows, function ($a, $b) {
            return (float)($a['stock_sum'] ?? 0) <=> (float)($b['stock_sum'] ?? 0);
        });

        $rows = array_merge($alertRows, $basicRows);

        $noticeShortageCount = 0;
        $noticeSoldoutCount = 0;
        $totalPackageOut = 0;
        $totalOneQty = 0;
        $totalSetQty = 0;

        foreach ($rows as $row) {
            $totalPackageOut += (int)($row['packageOut'] ?? 0);
            $totalOneQty += (float)($row['one_qty'] ?? 0);
            $totalSetQty += (float)($row['set_qty'] ?? 0);
            if (($row['mode'] ?? '') === 'stock_over') {
                $noticeShortageCount++;
            } elseif (($row['mode'] ?? '') === 'stock_zero') {
                $noticeSoldoutCount++;
            }
        }

        return [
            'idx' => $uid,
            'sort' => $sort === 'brand' ? 'brand' : 'qty',
            'history' => $historyArray,
            'meta_data' => $metaData,
            'error_list' => $errorData['result'],
            'rows' => $rows,
            'prd_count' => count($rows),
            'notice_shortage_count' => $noticeShortageCount,
            'notice_soldout_count' => $noticeSoldoutCount,
            'error_count' => count($errorData['result']),
            'total_package_out' => $totalPackageOut,
            'total_one_qty' => $totalOneQty,
            'total_set_qty' => $totalSetQty,
            'is_completed' => $step === '2',
            'is_temp' => $step === '1',
        ];
    }


    /**
     * 피킹리스트 조회
     * @param int $idx
     * @return array
     */
    public function getPickingList($idx)
    {

        if (!$idx) {
            throw new Exception('idx is required');
        }

        $productStockHistoryData = ProductStockHistoryModel::find($idx);
        
        if (!$productStockHistoryData) {
            throw new Exception('ProductStockHistory not found');
        }

        $productStockHistoryArray = $productStockHistoryData->toArray();
        $pickingData = json_decode($productStockHistoryArray['data'] ?? '[]', true);

        if (!is_array($pickingData) || empty($pickingData)) {
            return [];
        }

        // 1단계: 모든 외래키 수집 (중복 제거)
        $psIdxList = [];
        $cdIdxList = [];
        $brandIdxList = [];

        foreach ($pickingData as $row) {
            if (!empty($row['ps_idx'])) {
                $psIdxList[] = (int)$row['ps_idx'];
            }
            if (!empty($row['cd_idx'])) {
                $cdIdxList[] = (int)$row['cd_idx'];
            }
            if (!empty($row['brand_idx'])) {
                $brandIdxList[] = (int)$row['brand_idx'];
            }
        }

        $psIdxList = array_unique($psIdxList);
        $cdIdxList = array_unique($cdIdxList);
        $brandIdxList = array_unique($brandIdxList);

        // 2단계: 각 모델에서 whereIn으로 한 번에 조회
        $productStocks = [];
        if (!empty($psIdxList)) {
            $productStocksData = ProductStockModel::query()
                ->select(['ps_idx', 'ps_prd_idx', 'ps_rack_code', 'ps_stock'])
                ->whereIn('ps_idx', $psIdxList)
                ->get()
                ->toArray();
            
            foreach ($productStocksData as $stock) {
                $productStocks[$stock['ps_idx']] = $stock;
            }
        }

        $products = [];
        if (!empty($cdIdxList)) {
            $productsData = ProductModel::query()
                ->select(['CD_IDX', 'CD_NAME', 'CD_IMG', 'CD_CODE', 'CD_BRAND_IDX', 'cd_size_fn', 'cd_add_img'])
                ->whereIn('CD_IDX', $cdIdxList)
                ->get()
                ->toArray();
            
            $productService = new ProductService();

            foreach ($productsData as $product) {
                $product['cd_size_fn'] = json_decode($product['cd_size_fn'] ?? '{}', true);
                $product['cd_add_img'] = json_decode($product['cd_add_img'] ?? '{}', true);
                if (!is_array($product['cd_size_fn'])) {
                    $product['cd_size_fn'] = [];
                }

                $_cd_size_w = (float)($product['cd_size_fn']['package']['W'] ?? 0);
                $_cd_size_h = (float)($product['cd_size_fn']['package']['H'] ?? 0);
                $_cd_size_d = (float)($product['cd_size_fn']['package']['D'] ?? 0);

                if( !empty($_cd_size_w) || !empty($_cd_size_h) || !empty($_cd_size_d) ){
                    $_cd_size_volume = $_cd_size_w * $_cd_size_h * $_cd_size_d;
                    $product['package_volume'] = $_cd_size_volume;
                    $product['package_volume_m3'] = $_cd_size_volume / 1000000;
                    $product['package_volume_level'] = $productService->getVolumeLevel($_cd_size_volume);
                }else{
                    $product['package_volume'] = 0;
                    $product['package_volume_m3'] = 0;
                    $product['package_volume_level'] = 0;    
                }

                $add_img3_filename = $product['cd_add_img']['add3']['filename'] ?? null;
                if($add_img3_filename){
                    $product['add_img3_filename'] = $add_img3_filename;
                }

                $products[$product['CD_IDX']] = $product;
            }
        }

        $brands = [];
        if (!empty($brandIdxList)) {
            $brandsData = BrandModel::query()
                ->select(['BD_IDX', 'BD_NAME'])
                ->whereIn('BD_IDX', $brandIdxList)
                ->get()
                ->toArray();
            
            foreach ($brandsData as $brand) {
                $brands[$brand['BD_IDX']] = $brand;
            }
        }

        // 3단계: 데이터 맵핑 및 결과 배열 구성
        $pickingDataResult = [];

        foreach ($pickingData as $row) {
            $psIdx = (int)($row['ps_idx'] ?? 0);
            $cdIdx = (int)($row['cd_idx'] ?? 0);
            $brandIdx = (int)($row['brand_idx'] ?? 0);

            // ProductStock 데이터
            $stockData = $productStocks[$psIdx] ?? null;
            
            // Product 데이터
            $productData = $products[$cdIdx] ?? null;
            
            // Brand 데이터 (product에서 CD_BRAND_IDX를 사용하거나 row의 brand_idx 사용)
            $brandIdxToUse = $brandIdx;
            if (!$brandIdxToUse && $productData) {
                $brandIdxToUse = (int)($productData['CD_BRAND_IDX'] ?? 0);
            }
            $brandData = $brands[$brandIdxToUse] ?? null;

            // 원본 $pickingData의 모든 데이터 + 조회된 JOIN 데이터 병합
            // $row의 모든 키-값이 유지되며, 아래 키들이 추가/덮어쓰기됨
            $currentStock = (int)($stockData['ps_stock'] ?? 0);
            $currentQty = (int)($row['qty'] ?? 0);
            
            $resultRow = array_merge($row, [
                // ProductStock 데이터
                'ps_prd_idx' => $stockData['ps_prd_idx'] ?? null,
                'ps_rack_code' => $stockData['ps_rack_code'] ?? '',
                'ps_stock' => $currentStock,
                'ps_stock_sum' => $currentStock - $currentQty,
                
                // Product 데이터
                'CD_NAME' => $productData['CD_NAME'] ?? '',
                'CD_IMG' => $productData['CD_IMG'] ?? '',
                'CD_CODE' => $productData['CD_CODE'] ?? '',
                'package_volume' => $productData['package_volume'] ?? 0,
                'package_volume_m3' => $productData['package_volume_m3'] ?? 0,
                'package_volume_level' => $productData['package_volume_level'] ?? 0,
                
                // Brand 데이터
                'BD_NAME' => $brandData['BD_NAME'] ?? '',
                'brand_name' => $brandData['BD_NAME'] ?? '', // 뷰 호환성
                
                // 계산 필드
                'ps_stock_sum' => $currentStock - $currentQty, // 남는 재고

                'add_img3_filename' => $productData['add_img3_filename'] ?? null,
            ]);

            $pickingDataResult[] = $resultRow;
        }

        // ps_rack_code 기준 오름차순 정렬
        usort($pickingDataResult, function($a, $b) {
            $aCode = $a['ps_rack_code'] ?? '';
            $bCode = $b['ps_rack_code'] ?? '';
            return strcmp($aCode, $bCode); // 오름차순 (ASC)
        });

        // error 필드 처리 (JSON 디코드 및 배열로 변환)
        $errorData = $productStockHistoryArray['error'] ?? [];
        
        // error가 JSON 문자열인 경우 디코드
        if (is_string($errorData)) {
            $decodedError = json_decode($errorData, true);
            
            // JSON 디코드 성공 시
            if (is_array($decodedError)) {
                // 'result' 키가 있으면 그것을 사용
                if (isset($decodedError['result']) && is_array($decodedError['result'])) {
                    $errorData = $decodedError['result'];
                } else {
                    // 'result' 키가 없으면 전체 배열 사용
                    $errorData = $decodedError;
                }
            } else {
                // JSON 디코드 실패 시 빈 문자열이면 빈 배열, 아니면 해당 문자열을 배열로
                $errorData = !empty($errorData) ? [$errorData] : [];
            }
        }
        
        // error가 배열이 아닌 경우 빈 배열로 처리
        if (!is_array($errorData)) {
            $errorData = [];
        }

        $result = [
            'pickingList' => $pickingDataResult,
            'error' => $errorData,
        ];

        return $result;
    }


    /**
     * 일일재고 임시저장
     */
    public function saveDailyStockTemp(array $criteria)
    {
        $fileName = trim((string)($criteria['file_name'] ?? ''));
        if ($fileName === '') {
            $fileName = date('Ymd') . '_';
        }

        $existsFileName = ProductStockHistoryModel::where('file_name', $fileName)->first();
        if (!empty($existsFileName)) {
            throw new Exception('이미 등록된 파일명입니다. 파일명을 변경해주세요.');
        }

        $mode = $criteria['mode'] ?? 'p';
        $startDate = $criteria['start_date'] ?? date('Y-m-d');
        $endDate = $criteria['end_date'] ?? date('Y-m-d');

        $godoApiService = new GodoApiService();
        $orderSummary = $godoApiService->getOrderGoodsSummary([
            'mode' => $mode,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $stockRows = $this->buildStockHistoryRows($orderSummary['stock'] ?? []);
        $errorRows = $this->buildStockErrorRows($orderSummary['error'] ?? []);

        $regId = '';
        try {
            $regId = (string)(AuthAdmin::getSession('sess_id') ?? '');
        } catch (\Throwable $e) {
            $regId = '';
        }

        $insertData = [
            'file_name' => $fileName,
            'source_type' => 'fetch',
            'meta_data' => json_encode([
                'filters' => [
                    'mode' => (string)$mode,
                    'start_date' => (string)$startDate,
                    'end_date' => (string)$endDate,
                ],
            ], JSON_UNESCAPED_UNICODE),
            'reg_time' => date('Y-m-d H:i:s'),
            'end_time' => '0000-00-00 00:00:00',
            'reg_id' => $regId,
            'data' => json_encode($stockRows['rows'], JSON_UNESCAPED_UNICODE),
            'step' => '1',
            'info' => json_encode([
                'order_count' => $stockRows['order_count'],
                'pd_count' => count($stockRows['rows']),
                'package_out' => $stockRows['package_out'],
            ], JSON_UNESCAPED_UNICODE),
            'error' => json_encode([
                'count' => count($errorRows),
                'result' => $errorRows,
            ], JSON_UNESCAPED_UNICODE),
        ];

        $uid = ProductStockHistoryModel::createDailyStockTemp($insertData);
        if ($uid <= 0) {
            throw new Exception('일일재고 임시저장에 실패했습니다.');
        }

        return [
            'uid' => $uid,
        ];
    }

    /**
     * 고도몰 집계 stock 데이터를 일일재고 저장 포맷으로 변환
     *
     * @param array $stockData
     * @return array{rows: array, order_count: int, package_out: int}
     */
    private function buildStockHistoryRows(array $stockData): array
    {
        $rows = [];
        $orderNoMap = [];
        $packageOutTotal = 0;

        foreach ($stockData as $psIdx => $stockRow) {
            if (!is_array($stockRow)) {
                continue;
            }

            $productData = $stockRow['product_data'] ?? [];
            if (!is_array($productData)) {
                $productData = [];
            }

            $oneOrderNums = [];
            $setOrderNums = [];
            $oneQty = 0;
            $setQty = 0;

            $orderInfoRows = $stockRow['order_info'] ?? [];
            if (!is_array($orderInfoRows)) {
                $orderInfoRows = [];
            }

            foreach ($orderInfoRows as $orderInfo) {
                if (!is_array($orderInfo)) {
                    continue;
                }

                $orderNo = (string)($orderInfo['orderNo'] ?? '');
                if ($orderNo !== '') {
                    $orderNoMap[$orderNo] = true;
                }

                $orderQty = (float)($orderInfo['goods_qty'] ?? 0);
                $orderNumData = [
                    'num' => $orderNo,
                    'qty' => $orderQty,
                ];

                $itemType = (string)($orderInfo['item_type'] ?? '');
                if ($itemType === 'stock') {
                    $oneQty += $orderQty;
                    $oneOrderNums[] = $orderNumData;
                } else {
                    $setQty += $orderQty;
                    $setOrderNums[] = $orderNumData;
                }
            }

            $packageOut = (int)($stockRow['package_remove_qty'] ?? 0);
            $packageOutTotal += $packageOut;

            $rows[] = [
                'brand_idx' => $productData['CD_BRAND_IDX'] ?? '',
                'cd_idx' => $productData['CD_IDX'] ?? '',
                'ps_idx' => $productData['ps_idx'] ?? $psIdx,
                'qty' => (float)($stockRow['goods_qty'] ?? ($oneQty + $setQty)),
                'one' => [
                    'name' => '단일',
                    'qty' => $oneQty,
                    'order_num' => $oneOrderNums,
                ],
                'set' => [
                    'name' => '세트',
                    'qty' => $setQty,
                    'order_num' => $setOrderNums,
                ],
                'packageOut' => $packageOut,
            ];
        }

        return [
            'rows' => $rows,
            'order_count' => count($orderNoMap),
            'package_out' => $packageOutTotal,
        ];
    }

    /**
     * 고도몰 집계 error 데이터를 저장용 에러 배열로 변환
     *
     * @param array $errors
     * @return array
     */
    private function buildStockErrorRows(array $errors): array
    {
        $result = [];

        foreach ($errors as $error) {
            $code = trim((string)$error);
            if ($code === '') {
                continue;
            }
            $result[] = '코드 확인: ' . $code;
        }

        return $result;
    }


    /**
     * 고도몰 엑셀(CSV) 업로드 → 일일재고 임시저장
     *
     * @param array $file $_FILES['userfile'] 형태
     * @return array{uid:int}
     */
    public function uploadStockExcel(array $file): array
    {
        $tmpName = (string)($file['tmp_name'] ?? '');
        $fileName = (string)($file['name'] ?? '');

        if ($tmpName === '' || !file_exists($tmpName)) {
            throw new Exception('파일을 찾을 수 없습니다.');
        }
        if ($fileName === '') {
            throw new Exception('파일명이 없습니다.');
        }

        @setlocale(LC_CTYPE, 'ko_KR.eucKR');

        $parsed = $this->parseGodoStockExcel($tmpName);

        $stockAllCode = $this->mergeStockCodes(
            $parsed['stock_codes'],
            $parsed['stock_set_codes']
        );

        $orderNums = array_values(array_unique($parsed['order_nums']));
        $stockMap = $this->loadStockProductMap($stockAllCode);

        $rows = [];
        $packageOutTotal = 0;

        foreach ($stockAllCode as $psIdx) {
            $psIdxKey = (string)$psIdx;
            $prdData = $stockMap[$psIdxKey] ?? [];

            $stockQty = (int)($parsed['stock_qty'][$psIdxKey] ?? 0);
            $stockSetQty = (int)($parsed['stock_set_qty'][$psIdxKey] ?? 0);
            $packageOut = (int)($parsed['package_out'][$psIdxKey] ?? 0)
                + (int)($parsed['package_out_set'][$psIdxKey] ?? 0);
            $packageOutTotal += $packageOut;
            $sumQty = $stockQty + $stockSetQty;

            $kindOne = [
                'name' => '단일',
                'qty' => $stockQty,
                'order_num' => $parsed['order_num_by_code'][$psIdxKey] ?? [],
            ];
            $kindSet = [
                'name' => '세트',
                'qty' => $stockSetQty,
                'order_num' => $parsed['order_set_num_by_code'][$psIdxKey] ?? [],
            ];

            $rows[] = [
                'brand_idx' => $prdData['CD_BRAND_IDX'] ?? '',
                'cd_idx' => $prdData['CD_IDX'] ?? '',
                'ps_idx' => $prdData['ps_idx'] ?? '',
                'qty' => $sumQty,
                'one' => $kindOne,
                'set' => $kindSet,
                'packageOut' => $packageOut,
            ];
        }

        $regId = '';
        try {
            $regId = (string)(AuthAdmin::getSession('sess_id') ?? '');
        } catch (Throwable $e) {
            $regId = '';
        }

        $insertData = [
            'file_name' => $fileName,
            'source_type' => 'excel',
            'reg_time' => date('Y-m-d H:i:s'),
            'end_time' => '0000-00-00 00:00:00',
            'reg_id' => $regId,
            'data' => json_encode($rows, JSON_UNESCAPED_UNICODE),
            'step' => '1',
            'info' => json_encode([
                'order_count' => count($orderNums),
                'pd_count' => count($stockAllCode),
                'package_out' => $packageOutTotal,
            ], JSON_UNESCAPED_UNICODE),
            'error' => json_encode([
                'count' => count($parsed['errors']),
                'result' => $parsed['errors'],
            ], JSON_UNESCAPED_UNICODE),
        ];

        $uid = ProductStockHistoryModel::createDailyStockTemp($insertData);
        if ($uid <= 0) {
            throw new Exception('일일재고 엑셀 등록에 실패했습니다.');
        }

        return [
            'uid' => $uid,
        ];
    }

    /**
     * 일일 재고 입출고 등록
     *
     * @param array $payload
     * @return array{idx:string}
     */
    public function registerDayStock(array $payload): array
    {
        $stockKeys = is_array($payload['stock_key'] ?? null) ? $payload['stock_key'] : [];
        $stockModes = is_array($payload['stock_mode'] ?? null) ? $payload['stock_mode'] : [];
        $stockQtys = is_array($payload['stock_qry'] ?? null) ? $payload['stock_qry'] : [];
        $stockKinds = is_array($payload['stock_kind'] ?? null) ? $payload['stock_kind'] : [];
        $stockMemos = is_array($payload['stock_memo'] ?? null) ? $payload['stock_memo'] : [];
        $stockDay = (string)($payload['stock_day'] ?? '');
        $stockHistoryIdx = trim((string)($payload['stock_history_idx'] ?? ''));

        if ($stockHistoryIdx === '') {
            throw new Exception('재고 이력 번호가 없습니다.');
        }

        $now = date('Y-m-d H:i:s');
        $adminId = '';
        try {
            $adminId = (string)(AuthAdmin::getSession('sess_id') ?? '');
        } catch (Throwable $e) {
            $adminId = '';
        }

        $reg = json_encode([
            'reg' => [
                'mode' => 'stock_excel',
                'info' => AuthAdmin::getConnectionInfo(),
            ],
        ], JSON_UNESCAPED_UNICODE);

        $shortage = ['domestic' => [], 'import' => []];
        $soldout = ['domestic' => [], 'import' => []];
        $stockAlarm = [];

        $psIdxList = [];
        $count = count($stockKeys);
        for ($i = 0; $i < $count; $i++) {
            $qty = (float)($stockQtys[$i] ?? 0);
            $psIdx = (string)($stockKeys[$i] ?? '');
            if ($qty > 0 && $psIdx !== '') {
                $psIdxList[] = $psIdx;
            }
        }
        $stockMap = $this->loadStockProductMap($psIdxList);

        $connection = app('db');
        $ownsTransaction = $connection instanceof \PDO && !$connection->inTransaction();

        try {
            if ($ownsTransaction) {
                $connection->beginTransaction();
            }

            $historyRow = $this->lockStockHistory($connection, $stockHistoryIdx);
            if ((string)($historyRow['step'] ?? '') === '2') {
                throw new Exception('이미 등록 완료된 건입니다.', 409);
            }

            for ($i = 0; $i < $count; $i++) {
                $psIdx = (string)($stockKeys[$i] ?? '');
                $psuMode = (string)($stockModes[$i] ?? '');
                $psuQty = (float)($stockQtys[$i] ?? 0);
                $psuKind = (string)($stockKinds[$i] ?? '');
                $psuMemo = (string)($stockMemos[$i] ?? '');

                if ($psuQty <= 0 || $psIdx === '') {
                    continue;
                }

                $prdData = $stockMap[$psIdx] ?? [
                    'ps_stock' => 0,
                    'ps_stock_object' => 'N',
                    'ps_alarm_count' => 0,
                    'CD_NAME' => '',
                    'cd_national' => '',
                ];

                $currentStock = (int)($prdData['ps_stock'] ?? 0);
                $resultStock = $currentStock;

                if ($psuMode === 'plus') {
                    $resultStock = $currentStock + $psuQty;
                } elseif ($psuMode === 'minus') {
                    $resultStock = $currentStock - $psuQty;
                    $isStockObject = (($prdData['ps_stock_object'] ?? 'N') === 'Y');
                    $nationalGroup = (($prdData['cd_national'] ?? '') === 'kr') ? 'domestic' : 'import';
                    $itemName = (string)($prdData['CD_NAME'] ?? '');

                    if ($resultStock < 0 && $isStockObject) {
                        $shortage[$nationalGroup][] = [
                            'name' => $itemName,
                            'count' => $resultStock,
                        ];
                    } elseif ($resultStock == 0) {
                        $soldout[$nationalGroup][] = [
                            'name' => $itemName,
                            'count' => $resultStock,
                        ];
                    }

                    $alarmCount = (int)($prdData['ps_alarm_count'] ?? 0);
                    if ($alarmCount > 0 && $resultStock < $alarmCount && $isStockObject) {
                        $stockAlarm[] = [
                            'name' => $itemName,
                            'count' => $resultStock,
                        ];
                    }

                    $soldoutDate = ($resultStock < 1) ? $now : '0000-00-00 00:00:00';

                    ProductStockModel::query()
                        ->where('ps_idx', '=', $psIdx)
                        ->update([
                            'ps_stock' => $resultStock,
                            'ps_update_date' => $now,
                            'ps_last_date' => $now,
                            'ps_soldout_date' => $soldoutDate,
                        ]);

                    if (isset($stockMap[$psIdx])) {
                        $stockMap[$psIdx]['ps_stock'] = $resultStock;
                    }
                }

                ProductStockUnitModel::query()->insert([
                    'psu_stock_idx' => $psIdx,
                    'psu_day' => $stockDay,
                    'psu_mode' => $psuMode,
                    'psu_qry' => $psuQty,
                    'psu_stock' => $resultStock,
                    'psu_kind' => $psuKind,
                    'psu_memo' => $psuMemo,
                    'psu_id' => $adminId,
                    'psu_date' => time(),
                    'psu_token' => null,
                    'reg' => $reg,
                ]);
            }

            // info 컬럼은 주문/상품 건수용이라 applied 목록을 넣으면 길이 초과(1406)가 난다.
            // 되돌리기는 data.qty로 복원한다.
            ProductStockHistoryModel::query()
                ->where('uid', '=', $stockHistoryIdx)
                ->update([
                    'step' => '2',
                    'end_time' => $now,
                ]);

            if ($ownsTransaction && $connection->inTransaction()) {
                $connection->commit();
            }
        } catch (Throwable $e) {
            if ($ownsTransaction && $connection instanceof \PDO && $connection->inTransaction()) {
                $connection->rollBack();
            }
            throw $e;
        }

        $this->sendDayStockTelegramAlerts($stockDay, $shortage, $soldout, $stockAlarm);

        return [
            'idx' => $stockHistoryIdx,
        ];
    }

    /**
     * 일일 재고 입출고 되돌리기 (등록 완료 건의 차감 수량을 복구)
     *
     * @param array $payload
     * @return array{idx:string}
     */
    public function revertDayStock(array $payload): array
    {
        $stockHistoryIdx = trim((string)($payload['stock_history_idx'] ?? $payload['idx'] ?? ''));
        if ($stockHistoryIdx === '') {
            throw new Exception('재고 이력 번호가 없습니다.');
        }

        $now = date('Y-m-d H:i:s');
        $adminId = '';
        try {
            $adminId = (string)(AuthAdmin::getSession('sess_id') ?? '');
        } catch (Throwable $e) {
            $adminId = '';
        }

        $reg = json_encode([
            'reg' => [
                'mode' => 'stock_excel_revert',
                'info' => AuthAdmin::getConnectionInfo(),
            ],
        ], JSON_UNESCAPED_UNICODE);

        $connection = app('db');
        $ownsTransaction = $connection instanceof \PDO && !$connection->inTransaction();

        try {
            if ($ownsTransaction) {
                $connection->beginTransaction();
            }

            $historyRow = $this->lockStockHistory($connection, $stockHistoryIdx);
            if ((string)($historyRow['step'] ?? '') !== '2') {
                throw new Exception('등록 완료된 건만 되돌릴 수 있습니다.', 409);
            }

            $applied = $this->resolveAppliedStockRows($historyRow);
            if (empty($applied)) {
                throw new Exception('되돌릴 재고 내역이 없습니다.');
            }

            $psIdxList = [];
            foreach ($applied as $item) {
                if (($item['ps_idx'] ?? '') !== '') {
                    $psIdxList[] = $item['ps_idx'];
                }
            }
            $stockMap = $this->loadStockProductMap($psIdxList);

            foreach ($applied as $item) {
                $psIdx = (string)($item['ps_idx'] ?? '');
                $psuQty = (float)($item['qty'] ?? 0);
                $originalMode = (string)($item['mode'] ?? 'minus');

                if ($psIdx === '' || $psuQty <= 0) {
                    continue;
                }

                $prdData = $stockMap[$psIdx] ?? ['ps_stock' => 0];
                $currentStock = (int)($prdData['ps_stock'] ?? 0);

                if ($originalMode === 'plus') {
                    $resultStock = $currentStock - $psuQty;
                    $revertMode = 'minus';
                } else {
                    $resultStock = $currentStock + $psuQty;
                    $revertMode = 'plus';
                }

                $soldoutDate = ($resultStock < 1) ? $now : '0000-00-00 00:00:00';

                ProductStockModel::query()
                    ->where('ps_idx', '=', $psIdx)
                    ->update([
                        'ps_stock' => $resultStock,
                        'ps_update_date' => $now,
                        'ps_last_date' => $now,
                        'ps_soldout_date' => $soldoutDate,
                    ]);

                ProductStockUnitModel::query()->insert([
                    'psu_stock_idx' => $psIdx,
                    'psu_day' => date('Y-m-d'),
                    'psu_mode' => $revertMode,
                    'psu_qry' => $psuQty,
                    'psu_stock' => $resultStock,
                    'psu_kind' => '판매 (엑셀) 되돌리기',
                    'psu_memo' => '일일 재고관리 되돌리기',
                    'psu_id' => $adminId,
                    'psu_date' => time(),
                    'psu_token' => null,
                    'reg' => $reg,
                ]);

                if (isset($stockMap[$psIdx])) {
                    $stockMap[$psIdx]['ps_stock'] = $resultStock;
                }
            }

            ProductStockHistoryModel::query()
                ->where('uid', '=', $stockHistoryIdx)
                ->update([
                    'step' => '1',
                    'end_time' => '0000-00-00 00:00:00',
                ]);

            if ($ownsTransaction && $connection->inTransaction()) {
                $connection->commit();
            }
        } catch (Throwable $e) {
            if ($ownsTransaction && $connection instanceof \PDO && $connection->inTransaction()) {
                $connection->rollBack();
            }
            throw $e;
        }

        return [
            'idx' => $stockHistoryIdx,
        ];
    }

    /**
     * 입출고 처리 전 이력을 잠근다.
     *
     * @param \PDO $connection
     * @param string $uid
     * @return array
     */
    private function lockStockHistory($connection, string $uid): array
    {
        $stmt = $connection->prepare('SELECT uid, step, data, info FROM prd_stock_history WHERE uid = :uid FOR UPDATE');
        $stmt->execute([':uid' => $uid]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            throw new Exception('재고 이력을 찾을 수 없습니다.');
        }

        return $row;
    }

    /**
     * 되돌릴 실제 차감/증가 수량. 등록 시 저장한 applied가 없으면 data.qty로 복원한다.
     *
     * @param array $historyRow
     * @return array
     */
    private function resolveAppliedStockRows(array $historyRow): array
    {
        $info = json_decode($historyRow['info'] ?? '{}', true);
        if (is_array($info) && !empty($info['applied']) && is_array($info['applied'])) {
            return $info['applied'];
        }

        $data = json_decode($historyRow['data'] ?? '[]', true);
        if (!is_array($data)) {
            return [];
        }

        $applied = [];
        foreach ($data as $row) {
            if (!is_array($row)) {
                continue;
            }
            $psIdx = (string)($row['ps_idx'] ?? '');
            $qty = (float)($row['qty'] ?? 0);
            if ($psIdx === '' || $qty <= 0) {
                continue;
            }
            $applied[] = [
                'ps_idx' => $psIdx,
                'qty' => $qty,
                'mode' => 'minus',
            ];
        }

        return $applied;
    }

    /**
     * 일일재고 이력 삭제 (미처리 건만)
     *
     * @param int|string $idx
     * @return array{idx:string}
     */
    public function deleteDayStock($idx): array
    {
        $uid = (int)$idx;
        if ($uid <= 0) {
            throw new Exception('삭제 대상이 없습니다.');
        }

        $row = ProductStockHistoryModel::find($uid);
        if (!$row) {
            throw new Exception('삭제 대상이 없습니다.');
        }

        $rowArray = is_array($row) ? $row : $row->toArray();
        if ((string)($rowArray['step'] ?? '') === '2') {
            throw new Exception('완료된건은 삭제 불가합니다.');
        }

        ProductStockHistoryModel::query()
            ->where('uid', '=', $uid)
            ->delete();

        return [
            'idx' => (string)$uid,
        ];
    }

    /**
     * 고도몰 재고 엑셀(CSV) 파싱
     *
     * @param string $tmpName
     * @return array
     */
    private function parseGodoStockExcel(string $tmpName): array
    {
        $fp = fopen($tmpName, 'r');
        if ($fp === false) {
            throw new Exception('파일을 열 수 없습니다.');
        }

        $errors = [];
        $orderNums = [];
        $stockCodes = [];
        $stockSetCodes = [];
        $stockQty = [];
        $stockSetQty = [];
        $orderNumByCode = [];
        $orderSetNumByCode = [];
        $packageOut = [];
        $packageOutSet = [];
        $existsCache = [];
        $count = 0;

        try {
            while (($row = fgetcsv($fp, 0, ',', '"', '\\')) !== false) {
                $count++;
                if (!is_array($row)) {
                    continue;
                }

                $name = $this->convertCsvCell($row[0] ?? '');
                $phone = $this->convertCsvCell($row[1] ?? '');
                $prdCode = $this->convertCsvCell($row[4] ?? '');
                $prdCodeSub = $this->convertCsvCell($row[5] ?? '');
                $qty = (int)($row[6] ?? 0);
                $orderNum = $this->convertCsvCell($row[8] ?? '');
                $option = $this->convertCsvCell($row[12] ?? '');

                if ($count <= 1 || $name === '') {
                    continue;
                }

                $orderNums[] = $orderNum;
                $isPackageOut = $this->isPackageRemoveOption($option);

                if (is_numeric($prdCode)) {
                    $this->accumulateStockQty(
                        $prdCode,
                        $qty,
                        $orderNum,
                        $stockQty,
                        $stockCodes,
                        $orderNumByCode,
                        $existsCache,
                        $errors,
                        $count,
                        $name,
                        $phone,
                        false
                    );
                    if ($isPackageOut) {
                        $packageOut[(string)$prdCode] = (int)($packageOut[(string)$prdCode] ?? 0) + $qty;
                    }
                    continue;
                }

                if ($prdCode === '') {
                    $errors[] = '[' . $count . '] ' . $name . ' / ' . $phone . ' |  상품자체코드 값 없음';
                    continue;
                }

                if (strpos($prdCode, 'set') !== false) {
                    if ($prdCodeSub) {
                        $subCodes = explode('/', $prdCodeSub);
                        foreach ($subCodes as $setPrdCode) {
                            if ($setPrdCode === '') {
                                continue;
                            }
                            $this->accumulateStockQty(
                                $setPrdCode,
                                $qty,
                                $orderNum,
                                $stockSetQty,
                                $stockSetCodes,
                                $orderSetNumByCode,
                                $existsCache,
                                $errors,
                                $count,
                                $name,
                                $phone,
                                true
                            );
                        }
                    }
                    if ($isPackageOut) {
                        $packageOutSet[$prdCode] = (int)($packageOutSet[$prdCode] ?? 0) + $qty;
                    }
                    continue;
                }

                if (strpos($prdCode, 'one') !== false) {
                    $parts = explode('@', $prdCode);
                    $subCodes = explode('/', $parts[1] ?? '');
                    foreach ($subCodes as $setPrdCode) {
                        if ($setPrdCode === '') {
                            continue;
                        }
                        $this->accumulateStockQty(
                            $setPrdCode,
                            $qty,
                            $orderNum,
                            $stockSetQty,
                            $stockSetCodes,
                            $orderSetNumByCode,
                            $existsCache,
                            $errors,
                            $count,
                            $name,
                            $phone,
                            true
                        );
                    }
                    if ($isPackageOut) {
                        $packageOutSet[$prdCode] = (int)($packageOutSet[$prdCode] ?? 0) + $qty;
                    }
                    continue;
                }

                if (strpos($prdCode, 'qty') !== false) {
                    if ($prdCodeSub) {
                        $qtyParts = explode('@', $prdCodeSub);
                        $thisCode = ((int)($qtyParts[0] ?? 0));
                        $thisQty = ((int)($qtyParts[1] ?? 0)) * $qty;
                        $this->accumulateStockQty(
                            (string)$thisCode,
                            $thisQty,
                            $orderNum,
                            $stockSetQty,
                            $stockSetCodes,
                            $orderSetNumByCode,
                            $existsCache,
                            $errors,
                            $count,
                            $name,
                            $phone,
                            true
                        );
                    }
                    if ($isPackageOut) {
                        $packageOutSet[$prdCode] = (int)($packageOutSet[$prdCode] ?? 0) + $qty;
                    }
                    continue;
                }

                if (strpos($prdCode, '결제명 상품') !== false) {
                    continue;
                }

                $errors[] = '[' . $count . '] ' . $name . ' / ' . $phone . ' |  (' . $prdCode . ') 코드 확인';
            }
        } finally {
            fclose($fp);
        }

        return [
            'errors' => $errors,
            'order_nums' => $orderNums,
            'stock_codes' => $stockCodes,
            'stock_set_codes' => $stockSetCodes,
            'stock_qty' => $stockQty,
            'stock_set_qty' => $stockSetQty,
            'order_num_by_code' => $orderNumByCode,
            'order_set_num_by_code' => $orderSetNumByCode,
            'package_out' => $packageOut,
            'package_out_set' => $packageOutSet,
        ];
    }

    /**
     * 재고코드별 수량/주문번호 누적
     */
    private function accumulateStockQty(
        $code,
        $qty,
        string $orderNum,
        array &$qtyMap,
        array &$codeList,
        array &$orderMap,
        array &$existsCache,
        array &$errors,
        int $lineNo,
        string $name,
        string $phone,
        bool $isSet
    ): void {
        $codeKey = (string)$code;
        $orderMap[$codeKey][] = [
            'num' => $orderNum,
            'qty' => $qty,
        ];

        if (isset($qtyMap[$codeKey]) && $qtyMap[$codeKey] > 0) {
            $qtyMap[$codeKey] = $qtyMap[$codeKey] + $qty;
            return;
        }

        if ($this->stockIdxExists($codeKey, $existsCache)) {
            $codeList[] = $codeKey;
            $qtyMap[$codeKey] = $qty;
            return;
        }

        $label = $isSet ? '세트 재고 상품 데이터 없음' : '재고 상품 데이터 없음';
        $errors[] = '[' . $lineNo . '] ' . $name . ' / ' . $phone . ' |  (' . $codeKey . ')  ' . $label;
    }

    private function stockIdxExists(string $psIdx, array &$cache): bool
    {
        if ($psIdx === '') {
            return false;
        }
        if (array_key_exists($psIdx, $cache)) {
            return $cache[$psIdx];
        }

        $row = ProductStockModel::query()
            ->select(['ps_idx'])
            ->where('ps_idx', '=', $psIdx)
            ->first();

        $cache[$psIdx] = !empty($row);
        return $cache[$psIdx];
    }

    private function mergeStockCodes(array $stockCodes, array $stockSetCodes): array
    {
        if (count($stockCodes) > 0 && count($stockSetCodes) > 0) {
            return array_values(array_unique(array_merge($stockCodes, $stockSetCodes)));
        }
        if (count($stockCodes) === 0 && count($stockSetCodes) > 0) {
            return $stockSetCodes;
        }
        return $stockCodes;
    }

    /**
     * 재고코드 목록으로 상품/브랜드 정보를 한 번에 조회
     *
     * @param array $psIdxList
     * @return array
     */
    private function loadStockProductMap(array $psIdxList): array
    {
        $psIdxList = array_values(array_unique(array_filter($psIdxList, function ($value) {
            return $value !== '' && $value !== null;
        })));

        if (empty($psIdxList)) {
            return [];
        }

        $rows = ProductStockModel::query()
            ->select([
                'prd_stock.ps_idx',
                'prd_stock.ps_prd_idx',
                'prd_stock.ps_rack_code',
                'prd_stock.ps_stock',
                'prd_stock.ps_stock_object',
                'prd_stock.ps_alarm_count',
                'COMPARISON_DB.CD_IDX',
                'COMPARISON_DB.CD_NAME',
                'COMPARISON_DB.CD_IMG',
                'COMPARISON_DB.CD_CODE',
                'COMPARISON_DB.CD_BRAND_IDX',
                'COMPARISON_DB.cd_national',
                'BRAND_DB.BD_NAME',
            ])
            ->leftJoin('COMPARISON_DB', 'prd_stock.ps_prd_idx', '=', 'COMPARISON_DB.CD_IDX')
            ->leftJoin('BRAND_DB', 'COMPARISON_DB.CD_BRAND_IDX', '=', 'BRAND_DB.BD_IDX')
            ->whereIn('prd_stock.ps_idx', $psIdxList)
            ->get()
            ->toArray();

        $map = [];
        foreach ($rows as $row) {
            $map[(string)($row['ps_idx'] ?? '')] = $row;
        }

        return $map;
    }

    private function convertCsvCell($value): string
    {
        $value = (string)($value ?? '');
        if ($value === '') {
            return '';
        }
        return (string)mb_convert_encoding($value, 'UTF-8', 'EUC-KR');
    }

    private function isPackageRemoveOption(string $option): bool
    {
        return strpos($option, '패키지 제거 여부 : 패키지 제거') !== false;
    }

    private function sendDayStockTelegramAlerts(string $stockDay, array $shortage, array $soldout, array $stockAlarm): void
    {
        $shortageMessage = '';
        if (!empty($shortage['domestic'])) {
            $shortageMessage .= "★★ 재고부족 - 국내 ★★\n\n";
            foreach ($shortage['domestic'] as $val) {
                $shortageMessage .= '(' . ($val['name'] ?? '') . ') : ' . ($val['count'] ?? '') . "\n";
            }
            $shortageMessage .= "\n---------------------------\n\n";
        }
        if (!empty($shortage['import'])) {
            $shortageMessage .= "★★ 재고부족 - 수입 ★★\n\n";
            foreach ($shortage['import'] as $val) {
                $shortageMessage .= '(' . ($val['name'] ?? '') . ') : ' . ($val['count'] ?? '') . "\n";
            }
        }
        if ($shortageMessage !== '') {
            $this->sendStockTelegramMessage("\n(" . $stockDay . ") 재고부족\n————————————\n\n" . $shortageMessage);
        }

        $soldoutMessage = '';
        if (!empty($soldout['domestic'])) {
            $soldoutMessage .= "★★ 품절 - 국내 ★★\n\n";
            foreach ($soldout['domestic'] as $val) {
                $soldoutMessage .= '(' . ($val['name'] ?? '') . ") : 품절\n";
            }
            $soldoutMessage .= "\n---------------------------\n\n";
        }
        if (!empty($soldout['import'])) {
            $soldoutMessage .= "★★ 품절 - 수입 ★★\n\n";
            foreach ($soldout['import'] as $val) {
                $soldoutMessage .= '(' . ($val['name'] ?? '') . ") : 품절\n";
            }
        }
        if ($soldoutMessage !== '') {
            $this->sendStockTelegramMessage("\n(" . $stockDay . ") 품절\n————————————\n\n" . $soldoutMessage);
        }

        $alarmMessage = '';
        if (!empty($stockAlarm)) {
            $alarmMessage .= "★★ 재고관리 대상  ★★\n\n";
            foreach ($stockAlarm as $val) {
                $alarmMessage .= '(' . ($val['name'] ?? '') . ') : ' . ($val['count'] ?? '') . "\n";
            }
            $alarmMessage .= "\n---------------------------\n\n";
        }
        if ($alarmMessage !== '') {
            $this->sendStockTelegramMessage("\n(" . $stockDay . ") 재고관리 대상\n————————————\n\n" . $alarmMessage);
        }
    }

    private function sendStockTelegramMessage(string $message): void
    {
        if ($message === '') {
            return;
        }

        try {
            $telegram = new TelegramUtils();
            $telegram->sendMessage('-1003968498110', $message);
        } catch (Throwable $e) {
            // 레거시와 동일하게 알림 실패는 입출고 처리를 막지 않는다.
        }
    }


}