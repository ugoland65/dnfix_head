<?php

namespace App\Services;

use App\Core\BaseClass;
use App\Models\ProductModel;

class ProductService extends BaseClass {

    /**
     * 상품 목록 조회
     * @param array $getData 파라미터
     * @param array|null $extraData 추가 파라미터
     * └ $extraData['showMode'] : 표시 모드 (hbti, normal) hbti - hbti가 있는 상품만 표시, normal - 모든 상품 표시
     * @return array 상품 목록 데이터
     */
    public function getProductList($getData, $extraData=null) 
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
        if ($s_hbti_type) {
            $query->where('COMPARISON_DB.cd_hbti', $s_hbti_type);
        }

        // 검색어 처리
        if($s_text) {
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
        if ($s_brand) {
            $query->where(function($query) use ($s_brand) {
                $query->where('COMPARISON_DB.CD_BRAND_IDX', $s_brand)
                    ->orWhere('COMPARISON_DB.CD_BRAND2_IDX', $s_brand);
            });
        }

        // 상품 종류 검색
        if ($s_kind_code) {
            $query->where('COMPARISON_DB.CD_KIND_CODE', $s_kind_code);
        }

        // 수입국가 검색
        if ($s_national) {
            $query->where('COMPARISON_DB.cd_national', $s_national);
        }
        
        // 티어 검색
        if ($s_tier) {
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
                $_cd_hbti_data = json_decode($item['cd_hbti_data'], true);
                $hbti_html = '';

                if(isset($_cd_hbti_data)){
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
     * @param int $ps_in_sale_s 할인 시작 시간
     * @param int $ps_in_sale_e 할인 종료 시간
     * @param string $ps_in_sale_data 할인 데이터
     * @return string 할인 아이콘 HTML
     */
    public function inSaleIcon($ps_in_sale_s, $ps_in_sale_e, $ps_in_sale_data) {
        
        // 현재 시간 가져오기
        $current_time = time();
        $shtml = '';
    
        // 할인 기간 체크
        if ($ps_in_sale_s <= $current_time && $ps_in_sale_e >= $current_time) {
            // JSON 데이터 파싱
            $_data = json_decode($ps_in_sale_data, true);
    
            // JSON 파싱 실패 또는 필수 데이터 누락 시 빈 문자열 반환
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
     * 상품 데이터 조회
     * @param int $prdIdx 상품 인덱스
     * @return array 상품 데이터
     */
    public function getProductData($prdIdx) 
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
            ->first();

        // JSON 데이터 디코딩 처리
        if ($productData) {

            // CD_SIZE 디코딩
            if (!empty($productData['CD_SIZE'])) {
                $productData['CD_SIZE'] = json_decode($productData['CD_SIZE'], true);
            }
            
            // cd_size_fn 디코딩
            if (!empty($productData['cd_size_fn'])) {
                $productData['cd_size_fn'] = json_decode($productData['cd_size_fn'], true);
            }
            
            // cd_weight_fn 디코딩
            if (!empty($productData['cd_weight_fn'])) {
                $productData['cd_weight_fn'] = json_decode($productData['cd_weight_fn'], true);
            }
            
            // cd_add_img 디코딩
            if (!empty($productData['cd_add_img'])) {
                $productData['cd_add_img'] = json_decode($productData['cd_add_img'], true);
            }

            // cd_add_img 디코딩
            if (!empty($productData['cd_hbti_data'])) {
                $productData['cd_hbti_data'] = json_decode($productData['cd_hbti_data'], true);
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