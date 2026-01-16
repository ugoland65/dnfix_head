<?php

namespace App\Services;

use App\Core\BaseClass;
use App\Models\ProductPartnerModel;

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
            ->first();

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

            $sale_price = (int)preg_replace('/[,\s]/', '', $postData['sale_price'] ?? 0); // 판매가
            $cost_price = (int)preg_replace('/[,\s]/', '', $postData['cost_price'] ?? 0); // 원가
            $order_price = (int)preg_replace('/[,\s]/', '', $postData['order_price'] ?? 0); // 주문가
            $delivery_fee = (int)preg_replace('/[,\s]/', '', $postData['delivery_fee'] ?? 0); // 배송비
            $is_vat = $postData['is_vat'] ?? 'Y'; // 부가세
            //$vat = preg_replace('/[,\s]/', '', $postData['vat'] ?? 10); // 부가세

            // 원가가 없고 주문가와 배송비가 있으면 원가 계산
            /*
            if(empty($cost_price) && !empty($order_price) && !empty($delivery_fee)){
                $cost_price = $order_price + $delivery_fee;
                $vat = $cost_price * 0.1;
            }
            */

            // cost_price에 vat 값을 더함 (모든 경우)
            //$cost_price = $cost_price + $vat;

            if($is_vat == 'N'){
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

            $inputData = [
                'kind' => $postData['kind'] ?? '', // 상품 구분 (상품 카테고리 코드)
                'brand_idx' => $postData['brand_idx'] ?? '', // 브랜드 인덱스 (BRAND_DB 테이블의 BD_IDX)
                'partner_idx' => $postData['partner_idx'] ?? '', // 공급사 인덱스 (partners 테이블의 idx)
                'name' => $postData['name'] ?? '', // 판매 상품명
                'name_p' => $postData['name_p'] ?? '', // 공급사 상품명
                'sale_price' => $sale_price, // 판매가
                'cost_price' => $cost_price, // 원가 (vat 포함)
                'order_price' => $order_price, // 주문가
                'price_data' => $price_data, // 가격 데이터
                'img_mode' => $postData['img_mode'] ?? '', // 이미지 모드 (out: 외부 이미지, this: 서버에 등록)
                'img_src' => $postData['img_src'] ?? '', // 이미지 URL 또는 경로
                'hbti_type' => $postData['hbti_type'] ?? '', // HBTI 타입 정보
                'godo_goodsNo' => $postData['godo_goodsNo'] ?? '', // 고도몰 상품코드
                'matching_code' => $postData['matching_code'] ?? '', // 공급사 매칭코드
                'memo' => $postData['memo'] ?? '', // 메모
            ];

            if(empty($postData['prd_idx'])){
                // prd_idx가 없으면 새 레코드 삽입
                $result = ProductPartnerModel::insert($inputData);
            }else{
                // prd_idx가 있으면 기존 레코드 업데이트
                $result = ProductPartnerModel::find($postData['prd_idx'])->update($inputData);
            }

            if($result){
                return ['status' => 'success', 'message' => '저장되었습니다.'];
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
        return ProductPartnerModel::whereIn('idx', $idxs)
            ->get()
            ->keyBy('idx')
            ->toArray();
    }


}
