<?php

namespace App\Services;

use App\Core\BaseClass;
use App\Models\ProductModel;
use App\Models\BrandModel;

class ProductService extends BaseClass 
{

    /**
     * 상품 목록 조회
     * 
     * @param array $criteria 검색 조건
     * @return array 상품 목록 데이터
     */
    public function getProductList($criteria)
    {

        $kind_code = $criteria['kind_code'] ?? null;
        $site_show = $criteria['site_show'] ?? null;
        $paging = $criteria['paging'] ?? true;
        $perPage = $criteria['per_page'] ?? 100;
        $page = $criteria['page'] ?? 1;
        $show_mode = $criteria['show_mode'] ?? null;
        $search_value = $criteria['search_value'] ?? '';
        $tier = $criteria['tier'] ?? null;
        $brand_idx = $criteria['brand_idx'] ?? null;

        $query = ProductModel::query()
            ->when($kind_code, function($query) use ($kind_code) {
                $query->where('CD_KIND_CODE', $kind_code);
            })
            ->when($site_show, function($query) use ($site_show) {
                $query->where('cd_site_show', $site_show);
            })
            ->when($tier, function($query) use ($tier) {
                $query->where('cd_tier', $tier);
            })
            ->when($brand_idx, function($query) use ($brand_idx) {
                $query->where('CD_BRAND_IDX', $brand_idx);
            })
            ->orderBy('CD_IDX', 'desc');

        // 검색어 처리 (빈 문자열 체크)
        if (!empty($search_value)) {
            // 검색어 이스케이프 (SQL Injection 방지)
            $searchEscaped = addslashes($search_value);
            
            // 영문 검색 여부 확인
            if (preg_match("/[a-zA-Z]/", $searchEscaped)) {
                // 영문 포함 - 대소문자 구분 없이 검색
                $query->where(function($q) use ($searchEscaped) {
                    $q->whereRaw("LOWER(CD_NAME) LIKE '%".strtolower($searchEscaped)."%'")
                      ->orWhereRaw("LOWER(REPLACE(CD_NAME, ' ', '')) LIKE '%".strtolower($searchEscaped)."%'")
                      ->orWhereRaw("LOWER(CD_SEARCH_TERM) LIKE '%".strtolower($searchEscaped)."%'")
                      ->orWhereRaw("LOWER(CD_NAME_OG) LIKE '%".strtolower($searchEscaped)."%'");
                });
            } else {
                // 한글 등 - 그대로 검색
                $query->where(function($q) use ($searchEscaped) {
                    $q->whereRaw("CD_NAME LIKE '%".$searchEscaped."%'")
                      ->orWhereRaw("REPLACE(CD_NAME, ' ', '') LIKE '%".$searchEscaped."%'")
                      ->orWhereRaw("CD_SEARCH_TERM LIKE '%".$searchEscaped."%'")
                      ->orWhereRaw("CD_NAME_OG LIKE '%".$searchEscaped."%'");
                });
            }
        }

        if( $show_mode == 'onadb_main' ){
            $query->select('CD_IDX', 'CD_NAME', 'CD_NAME_OG', 'CD_BRAND_IDX', 'CD_IMG', 'CD_IMG2', 'cd_tier');
        }

        $result = $paging ? $query->paginate($perPage, $page)
            : $query->get()->toArray();

        // 브랜드명 추가
        $result = $this->attachBrandNames($result, $paging);

        return $result;

    }
    

    /**
     * 상품 목록 조회 (관리자용)
     * 
     * @param array $criteria 검색 조건
     * @return array 상품 목록 데이터
     */
    public function getProductListForAdmin($criteria)
    {

        $paging = $criteria['paging'] ?? true;
        $perPage = $criteria['per_page'] ?? 100;
        $page = $criteria['page'] ?? 1;
        $show_mode = $criteria['show_mode'] ?? '';


        // 기본 쿼리
        $query = ProductModel::query()
            ->from('COMPARISON_DB as A');

        // prd_stock에 연결된 상품만 (재고 관련 모드)
        if ($show_mode === 'product_stock') {

            // INNER JOIN으로 재고 있는 상품만 자동 필터링 + 정렬도 가능
            // 상품당 재고 1개이므로 GROUP BY 불필요
            $query->join('prd_stock as D', 'D.ps_prd_idx', '=', 'A.CD_IDX')
                  ->select('A.*', 'D.*');  // 상품 + 재고 정보 모두 가져오기

            $sort_mode = $criteria['sort_mode'] ?? 'stock';
        }else{
            $sort_mode = $criteria['sort_mode'] ?? 'idx';
        }

        // 정렬 적용
        $this->applySortMode($query, $sort_mode);

        $result = $paging ? $query->paginate($perPage, $page)
            : $query->get()->toArray();

        // 브랜드명 추가
        $result = $this->attachBrandNames($result, $paging);

        // 페이지네이션인 경우 data 키에서 가져옴
        $products = $paging ? ($result['data'] ?? []) : $result;

        $config_product = config('admin.product');
        $prd_kind_name = $config_product['prd_kind_name'] ?? [];

        // 상품 종류 추가
        foreach ($products as &$product) {

            $product['cd_size_fn'] = json_decode($product['cd_size_fn'] ?? '{}', true);
            if (!is_array($product['cd_size_fn'])) {
                $product['cd_size_fn'] = [];
            }

            // 패키지 사이즈 부피구하기 (명시적 타입 캐스팅)
            $_cd_size_w = (float)($product['cd_size_fn']['package']['W'] ?? 0);
            $_cd_size_h = (float)($product['cd_size_fn']['package']['H'] ?? 0);
            $_cd_size_d = (float)($product['cd_size_fn']['package']['D'] ?? 0);
            $_cd_size_volume = $_cd_size_w * $_cd_size_h * $_cd_size_d;
            
            $product['package_volume'] = $_cd_size_volume;  // cm³ (세제곱센티미터)
            $product['package_volume_m3'] = $_cd_size_volume / 1000000;  // m³ (세제곱미터)

            $product['package_volume_level'] = $this->getVolumeLevel($_cd_size_volume) ?? 0;

            $product['cd_weight_fn'] = json_decode($product['cd_weight_fn'] ?? '{}', true);
            if (!is_array($product['cd_weight_fn'])) {
                $product['cd_weight_fn'] = [];
            }

            $_cd_weight_1 = $product['cd_weight_fn']['1'] ?? null;
            $_cd_weight_2 = $product['cd_weight_fn']['2'] ?? null;
            $_cd_weight_3 = $product['cd_weight_fn']['3'] ?? null;

            // 명시적으로 숫자로 캐스팅
            $_cd_weight_2 = !empty($_cd_weight_2) ? (float)$_cd_weight_2 : 0;
            $_cd_weight_3 = !empty($_cd_weight_3) ? (float)$_cd_weight_3 : 0;

            $_weight = "";
            if( !$_cd_weight_2 && !$_cd_weight_3 ){
                $_weight = "";
            }elseif( $_cd_weight_3 ){
                $_weight = $_cd_weight_3;
            }elseif( $_cd_weight_2 && !$_cd_weight_3 ){
                $_weight = $_cd_weight_2;
            }

            $product['weight'] = $_weight;

            $product['cd_code_fn'] = json_decode($product['cd_code_fn'] ?? '{}', true);
            if (!is_array($product['cd_code_fn'])) {
                $product['cd_code_fn'] = [];
            }

            $product['ps_sale_log'] = json_decode($product['ps_sale_log'] ?? '[]', true);
            if (!is_array($product['ps_sale_log'])) {
                $product['ps_sale_log'] = [];
            }

            $product['last_sale'] = [
                'sale_date' => $product['ps_sale_date'] ?? '',
                'sale_count' => count($product['ps_sale_log']) ?? 0,
                'sale_subject' => $product['ps_sale_log'][0]['pg_subject'] ?? '',
                'sale_per' => $product['ps_sale_log'][0]['sale_per'] ?? 0,
            ];

            $product['prd_kind_name'] = $prd_kind_name[$product['CD_KIND_CODE']] ?? '미지정';

        }
        unset($product); // 참조 변수 해제

        if ($paging) {
            $result['data'] = $products;
        } else {
            $result = $products;
        }

        return $result;

    }


/**
 * 부피(cm³)에 따라 스케일 레벨 리턴
 *
 * 기준 스케일:
 *  - 512,000 cm³  → 레벨 3
 *  - 2,075,625 cm³ → 레벨 5
 *
 * 위 두 점 사이의 간격을 기준으로
 * 선형으로 레벨을 확장 (10 넘어가도 허용)
 *
 * 예)
 *  - 512,000  → 3
 *  - 2,075,625 → 5
 *  - 9,856,000 → 약 15
 *
 * @param int|float $volumeCm3 부피(cm³)
 * @return int 1 이상 레벨 (정수)
 */
private function getVolumeLevel($volumeCm3): int
{
    $volume = (float) $volumeCm3;

    // 기준 부피
    $baseVolume   = 512000;     // 기존 1단계 기준 → 레벨 3에 매핑
    $centerVolume = 2075625;    // 기존 3단계 기준 → 레벨 5에 매핑

    // 레벨 차이 (3 → 5 사이 2레벨)
    $baseLevel   = 3.0;
    $centerLevel = 5.0;

    // 부피 1레벨당 증가량 S
    $stepVolume = ($centerVolume - $baseVolume) / ($centerLevel - $baseLevel); // ≈ 781,812.5

    // stepVolume이 0이 되는 비정상 상황 가드
    if ($stepVolume <= 0) {
        return 1;
    }

    // 기본 레벨 계산 (선형 스케일)
    $rawLevel = $baseLevel + (($volume - $baseVolume) / $stepVolume);

    // 반올림해서 정수 레벨로 (최소 1 보장)
    $level = (int) round($rawLevel);

    if ($level < 1) {
        $level = 1;
    }

    return $level;
}




    /**
     * 정렬 모드 적용
     * 
     * @param QueryBuilder $query
     * @param string $sort_mode
     * @return void
     */
    private function applySortMode($query, $sort_mode)
    {
        switch ($sort_mode) {

            case 'stock':
                // 재고 많은순 (idx_prd_stock_stock)
                $query->orderBy('D.ps_stock', 'DESC')
                    ->orderBy('A.CD_IDX', 'DESC');
                break;

            case 'stock_asc':
                // 재고 적은순 (idx_prd_stock_stock)
                $query->orderBy('D.ps_stock', 'ASC')
                    ->orderBy('A.CD_IDX', 'DESC');
                break;

            case 'rack_code':
                // 랙코드순 (idx_prd_stock_rack_code)
                $query->orderBy('D.ps_rack_code', 'ASC')
                    ->orderBy('A.CD_IDX', 'DESC');
                break;

            case 'soldout':
                /**
                 * 품절 먼저, 그 안에서 품절일 최근순
                 *  - D.ps_stock < 1 : 품절/마이너스 재고
                 *  - D.ps_soldout_date : idx_prd_stock_soldout (ps_stock, ps_soldout_date)
                 */
                $query->orderByRaw('D.ps_stock < 1 DESC')   // 품절(1) → 재고있음(0)
                    ->orderBy('D.ps_soldout_date', 'DESC');
                break;

            case 'soldout_asc':
                // 품절 먼저, 품절일 오래된 순
                $query->orderByRaw('D.ps_stock < 1 DESC')
                    ->orderBy('D.ps_soldout_date', 'ASC');
                break;

            case 'price_desc':
                // 판매가 높은순 (idx_comparison_db_cd_sale_price)
                $query->orderBy('A.cd_sale_price', 'DESC')
                    ->orderBy('A.CD_IDX', 'DESC');
                break;

            case 'price_asc':
                /**
                 * 판매가 낮은순
                 *  - 0원(미설정)은 맨 뒤로 보내고 싶다면: cd_sale_price = 0 플래그 먼저, 그다음 가격
                 */
                $query->orderByRaw('A.cd_sale_price = 0 ASC')   // 0 → true(1) 이라서 마지막으로
                    ->orderBy('A.cd_sale_price', 'ASC')
                    ->orderBy('A.CD_IDX', 'DESC');
                break;

            case 'margin':
                /**
                 * 마진율 높은순
                 *  - 아직 마진 컬럼이 없다고 보고 런타임 계산
                 *  - A alias 사용 (from COMPARISON_DB as A)
                 */
                $query->selectRaw("
                        A.*,
                        D.*,
                        CASE
                            WHEN A.cd_sale_price > 0 AND A.cd_cost_price > 0 THEN
                                CASE
                                    WHEN A.cd_sale_price > 29999
                                        THEN (((A.cd_sale_price - A.cd_cost_price) + 2500) / A.cd_sale_price) * 100
                                    ELSE ((A.cd_sale_price - A.cd_cost_price) / A.cd_sale_price) * 100
                                END
                            ELSE NULL
                        END AS margin_per
                    ")
                    // 마진 계산 불가능(0, null)은 뒤로
                    ->orderByRaw('margin_per IS NULL ASC')
                    ->orderBy('margin_per', 'DESC');
                break;

            case 'release_date':
                /**
                 * 출시일 최근순
                 *  - 재고 있는 상품 우선, 그 안에서 최신 출시일
                 *  - idx_comparison_db_cd_release_date + ps_stock 인덱스 조합 활용
                 */
                $query->orderByRaw('D.ps_stock > 0 DESC')         // 재고있음(1) → 품절(0)
                    ->orderBy('A.CD_RELEASE_DATE', 'DESC')
                    ->orderBy('A.CD_IDX', 'DESC');
                break;

            case 'old_release_date':
                /**
                 * 출시일 오래된 순
                 *  - 출시일 0(미등록)은 맨 뒤로
                 */
                $query->orderByRaw('D.ps_stock > 0 DESC')
                    ->orderByRaw('A.CD_RELEASE_DATE = 0 ASC')
                    ->orderBy('A.CD_RELEASE_DATE', 'ASC')
                    ->orderBy('A.CD_IDX', 'DESC');
                break;

            case 'old_sale_date':
                /**
                 * 판매일 오래된 순
                 *  - 재고 있는 상품 우선
                 *  - idx_prd_stock_last_in_sale (ps_last_date, ps_in_date, ps_sale_date)
                 */
                $query->orderByRaw('D.ps_stock > 0 DESC')
                    ->orderBy('D.ps_last_date', 'ASC')
                    ->orderBy('D.ps_in_date', 'ASC')
                    ->orderBy('D.ps_sale_date', 'ASC')
                    ->orderBy('A.CD_IDX', 'DESC');
                break;

            case 'new_dis_date':
                /**
                 * 할인일(판매일) 최근순
                 *  - idx_prd_stock_sale_date (ps_sale_date, ps_stock)
                 */
                $query->orderBy('D.ps_sale_date', 'DESC')
                    ->orderBy('D.ps_stock', 'DESC')
                    ->orderBy('A.CD_IDX', 'DESC');
                break;

            case 'old_dis_date':
                // 할인일 오래된 순
                $query->orderBy('D.ps_sale_date', 'ASC')
                    ->orderBy('D.ps_stock', 'DESC')
                    ->orderBy('A.CD_IDX', 'DESC');
                break;

            case 'idx':
            default:
                // 기본: 최신 등록순 (PK 인덱스 사용)
                $query->orderBy('A.CD_IDX', 'DESC');
                break;
        }
    }


    /**
     * 상품 목록에 브랜드명 추가
     * 
     * @param array|object $result 상품 목록 (페이지네이션 또는 배열)
     * @param bool $paging 페이지네이션 여부
     * @return array
     */
    private function attachBrandNames($result, $paging = true)
    {
        // 페이지네이션인 경우 data 키에서 가져옴
        $products = $paging ? ($result['data'] ?? []) : $result;
        
        if (empty($products)) {
            return $result;
        }
        
        // 1. CD_BRAND_IDX와 CD_BRAND2_IDX 추출
        $brandIds = [];
        
        foreach ($products as $product) {
            // CD_BRAND_IDX 추가
            if (!empty($product['CD_BRAND_IDX'])) {
                $brandIds[] = $product['CD_BRAND_IDX'];
            }
            // CD_BRAND2_IDX 추가 (존재하고 0이 아닐 경우)
            if (!empty($product['CD_BRAND2_IDX']) && $product['CD_BRAND2_IDX'] != 0) {
                $brandIds[] = $product['CD_BRAND2_IDX'];
            }
        }
        
        $brandIds = array_unique($brandIds); // 중복 제거
        
        if (empty($brandIds)) {
            return $result;
        }
        
        // 2. 브랜드명 조회 (whereIn 사용, 필요한 컬럼만 select)
        $brands = BrandModel::query()
            ->select(['BD_IDX', 'BD_NAME'])
            ->whereIn('BD_IDX', $brandIds)
            ->get()
            ->toArray();
        
        // 3. BD_IDX를 키로 하는 배열로 변환
        $brandMap = [];
        foreach ($brands as $brand) {
            $brandMap[$brand['BD_IDX']] = $brand['BD_NAME'] ?? '';
        }
        
        // 4. 상품에 브랜드명 추가
        foreach ($products as &$product) {
            // 첫 번째 브랜드명
            $product['brand_name'] = $brandMap[$product['CD_BRAND_IDX']] ?? '';
            
            // 두 번째 브랜드명 (존재하고 0이 아닐 경우)
            if (!empty($product['CD_BRAND2_IDX']) && $product['CD_BRAND2_IDX'] != 0) {
                $product['brand_name2'] = $brandMap[$product['CD_BRAND2_IDX']] ?? '';
            } else {
                $product['brand_name2'] = '';
            }
        }
        
        // 5. 결과 반환
        if ($paging) {
            $result['data'] = $products;
        } else {
            $result = $products;
        }
        
        return $result;
    }


    /**
     * 상품 목록 조회 (구버전)
     * 
     * @param array $getData 파라미터
     * @param array|null $extraData 추가 파라미터
     * └ $extraData['showMode'] : 표시 모드 (hbti, normal) hbti - hbti가 있는 상품만 표시, normal - 모든 상품 표시
     * @return array 상품 목록 데이터
     */
    public function getProductListOld($getData, $extraData=null) 
    {

        $s_text = $getData['s_text'] ?? '';
        $s_brand = $getData['s_brand'] ?? '';
        $s_kind_code = $getData['s_kind_code'] ?? '';
        $s_national = $getData['s_national'] ?? '';
        $s_tier = $getData['s_tier'] ?? '';
        $s_hbti_type = $getData['s_hbti_type'] ?? '';
        $page = isset($getData['page']) ? $getData['page'] : 1;
        $perPage = isset($getData['per_page']) ? $getData['per_page'] : 100;


        // 쿼리 생성
        $query = ProductModel::query()
            ->leftJoin(_DB_BRAND.' as B', 'B.BD_IDX', '=', 'COMPARISON_DB.CD_BRAND_IDX')
            ->leftJoin(_DB_BRAND.' as C', 'C.BD_IDX', '=', 'COMPARISON_DB.CD_BRAND2_IDX', 'COMPARISON_DB.CD_BRAND2_IDX IS NOT NULL')
            ->leftJoin('prd_stock as D', 'D.ps_prd_idx', '=', 'COMPARISON_DB.CD_IDX');

        // 표시 모드 처리
        if(isset($extraData['showMode']) && $extraData['showMode'] === 'hbti'){
            $query->whereRaw('COMPARISON_DB.cd_hbti IS NOT NULL');
            $query->where('COMPARISON_DB.cd_hbti', '!=', '');
        }

        /*
        if ($s_hbti_type) {
            $json_string = json_encode(str_split($s_hbti_type), JSON_UNESCAPED_UNICODE); // ["S","F","J","E"]
            $query->where('COMPARISON_DB.cd_hbti_data', '=', $json_string);
        }
        */
        if ( $s_hbti_type ) {
            $query->where('COMPARISON_DB.cd_hbti', $s_hbti_type);
        }

        // 검색어 처리
        if( $s_text ) {
            if (preg_match("/[a-zA-Z]/", $s_text)){
                $query->where(function($query) use ($s_text) {
                    $query->whereRaw("INSTR(LOWER(COMPARISON_DB.CD_NAME), LOWER(?))", [$s_text])
                        ->orWhereRaw("INSTR(replace(COMPARISON_DB.CD_NAME,' ',''), LOWER(?))", [$s_text])
                        ->orWhereRaw("INSTR(LOWER(COMPARISON_DB.CD_NAME_OG), LOWER(?))", [$s_text])
                        ->orWhereRaw("INSTR(LOWER(COMPARISON_DB.CD_SEARCH_TERM), LOWER(?))", [$s_text])
                        ->orWhereRaw("INSTR(COMPARISON_DB.cd_code_fn, ?)", [$s_text]);
                });
            } else {
                $query->where(function($query) use ($s_text) {
                    $query->whereRaw("INSTR(COMPARISON_DB.CD_NAME, ?)", [$s_text])
                        ->orWhereRaw("INSTR(replace(COMPARISON_DB.CD_NAME,' ',''), ?)", [$s_text])
                        ->orWhereRaw("INSTR(COMPARISON_DB.CD_NAME_OG, ?)", [$s_text])
                        ->orWhereRaw("INSTR(COMPARISON_DB.CD_SEARCH_TERM, ?)", [$s_text])
                        ->orWhereRaw("INSTR(COMPARISON_DB.cd_code_fn, ?)", [$s_text]);
                });
            }
        }

        // 브랜드 검색
        if ( $s_brand ) {
            $query->where(function($query) use ($s_brand) {
                $query->where('COMPARISON_DB.CD_BRAND_IDX', $s_brand)
                    ->orWhere('COMPARISON_DB.CD_BRAND2_IDX', $s_brand);
            });
        }

        // 상품 종류 검색
        if ( $s_kind_code ) {
            $query->where('COMPARISON_DB.CD_KIND_CODE', $s_kind_code);
        }

        // 수입국가 검색
        if ( $s_national ) {
            $query->where('COMPARISON_DB.cd_national', $s_national);
        }
        
        // 티어 검색
        if ( $s_tier ) {
            $query->where('COMPARISON_DB.cd_tier', $s_tier);
        }

        // 최종 쿼리 실행
        $result = $query->select(
                'COMPARISON_DB.*', 
                'B.BD_NAME as brand_name1',
                'COALESCE(C.BD_NAME, "") as brand_name2',
                'D.ps_idx', 'D.ps_in_sale_s', 'D.ps_in_sale_e', 'D.ps_in_sale_data'
            )
            ->orderBy('COMPARISON_DB.CD_IDX', 'desc')
            ->paginate($perPage, $page);
            
        // 각 상품에 할인 아이콘 정보 추가
        if(isset($result['data']) && is_array($result['data'])) {

            foreach($result['data'] as &$item) {
                
                // HBTI 데이터 추가
                $_cd_hbti_data = json_decode($item['cd_hbti_data'] ?? '[]', true);
                
                // 배열 검증
                if (!is_array($_cd_hbti_data)) {
                    $_cd_hbti_data = [];
                }
                
                $hbti_html = '';

                if(!empty($_cd_hbti_data)){
                    foreach($_cd_hbti_data as $value){
                        if($value){
                            $hbti_html .= $value;
                        }
                    }
                }

                if($hbti_html){
                    $item['hbti_html'] = $hbti_html;
                } else {
                    $item['hbti_html'] = '';
                }

                // 세일 데이터가 있는 경우에만 아이콘 생성
                if(!empty($item['ps_in_sale_s']) && !empty($item['ps_in_sale_e']) && !empty($item['ps_in_sale_data'])) {
                    $item['inSaleIconHtml'] = $this->inSaleIcon($item['ps_in_sale_s'], $item['ps_in_sale_e'], $item['ps_in_sale_data']);
                } else {
                    $item['inSaleIconHtml'] = '';
                }

            }

        }

        return  $result;

    }


    /**
     * 상품 할인 아이콘 생성 헬퍼
     * 
     * @param int $ps_in_sale_s 할인 시작 시간
     * @param int $ps_in_sale_e 할인 종료 시간
     * @param string $ps_in_sale_data 할인 데이터
     * @return string 할인 아이콘 HTML
     */
    public function inSaleIcon($ps_in_sale_s, $ps_in_sale_e, $ps_in_sale_data) 
    {
        
        // 현재 시간 가져오기
        $current_time = time();
        $shtml = '';
    
        // 할인 기간 체크
        if ($ps_in_sale_s <= $current_time && $ps_in_sale_e >= $current_time) {
            // JSON 데이터 파싱
            $_data = json_decode($ps_in_sale_data ?? '{}', true);
    
            // 배열 검증 및 JSON 파싱 실패 또는 필수 데이터 누락 시 빈 문자열 반환
            if (!is_array($_data) || empty($_data['sale_mode']) || empty($_data['sale_per'])) {
                return '';
            }
    
            // 할인 모드 설정
            $_sale_name = ($_data['sale_mode'] === "period") ? "기간할인중 " : "일일할인중 ";
    
            // 할인 아이콘 HTML 생성
            $shtml = sprintf(
                "<div class='in-sale-icon-wrap'>
                    <span class='isi %s'>%s <b>%d</b>%%</span> 
                    <span class='isi-date'>%s ~ %s</span>
                </div>",
                htmlspecialchars($_data['sale_mode'], ENT_QUOTES, 'UTF-8'), // XSS 방지
                $_sale_name,
                (int) $_data['sale_per'], // 숫자형 변환
                date('y.m.d H:i', strtotime($ps_in_sale_s)),
                date('y.m.d H:i', strtotime($ps_in_sale_e))
            );
        }
    
        return $shtml;

    }
    

    /**
     * 사이트노출용 - 상품 데이터 조회
     * 
     * @param int $prdIdx 상품 인덱스
     * @return array 상품 데이터
     */
    public function getProductDataForSite($prdIdx)
    {

        $productData = ProductModel::query()
            ->where('CD_IDX', $prdIdx)
            ->first()
            ->toArray();

        if (!empty($productData)) {
            if (!empty($productData['CD_PD_INFO'])) {
                $productData['CD_PD_INFO'] = json_decode($productData['CD_PD_INFO'] ?? '[]', true);
                if (!is_array($productData['CD_PD_INFO'])) {
                    $productData['CD_PD_INFO'] = [];
                }
            }

            if (!empty($productData['CD_SIZE'])) {
                $productData['CD_SIZE'] = json_decode($productData['CD_SIZE'] ?? '[]', true);
                if (!is_array($productData['CD_SIZE'])) {
                    $productData['CD_SIZE'] = [];
                }
            }

            if(!empty($productData['cd_weight_fn'])){
                $productData['cd_weight_fn'] = json_decode($productData['cd_weight_fn'] ?? '{}', true);
                if (!is_array($productData['cd_weight_fn'])) {
                    $productData['cd_weight_fn'] = [];
                }
            }
        }

        return $productData;

    }


    /**
     * 관리자용 - 상품 데이터 조회
     * 
     * @param int $prdIdx 상품 인덱스
     * @return array 상품 데이터
     */
    public function getProductDataForAdmin($prdIdx) 
    {

        $productData = ProductModel::query()
            ->select([
                'COMPARISON_DB.*',
                'prd_stock.ps_idx', 'prd_stock.ps_rack_code', 'prd_stock.ps_stock_object', 'prd_stock.ps_alarm_count',
                'BRAND_DB.BD_NAME'
            ])
            ->leftJoin('prd_stock', 'prd_stock.ps_prd_idx', '=', 'COMPARISON_DB.CD_IDX')
            ->leftJoin('BRAND_DB', 'BRAND_DB.BD_IDX', '=', 'COMPARISON_DB.CD_BRAND_IDX')
            ->where('COMPARISON_DB.CD_IDX', '=', $prdIdx)
            ->first()
            ->toArray();

        // JSON 데이터 디코딩 처리
        if ($productData) {

            // CD_SIZE 디코딩
            if (!empty($productData['CD_SIZE'])) {
                $productData['CD_SIZE'] = json_decode($productData['CD_SIZE'] ?? '[]', true);
                if (!is_array($productData['CD_SIZE'])) {
                    $productData['CD_SIZE'] = [];
                }
            }
            
            // cd_size_fn 디코딩
            if (!empty($productData['cd_size_fn'])) {
                $productData['cd_size_fn'] = json_decode($productData['cd_size_fn'] ?? '{}', true);
                if (!is_array($productData['cd_size_fn'])) {
                    $productData['cd_size_fn'] = [];
                }
            }
            
            // cd_weight_fn 디코딩
            if (!empty($productData['cd_weight_fn'])) {
                $productData['cd_weight_fn'] = json_decode($productData['cd_weight_fn'] ?? '{}', true);
                if (!is_array($productData['cd_weight_fn'])) {
                    $productData['cd_weight_fn'] = [];
                }
            }
            
            // cd_add_img 디코딩
            if (!empty($productData['cd_add_img'])) {
                $productData['cd_add_img'] = json_decode($productData['cd_add_img'] ?? '{}', true);
                if (!is_array($productData['cd_add_img'])) {
                    $productData['cd_add_img'] = [];
                }
            }

            // cd_hbti_data 디코딩
            if (!empty($productData['cd_hbti_data'])) {
                $productData['cd_hbti_data'] = json_decode($productData['cd_hbti_data'] ?? '[]', true);
                if (!is_array($productData['cd_hbti_data'])) {
                    $productData['cd_hbti_data'] = [];
                }
            }

        }

        return $productData;
    }


    /**
     * HBTI 상품 등록현황 카운터
     * @return array HBTI 코드별 상품 수
     */
    public function gethbtiCount() 
    {
        $query = ProductModel::query()
            ->whereRaw('cd_hbti IS NOT NULL')
            ->where('cd_hbti', '!=', '')
            ->select('cd_hbti')
            ->get()
            ->toArray();

        $counts = [];
        foreach ($query as $row) {
            //$json = json_decode($row['cd_hbti_data'], true);
            $code = $row['cd_hbti'];

            if ($code) {
                $counts[$code] = ($counts[$code] ?? 0) + 1;
            }
        }

        // 4글자 조합과 그 외 조합 분리
        $fourCharCounts = [];
        $otherCounts = [];
        foreach ($counts as $code => $count) {
            if (strlen($code) === 4) {
                $fourCharCounts[$code] = $count;
            } else {
                $otherCounts[$code] = $count;
            }
        }

        // 각각 내림차순 정렬
        arsort($fourCharCounts);
        arsort($otherCounts);

        // 두 배열 합치기
        $sortedCounts = $fourCharCounts + $otherCounts;

        // 가장 많이 등록된 유형 찾기 (4글자 조합 중에서만)
        $mostUsed = key($fourCharCounts);

        return [
            'counts' => $sortedCounts,
            'mostUsed' => $mostUsed,
            'mostUsedCount' => $fourCharCounts[$mostUsed] ?? 0,
        ];
    }

}