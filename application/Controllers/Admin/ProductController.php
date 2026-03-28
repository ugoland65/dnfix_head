<?php

namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;

use App\Services\ProductService;
use App\Services\BrandService;
use App\Services\ProductPartnerService;
use App\Services\PartnersService;
use App\Utils\Pagination;
class ProductController extends BaseClass 
{

    private $productService;
    private $productPartnerService;
    private $partnersService;

    public function __construct() {
        parent::__construct();
        $this->productService = new ProductService();
        $this->productPartnerService = new ProductPartnerService();
        $this->partnersService = new PartnersService();
    }


    /**
     * 상품 DB 목록 화면
     * 
     * @param Request $request
     * @return view
     */
    public function prdDbList(Request $request) 
    {

        try{
            
            $requestData = $request->all();

            $page = $requestData['page'] ?? 1;
            $sort_mode = $requestData['sort_mode'] ?? 'idx';
            $rack_code = $requestData['rack_code'] ?? null;

            $in_stock = $requestData['in_stock'] ?? 'have';
            $s_brand = $requestData['s_brand'] ?? null;
            $s_prd_kind = $requestData['s_prd_kind'] ?? null;
            $s_importing_country = $requestData['s_importing_country'] ?? null;
            $s_margin_group = $requestData['s_margin_group'] ?? null;
            $search_value = $requestData['search_value'] ?? null;
            $rack_code = $requestData['rack_code'] ?? null;
            $s_sale_mode = $requestData['s_sale_mode'] ?? null;
            $s_discontinued = $requestData['s_discontinued'] ?? null;

            //서비스로 넘겨주는 값
            $payload = [
                'paging' => true,
                'page' => $page,
                'per_page' => 100,
                'in_stock' => $in_stock,
                'sort_mode' => $sort_mode,
                'rack_code' => $rack_code,
                's_brand' => $s_brand,
                's_prd_kind' => $s_prd_kind,
                's_importing_country' => $s_importing_country,
                's_margin_group' => $s_margin_group,
                'search_value' => $search_value,
                's_sale_mode' => $s_sale_mode,
                's_discontinued' => $s_discontinued,
            ];

            $productList = $this->productService->getProductListForAdmin($payload);

            $pagination = new Pagination(
                $productList['total'],
                $productList['per_page'],
                $productList['current_page'],
                10
            );

            $paginationHtml = $pagination->renderLinks();
            $paginationArray = $pagination->toArray();

            // 브랜드 셀렉트바를 위한 조회
            $brandService = new BrandService();
            $brandForSelect = $brandService->getBrandForSelect(['listActive' => true]);

            $config_product = config('admin.product');
            $prdKindSelect = $config_product['prd_kind_name'] ?? [];
            $importingCountrySelect = $config_product['importing_country'] ?? [];

            $data = [
                's_brand' => $s_brand,
                's_prd_kind' => $s_prd_kind,
                's_importing_country' => $s_importing_country,
                's_margin_group' => $s_margin_group,
                's_sale_mode' => $s_sale_mode,
                's_discontinued' => $s_discontinued,
                'rack_code' => $rack_code,
                'in_stock' => $in_stock,
                'search_value' => $search_value,
                'productList' => $productList['data'],
                'brandForSelect' => $brandForSelect,
                'prdKindSelect' => $prdKindSelect,
                'importingCountrySelect' => $importingCountrySelect,
                'sort_mode' => $sort_mode,
                'paginationHtml' => $paginationHtml,
                'paginationArray' => $paginationArray
            ];

            return view('admin.product.product_db', $data)
                ->extends('admin.layout.layout',['pageGroup2' => 'prd', 'pageNameCode' => 'prd_db']);

        } catch (Throwable $e) {
            dump($e->getMessage());
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }

    }


    /**
     * 상품 재고 목록 화면
     * 
     * @param Request $request
     * @return view
     */
    public function productStock(Request $request) 
    {

        try{

            $requestData = $request->all();

            $page = $requestData['page'] ?? 1;
            $sort_mode = $requestData['sort_mode'] ?? 'stock';
            $rack_code = $requestData['rack_code'] ?? null;

            $in_stock = $requestData['in_stock'] ?? 'have';
            $s_brand = $requestData['s_brand'] ?? null;
            $s_prd_kind = $requestData['s_prd_kind'] ?? null;
            $s_importing_country = $requestData['s_importing_country'] ?? null;
            $s_margin_group = $requestData['s_margin_group'] ?? null;
            $search_value = $requestData['search_value'] ?? null;
            $rack_code = $requestData['rack_code'] ?? null;
            $s_sale_mode = $requestData['s_sale_mode'] ?? null;
            $s_discontinued = $requestData['s_discontinued'] ?? null; // 단종여부

            //서비스로 넘겨주는 값
            $payload = [
                'paging' => true,
                'page' => $page,
                'per_page' => 100,
                'show_mode' => 'product_stock',
                'in_stock' => $in_stock,
                'sort_mode' => $sort_mode,
                'rack_code' => $rack_code,
                's_brand' => $s_brand,
                's_prd_kind' => $s_prd_kind,
                's_importing_country' => $s_importing_country,
                's_margin_group' => $s_margin_group,
                'search_value' => $search_value,
                's_sale_mode' => $s_sale_mode,
                's_discontinued' => $s_discontinued,
            ];

            $productList = $this->productService->getProductListForAdmin($payload);

            $pagination = new Pagination(
                $productList['total'],
                $productList['per_page'],
                $productList['current_page'],
                10
            );

            $paginationHtml = $pagination->renderLinks();
            $paginationArray = $pagination->toArray();

            // 브랜드 셀렉트바를 위한 조회
            $brandService = new BrandService();
            $brandForSelect = $brandService->getBrandForSelect(['listActive' => true]);

            $config_product = config('admin.product');
            $prdKindSelect = $config_product['prd_kind_name'] ?? [];
            $importingCountrySelect = $config_product['importing_country'] ?? [];

            $data = [
                's_brand' => $s_brand,
                's_prd_kind' => $s_prd_kind,
                's_importing_country' => $s_importing_country,
                's_margin_group' => $s_margin_group,
                's_sale_mode' => $s_sale_mode,
                'rack_code' => $rack_code,
                'in_stock' => $in_stock,
                'search_value' => $search_value,
                'productList' => $productList['data'],
                'brandForSelect' => $brandForSelect,
                'prdKindSelect' => $prdKindSelect,
                'importingCountrySelect' => $importingCountrySelect,
                'sort_mode' => $sort_mode,
                'paginationHtml' => $paginationHtml,
                'paginationArray' => $paginationArray
            ];

            return view('admin.product.product_stock', $data)
                ->extends('admin.layout.layout',['pageGroup2' => 'prd', 'pageNameCode' => 'product_stock']);

        } catch (Throwable $e) {
            dump($e->getMessage());
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }

    }


    /**
     * 상품 디테일 (베이직)
     */
    public function prdDetailBasicPage(Request $request)
    {
        try{

            $requestData = $request->all();
            $prdIdx = $requestData['prd_idx'] ?? null;

            $productService = new ProductService();
            $productData = $productService->getProductDataForAdmin($prdIdx);

            $config_product = config('admin.product');
            $prd_kind_name = $config_product['prd_kind_name'] ?? [];

            // 브랜드 셀렉트바를 위한 조회
            $brandService = new BrandService();
            $brandForSelect = $brandService->getBrandForSelect();

            $data = [
                'mode' => 'edit',
                'prd_idx' => $prdIdx,
                'productData' => $productData,
                'prd_kind_name' => $prd_kind_name,
                'brandForSelect' => $brandForSelect
            ];

            return view('admin.product.prd_detail_basic', $data);

        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
        
    }


    /**
     * 상품 디테일 (가격정보)
     */
    public function prdDetailPricePage(Request $request)
    {
        try{

            $requestData = $request->all();
            $prdIdx = $requestData['prd_idx'] ?? null;

            $productService = new ProductService();
            $productData = $productService->getProductDataForAdmin($prdIdx);

            $data = [
                'productData' => $productData,
            ];

            return view('admin.product.prd_detail_price', $data);

        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
        
    }


    /**
     * 상품 베이직 저장
     */
    public function saveProduct(Request $request)
    {
        try{

            $requestData = $request->all();
            
            $productService = new ProductService();
            $result = $productService->saveProduct($requestData);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? '상품 정보가 저장되었습니다.',
                    'data' => $result,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? $result['msg'] ?? '상품 정보 저장에 실패했습니다.',
                'data' => $result,
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * 상품 매입정보 저장
     */
    public function saveProductPrice(Request $request)
    {
        try{

            $requestData = $request->all();
            
            $productService = new ProductService();
            $result = $productService->saveProductPrice($requestData);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? '상품 정보가 저장되었습니다.',
                    'data' => $result,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? $result['msg'] ?? '상품 정보 저장에 실패했습니다.',
                'data' => $result,
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * 상품 처리 액션
     */
    public function productAction(Request $request)
    {
        try{

            $requestData = $request->all();
            $actionMode = $requestData['action_mode'] ?? null;

            switch ($actionMode) {

                case 'set_product_discontinued':
                    $result = $this->productService->setProductDiscontinued($requestData);
                    break;

                case 'unset_product_discontinued':
                    $result = $this->productService->unsetProductDiscontinued($requestData);
                    break;

            }

            $message = (is_array($result) && isset($result['message'])) ? $result['message'] : '처리 완료';

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $result,
            ]);

        }
        catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }


    /**
     * @deprecated 어디서 사용하는지 미확인
     * 상품 DB 목록 화면
     * 
     * @skin : skin.prd_db.php
     * @return array
     */
    public function prdDbIndex() 
    {

        $getData = $this->requestHandler->getAll(); // GET 데이터 받기

        $extraData = [];

        // 상품 데이터 조회
        $result = $this->productService->getProductListOld($getData, $extraData);
        $pagination = new Pagination($result['total'], $result['per_page'], $result['current_page'], 10);
        $paginationHtml = $pagination->renderLinks();

        // Pagination 객체를 배열로 변환
        $paginationArray = $pagination->toArray();

        // 브랜드 셀렉트바를 위한 조회
        $extraData = [
            'listActive' => true
        ];
        $brandService = new BrandService();
        $brandForSelect = $brandService->getBrandForSelect($extraData);

        return [
            'test' => $getData,
            'prdList' => $result['data'],
            'pagination' => $paginationArray,
            'paginationHtml' => $paginationHtml,
            'brandForSelect' => $brandForSelect
        ];

    }


    /**
     * @deprecated 사용하지 않을 예정
     * 상품 등록 폼 화면
     * @skin : skin.prd_reg_form.php
     * @return array
     */
    public function prdRegFormIndex() {

        $getParam = $this->requestHandler->getAllPost();
        $prdIdx = $getParam['prd_idx'] ?? null;
        
        // prd_idx가 없는 경우 기본값 설정 또는 오류 처리
        if (!$prdIdx) {
            // 새 상품 등록 모드로 간주하고 빈 데이터 반환
            return [
                'mode' => 'new',
                'message' => '새 상품 등록 모드입니다.'
            ];
        }
        
        // 상품 데이터 조회
        $productData = $this->productService->getProductDataForAdmin($prdIdx);

        // 조회 결과가 없는 경우 처리
        if (!$productData) {
            return [
                'error' => true,
                'message' => '상품 정보를 찾을 수 없습니다.'
            ];
        }

        // 브랜드 셀렉트바를 위한 조회
        $brandService = new BrandService();
        $brandForSelect = $brandService->getBrandForSelect();
        
        $data=[
            'mode' => 'edit',
            'prd_idx' => $prdIdx,
            'productData' => $productData,
            'brandForSelect' => $brandForSelect
        ];
        
        return $data;

    }


    /**
     * HBTI 상품 목록 화면
     * @skin : skin.hbti_prd.php
     * @return array
     */
    public function hbtiPrdIndex() 
    {    

        $getData = $this->requestHandler->getAll(); // GET 데이터 받기

        $extraData = [
            'showMode' => 'hbti'
        ];

        // 상품 데이터 조회
        $result = $this->productService->getProductListOld($getData, $extraData);
        $pagination = new Pagination($result['total'], $result['per_page'], $result['current_page'], 10);
        $paginationHtml = $pagination->renderLinks();

        // Pagination 객체를 배열로 변환
        $paginationArray = $pagination->toArray();

        // 브랜드 셀렉트바를 위한 조회
        $extraData = [
            'listActive' => true
        ];
        $brandService = new BrandService();
        $brandForSelect = $brandService->getBrandForSelect($extraData);

        $hbtiCount = $this->productService->gethbtiCount();

        $data=[
            'hbtiCount' => $hbtiCount,
            'prdList' => $result['data'],
            'pagination' => $paginationArray,
            'paginationHtml' => $paginationHtml,
            'brandForSelect' => $brandForSelect
        ];

        return $data; 
        
    }
    

    /**
     * @deprecated 사용하지 않을 예정
     * 상품 공급사 목록 화면
     * 
     * @skin : skin.prd_provider.php
     * @return array
     */
    public function prdProviderIndex() {

        $getData = $this->requestHandler->getAll(); // GET 데이터 받기

        //$getData['page'] = 1;
        $getData['per_page'] = 100;
        $result = $this->productPartnerService->getProductPartnerList($getData);

        $pagination = new Pagination($result['total'], $result['per_page'], $result['current_page'], 10);
        $paginationHtml = $pagination->renderLinks();

        // Pagination 객체를 배열로 변환
        $paginationArray = $pagination->toArray();

        // 브랜드 셀렉트바
        $extraData = ['listActive' => true];
        $brandService = new BrandService();
        $brandForSelect = $brandService->getBrandForSelect($extraData);

        // 공급사 셀렉트바
        $extraData = ['showMode' => 'WHOLE_SUPPLIER'];
        $partnerForSelect = $this->partnersService->getPartnersForSelect($extraData);


        $hbtiCount = $this->productService->gethbtiCount();

        $data = [
            'productPartnerList' => $result['data'],
            'pagination' => $paginationArray,
            'paginationHtml' => $paginationHtml,
            'brandForSelect' => $brandForSelect,
            'partnerForSelect' => $partnerForSelect
        ];

        return $data;
    }


    /**
     *@deprecated 사용하지 않을 예정

     * 상품 공급사 상세 화면
     * @skin : skin.prd_provider_info.php
     * @return array
     */
    public function prdProviderInfoIndex() 
    {    

        $getData = $this->requestHandler->getAll(); // GET 데이터 받기
        $prdIdx = $getData['prd_idx'] ?? '';

        $result = $this->productPartnerService->getProductPartnerInfo($prdIdx);

        // 브랜드 셀렉트바
        $extraData = ['listActive' => true];
        $brandService = new BrandService();
        $brandForSelect = $brandService->getBrandForSelect($extraData);

        // 공급사 셀렉트바
        $extraData = ['showMode' => 'WHOLE_SUPPLIER'];
        $partnerForSelect = $this->partnersService->getPartnersForSelect($extraData);

        $data = [
            'productPartnerInfo' => $result,
            'brandForSelect' => $brandForSelect,
            'partnerForSelect' => $partnerForSelect
        ];

        return $data;
        
    }


    /**
     * @deprecated 사용하지 않을 예정
     * 공급사 상품 저장
     * @return array
     */
    public function saveProductPartner()
    {
        try {

            $postData = $this->requestHandler->getAllPost();
            $result = $this->productPartnerService->saveProductPartner($postData);

            if($result['status'] == 'success'){
                return ['status' => 'success', 'message' => '저장되었습니다.'];
            }else{
                throw new \Exception($result['message']);
            }

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

} 