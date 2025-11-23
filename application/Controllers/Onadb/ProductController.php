<?php

namespace App\Controllers\Onadb;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Services\ProductService;
use App\Services\ProductScoreService;
use App\Services\ProductCommentService;
use App\Utils\Pagination;

class ProductController extends BaseClass
{

    /**
     * 상품상세페이지
     * 
     * @param Request $request
     * @param string $idx 상품IDX
     * @return array
     */
    public function productDetail( Request $request, $idx )
    {

        $requestData = $request->all();

        $page = $requestData['page'] ?? 1;
        $per_page = $requestData['per_page'] ?? 50;

        try {

            if (empty($idx)) {
                throw new Exception('잘못된 요청입니다. 식별값이 없습니다.');
            }

            // 숫자 유효성 검사
            if (!is_numeric($idx) || $idx <= 0) {
                throw new Exception('잘못된 요청입니다. 올바른 상품 번호가 아닙니다.');
            }

            // 정수로 변환
            $idx = (int) $idx;

            $productService = new ProductService();
            $productData = $productService->getProductDataForSite($idx);

            // 상품 데이터 존재 여부 확인
            if (empty($productData)) {
                throw new Exception('상품을 찾을 수 없습니다.');
            }

            $productScoreService = new ProductScoreService();
            $data_score = $productScoreService->getProductScoreByPdIdx($idx);

            $productCommentService = new ProductCommentService();
            $data_comment = $productCommentService->getProductCommentList([
                'pd_idx' => $idx,
                'paging' => true,
                'per_page' => 50,
                'page' => $page,
            ]);

            $pagination = new Pagination(
                $data_comment['total'],
                $data_comment['per_page'],
                $data_comment['current_page'],
                10
            );
            $paginationHtml = $pagination->renderLinks();

            $config_comment = config('onadb.comment');
            $onahole_score_name = $config_comment['onahole_score_name'];

            //dd($onahole_score_name);

            $data = [
                'productData' => $productData,
                'data_score' => $data_score,
                'data_comment' => $data_comment,
                'paginationHtml' => $paginationHtml,
                'onahole_score_name' => $onahole_score_name,
            ];

            return view('onadb.product.product_detail', $data)
                ->extends('onadb.layout.layout');

        } catch (Throwable $e) {

            return view('onadb.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }

    }

    /**
     * 상품평 등록
     * 
     * @param Request $request
     * @return array
     */
    public function productCommentReg( Request $request )
    {
        $requestData = $request->all();
        
    }

}