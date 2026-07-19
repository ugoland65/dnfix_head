<?php

namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Models\ProductModel;
use App\Models\CsRequestModel;
use App\Models\GodoOrderGoodsModel;

use App\Services\ProductStockService;
use App\Services\GodoApiService;
use App\Services\GodoOrderSyncService;
use App\Services\GodoPurchaseOrderService;
use App\Services\ProductSupplierPyApiService;
use App\Utils\HttpClient; 

class GodoApiController extends BaseClass
{

    public function __construct() {
        parent::__construct();
    }


    /**
     * 고도몰 주문서 조회
     * 
     * @param Request $request 요청 데이터
     * @return view
     */
    public function godoOrderList(Request $request) 
    {

        try{

            $requestData = $request->all();

            $today = date('Y-m-d');
            $default_start_date = date('Y-m-d', strtotime('-7 days'));

            $start_date = $requestData['start_date'] ?? $default_start_date;
            $end_date = $requestData['end_date'] ?? $today;
            $mode = $requestData['mode'] ?? 'p';
     
            $payload = [
                'start_date' => $start_date,
                'end_date' => $end_date,
                'mode' => $mode,
            ];
            $godoApiService = new GodoApiService();
            $orderList = $godoApiService->getOrderList($payload);

            $data = [
                'start_date' => $start_date,
                'end_date' => $end_date,
                'mode' => $mode,
                'orderList' => $orderList,
            ];

            return view('admin.order.godo_order_list', $data)
                ->extends('admin.layout.layout',[
                    'pageGroup2' => 'order',
                    'pageNameCode' => 'godo_order_list'
                ]);

        } catch (Exception $e) {
            return view('onadb.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }
        

    /**
     * 고도몰 주문서 상품별 조회
     * 
     * @param Request $request 요청 데이터
     * @return view
     */
    public function godoOrderGoodsList(Request $request) 
    {
        
        try{

            $requestData = $request->all();

            //dd($requestData);

            $today = date('Y-m-d');
            $default_start_date = date('Y-m-d', strtotime('-7 days'));

            $start_date = $requestData['start_date'] ?? $default_start_date;
            $end_date = $requestData['end_date'] ?? $today;
            $mode = $requestData['mode'] ?? 'p';
     
            $payload = [
                'start_date' => $start_date,
                'end_date' => $end_date,
                'mode' => $mode,
            ];
            $godoApiService = new GodoApiService();
            $orderList = $godoApiService->getOrderGoodsList($payload);

            $orderRows = $orderList['orderData']['data'] ?? [];
            $orderNos = [];
            $orderGoodsSnos = [];
            foreach ($orderRows as $orderRow) {
                $orderNo = (string)($orderRow['orderNo'] ?? '');
                if ($orderNo !== '') {
                    $orderNos[$orderNo] = true;
                }

                $orderGoodsSno = $this->normalizeOrderGoodsSno($orderRow['orderGoodsSno'] ?? null);
                if ($orderGoodsSno !== null) {
                    $orderGoodsSnos[$orderGoodsSno] = true;
                }
            }

            $csRequestCountMap = [];
            if (!empty($orderNos)) {
                $csRequestCounts = CsRequestModel::query()
                    ->select(['order_no', 'COUNT(*) as count'])
                    ->whereIn('order_no', array_keys($orderNos))
                    ->groupBy('order_no')
                    ->get()
                    ->toArray();

                foreach ($csRequestCounts as $countRow) {
                    $orderNo = (string)($countRow['order_no'] ?? '');
                    if ($orderNo === '') {
                        continue;
                    }
                    $csRequestCountMap[$orderNo] = (int)($countRow['count'] ?? 0);
                }
            }

            $purchaseStatusMap = [];
            $purchaseOrderIdxMap = [];
            $savedOrderGoodsMap = [];
            if (!empty($orderGoodsSnos)) {
                $purchaseStatusRows = GodoOrderGoodsModel::query()
                    ->select(['order_goods_sno', 'purchase_status', 'purchase_order_idx'])
                    ->whereIn('order_goods_sno', array_keys($orderGoodsSnos))
                    ->get()
                    ->toArray();

                foreach ($purchaseStatusRows as $statusRow) {
                    $orderGoodsSno = $this->normalizeOrderGoodsSno($statusRow['order_goods_sno'] ?? null);
                    if ($orderGoodsSno === null) {
                        continue;
                    }
                    $savedOrderGoodsMap[$orderGoodsSno] = true;
                    $purchaseStatusMap[$orderGoodsSno] = (string)($statusRow['purchase_status'] ?? '');
                    $purchaseOrderIdxMap[$orderGoodsSno] = (int)($statusRow['purchase_order_idx'] ?? 0);
                }
            }

            $data = [
                'start_date' => $start_date,
                'end_date' => $end_date,
                'mode' => $mode,
                'orderList' => $orderList['orderData'] ?? [],
                'csRequestCountMap' => $csRequestCountMap,
                'purchaseStatusMap' => $purchaseStatusMap,
                'purchaseOrderIdxMap' => $purchaseOrderIdxMap,
                'savedOrderGoodsMap' => $savedOrderGoodsMap,
            ];

            return view('admin.order.godo_order_goods_list', $data)
                ->extends('admin.layout.layout',[
                    'pageGroup2' => 'order',
                    'pageNameCode' => 'godo_order_goods_list'
                ]);

        } catch (Exception $e) {
            return view('onadb.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }

    /**
     * 모브 가용 예치금을 조회한다.
     */
    public function getMobPayBalance(Request $request)
    {
        try {
            $productSupplierPyApiService = new ProductSupplierPyApiService();
            $result = $productSupplierPyApiService->getMobPayBalance($request->all());

            return response()->json([
                'success' => true,
                'available_deposit' => (int)($result['available_deposit'] ?? 0),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => '모브 예치금 조회에 실패했습니다.',
            ], 400);
        }
    }


    /**
     * 고도몰 주문 (개별주문)
     * 
     * @param Request $request 요청 데이터
     * @return view
     */
    public function godoOrderPurchaseList(Request $request) 
    {
        try{

            $requestData = $request->all();

            $today = date('Y-m-d');
            $default_start_date = date('Y-m-d', strtotime('-7 days'));

            $start_date = $requestData['start_date'] ?? $default_start_date;
            $end_date = $requestData['end_date'] ?? $today;
            $mode = $requestData['mode'] ?? 'p';
     
            $payload = [
                'prd_type' => 'prdDB',
                'start_date' => $start_date,
                'end_date' => $end_date,
                'mode' => $mode,
                'scmNo' => '14,18',
            ];
            $godoApiService = new GodoApiService();
            $orderList = $godoApiService->getOrderGoodsList($payload);
            try {
                (new GodoOrderSyncService())->syncOrderGoodsList($orderList);
            } catch (\Throwable $syncException) {
                // 동기화 실패로 목록 화면 진입까지 막히지 않도록 화면 렌더링은 계속 진행한다.
            }

            //dd($orderList);

            $orderRows = $orderList['orderData']['data'] ?? [];
            $orderNos = [];
            $orderGoodsSnos = [];
            foreach ($orderRows as $orderRow) {
                $orderNo = (string)($orderRow['orderNo'] ?? '');
                if ($orderNo !== '') {
                    $orderNos[$orderNo] = true;
                }

                $orderGoodsSno = $this->normalizeOrderGoodsSno($orderRow['orderGoodsSno'] ?? null);
                if ($orderGoodsSno !== null) {
                    $orderGoodsSnos[$orderGoodsSno] = true;
                }
            }

            $csRequestCountMap = [];
            if (!empty($orderNos)) {
                $csRequestCounts = CsRequestModel::query()
                    ->select(['order_no', 'COUNT(*) as count'])
                    ->whereIn('order_no', array_keys($orderNos))
                    ->groupBy('order_no')
                    ->get()
                    ->toArray();

                foreach ($csRequestCounts as $countRow) {
                    $orderNo = (string)($countRow['order_no'] ?? '');
                    if ($orderNo === '') {
                        continue;
                    }
                    $csRequestCountMap[$orderNo] = (int)($countRow['count'] ?? 0);
                }
            }

            $purchaseStatusMap = [];
            $purchaseOrderIdxMap = [];
            $savedOrderGoodsMap = [];
            if (!empty($orderGoodsSnos)) {
                $purchaseStatusRows = GodoOrderGoodsModel::query()
                    ->select(['order_goods_sno', 'purchase_status', 'purchase_order_idx'])
                    ->whereIn('order_goods_sno', array_keys($orderGoodsSnos))
                    ->get()
                    ->toArray();

                foreach ($purchaseStatusRows as $statusRow) {
                    $orderGoodsSno = $this->normalizeOrderGoodsSno($statusRow['order_goods_sno'] ?? null);
                    if ($orderGoodsSno === null) {
                        continue;
                    }
                    $savedOrderGoodsMap[$orderGoodsSno] = true;
                    $purchaseStatusMap[$orderGoodsSno] = (string)($statusRow['purchase_status'] ?? '');
                    $purchaseOrderIdxMap[$orderGoodsSno] = (int)($statusRow['purchase_order_idx'] ?? 0);
                }
            }

            $data = [
                'start_date' => $start_date,
                'end_date' => $end_date,
                'mode' => $mode,
                'orderList' => $orderList['orderData'] ?? [],
                'csRequestCountMap' => $csRequestCountMap,
                'purchaseStatusMap' => $purchaseStatusMap,
                'purchaseOrderIdxMap' => $purchaseOrderIdxMap,
                'savedOrderGoodsMap' => $savedOrderGoodsMap,
            ];

            return view('admin.order.godo_order_purchase_list', $data)
                ->extends('admin.layout.layout',[
                    'pageGroup2' => 'order',
                    'pageNameCode' => 'godo_order_purchase_list'
                ]);

        } catch (Exception $e) {
            return view('onadb.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }

    }

    /**
     * 고도몰 구매대행 주문 선택건으로 발주서를 생성한다.
     *
     * @param Request $request
     * @return \App\Core\JsonResponse
     */
    public function createGodoPurchaseOrderSheet(Request $request)
    {
        try {
            $requestData = $request->all();
            $orderGoodsSnos = $requestData['order_goods_snos'] ?? [];
            if (is_string($orderGoodsSnos)) {
                $orderGoodsSnos = array_filter(array_map('trim', explode(',', $orderGoodsSnos)), function ($value) {
                    return $value !== '';
                });
            }
            if (!is_array($orderGoodsSnos)) {
                $orderGoodsSnos = [];
            }
            $orderName = trim((string)($requestData['order_name'] ?? ''));

            $purchaseOrderService = new GodoPurchaseOrderService();
            $result = $purchaseOrderService->createPurchaseOrderSheet($orderGoodsSnos, $orderName);

            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? '발주서가 생성되었습니다.',
                'purchase_order_idx' => (int)($result['purchase_order_idx'] ?? 0),
                'download_url' => (string)($result['download_url'] ?? ''),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 발주서 CSV 다운로드
     *
     * @param Request $request
     * @return string
     */
    public function downloadGodoPurchaseOrderExcel(Request $request)
    {
        try {
            $requestData = $request->all();
            $purchaseOrderIdx = (int)($requestData['purchase_order_idx'] ?? 0);

            $purchaseOrderService = new GodoPurchaseOrderService();
            $csvPayload = $purchaseOrderService->buildPurchaseOrderCsv($purchaseOrderIdx);

            $filename = (string)($csvPayload['filename'] ?? ('purchase_order_' . $purchaseOrderIdx . '.xlsx'));
            $encodedFilename = rawurlencode($filename);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename*=UTF-8''" . $encodedFilename);
            header('Pragma: no-cache');
            header('Expires: 0');

            return (string)($csvPayload['content'] ?? '');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    /**
     * 고도 주문서 출력 화면
     * @skin : skin.godo_order_print.php
     * @return array
     */
    public function godoOrderPrintIndex() {

		$getData = $this->requestHandler->getAll(); // GET 데이터 받기

        // 기본값 설정
        $today = date('Y-m-d');
        $dayOfWeek = date('N'); // 1(월요일) ~ 7(일요일)
        
        // end_date 기본값: 오늘
        $default_end_date = $today;
        
        // start_date 기본값: 오늘이 월요일이면 금요일(-3일), 그 외에는 어제(-1일)
        if ($dayOfWeek == 1) { // 월요일
            $default_start_date = date('Y-m-d', strtotime('-3 days'));
        } else {
            $default_start_date = date('Y-m-d', strtotime('-1 day'));
        }

        $mode = $getData['mode'] ?? 'b';
        $start_date = $getData['start_date'] ?? $default_start_date;
        $end_date = $getData['end_date'] ?? $default_end_date;


        $apiUrl = 'https://showdang.co.kr/dnfix/api/order_api.php?mode='.$mode.'&start_date='.$start_date.'&end_date='.$end_date;
        $response = HttpClient::getData($apiUrl);
        
        $apiData = json_decode($response, true);

        if( $apiData['total'] > 0 ){

            $errorGoodsList = []; // 오류 데이터 저장
            $numericGoodsList = [];
            $setGoodsList = [];
            $otherGoodsList = [];

            foreach ($apiData['data'] as &$order) {
                foreach ($order['orderGoods'] as &$goods) {
                    $goodsCd = $goods['goodsCd'];

                    // goodsCd 값에 한글이 포함되어 있는지 검사
                    if (preg_match('/[가-힣]/u', $goodsCd)) {
                        $goods['match'] = "error";
                        $errorGoodsList[] = [
                            'goodsNo' => $goods['goodsNo'],
                            'goodsCd' => $goodsCd,
                            'orderNo' => $order['orderNo'],
                            'error_message' => 'goodsCd에 한글 포함됨',
                        ];
                        continue; // 오류 데이터는 매칭하지 않음
                    }

                    // 숫자로만 이루어진 goodsCd
                    if (ctype_digit($goodsCd)) {
                        $goods['match'] = "stock";
                        $numericGoodsList[] = $goodsCd;
                    }
                    // "set" 또는 "qty" 단어가 포함된 goodsCd
                    elseif (stripos($goodsCd, 'set') !== false || stripos($goodsCd, 'qty') !== false) {
                        $goods['match'] = "set";
                        $setGoodsList[] = $goodsCd;
                    }
                    // 기타 (한글은 없지만 숫자가 아닌 다른 문자 포함)
                    else {
                        $goods['match'] = "scm";
                        $otherGoodsList[] = $goodsCd;
                    }

                }
            }

            // 중복 제거 및 인덱스 재정렬
            $numericGoodsList = array_values(array_unique($numericGoodsList));
            $setGoodsList = array_values(array_unique($setGoodsList));
            $otherGoodsList = array_values(array_unique($otherGoodsList));

            $productStockService = new ProductStockService();
            $productData = $productStockService->getProductStockWhereIn($numericGoodsList);
            
            
            foreach ($apiData['data'] as &$order) {
                foreach ($order['orderGoods'] as &$goods) {
                    if ($goods['match'] == "stock") {
                        $goods['stock'] = $productData[$goods['goodsCd']]['ps_stock'];
                        $goods['rack_code'] = $productData[$goods['goodsCd']]['ps_rack_code'];
                        $goods['bar_code'] = $productData[$goods['goodsCd']]['CD_CODE'];
                    }
                }
            }

            $test = [
                'errorGoodsList' => $errorGoodsList,
                'numericGoodsList' => $numericGoodsList,
                'setGoodsList' => $setGoodsList,
                'otherGoodsList' => $otherGoodsList,
                'productData' => $productData,
            ];

        }

        $data = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'orderData' => $apiData ?? []
        ];

        return $data;

    }

    /**
     * orderGoodsSno 키 정규화
     * - 숫자만 허용
     * - 선행 0 제거
     *
     * @param mixed $value
     * @return string|null
     */
    private function normalizeOrderGoodsSno($value)
    {
        $value = trim((string)$value);
        if ($value === '' || !ctype_digit($value)) {
            return null;
        }

        $normalized = ltrim($value, '0');
        return $normalized === '' ? '0' : $normalized;
    }

} 