<?php
namespace App\Services;

use App\Models\ProductCommentModel;
use App\Models\ProductModel;

class ProductCommentService
{

    /**
     * 상품 댓글 목록 조회
     * 
     * @param array $getData 파라미터
     * @param array|null $extraData 추가 파라미터
     * @return array
     */
    public function getProductCommentList($criteria)
    {

        $pd_idx = $criteria['pd_idx'] ?? null;
        $paging = $criteria['paging'] ?? true;
        $perPage = $criteria['per_page'] ?? 100;
        $page = $criteria['page'] ?? 1;

        $query = ProductCommentModel::query()
            ->with('userSimple')
            ->where('pc_kind', 'onadb')
            ->when($pd_idx, function($query) use ($pd_idx) {
                $query->where('pc_pd_idx', $pd_idx);
            })
            ->orderBy('pc_reg_date', 'DESC');

        $result = $query->paginate($perPage, $page);

        foreach ($result['data'] as &$comment) {
            $comment['pc_reg_info'] = json_decode($comment['pc_reg_info'], true);
            $comment['pc_score'] = json_decode($comment['pc_score'], true);
        }
        unset($comment);


        return $result;

    }

    /**
     * 최근 댓글 15개 조회 (상품명 포함)
     * 
     * @return array
     */
    public function getRecentComments()
    {
        // 1. 최근 댓글 15개 조회
        $comments = ProductCommentModel::query()
            ->where('pc_kind', 'onadb')
            ->orderBy('pc_reg_date', 'DESC')
            ->limit(15)
            ->get()
            ->toArray();
        
        if (empty($comments)) {
            return [];
        }
        
        // 2. pc_pd_idx 추출
        $productIds = array_column($comments, 'pc_pd_idx');
        $productIds = array_filter($productIds); // null 제거
        $productIds = array_unique($productIds); // 중복 제거
        
        if (empty($productIds)) {
            return $comments;
        }
        
        // 3. 상품명 조회 (whereIn 사용, 필요한 컬럼만 select)
        $products = ProductModel::query()
            ->select(['CD_IDX', 'CD_NAME'])
            ->whereIn('CD_IDX', $productIds)
            ->get()
            ->toArray();
        
        // 4. CD_IDX를 키로 하는 배열로 변환
        $productMap = [];
        foreach ($products as $product) {
            $productMap[$product['CD_IDX']] = $product['CD_NAME'] ?? '';
        }
        
        // 5. 댓글에 상품명 추가
        foreach ($comments as &$comment) {
            $comment['prd_name'] = $productMap[$comment['pc_pd_idx']] ?? '';
        }
        
        return $comments;
    }

}