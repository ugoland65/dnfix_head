<?php

namespace App\Controllers\Admin;

use App\Core\BaseClass;
use App\Models\ProductModel;
use App\Services\ProductStockService;
use App\Utils\HttpClient; 

class GodoApiController extends BaseClass {

    public function __construct() {
        parent::__construct();
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

} 