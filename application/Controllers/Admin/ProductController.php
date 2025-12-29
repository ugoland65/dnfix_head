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
                ->extends('admin.layout.layout',['pageGroup2' => 'prd']);

        } catch (Throwable $e) {
            dump($e->getMessage());
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }

    }


    /**
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
     * 상품 공급사 목록 화면
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