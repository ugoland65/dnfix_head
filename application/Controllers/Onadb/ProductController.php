<?php

namespace App\Controllers\Onadb;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Services\ProductService;
use App\Services\ProductScoreService;
use App\Services\ProductCommentService;
use App\Services\BrandService;
use App\Utils\Pagination;
use Jenssegers\Agent\Agent;

class ProductController extends BaseClass
{

    /**
     * 티어정보
     * 
     * @param Request $request
     * @param string $tier 티어
     * @return array
     */
    public function productTierList( Request $request, $tier )
    {

        try {

            if (empty($tier)) {
                throw new Exception('잘못된 요청입니다. 티어가 없습니다.');
            }

            // 티어값 유효성 검사 (1, 2만 허용)
            if (!in_array($tier, ['1', '2'])) {
                throw new Exception('잘못된 티어값입니다. 티어는 1 또는 2만 가능합니다.');
            }

            $agent = new Agent();

            //모바일, 태블릿, 데스크톱 구분
            if ($agent->isMobile()) {
                $default_per_page = 42;
            } elseif ($agent->isTablet()) {
                $default_per_page = 42;
            } else {
                $default_per_page = 40;
            }

            $requestData = $request->all();
            $page = $requestData['page'] ?? 1;
            $per_page = $requestData['per_page'] ?? $default_per_page;
            $search_value = $requestData['search_value'] ?? '';

            $productService = new ProductService();

            $payload = [
                'kind_code' => 'ONAHOLE',
                'site_show' => 'Y',
                'paging' => true,
                'page' => $page,
                'per_page' => $per_page,
                'show_mode' => 'onadb_main',
                'search_value' => $search_value,
                'tier' => $tier,
            ];
    
            $productList = $productService->getProductList($payload);

            $pagination = new Pagination(
                $productList['total'],
                $productList['per_page'],
                $productList['current_page'],
                10
            );
            $paginationHtml = $pagination->renderLinks();
    
            // Pagination 객체를 배열로 변환
            $paginationArray = $pagination->toArray();

            $data = [
                'tier' => $tier,
                'productList' => $productList ?? [],
                'paginationHtml' => $paginationHtml,
                'paginationArray' => $paginationArray,
            ];

            return view('onadb.product.tier_list', $data)
                ->extends('onadb.layout.layout');

        } catch (Throwable $e) {
            return view('onadb.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }

    }


    /**
     * 브랜드 상품
     * 
     * @param Request $request
     * @param string $idx 브랜드IDX
     * @return array
     */
    public function brandProductList( Request $request, $idx )
    {
        try {

            if (empty($idx)) {
                throw new Exception('잘못된 요청입니다. 식별값이 없습니다.');
            }

            // 숫자 유효성 검사
            if (!is_numeric($idx) || $idx <= 0) {
                throw new Exception('잘못된 요청입니다. 올바른 브랜드 번호가 아닙니다.');
            }

            // 정수로 변환
            $idx = (int) $idx;

            $brandService = new BrandService();
            $brandInfo = $brandService->getBrandInfo($idx);
            

            $agent = new Agent();

            //모바일, 태블릿, 데스크톱 구분
            if ($agent->isMobile()) {
                $default_per_page = 42;
            } elseif ($agent->isTablet()) {
                $default_per_page = 42;
            } else {
                $default_per_page = 40;
            }

            $productService = new ProductService();

            $payload = [
                'kind_code' => 'ONAHOLE',
                'site_show' => 'Y',
                'paging' => true,
                'page' => $page,
                'per_page' => $per_page,
                'show_mode' => 'onadb_main',
                'search_value' => $search_value,
                'brand_idx' => $idx,
            ];
    
            $productList = $productService->getProductList($payload);

            $pagination = new Pagination(
                $productList['total'],
                $productList['per_page'],
                $productList['current_page'],
                10
            );
            $paginationHtml = $pagination->renderLinks();
    
            // Pagination 객체를 배열로 변환
            $paginationArray = $pagination->toArray();


            $data = [
                'brandInfo' => $brandInfo ?? [],
                'productList' => $productList ?? [],
                'paginationHtml' => $paginationHtml,
                'paginationArray' => $paginationArray,
            ];

            return view('onadb.product.brand_product_list', $data)
            ->extends('onadb.layout.layout');

        } catch (Throwable $e) {
            return view('onadb.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }

    }


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
                'pd_idx' => $idx,
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

        $productCommentService = new ProductCommentService();
        $result = $productCommentService->saveComment($requestData);

        return response()->json([
            'success' => true,
            'message' => '등록완료',
            'result' => $result,
        ]);

    }

}