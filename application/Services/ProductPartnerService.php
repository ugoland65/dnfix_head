<?php

namespace App\Services;

use Exception;
use App\Core\BaseClass;
use App\Models\ProductPartnerModel;
use App\Models\ProductModel;
use App\Models\ProductCategoryMappingModel;
use App\Services\AdminActionLogService;
use App\Services\ProductPartnerApiService;
use App\Services\GodoApiService;
use App\Services\BrandService;
use App\Core\AuthAdmin;
use App\Services\ProductStockService;

class ProductPartnerService extends BaseClass
{
    
    /**
     * 상품 공급사 목록 조회
     * 
     * @param array $getData 파라미터
     * @param array|null $extraData 추가 파라미터
     * @return array
     */
    public function getProductPartnerList($getData, $extraData=null) 
    {

        // extraData 우선 병합
        $payloadData = array_replace((array)$getData, (array)$extraData);

        $s_text = $payloadData['s_text'] ?? '';
        $s_partner = $payloadData['s_partner'] ?? '';
        $s_godo_match = $payloadData['s_godo_match'] ?? null;
        $s_supplier_match = $payloadData['s_supplier_match'] ?? null;
        $s_keyword = $payloadData['s_keyword'] ?? '';
        $match_status = $payloadData['match_status'] ?? null;
        $s_brand = $payloadData['s_brand'] ?? null;
        $s_prd_kind = $payloadData['s_prd_kind'] ?? null;
        $s_prd_kind_second = $payloadData['s_prd_kind_second'] ?? null;
        $sort_mode = $payloadData['sort_mode'] ?? 'idx';
        $s_godo_sale_status = $payloadData['s_godo_sale_status'] ?? null; // 고도몰 판매상태
        $s_discontinued = $payloadData['s_discontinued'] ?? null; // 단종/취급중단
        $with_api_data = $payloadData['with_api_data'] ?? false;
        $is_match_excluded = $payloadData['is_match_excluded'] ?? null;

        $page = $payloadData['page'] ?? null;
        $perPage = $payloadData['per_page'] ?? null;

        $query = ProductPartnerModel::query()
            ->select([
                'prd_partner.*',
                'BRAND_DB.BD_NAME AS brand_name',
                'partners.name AS partner_name', 
                'partners.idx AS partner_idx'
            ])
            ->leftJoin('BRAND_DB', 'BRAND_DB.BD_IDX', '=', 'prd_partner.brand_idx')
            ->leftJoin('partners', 'partners.idx', '=', 'prd_partner.partner_idx')
            ->selectRaw("(SELECT MIN(CD_IDX) FROM COMPARISON_DB WHERE COMPARISON_DB.supplier_prd_idx = prd_partner.idx) AS cd_idx")
            ->selectRaw("(SELECT COUNT(*) FROM COMPARISON_DB WHERE COMPARISON_DB.supplier_prd_idx = prd_partner.idx) AS comparison_cnt");

        if($s_partner){
            $query->where('prd_partner.partner_idx', $s_partner);
        }

        //매칭 pk가 없는 것만 조회  
        if( $match_status == 'unmatched'  ){
            $query->where('prd_partner.supplier_prd_idx','=', 0);
        }

        // 고도몰 판매상태
        if( !empty($s_godo_sale_status) ){
            $query->where('prd_partner.status', $s_godo_sale_status);
        }

        // 단종/취급중단 검색
        if ($s_discontinued !== null && $s_discontinued !== '') {
            if ((string)$s_discontinued === '1') {
                $query->where('prd_partner.is_discontinued', 1);
            } elseif ((string)$s_discontinued === 'stopped') {
                $query->where('prd_partner.is_handling_stopped', 1);
            } elseif ((string)$s_discontinued === '0') {
                $query->where('prd_partner.is_discontinued', 0)
                    ->where('prd_partner.is_handling_stopped', 0);
            }
        }


        if( $s_supplier_match == 'matched' ){
            $query->where('prd_partner.supplier_prd_idx', '!=', 0);
        }elseif( $s_supplier_match == 'unmatched' ){
            $query->where('prd_partner.supplier_prd_idx', '=', 0);
        }

        if( $s_keyword ){
            $query->where('prd_partner.name', 'like', '%'.$s_keyword.'%');
            $query->orWhere('prd_partner.name_p', 'like', '%'.$s_keyword.'%');
            $query->orWhere('prd_partner.idx', 'like', '%'.$s_keyword.'%');
        }
        
        if( !empty($s_brand) ){
            $query->where('prd_partner.brand_idx', $s_brand);
        }

        if (!empty($s_prd_kind)) {
            // prd_partner.kind 컬럼은 분류명을 한글 value로 저장하므로 key를 value로 정규화한다.
            $kindName = $this->normalizeKindName((string)$s_prd_kind);
            if ($kindName !== '') {
                $query->where('prd_partner.kind', $kindName);
            }
        }

        if (!empty($s_prd_kind_second)) {
            $categoryCodeMap = $this->buildCategoryCodeMapByKind();
            $secondCategoryCode = trim((string)($categoryCodeMap[(string)$s_prd_kind_second] ?? ''));
            if ($secondCategoryCode !== '') {
                $query->where('prd_partner.category_code', $secondCategoryCode);
            }
        }

        // 매칭제외 여부
        if( !empty($is_match_excluded) ){
            $query->where('prd_partner.is_match_excluded', $is_match_excluded);
        }

        if( $sort_mode == 'idx' ){
            $query->orderBy('idx', 'DESC');
        }elseif( $sort_mode == 'updated_at' ){
            $query->orderBy('updated_at', 'DESC');
        }

        if ($perPage !== null ) {
            $result = $query->paginate($perPage, $page);
        } else {
            $result = $query->get();
        }

        $decodeSupplierOptionData = function (&$rows) {
            if (!is_array($rows)) {
                return;
            }
            foreach ($rows as &$row) {
                if (!is_array($row)) {
                    continue;
                }
                $row['supplier_option_data'] = !empty($row['supplier_option_data'])
                    ? (json_decode($row['supplier_option_data'], true) ?: [])
                    : [];
            }
            unset($row);
        };

        // 페이지네이션 결과(data) / 일반 리스트 모두 대응
        if (is_array($result) && isset($result['data']) && is_array($result['data'])) {
            $decodeSupplierOptionData($result['data']);
        } else {
            $decodeSupplierOptionData($result);
        }

        return $result;

    }


    /**
     * 상품 공급사 상세 조회
     * 
     * @param array $getData 파라미터
     * @param int $prdIdx 상품 인덱스
     * @return array
     */
    public function getProductPartnerInfo($prdIdx) 
    {

        $result = ProductPartnerModel::query()
            ->select([
                'prd_partner.*',
                'BRAND_DB.BD_NAME AS brand_name',
                'partners.name AS partner_name', 
                'partners.idx AS partner_idx'
            ])
            ->leftJoin('BRAND_DB', 'BRAND_DB.BD_IDX', '=', 'prd_partner.brand_idx')
            ->leftJoin('partners', 'partners.idx', '=', 'prd_partner.partner_idx')
            ->where('prd_partner.idx', $prdIdx)
            ->first()
            ->toArray();


        $result['price_data'] = !empty($result['price_data']) ? json_decode($result['price_data'], true) : [];
        $result['godo_option'] = !empty($result['godo_option']) ? json_decode($result['godo_option'], true) : [];
        $result['supplier_option_data'] = !empty($result['supplier_option_data']) ? json_decode($result['supplier_option_data'], true) : [];
        $result['supplier_detail_img'] = !empty($result['supplier_detail_img']) ? json_decode($result['supplier_detail_img'], true) : [];
        $result['cate_json'] = !empty($result['cate_json']) ? json_decode($result['cate_json'], true) : [];
        $result['godo_cate_json'] = !empty($result['godo_cate_json']) ? json_decode($result['godo_cate_json'], true) : [];
        $result['spec_data'] = !empty($result['spec_data']) ? json_decode($result['spec_data'], true) : [];
        if (!is_array($result['cate_json'])) {
            $result['cate_json'] = [];
        }
        if (!is_array($result['godo_cate_json'])) {
            $result['godo_cate_json'] = [];
        }
        if (!is_array($result['spec_data'])) {
            $result['spec_data'] = [];
        }
        $result['additional_category_codes'] = $this->getCategoryMappingCodes((int)$prdIdx, 'additional');
        $result['cd_sub_category_codes'] = $this->getCategoryMappingCodes((int)$prdIdx, 'sub');
        $result['preference_tag_codes'] = $this->getCategoryMappingCodes((int)$prdIdx, 'hashtag');
        
        return $result;

    }


    /**
     * 상품 공급사 저장
     * 
     * @param array $postData 파라미터
     * @return array
     */
    public function saveProductPartner($postData)
    {

        try {

            $toIntOrNull = function ($value) {
                if ($value === null) {
                    return null;
                }
                $value = is_string($value) ? trim($value) : $value;
                return $value === '' ? null : (int)$value;
            };

            $godo_goodsNo = $toIntOrNull($postData['godo_goodsNo'] ?? null); // 고도몰 상품번호
            $brand_idx = $postData['brand_idx'] ?? null; // 브랜드 인덱스
            $partner_idx = $postData['partner_idx'] ?? null; // 공급사 인덱스

            if(empty($godo_goodsNo)){
                $godo_goodsNo = null;
            }

            if(empty($brand_idx)){
                $brand_idx = null;
            }

            if(empty($partner_idx)){
                $partner_idx = 0;
            }

            $status = $postData['status'] ?? '등록대기'; // 상품 상태

            $sale_price = (int)preg_replace('/[,\s]/', '', $postData['sale_price'] ?? 0); // 판매가
            $cost_price = (int)preg_replace('/[,\s]/', '', $postData['cost_price'] ?? 0); // 원가
            $order_price = (int)preg_replace('/[,\s]/', '', $postData['order_price'] ?? 0); // 주문가
            $min_sale_price = (int)preg_replace('/[,\s]/', '', $postData['min_sale_price'] ?? 0); // 최저판매가
            $code = $postData['code'] ?? ''; // 상품 코드

            $is_vat = $postData['is_vat'] ?? 'Y'; // 부가세
            $delivery_fee = (int)preg_replace('/[,\s]/', '', $postData['delivery_fee'] ?? 0); // 배송비
            $delivery_com = $postData['delivery_com'] ?? null; // 배송회사
            $delivery_time = $postData['delivery_time'] ?? null; // 배송시간

            //$vat = preg_replace('/[,\s]/', '', $postData['vat'] ?? 10); // 부가세

            $action_url = $postData['action_url'] ?? ''; //로그용 변수
            $action_summary = $postData['action_summary'] ?? ''; //로그용 변수

            $supplier_status = $postData['supplier_status'] ?? '판매중'; // 공급사 판매상태

            $supplier_status_date =  null; // 공급사 판매상태 처리일

            if( $supplier_status != '판매중' ){
                $supplier_status_date = date('Y-m-d H:i:s');
            }

            $is_match_excluded = $postData['is_match_excluded'] ?? 'N'; // 매칭제외 여부


            // 원가가 없고 주문가와 배송비가 있으면 원가 계산
            /*
            if(empty($cost_price) && !empty($order_price) && !empty($delivery_fee)){
                $cost_price = $order_price + $delivery_fee;
                $vat = $cost_price * 0.1;
            }
            */

            // cost_price에 vat 값을 더함 (모든 경우)
            //$cost_price = $cost_price + $vat;

            if( $is_vat == 'N' ){
                $vat = $cost_price * 0.1;
                $cost_price_save = $cost_price;
                $order_price_save = $cost_price + $vat + ($delivery_fee ?? 0);
            }else{
                $vat = $cost_price / 11;
                $cost_price_save = ($cost_price / 1.1);
                $order_price_save = $cost_price + ($delivery_fee ?? 0); 
            }

            $price_data = [
                'is_vat' => $is_vat, // 부가세
                'cost_price' => $cost_price_save,
                'order_price' => $order_price_save,
                'delivery_fee' => $delivery_fee, // 배송비
                'delivery_com' => $delivery_com,
                'delivery_time' => $delivery_time,
                'vat' => $vat, // 부가세
            ];

            $price_data = json_encode($price_data, JSON_UNESCAPED_UNICODE);

            $name = $postData['name'] ?? '';
            $short_desc = trim((string)($postData['short_desc'] ?? ''));
            $name_ori = $postData['name_ori'] ?? '';
            $name_p = $postData['name_p'] ?? '';
            $kindInput = (string)($postData['kind'] ?? '');
            $kindCode = $this->normalizeKindCode($kindInput);
            $kind = $this->normalizeKindName($kindInput);
            $kindSecond = $postData['kind_second'] ?? '';
            $kindThird = $postData['kind_third'] ?? '';
            $categoryCode = $this->resolveCategoryCodeForSave(
                (string)$kindCode,
                (string)($postData['category_code'] ?? ''),
                (string)$kindSecond,
                (string)$kindThird
            );
            $additionalCategoryCodes = $this->normalizeAdditionalCategoryCodes(
                $postData['cd_additional_category_codes'] ?? [],
                $categoryCode
            );
            $preferenceTagCodes = $this->normalizePreferenceTagCodes($postData['preference_tag_codes'] ?? []);
            $subCategoryCodes = $this->normalizeSubCategoryCodes(
                $postData['cd_sub_category_codes'] ?? [],
                (string)$kindCode
            );
            $supplier_prd_idx = $toIntOrNull($postData['supplier_prd_idx'] ?? null);
            if ($supplier_prd_idx === null) {
                $supplier_prd_idx = 0;
            }
            $supplier_prd_pk = $toIntOrNull($postData['supplier_prd_pk'] ?? null);
            if ($supplier_prd_pk === null) {
                $supplier_prd_pk = 0;
            }
            $supplier_site = $postData['supplier_site'] ?? null;
            $supplier_2nd_name = $postData['supplier_2nd_name'] ?? null;
            $supplier_img_mode = $postData['supplier_img_mode'] ?? 'out';
            $supplier_img_src = $postData['supplier_img_src'] ?? null;
            $matching_code = $postData['matching_code'] ?? '';
            $memo = $postData['memo'] ?? '';
            $memo_work = $postData['memo_work'] ?? '';
            $img_mode = $postData['img_mode'] ?? 'out';
            $img_src = $postData['img_src'] ?? null;
            $hbti_type = $postData['hbti_type'] ?? '';
            $godo_goodsNo = $toIntOrNull($postData['godo_goodsNo'] ?? null);

            $cateItemsRaw = [];
            $cateJsonRaw = $postData['cate_json'] ?? '[]';
            if (is_string($cateJsonRaw) && trim($cateJsonRaw) !== '') {
                $cateJsonRaw = html_entity_decode($cateJsonRaw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $decodedCateJson = json_decode($cateJsonRaw, true);
                if (is_array($decodedCateJson)) {
                    $cateItemsRaw = $decodedCateJson;
                }
            }

            $cateItems = [];
            $cateDedupMap = [];
            foreach ($cateItemsRaw as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $depth1Code = trim((string)($item['depth1Code'] ?? ''));
                $depth2Code = trim((string)($item['depth2Code'] ?? ''));
                $depth3Code = trim((string)($item['depth3Code'] ?? ''));
                $selectedCode = trim((string)($item['selectedCode'] ?? ''));
                $pathLabel = trim((string)($item['pathLabel'] ?? ''));
                $key = trim((string)($item['key'] ?? ''));

                if ($selectedCode === '') {
                    $selectedCode = $key !== '' ? $key : ($depth3Code !== '' ? $depth3Code : ($depth2Code !== '' ? $depth2Code : $depth1Code));
                }

                if ($selectedCode === '') {
                    continue;
                }

                if (isset($cateDedupMap[$selectedCode])) {
                    continue;
                }
                $cateDedupMap[$selectedCode] = true;

                if ($key === '') {
                    $key = $selectedCode;
                }

                $cateItems[] = [
                    'key' => $key,
                    'depth1Code' => $depth1Code,
                    'depth2Code' => $depth2Code,
                    'depth3Code' => $depth3Code,
                    'selectedCode' => $selectedCode,
                    'pathLabel' => $pathLabel,
                ];
            }
            $cateJson = json_encode($cateItems, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $specData = (new ProductSpecService())->build($categoryCode, $postData);
            $specDataJson = empty($specData) ? null : json_encode($specData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            $inputData = [
                'name' => $name, // 판매 상품명
                'short_desc' => $short_desc, // 한줄 간략설명
                'name_ori' => $name_ori, // 원(영문,일어,중국어) 상품명
                'name_p' => $name_p, // 공급사 상품명
                'status' => $status, // 상품 상태
                'kind' => $kind, // 상품 구분 (상품 카테고리 코드)
                'category_code' => $categoryCode, // 분류 코드(2차 우선, 미선택시 1차)
                'brand_idx' => $brand_idx, // 브랜드 인덱스 (BRAND_DB 테이블의 BD_IDX)
                'partner_idx' => $partner_idx, // 공급사 인덱스 (partners 테이블의 idx)
                'is_match_excluded' => $is_match_excluded, // 매칭제외 여부
                'supplier_prd_idx' => $supplier_prd_idx, // 공급사 상품 인덱스 (supplier_product 테이블의 idx)
                'supplier_prd_pk' => $supplier_prd_pk, // 공급사 상품 고유번호
                'supplier_site' => $supplier_site, // 공급사 사이트
                'supplier_2nd_name' => $supplier_2nd_name, // 공급사 2차공급사
                'supplier_img_mode' => $supplier_img_mode, // 공급사 이미지 모드
                'supplier_img_src' => $supplier_img_src, // 공급사 이미지 URL
                'supplier_status' => $supplier_status, // 공급사 판매상태
                'supplier_status_date' => $supplier_status_date, // 공급사 판매상태 처리일
                'sale_price' => $sale_price, // 판매가
                'cost_price' => $cost_price, // 원가 (vat 포함)
                'order_price' => $order_price, // 주문가
                'min_sale_price' => $min_sale_price, // 최저판매가
                'price_data' => $price_data, // 가격 데이터
                'code' => $code, // 상품 코드
                'img_mode' => $img_mode, // 이미지 모드 (out: 외부 이미지, this: 서버에 등록)
                'img_src' => $img_src, // 이미지 URL 또는 경로
                'hbti_type' => $hbti_type, // HBTI 타입 정보
                'godo_goodsNo' => $godo_goodsNo, // 고도몰 상품코드
                'cate_json' => $cateJson, // 내부 상세분류(JSON)
                'spec_data' => $specDataJson,
                'matching_code' => $matching_code, // 공급사 매칭코드
                'memo' => $memo, // 메모
                'memo_work' => $memo_work, // 작업지시 메모
            ];

            $updateData = [];
            $fieldMap = [
                'name' => $name,
                'short_desc' => $short_desc,
                'name_ori' => $name_ori,
                'name_p' => $name_p,
                'status' => $status,
                'kind' => $kind,
                'category_code' => $categoryCode,
                'brand_idx' => $brand_idx,
                'partner_idx' => $partner_idx,
                'is_match_excluded' => $is_match_excluded, // 매칭제외 여부
                'supplier_prd_idx' => $supplier_prd_idx,
                'supplier_prd_pk' => $supplier_prd_pk,
                'supplier_site' => $supplier_site,
                'supplier_2nd_name' => $supplier_2nd_name,
                'supplier_img_mode' => $supplier_img_mode,
                'supplier_img_src' => $supplier_img_src,
                'supplier_status' => $supplier_status,
                'supplier_status_date' => $supplier_status_date,
                'sale_price' => $sale_price,
                'cost_price' => $cost_price,
                'order_price' => $order_price,
                'min_sale_price' => $min_sale_price,
                'price_data' => $price_data,
                'code' => $code,
                'img_mode' => $img_mode,
                'img_src' => $img_src,
                'hbti_type' => $hbti_type,
                'godo_goodsNo' => $godo_goodsNo,
                'cate_json' => $cateJson,
                'spec_data' => $specDataJson,
                'matching_code' => $matching_code,
                'memo' => $memo,
                'memo_work' => $memo_work,
            ];
            foreach ($fieldMap as $key => $value) {
                if (array_key_exists($key, $postData)) {
                    $updateData[$key] = $value;
                }
            }

            // delivery_fee 등 가격 관련 입력이 있으면 price_data를 강제로 반영한다.
            $hasPriceRelatedInput =
                array_key_exists('price_data', $postData) ||
                array_key_exists('delivery_fee', $postData) ||
                array_key_exists('order_price', $postData) ||
                array_key_exists('cost_price', $postData) ||
                array_key_exists('is_vat', $postData);
            if ($hasPriceRelatedInput) {
                $updateData['price_data'] = $price_data;
            }
            if (array_key_exists('spec_vendor', $postData) || array_key_exists('spec_measured', $postData) || array_key_exists('spec_option', $postData)) {
                $updateData['spec_data'] = $specDataJson;
            }

            // 공급사 판매상태가 '판매중'이 아니면 처리일을 강제로 갱신한다.
            if ($supplier_status !== '판매중') {
                $updateData['supplier_status_date'] = $supplier_status_date;
            }

            $beforeData = [];
            $actionMode = 'create';
            $targetPk = null;

            if(empty($postData['prd_idx'])){

                // prd_idx가 없으면 새 레코드 삽입
                $result = ProductPartnerModel::query()->insertGetId($inputData);
                $targetPk = $result;

            }else{
                
                $actionMode = 'update';
                $targetPk = $postData['prd_idx'];
                $beforeModel = ProductPartnerModel::find($postData['prd_idx']);
                $beforeData = $beforeModel ? $beforeModel->toArray() : [];
                $beforeData['additional_category_codes'] = $this->getCategoryMappingCodes((int)$targetPk, 'additional');
                $beforeData['cd_sub_category_codes'] = $this->getCategoryMappingCodes((int)$targetPk, 'sub');
                $beforeData['preference_tag_codes'] = $this->getCategoryMappingCodes((int)$targetPk, 'hashtag');
                // prd_idx가 있으면 기존 레코드 업데이트
                $result = ProductPartnerModel::find($postData['prd_idx'])->update($updateData);
                
            }

            if( $result ){
                if (array_key_exists('cd_additional_category_codes', $postData)) {
                    $this->syncCategoryMappingCodes((int)$targetPk, 'additional', $additionalCategoryCodes);
                }
                if (array_key_exists('cd_sub_category_codes', $postData)) {
                    $this->syncCategoryMappingCodes((int)$targetPk, 'sub', $subCategoryCodes);
                }
                if (array_key_exists('preference_tag_codes', $postData)) {
                    $this->syncCategoryMappingCodes((int)$targetPk, 'hashtag', $preferenceTagCodes);
                }

                $supplierDbActionMessage = null;

                $beforeSupplierStatus = $beforeData['supplier_status'] ?? null;
                $isDiscontinuedTarget = in_array($supplier_status, ['판매중단', '품절'], true);
                $shouldSyncDiscontinued = (
                    $actionMode === 'update' &&
                    ($beforeSupplierStatus === '판매중' || $beforeSupplierStatus === null) &&
                    $isDiscontinuedTarget &&
                    !empty($supplier_prd_idx)
                );

                if( $shouldSyncDiscontinued ){
                    $productPartnerApiService = new ProductPartnerApiService();
                    $productDiscontinued = $productPartnerApiService->productDiscontinued([
                        'idx' => $supplier_prd_idx,
                    ]);

                    $supplierDbActionMessage = ($supplier_status === '품절')
                        ? '공급사 DB 품절처리 완료'
                        : '공급사 DB 판매중단 처리 완료';
                }

                if(empty($action_summary)){
                    $action_summary = $actionMode === 'create' ? '파트너 상품 등록' : '파트너 상품 수정';
                }

                $afterData = array_merge($beforeData, $actionMode === 'create' ? $inputData : $updateData);
                $afterData['additional_category_codes'] = $this->getCategoryMappingCodes((int)$targetPk, 'additional');
                $afterData['cd_sub_category_codes'] = $this->getCategoryMappingCodes((int)$targetPk, 'sub');
                $afterData['preference_tag_codes'] = $this->getCategoryMappingCodes((int)$targetPk, 'hashtag');
                $adminActionLogService = new AdminActionLogService();
                $diff = $adminActionLogService->buildDiff($beforeData, $afterData);
                if ($supplierDbActionMessage !== null) {
                    $diff['supplier_db_action'] = [
                        'before' => null,
                        'after' => $supplierDbActionMessage,
                    ];
                }
                $adminActionLogService->log([
                    'target_type' => 'prd_partner',
                    'target_table' => 'prd_partner',
                    'target_pk' => (string)($targetPk ?? ''),
                    'action_mode' => $actionMode,
                    'action_summary' => $action_summary,
                    'before_json' => $beforeData,
                    'after_json' => $afterData,
                    'diff_json' => $diff,
                    'action_url' => $action_url ?? null,
                ]);

                return ['status' => 'success', 'message' => '저장되었습니다.', 'idx' => $targetPk];

            }else{
                throw new \Exception('상품 공급사 저장에 실패했습니다.');
            }

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }

    }


    /**
     * 상품 공급사 code Where In 조회
     * 
     * @param array $codes
     * @return array
     */
    public function getProductPartnerWhereInCode($codes) {

        return ProductPartnerModel::whereIn('code', $codes)
            ->get()
            ->keyBy('code')
            ->toArray();

    }

    
    /**
     * 상품 공급사 godo_goodsNo Where In 조회
     * 
     * @param array $codes
     * @return array
     */
    public function getProductPartnerWhereInGodoGoodsNo($goodsNos) {

        return ProductPartnerModel::whereIn('godo_goodsNo', $goodsNos)
            ->get()
            ->keyBy('godo_goodsNo')
            ->toArray();

    }


    /**
     * 상품 공급사 idx Where In 조회
     * 
     * @param array $idxs
     * @return array
     */
    public function getProductPartnerWhereInIdx($idxs) {

        if (!is_array($idxs)) {
            $idxs = is_string($idxs) ? explode(',', $idxs) : [];
        }
        $idxs = array_values(array_filter($idxs, static function ($value) {
            return $value !== null && $value !== '';
        }));

        if (empty($idxs)) {
            return [];
        }

        $idxs = array_values(array_unique($idxs));

        $result = ProductPartnerModel::query()
            ->select([
                'prd_partner.*',
                'BRAND_DB.BD_NAME AS brand_name',
                'partners.name AS partner_name', 
                'partners.idx AS partner_idx'
            ])
            ->leftJoin('BRAND_DB', 'BRAND_DB.BD_IDX', '=', 'prd_partner.brand_idx')
            ->leftJoin('partners', 'partners.idx', '=', 'prd_partner.partner_idx')
            ->whereIn('prd_partner.idx', $idxs)
            ->groupBy('prd_partner.idx')
            ->get()
            ->toArray();

        foreach($result as &$item){
            $item['price_data'] = !empty($item['price_data']) ? json_decode($item['price_data'], true) : [];
            $item['godo_option'] = !empty($item['godo_option']) ? json_decode($item['godo_option'], true) : [];
        }
        unset($item);

        return $result;

    }


    /**
     * 상품 공급사 품절처리 로그 남기기
     * 
     * @return array
     */
    public function soldOutPrdPartner( $data ) 
    {
        $idx = $data['idx'] ?? null;
        $action_url = $data['action_url'] ?? null;

        if( !empty($idx) ){
            $productPartner = ProductPartnerModel::find($idx);

            if( !empty($productPartner) ){

                $beforeMini = ['status' => $productPartner['status']];
                $afterMini  = ['status' => '품절'];

                $productPartner->update([
                    'status' => '품절',
                    'sold_out_date' => date('Y-m-d H:i:s'),
                ]);

                $adminActionLogService = new AdminActionLogService();
                $payload = [
                    'target_type' => 'prd_partner',
                    'target_table' => 'prd_partner',
                    'target_pk' => $idx,
                    'action_mode' => 'sold_out',
                    'action_summary' => '파트너 상품 품절처리',
                    'before_json' => $beforeMini,
                    'after_json' => $afterMini,
                    'action_url' => $action_url,
                ];
                $adminActionLogService->log($payload);
            }
        }
    }


    /**
     * 공급사 상품 매칭 제외
     * 
     * @param array $data
     * @return array
     */
    public function productMatchExcluded($data) 
    {
        $db1_idx = $data['db1_idx'] ?? null;
        $db2_idx = $data['db2_idx'] ?? null;
        $process_reason = $data['process_reason'] ?? null;

        if (!empty($db1_idx)) {
            
            $model = ProductPartnerModel::find($db1_idx);
            if (empty($model)) {
                return 0;
            }
    
            $beforeModel = $model->toArray();
            $rawMatchingData = $beforeModel['matching_data'] ?? '';
    
            if (is_array($rawMatchingData)) {
                $matchingData = $rawMatchingData;
            } elseif (is_string($rawMatchingData) && trim($rawMatchingData) !== '') {
                $decoded = json_decode($rawMatchingData, true);
                $matchingData = is_array($decoded) ? $decoded : [];
            } else {
                $matchingData = [];
            }
    
            $matchingData['match_excluded'] = [
                'reason' => $process_reason,
                'reg' => AuthAdmin::getConnectionInfo(),
            ];
    
            $result = ProductPartnerModel::where('idx', $db1_idx)->update([
                'is_match_excluded' => 'Y',
                'matching_data' => json_encode($matchingData, JSON_UNESCAPED_UNICODE),
            ]);

        }

        if (!empty($db2_idx)) {

            $productPartnerApiService = new ProductPartnerApiService();
            $productMatchExcluded = $productPartnerApiService->productMatchExcluded([
                'idx' => $db2_idx,
            ]);

        }

        return $result;
    }


    /**
     * 공급사 config pk로 코드 조회
     * 
     * @param int $idx
     * @return string
     */
    public function getProductPartnerCodeByIdx($pk) 
    {

        $config_provider = config('admin.provider');
        $supplier_code_data = $config_provider['supplier_code_data'];

        $site_code = '';
        foreach ($supplier_code_data as $supplierCode => $supplierInfo) {
            if ((int)($supplierInfo['idx'] ?? 0) === (int)($pk ?? 0)) {
                $site_code = (string)($supplierInfo['code'] ?? $supplierCode);
                break;
            }
        }

        return $site_code;

    }

    /**
     * 공급사 config scmNo로 pk 조회
     * 
     * @param int $idx
     * @return string
     */
    public function getProductPartnerIdxByScmNo($scmNo) 
    {

        $config_provider = config('admin.provider');
        $supplier_code_data = $config_provider['supplier_code_data'];

        foreach ($supplier_code_data as $supplierCode => $supplierInfo) {
            if ((int)($supplierInfo['scmNo'] ?? 0) === (int)($scmNo ?? 0)) {
                return (int)($supplierInfo['idx'] ?? 0);
            }
        }

        return null;
    }

    /**
     * 선택상품 일괄수정 (브랜드/상품구분/고도몰 등록상태)
     *
     * @param array $data
     * @return array
     */
    public function bulkUpdateSelectedProducts($data)
    {
        $pks = $data['pks'] ?? [];
        if (!is_array($pks)) {
            $pks = [$pks];
        }
        $pks = array_values(array_filter(array_map('intval', $pks), function ($v) {
            return $v > 0;
        }));
        if (empty($pks)) {
            throw new \Exception('변경할 상품을 선택해주세요.');
        }

        $brandIdx = (int)($data['brand_idx'] ?? 0);
        $kind = $this->normalizeKindName((string)($data['kind'] ?? ''));
        $status = trim((string)($data['status'] ?? ''));

        $updateData = [
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $updatedFields = [];
        if ($brandIdx > 0) {
            $updateData['brand_idx'] = $brandIdx;
            $updatedFields[] = '브랜드';
        }
        if ($kind !== '') {
            $updateData['kind'] = $kind;
            $updatedFields[] = '상품 구분';
        }
        if ($status !== '') {
            $updateData['status'] = $status;
            $updatedFields[] = '고도몰 등록상태';
        }

        if (count($updatedFields) === 0) {
            throw new \Exception('일괄수정할 항목을 1개 이상 선택해주세요.');
        }

        $beforeRows = ProductPartnerModel::query()
            ->whereIn('idx', $pks)
            ->get()
            ->toArray();

        $beforeMap = [];
        foreach ($beforeRows as $row) {
            $rowIdx = (int)($row['idx'] ?? 0);
            if ($rowIdx > 0) {
                $beforeMap[$rowIdx] = $row;
            }
        }

        $updatedCount = ProductPartnerModel::query()
            ->whereIn('idx', $pks)
            ->update($updateData);

        if ((int)$updatedCount < 1) {
            throw new \Exception('수정된 상품이 없습니다.');
        }

        $adminActionLogService = new AdminActionLogService();
        foreach ($pks as $pk) {
            $pk = (int)$pk;
            if ($pk < 1 || !isset($beforeMap[$pk])) {
                continue;
            }

            $beforeData = $beforeMap[$pk];
            $afterData = array_merge($beforeData, $updateData);
            $diff = $adminActionLogService->buildDiff($beforeData, $afterData);

            $adminActionLogService->log([
                'target_type' => 'prd_partner',
                'target_table' => 'prd_partner',
                'target_pk' => (string)$pk,
                'action_mode' => 'update',
                'action_summary' => '선택상품 일괄수정 (' . implode(', ', $updatedFields) . ')',
                'before_json' => $beforeData,
                'after_json' => $afterData,
                'diff_json' => $diff,
            ]);
        }

        return [
            'updated_count' => (int)$updatedCount,
            'updated_fields' => $updatedFields,
            'message' => '일괄수정 완료 (' . implode(', ', $updatedFields) . ' / ' . (int)$updatedCount . '건)',
        ];
    }

    /**
     * 하위 호환: 브랜드 일괄변경
     *
     * @param array $data
     * @return array
     */
    public function bulkUpdateBrand($data)
    {
        return $this->bulkUpdateSelectedProducts($data);
    }

    /**
     * 위탁상품 분류(1차/2차/3차) 단건 수정
     *
     * @param array $postData
     * @return array
     */
    public function updateProductPartnerCategory(array $postData): array
    {
        try {
            $prdIdx = (int)($postData['prd_idx'] ?? 0);
            if ($prdIdx <= 0) {
                throw new \Exception('위탁상품 고유번호가 없습니다.');
            }

            $kindCode = trim((string)($postData['kind_code'] ?? ''));
            if ($kindCode === '') {
                throw new \Exception('1차 카테고리를 선택해주세요.');
            }

            $kindSecondCode = trim((string)($postData['kind_second_code'] ?? ''));
            $kindThirdCode = trim((string)($postData['kind_third_code'] ?? ''));
            $postedCategoryCode = trim((string)($postData['category_code'] ?? ''));
            $resolvedCategoryCode = $this->resolveCategoryCodeForSave(
                $kindCode,
                $postedCategoryCode,
                $kindSecondCode,
                $kindThirdCode
            );
            $kindName = $this->normalizeKindName($kindCode);

            ProductPartnerModel::query()
                ->where('idx', $prdIdx)
                ->update([
                    'kind' => $kindName,
                    'category_code' => $resolvedCategoryCode,
                ]);

            return [
                'status' => 'success',
                'message' => '상품 분류가 수정되었습니다.',
                'data' => [
                    'kind' => $kindName,
                    'kind_code' => $kindCode,
                    'category_code' => $resolvedCategoryCode,
                ],
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }


    /**
     * 고도몰 정보로 상품 정보 갱신
     * 
     * @param array $data
     * @return array
     */
    public function updateProductPartnerByGodoGoodsInfo($data) 
    {

        $prd_idx = $data['prd_idx'] ?? null;
        $godo_goodsNo = $data['godo_goodsNo'] ?? null;
        $status = $data['status'] ?? '등록완료';

        $godoApiService = new GodoApiService();
        $godoGoods = $godoApiService->getGodoGoodsInfo($godo_goodsNo);

        if(empty($godoGoods)){
            throw new \Exception('고도몰 상품 정보 조회 실패');
        }

        $productPartner = ProductPartnerModel::find($prd_idx);
        if(empty($productPartner)){
            throw new \Exception('상품 공급사 정보 조회 실패');
        }
        $productPartner = $productPartner->toArray();

        $beforeData = [
            'name' => $productPartner['name'] ?? null,
            'status' => $productPartner['status'] ?? null,
            'sale_price' => $productPartner['sale_price'] ?? null,
            'img_src' => $productPartner['img_src'] ?? null,
            'code' => $productPartner['code'] ?? null,
            'brand_idx' => $productPartner['brand_idx'] ?? null,
            'godo_goodsNo' => $productPartner['godo_goodsNo'] ?? null,
            'godo_is_option' => $productPartner['godo_is_option'] ?? null,
            'godo_option' => $productPartner['godo_option'] ?? null,
            'godo_cate_json' => $productPartner['godo_cate_json'] ?? null,
            'godo_loaded_at' => $productPartner['godo_loaded_at'] ?? null,
        ];

        /*
        {"goodsNo":1000003629,
        "goodsNm":"트럼펫 마우스 텅",
        "goodsCd":"",
        "scmNo":3,
        "brandCd":"018",
        "goodsPrice":"46000.00",
        "purchaseGoodsNm":"","optionFl":"n","optionDisplayFl":"s","optionName":"","cateNm":"기타 브랜드",
        "thumbImageUrl":"https:\/\/godomall-storage.cdn-nhncommerce.com\/2f93719188848c1c1cec14525d2ccc5b\/goods\/1000003629\/image\/list\/thumb\/1000003629_list_093.jpg","options":[{"optionNo":1,"optionCode":"","optionPrice":0,"optionValue1":"","optionValue2":"","optionValue3":"","optionValue4":"","optionValue5":""}]}
        "options": [
    {
      "optionNo": 1,
      "optionCode": "",
      "optionPrice": 0,
      "optionValue1": "",
      "optionValue2": "",
      "optionValue3": "",
      "optionValue4": "",
      "optionValue5": ""
    }
  ],
  "categories": [
    {
      "cateCd": "006002",
      "cateNm": "체벌/스팽킹",
      "depth": 2,
      "parents": [
        {
          "cateCd": "006",
          "cateNm": "BDSM",
          "depth": 1
        }
      ],
      "path": [
        {
          "cateCd": "006",
          "cateNm": "BDSM",
          "depth": 1
        },
        {
          "cateCd": "006002",
          "cateNm": "체벌/스팽킹",
          "depth": 2
        }
      ]
    },
        */
        $name = $godoGoods['goodsNm'] ?? null;
        $code = $godoGoods['goodsCd'] ?? null;
        $scmNo = $godoGoods['scmNo'] ?? null;
        $partner_idx = $this->getProductPartnerIdxByScmNo($scmNo);
        $brandCd = $godoGoods['brandCd'] ?? null;
        $sale_price = $godoGoods['goodsPrice'] ?? null;
        $img_src = $godoGoods['thumbImageUrl'] ?? null;

        // 기존 브랜드가 이미 지정되어 있으면 고도몰 갱신 시에도 유지
        $existingBrandIdx = (int)($productPartner['brand_idx'] ?? 0);
        if ($existingBrandIdx > 0) {
            $brand_idx = $existingBrandIdx;
        } elseif (!empty($brandCd)) {
            $brandService = new BrandService();
            $brand_idx = $brandService->getBrandIdxByGodoCateCode($brandCd);
            if (empty($brand_idx)) {
                $brand_idx = null;
            }
        } else {
            $brand_idx = null;
        }
        

        if( $productPartner['partner_idx'] != $partner_idx ){

            // 공급사가 쑈당몰인지
            $myScmNos = [1,7,16,17,18,19,20,24,25];
            if( in_array($scmNo, $myScmNos) ){

                //공급사가 쑈당몰이고 재고코드가 등록되있다면
                $codeText = trim((string)$code);
                if( !empty($code) || ($codeText !== '' && preg_match('/^\d+$/', $codeText) === 1) ){

                    $productStockService = new ProductStockService();
                    $productStock = $productStockService->getProductStockWhereInCode($code);
                    if(empty($productStock)){
                        throw new \Exception('코드['.$code.'] : 공급사가 본사이지만 재고코드에 해당하는 상품이 없습니다.');
                    }

                    //재고수량이 존재할경우
                    $stockQty = (int)($productStock['ps_stock'] ?? 0);
                    if ($stockQty > 0) {
                        throw new \Exception('고도몰 데이터로드 실패 사유 : 재고 코드['.$code.'] : 해당상품은 현재 상품DB에 보유상품으로 등록되어 있고 재고코드도 존재하며 재고수량이 ('.$stockQty.')개 존재합니다. 이상품은 상품 DB가 우선 고도몰 상품을 점유합니다.');
                    }else{
                        throw new \Exception('고도몰 데이터로드 실패 사유 : 재고 코드['.$code.'] : 해당상품은 현재 상품DB에 보유상품으로 등록되어 있고 고도몰에도 재고코드로 등록되어 있으나 재고가 없는 상태입니다. 다시 사입할 예정이 없다면 공급사 정보를 변경해주세요.');
                    }

                }else{
                    throw new \Exception('공급사가 본사로 되어있지만 고도몰에 등록한 상품의 재고코드가 비어있습니다.');
                }

            }else{
                throw new \Exception('고도몰에 등록한 상품의 공급사와 현재 공급사가 다릅니다.');
            }

            //throw new \Exception('고도몰에 등록한 상품의 공급사와 현재 공급사가 다릅니다.');
        }

        $godo_is_option = 'N';
        $godo_option = [];
        $optionData = [];
        $godoCategories = $godoGoods['categories'] ?? [];
        if (!is_array($godoCategories)) {
            $godoCategories = [];
        }
        $godo_cate_json = json_encode($godoCategories, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $godo_loaded_at = date('Y-m-d H:i:s');

        $optionFl = strtolower($godoGoods['optionFl'] ?? 'n');
        
        if( $optionFl === 'y' ){
            $godo_is_option = 'Y';
            $optionName = explode('^|^', $godoGoods['optionName'] ?? '');
            $optionData = [
                'displayFl' => $godoGoods['optionDisplayFl'] ?? 'n',
                'name' =>$optionName,
                'items' => $godoGoods['options'] ?? [],
            ];
        }
        $godo_option = json_encode($optionData, JSON_UNESCAPED_UNICODE);

        $updateData = [
            'name' => $name,
            'status' => $status,
            'sale_price' => $sale_price,
            'img_src' => $img_src,
            'code' => $code,
            'brand_idx' => $brand_idx,
            'godo_goodsNo' => $godo_goodsNo,
            'godo_is_option' => $godo_is_option,
            'godo_option' => $godo_option,
            'godo_cate_json' => $godo_cate_json,
            'godo_loaded_at' => $godo_loaded_at,
        ];

        $result = ProductPartnerModel::where('idx', $prd_idx)->update($updateData);

        $adminActionLogService = new AdminActionLogService();

        $diff = $adminActionLogService->buildDiff($beforeData, $updateData);

        $adminActionLogService = new AdminActionLogService();

        $adminActionLogService->log([
            'target_type' => 'prd_partner',
            'target_table' => 'prd_partner',
            'target_pk' => (string)($prd_idx ?? ''),
            'action_mode' => 'update',
            'action_summary' => '고도몰 상품 정보 갱신',
            'before_json' => $beforeData,
            'after_json' => $updateData,
            'diff_json' => $diff,
        ]);

        return true;

    }

    /**
     * 공급사 상품 -> 상품DB로 등록후 매칭
     * 
     * @param array $data
     * @return array
     */
    public function productRegisterToSupplierProduct($data) 
    {

        $prd_idx = $data['prd_idx'] ?? null;

        if(empty($prd_idx)){
            throw new \Exception('상품 고유번호가 비어있습니다.');
        }

        $productPartner = ProductPartnerModel::find($prd_idx);

        if(empty($productPartner)){
            throw new \Exception('상품 공급사 정보 조회 실패');
        }

        $productPartner = $productPartner->toArray();

        $config_product = config('admin.product');
        $prd_kind_name = $config_product['prd_kind_name'] ?? [];

        // kind 값(한글명/코드)을 CD_KIND_CODE로 정규화
        $kindRaw = trim((string)($productPartner['kind'] ?? ''));
        $cdKindCode = '';
        if ($kindRaw !== '') {
            if (isset($prd_kind_name[$kindRaw])) {
                // 이미 코드(예: GEL)인 경우
                $cdKindCode = $kindRaw;
            } else {
                // 한글명(예: 윤활젤) -> 코드(예: GEL) 역매핑
                foreach ($prd_kind_name as $code => $name) {
                    if ((string)$name === $kindRaw) {
                        $cdKindCode = (string)$code;
                        break;
                    }
                }
            }
        }

        $providerCategoryCode = trim((string)($productPartner['category_code'] ?? ''));
        if ($providerCategoryCode === '') {
            $providerCategoryCode = $this->resolveCategoryCodeForSave($cdKindCode, '');
        }


        $supplier_is_option = $productPartner['supplier_is_option'] ?? 'N';
        $supplier_option_data = $productPartner['supplier_option_data'] ?? [];
        if (is_string($supplier_option_data) && trim($supplier_option_data) !== '') {
            $decodedOptionData = json_decode($supplier_option_data, true);
            $supplier_option_data = is_array($decodedOptionData) ? $decodedOptionData : [];
        }
        if (!is_array($supplier_option_data)) {
            $supplier_option_data = [];
        }

        $baseName = (string)($productPartner['name'] ?? '');
        $baseOriginalName = trim((string)($productPartner['name_ori'] ?? ''));
        if ($baseOriginalName === '') {
            $baseOriginalName = $baseName;
        }
        $optionNames = [];
        foreach ($supplier_option_data as $optionGroup) {
            if (!is_array($optionGroup)) {
                continue;
            }
            $items = $optionGroup['items'] ?? [];
            if (!is_array($items)) {
                continue;
            }
            foreach ($items as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $optionValue = trim((string)($item['value'] ?? ''));
                if ($optionValue !== '') {
                    $optionNames[] = $optionValue;
                }
            }
        }
        $optionNames = array_values(array_unique($optionNames));

        $insertData = [
            'CD_KIND_CODE' => $cdKindCode ?: '',
            'CD_CATEGORY_CODE' => $providerCategoryCode,
            'CD_BRAND_IDX' => $productPartner['brand_idx'] ?? 0,
            'CD_NAME' => $baseName !== '' ? $baseName : null,
            'CD_NAME_OG' => $baseOriginalName,
            'img_mode' => 'out',
            'CD_IMG' => $productPartner['img_src'] ?? null,
            'cd_cost_price' => $productPartner['cost_price'] ?? null,
            'supplier_prd_idx' => $prd_idx,
            'cd_site_show' => 'N',
            'cd_national' => 'kr',
        ];

        $productStockService = new ProductStockService();

        if ($supplier_is_option === 'Y' && count($optionNames) > 0) {
            foreach ($optionNames as $optionName) {
                $optionInsertData = $insertData;
                $optionInsertData['CD_NAME'] = $baseName !== '' ? ($baseName . ' - ' . $optionName) : $optionName;
                $createdPrdIdx = ProductModel::query()->insertGetId($optionInsertData);
                if (!empty($createdPrdIdx)) {
                    $productStockService->createStockCode(['prd_idx' => $createdPrdIdx]);
                }
            }
        } else {
            $createdPrdIdx = ProductModel::query()->insertGetId($insertData);
            if (!empty($createdPrdIdx)) {
                $productStockService->createStockCode(['prd_idx' => $createdPrdIdx]);
            }
        }

        return true;
    }

    /**
     * 위탁상품의 용도별 카테고리 매핑 코드를 조회한다.
     */
    private function getCategoryMappingCodes(int $productIdx, string $categoryType): array
    {
        if ($productIdx <= 0 || $categoryType === '') {
            return [];
        }

        $rows = ProductCategoryMappingModel::query()
            ->select(['category_code'])
            ->where('product_type', '=', 'provider')
            ->where('product_idx', '=', $productIdx)
            ->where('category_type', '=', $categoryType)
            ->orderBy('display_order', 'ASC')
            ->orderBy('idx', 'ASC')
            ->get()
            ->toArray();

        return array_values(array_filter(array_map(static function ($row) {
            return trim((string)($row['category_code'] ?? ''));
        }, $rows)));
    }

    private function normalizeAdditionalCategoryCodes($rawCodes, string $primaryCategoryCode): array
    {
        if (!is_array($rawCodes)) {
            $rawCodes = [$rawCodes];
        }

        $configProduct = config('admin.product');
        $categoryRows = $configProduct['categories'] ?? [];
        $validCodeMap = [];
        $collectCodes = static function (array $rows, int $depth = 1) use (&$collectCodes, &$validCodeMap): void {
            if ($depth > 4) {
                return;
            }
            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $code = trim((string)($row['code'] ?? ''));
                if ($code !== '') {
                    $validCodeMap[$code] = true;
                }
                $children = $row['children'] ?? [];
                if (is_array($children) && !empty($children)) {
                    $collectCodes($children, $depth + 1);
                }
            }
        };
        if (is_array($categoryRows)) {
            $collectCodes($categoryRows);
        }

        $normalizedCodes = [];
        $primaryCategoryCode = trim($primaryCategoryCode);
        foreach ($rawCodes as $rawCode) {
            $code = trim((string)$rawCode);
            if ($code === '' || $code === $primaryCategoryCode || !isset($validCodeMap[$code])) {
                continue;
            }
            $normalizedCodes[$code] = $code;
        }

        return array_values($normalizedCodes);
    }

    /**
     * 현재 1차 카테고리에 정의된 서브 카테고리 항목 코드만 허용한다.
     *
     * @param mixed $rawCodes
     * @param string $kindCode
     * @return array<int,string>
     */
    private function normalizeSubCategoryCodes($rawCodes, string $kindCode): array
    {
        if (is_string($rawCodes)) {
            $decodedCodes = json_decode($rawCodes, true);
            $rawCodes = is_array($decodedCodes) ? $decodedCodes : [$rawCodes];
        }
        if (!is_array($rawCodes)) {
            return [];
        }

        $kindCode = $this->normalizeProviderKindCode($kindCode);
        if ($kindCode === '') {
            return [];
        }

        $configProduct = config('admin.product');
        $groups = $configProduct['sub_categories_by_kind'][$kindCode] ?? [];
        $validCodeMap = [];
        if (is_array($groups)) {
            foreach ($groups as $group) {
                if (!is_array($group)) {
                    continue;
                }
                $children = $group['children'] ?? [];
                if (!is_array($children)) {
                    continue;
                }
                foreach ($children as $child) {
                    if (!is_array($child)) {
                        continue;
                    }
                    $code = trim((string)($child['code'] ?? ''));
                    if ($code !== '') {
                        $validCodeMap[$code] = true;
                    }
                }
            }
        }

        $normalizedCodes = [];
        foreach ($rawCodes as $rawCode) {
            $code = trim((string)$rawCode);
            if ($code === '' || !isset($validCodeMap[$code]) || isset($normalizedCodes[$code])) {
                continue;
            }
            $normalizedCodes[$code] = $code;
        }

        return array_values($normalizedCodes);
    }

    private function normalizePreferenceTagCodes($rawCodes): array
    {
        if (!is_array($rawCodes)) {
            $rawCodes = [$rawCodes];
        }

        $configProduct = config('admin.product');
        $preferenceTags = $configProduct['preference_tags'] ?? [];
        $validCodeMap = [];
        if (is_array($preferenceTags)) {
            foreach ($preferenceTags as $tag) {
                if (!is_array($tag) || empty($tag['is_active'])) {
                    continue;
                }
                $code = trim((string)($tag['code'] ?? ''));
                if ($code !== '') {
                    $validCodeMap[$code] = true;
                }
            }
        }

        $normalizedCodes = [];
        foreach ($rawCodes as $rawCode) {
            $code = trim((string)$rawCode);
            if ($code !== '' && isset($validCodeMap[$code])) {
                $normalizedCodes[$code] = $code;
            }
        }

        return array_values($normalizedCodes);
    }

    private function syncCategoryMappingCodes(int $productIdx, string $categoryType, array $categoryCodes): void
    {
        if ($productIdx <= 0 || $categoryType === '') {
            return;
        }

        $categoryCodes = array_values($categoryCodes);
        $now = date('Y-m-d H:i:s');
        foreach ($categoryCodes as $sortOffset => $categoryCode) {
            ProductCategoryMappingModel::updateOrCreate(
                [
                    'product_type' => 'provider',
                    'product_idx' => $productIdx,
                    'category_type' => $categoryType,
                    'category_code' => $categoryCode,
                ],
                [
                    'display_order' => $sortOffset + 1,
                    'updated_at' => $now,
                ]
            );
        }

        $deleteQuery = ProductCategoryMappingModel::where('product_type', 'provider')
            ->where('product_idx', $productIdx)
            ->where('category_type', $categoryType);
        if (!empty($categoryCodes)) {
            $deleteQuery->whereNotIn('category_code', $categoryCodes);
        }
        $deleteQuery->delete();
    }

    /**
     * 상품 저장 시 카테고리 코드를 결정한다.
     * - 3차, 2차, 1차 카테고리 순으로 선택한 코드를 우선 적용한다.
     */
    private function resolveCategoryCodeForSave(string $kindCode, string $postedCategoryCode, string $secondKindCode = '', string $thirdKindCode = ''): string
    {
        $kindCode = trim($kindCode);
        $postedCategoryCode = trim($postedCategoryCode);
        $secondKindCode = trim($secondKindCode);
        $thirdKindCode = trim($thirdKindCode);

        $categoryCodeMap = $this->buildCategoryCodeMapByKind();
        if ($thirdKindCode !== '' && isset($categoryCodeMap[$thirdKindCode])) {
            return (string)$categoryCodeMap[$thirdKindCode];
        }
        if ($secondKindCode !== '' && isset($categoryCodeMap[$secondKindCode])) {
            return (string)$categoryCodeMap[$secondKindCode];
        }

        if ($postedCategoryCode !== '') {
            return $postedCategoryCode;
        }

        if ($kindCode === '') {
            return '';
        }

        return (string)($categoryCodeMap[$kindCode] ?? '');
    }

    /**
     * config 카테고리 트리에서 kind => code 맵을 생성한다.
     *
     * @return array<string,string>
     */
    private function buildCategoryCodeMapByKind(): array
    {
        $configProduct = config('admin.product');
        $categories = $configProduct['categories'] ?? [];
        if (!is_array($categories)) {
            return [];
        }

        $map = [];
        foreach ($categories as $categoryRow) {
            if (!is_array($categoryRow)) {
                continue;
            }

            $parentKey = trim((string)($categoryRow['key'] ?? ''));
            $parentCode = trim((string)($categoryRow['code'] ?? ''));
            if ($parentKey !== '' && $parentCode !== '') {
                $map[$parentKey] = $parentCode;
            }

            $children = (isset($categoryRow['children']) && is_array($categoryRow['children'])) ? $categoryRow['children'] : [];
            foreach ($children as $childRow) {
                if (!is_array($childRow)) {
                    continue;
                }
                $childKey = trim((string)($childRow['key'] ?? ''));
                $childCode = trim((string)($childRow['code'] ?? ''));
                if ($childKey === '' || $childCode === '') {
                    continue;
                }
                $map[$childKey] = $childCode;

                $grandchildren = (isset($childRow['children']) && is_array($childRow['children'])) ? $childRow['children'] : [];
                foreach ($grandchildren as $grandchildRow) {
                    if (!is_array($grandchildRow)) {
                        continue;
                    }
                    $grandchildKey = trim((string)($grandchildRow['key'] ?? ''));
                    $grandchildCode = trim((string)($grandchildRow['code'] ?? ''));
                    if ($grandchildKey !== '' && $grandchildCode !== '') {
                        $map[$grandchildKey] = $grandchildCode;
                    }
                }
            }
        }

        return $map;
    }

    /**
     * 상품 구분 입력값(영문 key / 한글명)을 영문 key로 정규화한다.
     *
     * @param string $kindValue
     * @return string
     */
    private function normalizeKindCode(string $kindValue): string
    {
        $kindValue = trim($kindValue);
        if ($kindValue === '') {
            return '';
        }

        $configProduct = config('admin.product');
        $prdKindName = $configProduct['prd_kind_name'] ?? [];
        if (!is_array($prdKindName)) {
            return $kindValue;
        }

        if (isset($prdKindName[$kindValue])) {
            return $kindValue;
        }

        foreach ($prdKindName as $kindCode => $kindName) {
            if ((string)$kindName === $kindValue) {
                return (string)$kindCode;
            }
        }

        return $kindValue;
    }

    /**
     * 상품 구분 입력값(영문 key / 한글명)을 한글명으로 정규화한다.
     *
     * @param string $kindValue
     * @return string
     */
    private function normalizeKindName(string $kindValue): string
    {
        $kindValue = trim($kindValue);
        if ($kindValue === '') {
            return '';
        }

        $configProduct = config('admin.product');
        $prdKindName = $configProduct['prd_kind_name'] ?? [];
        if (!is_array($prdKindName)) {
            return $kindValue;
        }

        if (isset($prdKindName[$kindValue])) {
            return (string)$prdKindName[$kindValue];
        }

        foreach ($prdKindName as $kindName) {
            if ((string)$kindName === $kindValue) {
                return (string)$kindName;
            }
        }

        return $kindValue;
    }

    /**
     * 위탁상품 단종 설정
     */
    public function setProductDiscontinued(array $postData): array
    {
        return $this->updateSaleStopFlags($postData, 'discontinued', 1, '위탁상품 단종 설정');
    }

    /**
     * 위탁상품 단종 해제
     */
    public function unsetProductDiscontinued(array $postData): array
    {
        return $this->updateSaleStopFlags($postData, 'discontinued', 0, '위탁상품 단종 해제');
    }

    /**
     * 위탁상품 취급중단 설정
     */
    public function setProductHandlingStopped(array $postData): array
    {
        return $this->updateSaleStopFlags($postData, 'handling_stopped', 1, '위탁상품 취급중단 설정');
    }

    /**
     * 위탁상품 취급중단 해제
     */
    public function unsetProductHandlingStopped(array $postData): array
    {
        return $this->updateSaleStopFlags($postData, 'handling_stopped', 0, '위탁상품 취급중단 해제');
    }

    /**
     * 단종/취급중단 플래그 갱신. 둘은 동시에 켜지지 않는다.
     */
    private function updateSaleStopFlags(array $postData, string $flag, int $value, string $defaultSummary): array
    {
        $idx = (int)($postData['prd_idx'] ?? 0);
        if ($idx <= 0) {
            throw new Exception('위탁상품 고유번호가 없습니다.');
        }

        $oldProduct = ProductPartnerModel::query()
            ->select('idx', 'is_discontinued', 'is_handling_stopped')
            ->where('idx', '=', $idx)
            ->first();
        if (empty($oldProduct)) {
            throw new Exception('위탁상품 정보를 찾을 수 없습니다.');
        }
        $oldProduct = is_array($oldProduct) ? $oldProduct : $oldProduct->toArray();

        $currentDiscontinued = (int)($oldProduct['is_discontinued'] ?? 0);
        $currentHandlingStopped = (int)($oldProduct['is_handling_stopped'] ?? 0);

        if ($flag === 'discontinued') {
            if ($value === 1 && $currentDiscontinued === 1) {
                throw new Exception('이미 단종 처리된 상품입니다.');
            }
            if ($value === 0 && $currentDiscontinued === 0) {
                throw new Exception('이미 단종 해제된 상품입니다.');
            }
            $updateData = [
                'is_discontinued' => $value,
            ];
            if ($value === 1) {
                $updateData['is_handling_stopped'] = 0;
            }
        } else {
            if ($value === 1 && $currentHandlingStopped === 1) {
                throw new Exception('이미 취급중단 처리된 상품입니다.');
            }
            if ($value === 0 && $currentHandlingStopped === 0) {
                throw new Exception('이미 취급중단 해제된 상품입니다.');
            }
            $updateData = [
                'is_handling_stopped' => $value,
            ];
            if ($value === 1) {
                $updateData['is_discontinued'] = 0;
            }
        }

        ProductPartnerModel::query()
            ->where('idx', '=', $idx)
            ->update($updateData);

        $afterData = array_merge($oldProduct, $updateData);
        $adminActionLogService = new AdminActionLogService();
        $diff = $adminActionLogService->buildDiff($oldProduct, $afterData);
        $readableDiff = [];
        foreach ($diff as $key => $value) {
            if ($key === 'is_discontinued') {
                $readableDiff['단종'] = [
                    'before' => ((int)($value['before'] ?? 0) === 1) ? 'Y' : 'N',
                    'after' => ((int)($value['after'] ?? 0) === 1) ? 'Y' : 'N',
                ];
                continue;
            }
            if ($key === 'is_handling_stopped') {
                $readableDiff['취급중단'] = [
                    'before' => ((int)($value['before'] ?? 0) === 1) ? 'Y' : 'N',
                    'after' => ((int)($value['after'] ?? 0) === 1) ? 'Y' : 'N',
                ];
                continue;
            }
            $readableDiff[$key] = $value;
        }
        $actionSummary = (string)($postData['action_summary'] ?? '');
        if ($actionSummary === '') {
            $actionSummary = $defaultSummary;
        }
        $actionUrl = (string)($postData['action_url'] ?? ($_SERVER['REQUEST_URI'] ?? ''));
        $actionMode = ($flag === 'discontinued')
            ? ($value === 1 ? 'discontinued' : 'undiscontinued')
            : ($value === 1 ? 'handling_stopped' : 'unhandling_stopped');
        try {
            $adminActionLogService->log([
                'target_type' => 'prd_partner',
                'target_table' => 'prd_partner',
                'target_pk' => (string)$idx,
                'action_mode' => $actionMode,
                'action_summary' => $actionSummary,
                'before_json' => $oldProduct,
                'after_json' => $afterData,
                'diff_json' => $readableDiff,
                'action_url' => $actionUrl !== '' ? $actionUrl : null,
            ]);
        } catch (\Throwable $e) {
            // 로그 저장 실패는 상태 변경 성공/실패에 영향을 주지 않도록 분리한다.
        }

        return [
            'success' => true,
            'status' => 'success',
            'message' => '완료',
            'idx' => $idx,
        ];
    }

    /**
     * 위탁상품 1건 고도몰 검수 화면 데이터
     *
     * @param int $prdIdx
     * @return array
     * @throws Exception
     */
    public function getSingleProductGodoInspectionData(int $prdIdx): array
    {
        if ($prdIdx <= 0) {
            throw new Exception('위탁상품 번호가 없습니다.');
        }

        $product = $this->getProductPartnerInfo($prdIdx);
        if (empty($product) || !is_array($product)) {
            throw new Exception('위탁상품 정보를 찾을 수 없습니다.');
        }

        $godoCode = trim((string)($product['godo_goodsNo'] ?? ''));
        $godoApiErrorMessage = '';
        $godoGoods = [];
        $godoApiStartAt = microtime(true);

        if ($godoCode !== '' && $godoCode !== '0') {
            try {
                $godoApiService = new GodoApiService();
                $godoGoodsResponse = $godoApiService->getGodoGoodsInfoByGoodsNo($godoCode, 'Y');
                $godoGoodsRows = is_array($godoGoodsResponse['data'] ?? null)
                    ? $godoGoodsResponse['data']
                    : $godoGoodsResponse;
                if (!is_array($godoGoodsRows)) {
                    $godoGoodsRows = [];
                }
                foreach ($godoGoodsRows as $godoRow) {
                    if (!is_array($godoRow)) {
                        continue;
                    }
                    $matchedValue = trim((string)($godoRow['goodsNo'] ?? ''));
                    if ($matchedValue === $godoCode) {
                        $godoGoods = $godoRow;
                        break;
                    }
                }
            } catch (\Throwable $e) {
                $godoApiErrorMessage = $e->getMessage();
            }
        }

        $godoGoodsNo = trim((string)($godoGoods['goodsNo'] ?? ''));
        $cdKindCode = $this->normalizeProviderKindCode((string)($product['kind'] ?? ''));
        $cdCategoryCode = trim((string)($product['category_code'] ?? ''));
        $specData = (isset($product['spec_data']) && is_array($product['spec_data'])) ? $product['spec_data'] : [];
        $specMeasures = $this->extractProviderInspectionMeasures($specData, $cdKindCode, $cdCategoryCode);
        $orderPrice = (string)($product['order_price'] ?? '');
        $marginInfo = $this->calculateProviderMarginInfo(
            (float)($product['sale_price'] ?? 0),
            (float)$orderPrice
        );
        $godoCategoryLines = $this->buildGodoCategoryLines(
            (isset($godoGoods['categories']) && is_array($godoGoods['categories'])) ? $godoGoods['categories'] : []
        );

        $godoStockQty = 0;
        if (isset($godoGoods['totalStock']) && is_numeric($godoGoods['totalStock'])) {
            $godoStockQty = (int)$godoGoods['totalStock'];
        } elseif (isset($godoGoods['stockCnt']) && is_numeric($godoGoods['stockCnt'])) {
            $godoStockQty = (int)$godoGoods['stockCnt'];
        } elseif (isset($godoGoods['stock']) && is_numeric($godoGoods['stock'])) {
            $godoStockQty = (int)$godoGoods['stock'];
        } elseif (isset($godoGoods['goodsStock']) && is_numeric($godoGoods['goodsStock'])) {
            $godoStockQty = (int)$godoGoods['goodsStock'];
        }

        $item = [
            'pidx' => $prdIdx,
            'ps_idx' => 0,
            'qty' => 0,
            'is_false' => false,
            'cd_kind_code' => $cdKindCode,
            'cd_category_code' => $cdCategoryCode,
            'cd_sub_category_codes' => (isset($product['cd_sub_category_codes']) && is_array($product['cd_sub_category_codes']))
                ? $product['cd_sub_category_codes']
                : [],
            'preference_tag_codes' => (isset($product['preference_tag_codes']) && is_array($product['preference_tag_codes']))
                ? $product['preference_tag_codes']
                : [],
            'brand_name' => (string)($product['brand_name'] ?? ''),
            'name' => (string)($product['name'] ?? ''),
            'name_og' => (string)($product['name_ori'] ?? ''),
            'barcode' => (string)($product['code'] ?? ''),
            'cd_hbti' => strtoupper(trim((string)($product['hbti_type'] ?? ''))),
            'goods_price' => (string)($product['sale_price'] ?? ''),
            'cost_price' => $orderPrice,
            'goods_weight' => $specMeasures['weight_grams'],
            'inner_length' => $specMeasures['inner_length'],
            'margin_per' => (float)($marginInfo['margin_per'] ?? 0),
            'margin_grade' => (string)($marginInfo['margin_grade'] ?? ''),
            'stock_qty' => 0,
            'img_path' => (string)($product['img_src'] ?? ''),
            'cd_godo_code' => $godoCode,
            'godo_goods_no' => $godoGoodsNo,
            'godo_stock_qty' => $godoStockQty,
            'godo_goods_name' => trim((string)($godoGoods['goodsNm'] ?? '')),
            'godo_purchase_goods_name' => trim((string)($godoGoods['purchaseGoodsNm'] ?? '')),
            'godo_only_adult_fl' => strtolower(trim((string)($godoGoods['onlyAdultFl'] ?? ''))),
            'godo_goods_model_no' => trim((string)($godoGoods['goodsModelNo'] ?? '')),
            'godo_goods_price' => trim((string)($godoGoods['goodsPrice'] ?? '')),
            'godo_cost_price' => trim((string)($godoGoods['costPrice'] ?? '')),
            'godo_category_lines' => $godoCategoryLines,
        ];

        return [
            'item' => $item,
            'godoApiErrorMessage' => $godoApiErrorMessage,
            'godoInfoLoadedAt' => date('Y-m-d H:i:s'),
            'godoInfoLoadMs' => (int)round((microtime(true) - $godoApiStartAt) * 1000),
        ];
    }

    /**
     * 위탁상품 고도몰 검수 체크 항목 일괄 처리
     *
     * @param array $requestData
     * @return array
     * @throws Exception
     */
    public function processSingleProductGodoInspection(array $requestData): array
    {
        $prdIdx = (int)($requestData['prd_idx'] ?? 0);
        $relationPk = (int)($requestData['relation_pk'] ?? $prdIdx);
        $locationCode = trim((string)($requestData['location_code'] ?? ''));
        if ($locationCode === '') {
            $locationCode = InspectionProcessLogService::LOCATION_PROVIDER_PRODUCT_GODO_INSPECTION;
        }
        $selectedIssues = $requestData['selected_issues'] ?? [];
        if (!is_array($selectedIssues)) {
            $selectedIssues = [];
        }
        $selectedIssues = array_values(array_unique(array_filter(array_map(static function ($v) {
            return trim((string)$v);
        }, $selectedIssues), static function ($v) {
            return $v !== '';
        })));

        if ($prdIdx <= 0) {
            throw new Exception('위탁상품 번호가 없습니다.');
        }
        if (empty($selectedIssues)) {
            throw new Exception('선택된 자동처리 항목이 없습니다.');
        }

        $inspectionData = $this->getSingleProductGodoInspectionData($prdIdx);
        $item = (array)($inspectionData['item'] ?? []);
        $godoInspectionService = new GodoInspectionService();
        $inspectionVersion = $godoInspectionService->getInspectionVersion();
        $inspectionContext = $godoInspectionService->buildInspectionContext(
            $item,
            GodoInspectionService::CONTEXT_PROVIDER_PRODUCT
        );
        $issues = (isset($inspectionContext['inspection_issues']) && is_array($inspectionContext['inspection_issues']))
            ? $inspectionContext['inspection_issues']
            : [];
        $issueNameSet = [];
        foreach ($issues as $issueRow) {
            $name = trim((string)($issueRow['issue'] ?? ''));
            if ($name !== '') {
                $issueNameSet[$name] = true;
            }
        }

        $processableIssues = [];
        $intranetIssueNames = [];
        $godoIssueNames = [];
        $intranetBarcode = (string)($inspectionContext['intranet_barcode'] ?? ($item['barcode'] ?? ''));
        foreach ($selectedIssues as $issueName) {
            if (!isset($issueNameSet[$issueName])) {
                continue;
            }
            $actionMeta = $godoInspectionService->resolveIssueActionMeta($issueName, $intranetBarcode);
            $target = trim((string)($actionMeta['target'] ?? ''));
            $state = trim((string)($actionMeta['state'] ?? ''));
            if ($state !== '자동처리 가능') {
                continue;
            }
            $processableIssues[] = $issueName;
            if ($target === '인트라넷') {
                $intranetIssueNames[$issueName] = true;
            } elseif ($target === '고도몰') {
                $godoIssueNames[$issueName] = true;
            }
        }

        if (empty($processableIssues)) {
            throw new Exception('선택된 항목 중 자동처리 가능한 항목이 없습니다.');
        }

        $beforeValues = [
            'godo_goodsNo' => trim((string)($item['cd_godo_code'] ?? '')),
            'sale_price' => trim((string)($item['goods_price'] ?? '')),
            'godo_only_adult_fl' => strtolower(trim((string)($item['godo_only_adult_fl'] ?? ''))),
            'godo_goods_model_no' => trim((string)($item['godo_goods_model_no'] ?? '')),
            'godo_cost_price' => trim((string)($item['godo_cost_price'] ?? '')),
            'godo_goods_price' => trim((string)($item['godo_goods_price'] ?? '')),
        ];

        $normalizeNumeric = static function (string $value): string {
            return str_replace(',', '', trim($value));
        };

        $intranetUpdated = [];
        if (isset($intranetIssueNames['상품번호 불일치'])) {
            $godoGoodsNo = trim((string)($item['godo_goods_no'] ?? ''));
            if ($godoGoodsNo !== '' && $godoGoodsNo !== '0') {
                ProductPartnerModel::query()
                    ->where('idx', '=', $prdIdx)
                    ->update([
                        'godo_goodsNo' => $godoGoodsNo,
                    ]);
                $intranetUpdated[] = 'godo_goodsNo';
                $item['cd_godo_code'] = $godoGoodsNo;
            }
        }
        if (isset($intranetIssueNames['판매가 불일치'])) {
            $godoGoodsPrice = $normalizeNumeric((string)($item['godo_goods_price'] ?? ''));
            if ($godoGoodsPrice !== '' && is_numeric($godoGoodsPrice)) {
                ProductPartnerModel::query()
                    ->where('idx', '=', $prdIdx)
                    ->update([
                        'sale_price' => $godoGoodsPrice,
                    ]);
                $intranetUpdated[] = 'sale_price';
                $item['goods_price'] = $godoGoodsPrice;
            }
        }

        $godoCalled = false;
        $godoResponse = [];
        $columnUpdates = [];
        $addCategoryCds = '';
        $deleteCategoryCds = '';
        if (!empty($godoIssueNames)) {
            if (isset($godoIssueNames['성인인증'])) {
                $columnUpdates['godo_only_adult_fl'] = 'y';
            }
            if ((isset($godoIssueNames['바코드 미입력']) || isset($godoIssueNames['바코드 불일치'])) && trim((string)($item['barcode'] ?? '')) !== '') {
                $columnUpdates['godo_goods_model_no'] = trim((string)($item['barcode'] ?? ''));
            }
            if (isset($godoIssueNames['원가 미입력']) || isset($godoIssueNames['원가 불일치'])) {
                $intranetCost = $normalizeNumeric((string)($item['cost_price'] ?? ''));
                if ($intranetCost !== '') {
                    $columnUpdates['godo_cost_price'] = $intranetCost;
                }
            }

            $hasCategoryIssue = false;
            foreach (array_keys($godoIssueNames) as $issueName) {
                if (strpos($issueName, '카테고리') !== false) {
                    $hasCategoryIssue = true;
                    break;
                }
            }
            $columnUpdatePairs = [];
            foreach ($columnUpdates as $columnName => $columnValue) {
                $columnUpdatePairs[] = $columnName . '=' . (string)$columnValue;
            }
            $columnUpdateString = implode(',', $columnUpdatePairs);
            $addCategoryCds = $hasCategoryIssue ? (string)($inspectionContext['category_add_codes_for_sync'] ?? '') : '';
            $deleteCategoryCds = $hasCategoryIssue ? (string)($inspectionContext['category_delete_codes_for_sync'] ?? '') : '';

            $goodsNo = trim((string)($item['godo_goods_no'] ?? ''));
            if ($goodsNo !== '' && $goodsNo !== '0') {
                $godoApiService = new GodoApiService();
                $godoResponse = $godoApiService->autoStockUpdateAndInspection([
                    'goodsNo' => $goodsNo,
                    'columnUpdates' => $columnUpdateString,
                    'addCategoryCds' => $addCategoryCds,
                    'deleteCategoryCds' => $deleteCategoryCds,
                ]);
                $godoCalled = true;
            }
        }

        $afterValues = $beforeValues;
        if (!empty($intranetUpdated) && in_array('godo_goodsNo', $intranetUpdated, true)) {
            $afterValues['godo_goodsNo'] = trim((string)($item['cd_godo_code'] ?? ''));
        }
        if (!empty($intranetUpdated) && in_array('sale_price', $intranetUpdated, true)) {
            $afterValues['sale_price'] = trim((string)($item['goods_price'] ?? ''));
        }
        foreach ($columnUpdates as $columnName => $columnValue) {
            $afterValues[$columnName] = (string)$columnValue;
        }
        $categoryAddDisplay = $this->buildCategoryCodeDisplayText($addCategoryCds, $inspectionContext, $godoInspectionService);
        $categoryDeleteDisplay = $this->buildCategoryCodeDisplayText($deleteCategoryCds, $inspectionContext, $godoInspectionService);
        $beforeValues['category_add_codes_for_sync'] = '';
        $afterValues['category_add_codes_for_sync'] = $categoryAddDisplay;
        $beforeValues['category_delete_codes_for_sync'] = $categoryDeleteDisplay;
        $afterValues['category_delete_codes_for_sync'] = '';

        $resultPayload = [
            'success' => true,
            'status' => 'success',
            'message' => '처리완료',
            'inspection_version' => $inspectionVersion,
            'processed_issues' => $processableIssues,
            'intranet_updated_columns' => $intranetUpdated,
            'godo_called' => $godoCalled,
            'godo_response' => $godoResponse,
        ];

        try {
            $inspectionProcessLogService = new InspectionProcessLogService();
            $inspectionProcessLogService->logProductSingleGodoInspection([
                'location_code' => $locationCode,
                'relation_pk' => $relationPk,
                'prd_idx' => $prdIdx,
                'ps_idx' => 0,
                'godo_goods_no' => (string)($item['godo_goods_no'] ?? ''),
                'inspection_version' => $inspectionVersion,
                'process_content' => [
                    'inspection_version' => $inspectionVersion,
                    'selected_issues' => $selectedIssues,
                    'processable_issues' => $processableIssues,
                    'intranet_issue_names' => array_keys($intranetIssueNames),
                    'godo_issue_names' => array_keys($godoIssueNames),
                    'column_updates' => $columnUpdates,
                    'category_add_codes' => $addCategoryCds,
                    'category_delete_codes' => $deleteCategoryCds,
                ],
                'before_values' => $beforeValues,
                'after_values' => $afterValues,
                'result_content' => $resultPayload,
            ]);
        } catch (\Throwable $e) {
            // 로그 저장 실패는 메인 처리에 영향이 없도록 분리한다.
        }

        return $resultPayload;
    }

    /**
     * 위탁상품 마진은 판매가 - 주문가 기준이며, 상품DB의 배송비(2500) 보정을 쓰지 않는다.
     *
     * @return array{margin_per:float,margin_grade:string,margin_grade_color:string}
     */
    public function calculateProviderMarginInfo($salePrice, $orderPrice): array
    {
        $sale = (float)$salePrice;
        $order = (float)$orderPrice;

        $marginPer = 0.0;
        if ($sale > 0 && $order > 0) {
            $marginPer = round((($sale - $order) / $sale) * 100, 2);
        }

        $grade = '';
        $gradeColor = '';
        if ($marginPer > 39) {
            $grade = 'A';
            $gradeColor = '#28a745';
        } elseif ($marginPer >= 35) {
            $grade = 'B';
            $gradeColor = '#20c997';
        } elseif ($marginPer >= 30) {
            $grade = 'C';
            $gradeColor = '#17a2b8';
        } elseif ($marginPer >= 25) {
            $grade = 'D';
            $gradeColor = '#0dcaf0';
        } elseif ($marginPer >= 20) {
            $grade = 'E';
            $gradeColor = '#ffc107';
        } elseif ($marginPer >= 15) {
            $grade = 'F';
            $gradeColor = '#fd7e14';
        } elseif ($marginPer >= 10) {
            $grade = 'G';
            $gradeColor = '#dc3545';
        } elseif ($marginPer >= 5) {
            $grade = 'H';
            $gradeColor = '#d63384';
        } elseif ($marginPer > 0) {
            $grade = 'I';
            $gradeColor = '#6c757d';
        }

        return [
            'margin_per' => $marginPer,
            'margin_grade' => $grade,
            'margin_grade_color' => $gradeColor,
        ];
    }

    /**
     * 위탁상품 kind 값을 CD_KIND_CODE로 정규화한다.
     */
    private function normalizeProviderKindCode(string $kindRaw): string
    {
        $kindRaw = trim($kindRaw);
        if ($kindRaw === '') {
            return '';
        }

        $configProduct = config('admin.product');
        $prdKindName = (isset($configProduct['prd_kind_name']) && is_array($configProduct['prd_kind_name']))
            ? $configProduct['prd_kind_name']
            : [];
        if (isset($prdKindName[$kindRaw])) {
            return $kindRaw;
        }
        foreach ($prdKindName as $code => $name) {
            if ((string)$name === $kindRaw) {
                return (string)$code;
            }
        }

        return strtoupper($kindRaw);
    }

    /**
     * 위탁 spec_data에서 검수용 중량(g)·내부길이(cm)를 추출한다.
     *
     * @return array{weight_grams:string,inner_length:string}
     */
    private function extractProviderInspectionMeasures(array $specData, string $kindCode, string $categoryCode): array
    {
        $weightRaw = $this->extractProviderSpecNumber($specData, ['weight']);
        $innerLength = $this->extractProviderSpecNumber($specData, [
            'inner_length',
            'inner_length_vagina',
            'inner_length_1',
        ]);

        $weightGrams = '';
        if ($weightRaw !== '') {
            $weightValue = (float)$weightRaw;
            $specService = new ProductSpecService();
            $schema = $specService->getSchema($categoryCode);
            $unit = strtolower(trim((string)($schema['fields']['weight'][1] ?? '')));
            if ($unit === 'kg') {
                $weightGrams = (string)round($weightValue * 1000, 2);
            } elseif ($kindCode === 'ONAHOLE' && $unit === '' && $weightValue > 0 && $weightValue < 30) {
                // 오나홀 스펙 스키마가 없는 과거 데이터는 kg로 들어온 경우를 그램으로 환산한다.
                $weightGrams = (string)round($weightValue * 1000, 2);
            } else {
                $weightGrams = (string)$weightValue;
            }
        }

        return [
            'weight_grams' => $weightGrams,
            'inner_length' => $innerLength,
        ];
    }

    /**
     * spec_data에서 숫자값을 꺼낸다. 실측값을 업체제공값보다 우선한다.
     *
     * @param array<int,string> $keys
     */
    private function extractProviderSpecNumber(array $specData, array $keys): string
    {
        $sources = [
            (isset($specData['measured_size']) && is_array($specData['measured_size'])) ? $specData['measured_size'] : [],
            (isset($specData['vendor_size']) && is_array($specData['vendor_size'])) ? $specData['vendor_size'] : [],
        ];
        foreach ($sources as $source) {
            foreach ($keys as $key) {
                $raw = str_replace(',', '', trim((string)($source[$key] ?? '')));
                if ($raw !== '' && is_numeric($raw) && (float)$raw > 0) {
                    return $raw;
                }
            }
        }
        return '';
    }

    /**
     * 카테고리 코드 CSV를 "카테고리명(코드)" 표기 문자열로 변환한다.
     */
    private function buildCategoryCodeDisplayText(string $codesCsv, array $inspectionContext, GodoInspectionService $godoInspectionService): string
    {
        $codesCsv = trim($codesCsv);
        if ($codesCsv === '') {
            return '';
        }

        $lineByCode = [];
        $godoCategoryLines = (isset($inspectionContext['godo_category_lines']) && is_array($inspectionContext['godo_category_lines']))
            ? $inspectionContext['godo_category_lines']
            : [];
        foreach ($godoCategoryLines as $categoryRow) {
            if (!is_array($categoryRow)) {
                continue;
            }
            $cateCd = trim((string)($categoryRow['cateCd'] ?? ''));
            $line = trim((string)($categoryRow['line'] ?? ''));
            if ($cateCd === '') {
                continue;
            }
            if ($line !== '') {
                $lineByCode[$cateCd] = $line;
            }
        }

        $codes = array_values(array_unique(array_filter(array_map(static function ($v) {
            return trim((string)$v);
        }, explode(',', $codesCsv)), static function ($v) {
            return $v !== '';
        })));

        $displayRows = [];
        foreach ($codes as $cateCd) {
            $cateName = $lineByCode[$cateCd] ?? $godoInspectionService->getCategoryNameByCode($cateCd);
            if ($cateName === '') {
                $displayRows[] = $cateCd;
                continue;
            }
            $displayRows[] = $cateName . '(' . $cateCd . ')';
        }

        return implode(', ', $displayRows);
    }

    /**
     * 고도몰 categories 응답을 화면 표기용 목록으로 변환한다.
     *
     * @param array $categories
     * @return array<int,array{line:string,cateCd:string}>
     */
    private function buildGodoCategoryLines(array $categories): array
    {
        $lineRows = [];
        $seen = [];

        foreach ($categories as $categoryRow) {
            if (!is_array($categoryRow)) {
                continue;
            }
            $pathRows = (isset($categoryRow['path']) && is_array($categoryRow['path'])) ? $categoryRow['path'] : [];
            $pathNames = [];
            foreach ($pathRows as $pathRow) {
                if (!is_array($pathRow)) {
                    continue;
                }
                $cateNm = trim((string)($pathRow['cateNm'] ?? ''));
                if ($cateNm !== '') {
                    $pathNames[] = $cateNm;
                }
            }
            if (empty($pathNames)) {
                $cateNm = trim((string)($categoryRow['cateNm'] ?? ''));
                if ($cateNm !== '') {
                    $pathNames[] = $cateNm;
                }
            }
            if (empty($pathNames)) {
                continue;
            }
            $line = implode(' > ', $pathNames);
            if (isset($seen[$line])) {
                continue;
            }
            $seen[$line] = true;
            $lineRows[] = [
                'line' => $line,
                'cateCd' => trim((string)($categoryRow['cateCd'] ?? '')),
            ];
        }

        usort($lineRows, static function (array $a, array $b): int {
            return strcmp((string)($a['cateCd'] ?? ''), (string)($b['cateCd'] ?? ''));
        });

        return $lineRows;
    }

}
