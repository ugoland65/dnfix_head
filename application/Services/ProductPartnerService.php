<?php

namespace App\Services;

use App\Core\BaseClass;
use App\Models\ProductPartnerModel;
use App\Services\AdminActionLogService;
use App\Services\ProductPartnerApiService;

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
        $sort_mode = $payloadData['sort_mode'] ?? 'idx';
        $s_godo_sale_status = $payloadData['s_godo_sale_status'] ?? null; // 고도몰 판매상태
        $with_api_data = $payloadData['with_api_data'] ?? false;

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
            ->leftJoin('partners', 'partners.idx', '=', 'prd_partner.partner_idx');

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
            $code = $postData['code'] ?? ''; // 상품 코드
            $delivery_fee = (int)preg_replace('/[,\s]/', '', $postData['delivery_fee'] ?? 0); // 배송비
            $is_vat = $postData['is_vat'] ?? 'Y'; // 부가세
            //$vat = preg_replace('/[,\s]/', '', $postData['vat'] ?? 10); // 부가세

            $action_url = $postData['action_url'] ?? ''; //로그용 변수
            $action_summary = $postData['action_summary'] ?? ''; //로그용 변수

            $supplier_status = $postData['supplier_status'] ?? '판매중'; // 공급사 판매상태

            $supplier_status_date =  null; // 공급사 판매상태 처리일

            if( $supplier_status != '판매중' ){
                $supplier_status_date = date('Y-m-d H:i:s');
            }


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
            }else{
                $vat = $cost_price / 11;
                $cost_price_save = ($cost_price / 1.1);
            }

            $price_data = [
                'is_vat' => $is_vat, // 부가세
                'cost_price' => $cost_price_save,
                'delivery_fee' => $delivery_fee, // 배송비
                'vat' => $vat, // 부가세
            ];

            $price_data = json_encode($price_data, JSON_UNESCAPED_UNICODE);

            $name = $postData['name'] ?? '';
            $name_ori = $postData['name_ori'] ?? '';
            $name_p = $postData['name_p'] ?? '';
            $kind = $postData['kind'] ?? '';
            $supplier_prd_idx = $postData['supplier_prd_idx'] ?? 0;
            $supplier_prd_pk = $postData['supplier_prd_pk'] ?? null;
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

            $inputData = [
                'name' => $name, // 판매 상품명
                'name_ori' => $name_ori, // 원(영문,일어,중국어) 상품명
                'name_p' => $name_p, // 공급사 상품명
                'status' => $status, // 상품 상태
                'kind' => $kind, // 상품 구분 (상품 카테고리 코드)
                'brand_idx' => $brand_idx, // 브랜드 인덱스 (BRAND_DB 테이블의 BD_IDX)
                'partner_idx' => $partner_idx, // 공급사 인덱스 (partners 테이블의 idx)
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
                'price_data' => $price_data, // 가격 데이터
                'code' => $code, // 상품 코드
                'img_mode' => $img_mode, // 이미지 모드 (out: 외부 이미지, this: 서버에 등록)
                'img_src' => $img_src, // 이미지 URL 또는 경로
                'hbti_type' => $hbti_type, // HBTI 타입 정보
                'godo_goodsNo' => $godo_goodsNo, // 고도몰 상품코드
                'matching_code' => $matching_code, // 공급사 매칭코드
                'memo' => $memo, // 메모
                'memo_work' => $memo_work, // 작업지시 메모
            ];

            $updateData = [];
            $fieldMap = [
                'name' => $name,
                'name_ori' => $name_ori,
                'name_p' => $name_p,
                'status' => $status,
                'kind' => $kind,
                'brand_idx' => $brand_idx,
                'partner_idx' => $partner_idx,
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
                'price_data' => $price_data,
                'code' => $code,
                'img_mode' => $img_mode,
                'img_src' => $img_src,
                'hbti_type' => $hbti_type,
                'godo_goodsNo' => $godo_goodsNo,
                'matching_code' => $matching_code,
                'memo' => $memo,
                'memo_work' => $memo_work,
            ];
            foreach ($fieldMap as $key => $value) {
                if (array_key_exists($key, $postData)) {
                    $updateData[$key] = $value;
                }
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
                // prd_idx가 있으면 기존 레코드 업데이트
                $result = ProductPartnerModel::find($postData['prd_idx'])->update($updateData);
                
            }

            if( $result ){

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

}
