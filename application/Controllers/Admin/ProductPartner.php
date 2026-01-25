<?php

namespace App\Controllers\Admin;

use App\Core\BaseClass;
use App\Models\ProductPartnerModel;
use App\Utils\Pagination;

class ProductPartner extends BaseClass {

    /** 
     * @deprecated 사용하지 폐기할 예정
     * 상품 공급사 목록 조회
     * @return array
     */
    public function getProductPartnerList() {

		$getData = $this->requestHandler->getAll(); // GET 데이터 받기

		$page = isset($getData['page']) ? $getData['page'] : 1;
		$perPage = isset($getData['per_page']) ? $getData['per_page'] : 100;

        $ProductPartnerModel = new ProductPartnerModel();

        $productPartnerList = ProductPartnerModel::query()
            ->table('prd_partner AS A')
            ->select([
                'A.*',
                'B.BD_NAME AS brand_name',
                'C.name AS partner_name',
            ])
            ->join('BRAND_DB AS B', 'B.BD_IDX', '=', 'A.brand_idx', 'LEFT')
            ->join('partners AS C', 'C.idx', '=', 'A.partner_idx', 'LEFT')
            ->orderBy('idx', 'DESC')
            ->paginate($perPage, $page);

        $pagination = new Pagination($productPartnerList['total'], $productPartnerList['per_page'], $productPartnerList['current_page'], 10);

        $baseUrl = '';
        $renderLinks = $pagination->renderLinks($baseUrl);
    
        return [
            'productPartnerList' => $productPartnerList['data'],
            'paga_nation' => $renderLinks,
            'total' => $productPartnerList['total'],
            'per_page' => $productPartnerList['per_page'], // 페이지당  수
            'current_page' => $productPartnerList['current_page'], // 현재 페이지
            'last_page' => $productPartnerList['last_page'], // 전체 페이지 수
        ];

    }

}
