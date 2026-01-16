<?php

namespace App\Services;

use App\Core\BaseClass;
use App\Utils\HttpClient; 

class GodoApiService extends BaseClass {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 고도몰 회원 HBTI 통계 조회
     * @return array
     */
    public function getHbtiStatistics() {

        $apiUrl = 'https://showdang.co.kr/dnfix/api/member_hbti_reports_api.php';
        $response = HttpClient::getData($apiUrl);
        
        $apiData = json_decode($response, true);

        return $apiData;

    }

    /**
     * 고도몰 주문서 조회
     * 
     * @param array $criteria 요청 데이터
     * @return array
     */
    public function getOrderList($criteria) 
    {

        /*
        주문상태 단계
        const ORDER_STATUS = [
            'o'=>'미입금',
            'p'=>'입금',
            'g'=>'미배송',
            'd'=>'배송',
            's'=>'구매확정',
            'c'=>'취소',
            'b'=>'반품',
            'e'=>'교환',
            'r'=>'환불',
        ];
        */

        $mode = $criteria['mode'] ?? 'b';
        $start_date = $criteria['start_date'] ?? date('Y-m-d');
        $end_date = $criteria['end_date'] ?? date('Y-m-d');

        $apiUrl = 'https://showdang.co.kr/dnfix/api/order_api.php?mode='.$mode.'&start_date='.$start_date.'&end_date='.$end_date;
        $response = HttpClient::getData($apiUrl);

        $apiData = json_decode($response, true);

        $errorGoodsList = []; // 오류 데이터 저장
        $numericGoodsList = [];
        $setGoodsList = [];
        $otherGoodsList = [];
        $otherGoodNos = []; // 공급사 상품 번호 저장
        $packageRemoveList = []; // 패키지 제거 여부 카운트를 저장할 배열
        
        $errorList = []; // 오류 데이터 저장

        $orderMargin = [];
    
        if( $apiData['total'] > 0 ){
            
            $i = 0;
    
            /*
			// 결제방법
			$this->search['settleKind'] = [
				'all' => '=' . __('결제방법') . '=',
				'gb,fa' => __('무통장 입금'),
				'pb,fb,eb' => __('계좌이체'),
				'pc,fc,ec' => __('신용카드'),
				'ph,fh' => __('휴대폰'),
				'pv,fv,ev' => __('가상계좌'),
				'gz' => __('전액할인'),
				'gd' => __('예치금'),
				'gm' => __('마일리지'),
				'fp' => __('포인트'),
				'pn' => __('네이버페이'),
				'pk' => __('카카오페이'),
				'gr' => __('기타'),
			];
            */

            /*
            echo "<pre>";
            print_r($apiData['data']);
            echo "</pre>";
            */

            foreach ($apiData['data'] as &$order) {
                    //dd($order);

                $orderMargin[$i]['orderNo'] = $order['orderNo'];
                $orderMargin[$i]['orderDate'] = $order['regDt'];
                $orderMargin[$i]['paymentDt'] = $order['paymentDt']; //결제일
                $orderMargin[$i]['settleKind'] = $order['settleKind']; //결제방법
                $orderMargin[$i]['salePrice'] = $order['settlePrice']; // 총주문금액
                $orderMargin[$i]['refundPrice'] = $order['refundPrice']; // 환불금액
                $orderMargin[$i]['totalGoodsPrice'] = $order['totalGoodsPrice']; // 총상품금액
                $orderMargin[$i]['orderGoodsCnt'] = $order['orderGoodsCnt']; // 총상품개수
                $orderMargin[$i]['totalDcPrice'] = $order['dc_info']['totalDcPrice'] ?? 0; // 총할인금액
                $orderMargin[$i]['useMileage'] = $order['useMileage']; // 부가결제

                // 결제율: 총주문금액 / 총상품금액 * 100
                $orderMargin[$i]['paymentRate'] = ($order['totalGoodsPrice'] > 0)
                    ? round(($order['settlePrice'] / $order['totalGoodsPrice']) * 100, 2)
                    : 0;

                $orderMargin[$i]['goods'] = []; // 각 주문의 상품 코드를 저장할 배열
                $orderMargin[$i]['totalCost'] = 0; // 주문의 총 원가를 저장할 변수
    
                $orderMargin[$i]['scm_my'] = 0; // 보유상품
                $orderMargin[$i]['scm_not_my'] = 0; // 미보유상품
                $orderMargin[$i]['non_sale'] = 0; // 비판매상품


                $package_remove = false;

                // addField가 배열인 경우 순회하며 패키지 제거 여부 확인
                if(isset($order['addField']) && is_array($order['addField'])) {
                    foreach($order['addField'] as $field) {
                        if(isset($field['name']) && $field['name'] == '패키지 제거 여부' && 
                           isset($field['data']) && $field['data'] == '패키지 제거') {
                            $package_remove = true;
                            break;
                        }
                    }
                }
    
                /*
                $scmMapping = [
                    0  => ['name' => '오류', 'partner_key' => null, 'display'=>'none' ],
                    1  => ['name' => '주식회사 디엔픽스', 'partner_key' => null, 'display'=>'none' ],
                    2  => ['name' => '모브X', 'partner_key' => null, 'display'=>'none' ],
                    3  => ['name' => '모브', 'partner_key' => 3],
                    4  => ['name' => '공급사사입', 'partner_key' => null, 'display'=>'none'],
                    5  => ['name' => '바니컴퍼니', 'partner_key' => 8],
                    6  => ['name' => '바이담', 'partner_key' => 10],
                    7  => ['name' => '해외직구', 'partner_key' => null, 'display'=>'none'],
                    8  => ['name' => '그린쉘프', 'partner_key' => 12],
                    9  => ['name' => '울컨코리아', 'partner_key' => 7],
                    10 => ['name' => '모노프로', 'partner_key' => 11],
                    11 => ['name' => '핑크에그', 'partner_key' => 9],
                    12 => ['name' => '리퍼브', 'partner_key' => null, 'display'=>'none'],
                    13 => ['name' => 'MSHb2b', 'partner_key' => 5],
                    14 => ['name' => 'JPDOLL', 'partner_key' => 14],
                    15 => ['name' => '도라토이', 'partner_key' => 6],
                    16 => ['name' => '대형', 'partner_key' => null, 'display'=>'none'],
                    17 => ['name' => '리퍼브', 'partner_key' => null, 'display'=>'none'],
                    18 => ['name' => '랜덤박스', 'partner_key' => null, 'display'=>'none'],
                    19 => ['name' => '예비1', 'partner_key' => null, 'display'=>'none'],
                    20 => ['name' => '예비2', 'partner_key' => null, 'display'=>'none'],
                    21 => ['name' => '예비3', 'partner_key' => null, 'display'=>'none'],
                    22 => ['name' => '텐가', 'partner_key' => null, 'display'=>'none'],
                    23 => ['name' => '로마', 'partner_key' => null, 'display'=>'none'],
                    24 => ['name' => '기획세트 (트릭박스)', 'partner_key' => null, 'display'=>'none'],
                    25 => ['name' => '기획세트 (대형)', 'partner_key' => null, 'display'=>'none'],
                ];
                */

                $goodsInfo= [];

                //dd($order['orderGoods']);

                foreach ($order['orderGoods'] as &$goods) {
    
                    //공급사가 쑈당몰인지
                    $myScmNos = [1,7,16,17,18,19,20,24,25];
                    if( in_array($goods['scmNo'], $myScmNos) ){
                    //if( $goods['scmNo'] == 1 || $goods['scmNo'] == 7 || $goods['scmNo'] == 16 || $goods['scmNo'] == 17 || $goods['scmNo'] == 18 || $goods['scmNo'] == 19 || $goods['scmNo'] == 20 ){
                        $orderMargin[$i]['scm_my']++;
                        $goods['scm'] = "my";
                    }else{
                        $orderMargin[$i]['scm_not_my']++;
                        $goods['scm'] = "not";
                    }

                    $goodsNo = $goods['goodsNo'];
                    $goodsCd = $goods['goodsCd'];
                    $optionCodes = []; // 옵션에서 추출한 상품 코드들을 저장할 배열
                    $setCodes= []; // 세트상품에서 추출한 상품 코드들을 저장할 배열
                    
                    // optionInfo에서 상품 코드 추출
                    if (isset($goods['optionInfo']) && is_array($goods['optionInfo'])) {
                        foreach ($goods['optionInfo'] as $option) {
                            if (isset($option[2]) && !empty($option[2])) {
                                $code = $option[2];
                                
                                // 슬래시(/)로 구분된 코드 처리
                                if (strpos($code, '/') !== false) {
                                    $splitCodes = explode('/', $code);
                                    foreach ($splitCodes as $splitCode) {
                                        if (!empty($splitCode) && ctype_digit($splitCode)) {
                                            $optionCodes[] = $splitCode;
                                            //$orderMargin[$i]['goods'][] = $splitCode; // 주문별 상품 코드 추가
                                            $goodsInfo[] = [
                                                'item_type' => 'option',
                                                'code' => $splitCode,
                                                'qty' => $goods['goodsCnt'],
                                                'scm' => $goods['scm'],
                                            ];
                                        }
                                    }
                                } 

                                //수량 옵션일경우
                                elseif (strpos($code, '@') !== false) {

                                    $splitCodes = explode('@', $code);
                                    $optionCodes[] = $splitCodes[0];
                                    $goodsInfo[] = [
                                        'code' => $splitCodes[0],
                                        'qty' => $splitCodes[1],
                                        'scm' => $goods['scm'],
                                    ];

                                }

                                // 단일 코드 처리
                                else if (ctype_digit($code)) {
                                    $optionCodes[] = $code;
                                    //$orderMargin[$i]['goods'][] = $code; // 주문별 상품 코드 추가
                                    $goodsInfo[] = [
                                        'item_type' => 'main',
                                        'code' => $code,
                                        'qty' => $goods['goodsCnt'],
                                        'scm' => $goods['scm'],
                                    ];
                                }
                            }
                        }
                    }
    
                    // 추출된 옵션 코드들을 numericGoodsList에 추가
                    foreach ($optionCodes as $code) {
                        $numericGoodsList[] = $code;
                        if($package_remove) {
                            if (!isset($packageRemoveList[$code])) {
                                $packageRemoveList[$code] = 0;
                            }
                            //$packageRemoveList[$code]++;
                            $packageRemoveList[$code] += (int)$goods['goodsCnt'];
                        }
                    }
    
                    if( $goodsCd == '결제명 상품' ){
                        $orderMargin[$i]['non_sale']++;
                    }

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
                        
                        //$orderMargin[$i]['goods'][] = $goodsCd; // 주문별 상품 코드 추가
                        $goodsInfo[] = [
                            'code' => $goodsCd,
                            'qty' => $goods['goodsCnt'],
                            'scm' => $goods['scm'],
                            'scmNo' => $goods['scmNo'],
                            'goodsNo' => $goods['goodsNo'],
                        ];
                        
                        if($package_remove) {
                            $goods['package_remove'] = true;
                            // 패키지 제거 카운트 증가
                            if (!isset($packageRemoveList[$goodsCd])) {
                                $packageRemoveList[$goodsCd] = 0;
                            }
                            $packageRemoveList[$goodsCd]++;
                        }
                    
                    }
                    
                    // one 단어가 포함된 goodsCd
                    elseif (stripos($goodsCd, 'one') !== false) {

                        $_ary_prdCode1 = explode("@", $goodsCd);
						$_ary_prdCode2 = explode("/", $_ary_prdCode1[1]);

                        foreach ($_ary_prdCode2 as $prdCode) {
                            $numericGoodsList[] = $prdCode;
                            $goodsInfo[] = [
                                'code' => $prdCode,
                                'qty' => $goods['goodsCnt'],
                                'scm' => $goods['scm'],
                                'scmNo' => $goods['scmNo'],
                                'goodsNo' => $goods['goodsNo'],
                            ];
                        }

                        /*
                        if( $order['orderNo'] == '2601111820000061' ){
                            dd($goodsInfo);
                        }
                            */

                        $goods['match'] = "set";
                        $setGoodsList[] = $goodsCd;
                        if($package_remove) {
                            $goods['package_remove'] = true;
                        }
                    }

                    // set ,qty, one 단어가 포함된 goodsCd
                    elseif (stripos($goodsCd, 'set') !== false || stripos($goodsCd, 'qty') !== false ) {
                    //elseif (stripos($goodsCd, 'set') !== false || stripos($goodsCd, 'qty') !== false ) {
                        $goods['match'] = "set";
                        $setGoodsList[] = $goodsCd;
                        if($package_remove) {
                            $goods['package_remove'] = true;
                        }
                    }

                    // 기타 (한글은 없지만 숫자가 아닌 다른 문자 포함)
                    else {

                        $goods['match'] = "scm";
                        $otherGoodsList[] = $goodsCd;
                        $otherGoodNos[] = $goodsNo;

                        $goodsInfo[] = [
                            'code' => $goodsCd,
                            'qty' => $goods['goodsCnt'],
                            'scm' => $goods['scm'],
                            'scmNo' => $goods['scmNo'],
                            'goodsNo' => $goods['goodsNo'],
                            'goods_name' => $goods['goodsNm'],
                        ];

                        if($package_remove) {
                            $goods['package_remove'] = true;
                        }
                    }      
    
                }

                $orderMargin[$i]['goods'] = $goodsInfo;
                $orderMargin[$i]['goodsCnt'] = count($order['orderGoods']); // 대상상품 개수
                $i++;
    
            }
        
    
            $numericGoodsListOriginal = $numericGoodsList;
        
            // 중복 제거 및 인덱스 재정렬
            $numericGoodsList = array_values(array_unique($numericGoodsList));
            $setGoodsList = array_values(array_unique($setGoodsList));
            $otherGoodsList = array_values(array_unique($otherGoodsList));
            $otherGoodNos = array_values(array_unique($otherGoodNos)); // 공급사 상품 번호 중복 제거
        
            $productStockService = new ProductStockService();
            $productData = $productStockService->getProductStockWhereIn($numericGoodsList);
        
            // 공급사 제공 상품 데이터 가져오기
            $productPartnerService = new ProductPartnerService();
            $productPartnerData = [];
            /*
            if (!empty($otherGoodsList)) {
                $productPartnerData = $productPartnerService->getProductPartnerWhereInCode($otherGoodsList);
            }
            */
            if (!empty($otherGoodNos)) {
                $productPartnerData = $productPartnerService->getProductPartnerWhereInGodoGoodsNo($otherGoodNos);
            }
            
            // 매칭되지 않는 상품 찾기
            $unmatchedGoodsList = [];

            /*
            foreach ($otherGoodsList as $code) {
                if (!isset($productPartnerData[$code])) {
                    $unmatchedGoodsList[] = $code;

                    // 해당 코드를 가진 주문 상품 찾기
                    foreach ($apiData['data'] as $order) {
                        foreach ($order['orderGoods'] as $goods) {
                            if ($goods['goodsCd'] === $code) {
                                $errorList[] = [
                                    'orderDate' => $order['orderDate'],
                                    'paymentDt' => $order['paymentDt'],
                                    'error_code' => 'scm_code_not_match',
                                    'code' => $code,
                                    'orderNo' => $order['orderNo'],
                                    'orderGoods' => $goods
                                ];
                                break 2; // 주문과 상품을 찾았으면 루프 종료
                            }
                        }
                    }
                }
            }
            */
            if (!empty($otherGoodNos)) {
                foreach ($otherGoodNos as $goodsNo) {
                    if (!isset($productPartnerData[$goodsNo])) {
                        $unmatchedGoodsList[] = $goodsNo;

                        // 해당 코드를 가진 주문 상품 찾기
                        foreach ($apiData['data'] as $order) {
                            foreach ($order['orderGoods'] as $goods) {
                                if ($goods['goodsNo'] === $goodsNo) {
                                    $errorList[] = [
                                        'orderDate' => $order['orderDate'],
                                        'paymentDt' => $order['paymentDt'],
                                        'error_code' => 'scm_code_not_match',
                                        'goodsNo' => $goodsNo,
                                        'orderNo' => $order['orderNo'],
                                        'orderGoods' => $goods
                                    ];
                                    break 2; // 주문과 상품을 찾았으면 루프 종료
                                }
                            }
                        }

                    }
                }
            }
        
            // 상품 데이터를 이용하여 $orderMargin의 원가 정보 업데이트
            foreach ($orderMargin as &$order) {
                
                $order['totalCost'] = 0;
                $costGoodsCount = 0;
                
                foreach ($order['goods'] as &$goodsInfo) {

                    $goodsCode = $goodsInfo['code'];
                    $qty = $goodsInfo['qty'];
                    $scm = $goodsInfo['scm'];
                    $scmNo = $goodsInfo['scmNo'] ?? null;
                    $goodsNo = $goodsInfo['goodsNo'] ?? null;
                    $goodsStockCode = $goodsCode;

                    $dbGoods = null; // 초기화

                    if( $scm == "my" ){

                        if (isset($productData[$goodsCode])) {

                            $costPrice = $productData[$goodsCode]['cd_cost_price'];
                            $costPriceSum = $costPrice * $qty;
                            $idx = $productData[$goodsCode]['CD_IDX'];

                            $dbGoods = [
                                'goods_name' => $productData[$goodsCode]['CD_NAME'],
                                'cost_price' => $productData[$goodsCode]['cd_cost_price']
                            ];

                            if( $costPrice > 0 ){
                                $order['totalCost'] += $costPriceSum;
                                $costGoodsCount += 1;
                            }
                            // 원가가 없는 경우 에러 리스트에 추가
                            else{
                                $errorList[] = [
                                    'orderDate' => $order['orderDate'],
                                    'paymentDt' => $order['paymentDt'],
                                    'error_code' => 'cost_price_not_found',
                                    'mode' => 'stock',
                                    'code' => $goodsStockCode,
                                    'idx' => $idx,
                                    'scmNo' => $scmNo,
                                    'orderNo' => $order['orderNo'],
                                    'dbGoods' => $dbGoods
                                ];
                            }
                            
    
                            /*
                            $goodsInfo = [
                                'code' => $goodsCode,
                                'qty' => $qty,
                                'cost_price' => $costPrice
                            ];
                            */
                            $goodsInfo['is_owned'] = true;
                            $goodsInfo['cost_price'] = $costPrice;
                            $goodsInfo['cost_price_sum'] = $costPriceSum;
                            $goodsInfo['goods_name'] = $dbGoods['goods_name'] ?? null;
                            $goodsInfo['cd_idx'] = $idx;


                        } else {

                            /*
                            $goodsInfo = [
                                'code' => $goodsCode,
                                'qty' => $qty,
                                'cost_price' => 0
                            ];
                            */
                            $goodsInfo['cost_price'] = 0;
                            //$goodsInfo['goods_name'] = null;

                            // 상품 데이터가 없는 경우 에러 리스트에 추가
                            $errorList[] = [
                                'error_code' => 'product_data_not_found',
                                'code' => $goodsStockCode,
                                'orderNo' => $order['orderNo'],
                                'dbGoods' => null
                            ];
                        }

                    }else{

                        if( isset($productPartnerData[$goodsNo]) ){

                            $costPrice = $productPartnerData[$goodsNo]['cost_price'];
                            $costPriceSum = $costPrice * $qty;
                            $idx = $productPartnerData[$goodsNo]['idx'];

                            $dbGoods = [
                                'goods_name' => $productPartnerData[$goodsNo]['name'],
                                'cost_price' => $productPartnerData[$goodsNo]['cost_price']
                            ];

                            if( $costPrice > 0 ){
                                $order['totalCost'] += $costPriceSum;
                                $costGoodsCount += 1;
                            }
                            // 원가가 없는 경우 에러 리스트에 추가
                            else{
                                $errorList[] = [
                                    'error_code' => 'cost_price_not_found',
                                    'mode' => 'partner',
                                    'code' => $goodsNo,
                                    'idx' => $idx,
                                    'scmNo' => $scmNo,
                                    'orderNo' => $order['orderNo'],
                                    'dbGoods' => $dbGoods
                                ];
                            }

                            $goodsInfo['is_owned'] = false;
                            $goodsInfo['cost_price'] = $costPrice;
                            $goodsInfo['cost_price_sum'] = $costPriceSum;
                            $goodsInfo['goods_name'] = $dbGoods['goods_name'] ?? null;
                            $goodsInfo['pp_idx'] = $idx;

                        }

                    }
                }
                unset($goodsInfo);
        
                $order['costGoodsCount'] = $costGoodsCount;

                // 마진 금액과 마진율 계산
                $finalPrice = $order['salePrice'] - $order['refundPrice'];
                $order['finalPrice'] = $finalPrice; //정산금액
                $order['marginAmount'] = $finalPrice - $order['totalCost'];
                $order['marginRate'] = $finalPrice > 0 ? 
                    round(($order['marginAmount'] / $finalPrice) * 100, 2) : 0;

            }
        
            $productStockData = $this->calculateGoodsQuantity($numericGoodsListOriginal, $productData, $packageRemoveList);
        
        }

        $result = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'errorList' => $errorList,
            'otherGoodsList' => $otherGoodsList ?? [],
            'productPartnerData' => $productPartnerData ?? [],
            'unmatchedGoodsList' => $unmatchedGoodsList ?? [],
            'orderMargin' => $orderMargin ?? [],
            'errorGoodsList' => $errorGoodsList ?? [],
            'numericGoodsList' => $numericGoodsList ?? [],
            'setGoodsList' => $setGoodsList ?? [],
            'productStockData' => $productStockData ?? [],
            'orderData' => $apiData ?? [],
        ];
    
        return $result;

    }


    /**
     * 패킹리스트 조회
     * @param array $criteria 검색 조건
     * @return array
     */
    public function getOrderPackingList($criteria) 
    {
        
        $mode = $criteria['mode'] ?? 'b';
        $start_date = $criteria['start_date'] ?? date('Y-m-d');
        $end_date = $criteria['end_date'] ?? date('Y-m-d');

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
                        $goods['package_volume'] = $productData[$goods['goodsCd']]['package_volume'];
                        $goods['package_volume_m3'] = $productData[$goods['goodsCd']]['package_volume_m3'];
                        $goods['package_volume_level'] = $productData[$goods['goodsCd']]['package_volume_level'];
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

        return $apiData;

    }


    /**
     * 상품코드별 수량을 계산하는 함수
     * @param array $goodsList 상품 코드 배열
     * @param array $productData 상품 데이터 배열
     * @param array $packageRemoveList 패키지 제거 여부 배열
     * @return array 상품코드별 수량 배열 [['code' => 코드, 'qty' => 수량, 'package_remove_qty' => 패키지제거수량, ...], ...]
     */
    public function calculateGoodsQuantity($goodsList, $productData, $packageRemoveList) 
    {
        
        $result = [];
        $counts = array_count_values($goodsList);
        
        foreach ($counts as $code => $qty) {
            $product = $productData[$code];
            $packageRemoveQty = isset($packageRemoveList[$code]) ? $packageRemoveList[$code] : 0;
            $result[] = [
                'CD_NAME' => $product['CD_NAME'],
                'code' => $code,
                'qty' => $qty,
                'stock' => $product['ps_stock'],
                'rack_code' => $product['ps_rack_code'],
                'bar_code' => $product['CD_CODE'],
                'package_remove_qty' => $packageRemoveQty
            ];
        }
        
        // 수량(qty) 기준으로 내림차순 정렬
        usort($result, function($a, $b) {
            return $b['qty'] - $a['qty'];
        });
        
        return $result;
    }

}