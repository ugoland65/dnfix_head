<?php
namespace App\Services;

use App\Models\ProductCommentModel;

class ProductCommentService
{

    /**
     * 상품 댓글 목록 조회
     * 
     * @param array $getData 파라미터
     * @param array|null $extraData 추가 파라미터
     * @return array
     */
    public function getProductCommentList($getData, $extraData = null)
    {
        $query = ProductCommentModel::query()
            ->where('pc_kind', '=', 'onadb')
            ->orderBy('pc_reg_date', 'DESC')
            ->limit(15);

        return $query->get();
    }

    /**
     * 최근 댓글 15개 조회 (간단 버전)
     * 
     * @return array
     */
    public function getRecentComments()
    {
        return ProductCommentModel::query()
            ->where('pc_kind', 'onadb')
            ->orderBy('pc_reg_date', 'DESC')
            ->limit(15)
            ->get()
            ->toArray();
    }

}