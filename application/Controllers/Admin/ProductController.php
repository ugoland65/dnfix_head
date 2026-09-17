<?php

namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Core\AuthAdmin;
use App\Classes\Request;

use App\Services\ProductService;
use App\Services\ProductActionService;
use App\Services\BrandService;
use App\Services\ProductPartnerService;
use App\Services\ProductPartnerApiService;
use App\Services\PartnersService;
use App\Services\ProductStockSaleLogService;
use App\Services\ProductStockService;
use App\Services\GodoInspectionService;
use App\Services\InspectionProcessLogService;
use App\Services\CompetitorApiService;
use App\Services\ProductSupplierPyApiService;
use App\Services\ProductImageHostingService;
use App\Services\AdminActionLogService;
use App\Services\OrderGroupService;
use App\Services\ProductSpecInfoService;
use App\Services\ProductSpecService;
use App\Services\ProductDetailContentService;
use App\Models\ProductModel;
use App\Models\ProductCollectionItemModel;
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
     * 상품관리 종합현황
     */
    public function productOverview(Request $request)
    {
        $weekStart = date('Y-m-d 00:00:00', strtotime('-6 days'));
        $monthStart = date('Y-m-d 00:00:00', strtotime('-1 month'));
        $baseCriteria = [
            'paging' => true,
            'page' => 1,
            'per_page' => 30,
            'show_mode' => 'product_stock',
            'in_stock' => 'all',
        ];

        $recentSalePriceChanges = $this->productService->getProductListForAdmin(
            $baseCriteria + [
                'sort_mode' => 'sale_price_changed_at',
                'sale_price_changed_since' => $weekStart,
            ]
        );
        $recentSoldoutProducts = $this->productService->getProductListForAdmin(
            $baseCriteria + [
                'sort_mode' => 'soldout',
                'soldout_since' => $weekStart,
                'exclude_stock_management_disabled' => true,
            ]
        );
        $recentDeletedProducts = $this->productService->getRecentDeletedProducts($monthStart);

        return view('admin.product.product_overview', [
                'weekStart' => $weekStart,
                'monthStart' => $monthStart,
                'recentSalePriceChanges' => $recentSalePriceChanges,
                'recentSoldoutProducts' => $recentSoldoutProducts,
                'recentDeletedProducts' => $recentDeletedProducts,
            ])
            ->extends('admin.layout.layout', ['pageGroup2' => 'prd', 'pageNameCode' => 'product_overview']);
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

            $in_stock = $requestData['in_stock'] ?? 'all';
            $s_brand = $requestData['s_brand'] ?? null;
            $s_prd_kind = $requestData['s_prd_kind'] ?? null;
            $s_prd_kind_second = $requestData['s_prd_kind_second'] ?? null;
            $s_importing_country = $requestData['s_importing_country'] ?? null;
            $s_margin_group = $requestData['s_margin_group'] ?? null;
            $search_value = $requestData['search_value'] ?? null;
            $rack_code = $requestData['rack_code'] ?? null;
            $s_sale_mode = $requestData['s_sale_mode'] ?? null;
            $s_sale_status = $requestData['s_sale_status'] ?? null;
            $s_discontinued = $requestData['s_discontinued'] ?? null;
            $s_label_idx = $requestData['s_label_idx'] ?? null;
            $s_relation_group_idx = (int)($requestData['s_relation_group_idx'] ?? 0);
            $s_work_task_code = $requestData['s_work_task_code'] ?? null;
            $s_work_task_done = $requestData['s_work_task_done'] ?? null;

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
                's_prd_kind_second' => $s_prd_kind_second,
                's_importing_country' => $s_importing_country,
                's_margin_group' => $s_margin_group,
                'search_value' => $search_value,
                's_sale_mode' => $s_sale_mode,
                's_sale_status' => $s_sale_status,
                's_discontinued' => $s_discontinued,
                's_label_idx' => $s_label_idx,
                's_relation_group_idx' => $s_relation_group_idx,
                's_work_task_code' => $s_work_task_code,
                's_work_task_done' => $s_work_task_done,
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
            $categories = $config_product['categories'] ?? [];
            $saleStatusOptions = $config_product['sale_status_options'] ?? [];
            $workTaskItemOptions = $this->productService->getWorkCheckItemsForFilter($s_prd_kind);
            $productLabelForSelect = $this->productService->getActiveProductLabelOptions();

            $data = [
                's_brand' => $s_brand,
                's_prd_kind' => $s_prd_kind,
                's_prd_kind_second' => $s_prd_kind_second,
                's_importing_country' => $s_importing_country,
                's_margin_group' => $s_margin_group,
                's_sale_mode' => $s_sale_mode,
                's_sale_status' => $s_sale_status,
                's_discontinued' => $s_discontinued,
                's_label_idx' => $s_label_idx,
                's_relation_group_idx' => $s_relation_group_idx,
                's_work_task_code' => $s_work_task_code,
                's_work_task_done' => $s_work_task_done,
                'rack_code' => $rack_code,
                'in_stock' => $in_stock,
                'search_value' => $search_value,
                'productList' => $productList['data'],
                'brandForSelect' => $brandForSelect,
                'prdKindSelect' => $prdKindSelect,
                'importingCountrySelect' => $importingCountrySelect,
                'categories' => $categories,
                'sale_status_options' => $saleStatusOptions,
                'product_label_for_select' => $productLabelForSelect,
                'series_for_select' => $this->productService->getProductRelationGroupSeriesForFilter(),
                'workTaskItemOptions' => $workTaskItemOptions,
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
            $sort_mode = $requestData['sort_mode'] ?? 'idx';
            $rack_code = $requestData['rack_code'] ?? null;

            $in_stock = $requestData['in_stock'] ?? 'all';
            $s_brand = $requestData['s_brand'] ?? null;
            $s_prd_kind = $requestData['s_prd_kind'] ?? null;
            $s_prd_kind_second = $requestData['s_prd_kind_second'] ?? null;
            $s_importing_country = $requestData['s_importing_country'] ?? null;
            $s_margin_group = $requestData['s_margin_group'] ?? null;
            $search_value = $requestData['search_value'] ?? null;
            $rack_code = $requestData['rack_code'] ?? null;
            $s_sale_mode = $requestData['s_sale_mode'] ?? null;
            $s_sale_status = $requestData['s_sale_status'] ?? null;
            $s_discontinued = $requestData['s_discontinued'] ?? null; // 단종여부
            $s_label_idx = $requestData['s_label_idx'] ?? null;
            $s_relation_group_idx = (int)($requestData['s_relation_group_idx'] ?? 0);
            $s_work_task_code = $requestData['s_work_task_code'] ?? null;
            $s_work_task_done = $requestData['s_work_task_done'] ?? null;

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
                's_prd_kind_second' => $s_prd_kind_second,
                's_importing_country' => $s_importing_country,
                's_margin_group' => $s_margin_group,
                'search_value' => $search_value,
                's_sale_mode' => $s_sale_mode,
                's_sale_status' => $s_sale_status,
                's_discontinued' => $s_discontinued,
                's_label_idx' => $s_label_idx,
                's_relation_group_idx' => $s_relation_group_idx,
                's_work_task_code' => $s_work_task_code,
                's_work_task_done' => $s_work_task_done,
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
            $categories = $config_product['categories'] ?? [];
            $saleStatusOptions = $config_product['sale_status_options'] ?? [];
            $workTaskItemOptions = $this->productService->getWorkCheckItemsForFilter($s_prd_kind);
            $productLabelForSelect = $this->productService->getActiveProductLabelOptions();

            $data = [
                's_brand' => $s_brand,
                's_prd_kind' => $s_prd_kind,
                's_prd_kind_second' => $s_prd_kind_second,
                's_importing_country' => $s_importing_country,
                's_margin_group' => $s_margin_group,
                's_sale_mode' => $s_sale_mode,
                's_sale_status' => $s_sale_status,
                's_discontinued' => $s_discontinued,
                's_label_idx' => $s_label_idx,
                's_relation_group_idx' => $s_relation_group_idx,
                's_work_task_code' => $s_work_task_code,
                's_work_task_done' => $s_work_task_done,
                'rack_code' => $rack_code,
                'in_stock' => $in_stock,
                'search_value' => $search_value,
                'productList' => $productList['data'],
                'brandForSelect' => $brandForSelect,
                'prdKindSelect' => $prdKindSelect,
                'importingCountrySelect' => $importingCountrySelect,
                'categories' => $categories,
                'sale_status_options' => $saleStatusOptions,
                'product_label_for_select' => $productLabelForSelect,
                'series_for_select' => $this->productService->getProductRelationGroupSeriesForFilter(),
                'workTaskItemOptions' => $workTaskItemOptions,
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
     * 상품 DB 생성 화면
     *
     * @param Request $request
     * @return view
     */
    public function prdDbCreate(Request $request)
    {
        try {
            $config_product = config('admin.product');
            $prd_kind_name = $config_product['prd_kind_name'] ?? [];
            $categories = $config_product['categories'] ?? [];
            $saleStatusOptions = $config_product['sale_status_options'] ?? [];

            $brandService = new BrandService();
            $brandForSelect = $brandService->getBrandForSelect();

            $productData = [
                'CD_IDX' => '',
                'sale_status' => '가등록',
                'CD_KIND_CODE' => '',
                'CD_CATEGORY_CODE' => '',
                'CD_BRAND_IDX' => '',
                'CD_BRAND2_IDX' => '',
                'img_mode' => 'this',
                'cd_add_img' => [
                    'add1' => ['filename' => ''],
                    'add2' => ['filename' => ''],
                    'add3' => ['filename' => ''],
                ],
                'CD_SIZE' => [],
                'cd_weight_fn' => [],
                'cd_size_fn' => [
                    'package' => [],
                    'invoice' => [],
                    'import' => [],
                ],
                'cd_hbti_data' => [],
                'hbti_target' => 'Y',
                'cd_site_show' => 'N',
                'cd_reference_links' => [],
                'cd_accessories' => [],
                'cd_code_fn' => [],
                'work_check_list' => [],
                'product_label_options' => $this->productService->getActiveProductLabelOptions(),
                'selected_product_label_idxs' => [],
                'is_sale_month' => 0,
                'is_sale_special' => 0,
                'is_discontinued' => 0,
                'is_handling_stopped' => 0,
            ];

            $data = [
                'mode' => 'new',
                'prd_idx' => null,
                'productData' => $productData,
                'prd_kind_name' => $prd_kind_name,
                'categories' => $categories,
                'brandForSelect' => $brandForSelect,
                'sale_status_options' => $saleStatusOptions,
                'orderGroupCodeOptions' => (new OrderGroupService())->getOrderGroupCodeOptions(),
            ];

            return view('admin.product.prd_db_create', $data)
                ->extends('admin.layout.layout', ['pageGroup2' => 'prd', 'pageNameCode' => 'prd_db_create']);
                
        } catch (Throwable $e) {
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
            $configProduct = config('admin.product');
            $purchaseTypeOptions = $configProduct['purchase_type_options'] ?? [];
            $configProduct = config('admin.product');
            $purchaseTypeOptions = $configProduct['purchase_type_options'] ?? [];

            $config_product = config('admin.product');
            $prd_kind_name = $config_product['prd_kind_name'] ?? [];
            $categories = $config_product['categories'] ?? [];
            $saleStatusOptions = $config_product['sale_status_options'] ?? [];

            // 브랜드 셀렉트바를 위한 조회
            $brandService = new BrandService();
            $brandForSelect = $brandService->getBrandForSelect();

            $inspectionProcessLogService = new InspectionProcessLogService();
            $godoDiscontinuedLog = $inspectionProcessLogService->getLatestByPrdIdxAndLocation(
                (int)$prdIdx,
                InspectionProcessLogService::LOCATION_PRODUCT_GODO_DISCONTINUED
            );
            $godoHandlingStoppedLog = $inspectionProcessLogService->getLatestByPrdIdxAndLocation(
                (int)$prdIdx,
                InspectionProcessLogService::LOCATION_PRODUCT_GODO_HANDLING_STOPPED
            );
            $godoSpecialDiscountLog = $inspectionProcessLogService->getLatestByPrdIdxAndLocation(
                (int)$prdIdx,
                InspectionProcessLogService::LOCATION_PRODUCT_GODO_SPECIAL_DISCOUNT
            );

            $data = [
                'mode' => 'edit',
                'prd_idx' => $prdIdx,
                'productData' => $productData,
                'prd_kind_name' => $prd_kind_name,
                'categories' => $categories,
                'brandForSelect' => $brandForSelect,
                'sale_status_options' => $saleStatusOptions,
                'godoDiscontinuedLog' => $godoDiscontinuedLog,
                'godoHandlingStoppedLog' => $godoHandlingStoppedLog,
                'godoSpecialDiscountLog' => $godoSpecialDiscountLog,
                'orderGroupCodeOptions' => (new OrderGroupService())->getOrderGroupCodeOptions(),
            ];

            return view('admin.product.prd_detail_basic', $data);

        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
        
    }


    /**
     * 상품 디테일 (상품 정보수집 페이지
     */
    public function productInfoCollectionPage(Request $request)
    {
        $requestData = $request->all();
        $prdIdx = (int)($requestData['prd_idx'] ?? 0);
        $selectedCollectionIndex = max(0, (int)($requestData['collection_index'] ?? 0));
        $collectionData = [];
        $collectionError = '';
        $productData = [];
        $hostingImageUrls = [];
        $hostingCollectionItemIdx = 0;
        $collectionItemData = [];
        $collectionActionLogs = [];

        if ($prdIdx > 0) {
            try {
                $productData = $this->productService->getProductDataForAdmin($prdIdx);
            } catch (Throwable $e) {
                $productData = [];
            }

            try {
                $collectionData = (new ProductPartnerApiService())->getMakerProductDetails([
                    'matched_product_pk' => $prdIdx,
                    'page' => 1,
                    'limit' => 100,
                ]);
                $sourceItems = is_array($collectionData['data']['items'] ?? null)
                    ? $collectionData['data']['items']
                    : [];
                if (!isset($sourceItems[$selectedCollectionIndex])) {
                    $selectedCollectionIndex = 0;
                }
                $sourceItem = $sourceItems[$selectedCollectionIndex] ?? [];
                $sourceProductIdentifier = is_array($sourceItem) ? (string)($sourceItem['product_pk'] ?? '') : '';
                if ($sourceProductIdentifier === '' && is_array($sourceItem) && !empty($sourceItem['product_code'])) {
                    $sourceProductIdentifier = (string)$sourceItem['product_code'];
                }
                if (is_array($sourceItem) && !empty($sourceItem['site_code']) && $sourceProductIdentifier !== '') {
                    $sourceCollectedAt = is_array($sourceItem['collected_at'] ?? null)
                        ? (string)($sourceItem['collected_at']['date'] ?? '')
                        : (string)($sourceItem['collected_at'] ?? '');
                    $collectionItemData = ProductCollectionItemModel::query()
                        ->where('matched_product_pk', '=', $prdIdx)
                        ->where('source_type', '=', 'maker')
                        ->where('source_site_code', '=', (string)$sourceItem['site_code'])
                        ->where('source_product_pk', '=', $sourceProductIdentifier)
                        ->where('source_collected_at', '=', $sourceCollectedAt)
                        ->orderBy('idx', 'DESC')
                        ->first();
                    $collectionItemData = is_array($collectionItemData)
                        ? $collectionItemData
                        : ($collectionItemData ? $collectionItemData->toArray() : []);

                    if (empty($collectionItemData)) {
                        $sourceImageUrls = [];
                        $collectionPageUrl = trim((string)($sourceItem['source_url'] ?? ''));
                        foreach ((array)($sourceItem['image_sources'] ?? []) as $imageSource) {
                            $sourceUrl = is_array($imageSource) ? (string)($imageSource['full'] ?? $imageSource['src'] ?? '') : (string)$imageSource;
                            if ($sourceUrl !== '') {
                                $sourceImageUrls[] = ProductImageHostingService::resolveCollectedImageUrl($sourceUrl, $collectionPageUrl);
                            }
                        }
                        $previousCollectionItem = ProductCollectionItemModel::query()
                            ->where('matched_product_pk', '=', $prdIdx)
                            ->where('source_type', '=', 'maker')
                            ->where('source_site_code', '=', (string)$sourceItem['site_code'])
                            ->where('source_product_pk', '=', $sourceProductIdentifier)
                            ->orderBy('idx', 'DESC')
                            ->first();
                        $previousCollectionItem = $previousCollectionItem
                            ? (is_array($previousCollectionItem) ? $previousCollectionItem : $previousCollectionItem->toArray())
                            : [];
                        $collectionItemData = ProductCollectionItemModel::create([
                            'matched_product_pk' => $prdIdx,
                            'source_type' => 'maker',
                            'source_site_code' => (string)$sourceItem['site_code'],
                            'source_product_pk' => $sourceProductIdentifier,
                            'source_collected_at' => $sourceCollectedAt,
                            'image_storage_path' => (string)($productData['CD_IMAGE_STORAGE_PATH'] ?? ''),
                            'image_upload_status' => 'pending',
                            'source_image_urls_json' => json_encode($sourceImageUrls, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                            'image_total_count' => count($sourceImageUrls),
                            'image_success_count' => 0,
                            'image_failed_count' => 0,
                            'translated_image_alts_json' => (string)($previousCollectionItem['translated_image_alts_json'] ?? ''),
                        ]);
                        $collectionItemData = is_array($collectionItemData) ? $collectionItemData : $collectionItemData->toArray();
                    }

                    if (in_array(($collectionItemData['image_upload_status'] ?? ''), ['success', 'partial'], true)) {
                        $hostingImageUrls = json_decode((string)($collectionItemData['hosting_image_urls_json'] ?? '[]'), true);
                        $hostingImageUrls = is_array($hostingImageUrls) ? array_values(array_filter($hostingImageUrls, 'is_string')) : [];
                        $hostingCollectionItemIdx = (int)($collectionItemData['idx'] ?? 0);
                    }
                    if (!empty($collectionItemData['idx'])) {
                        $collectionActionLogs = (new AdminActionLogService())->getAdminActionLogList([
                            'target_type' => 'product_collection_item',
                            'target_pk' => (string)$collectionItemData['idx'],
                        ]);
                    }
                }
            } catch (Throwable $e) {
                $collectionError = $e->getMessage();
            }
        }

        return view('admin.product.product_info_collection', [
            'prd_idx' => $prdIdx,
            'productData' => $productData,
            'collectionData' => $collectionData,
            'collectionError' => $collectionError,
            'hostingImageUrls' => $hostingImageUrls,
            'hostingCollectionItemIdx' => $hostingCollectionItemIdx,
            'collectionItemData' => $collectionItemData,
            'collectionActionLogs' => $collectionActionLogs,
            'selectedCollectionIndex' => $selectedCollectionIndex,
        ]);
    }
    

    /**
     * 도메인별 상품 정보수집 요청을 크롤러 API로 전달한다.
     */
    public function requestProductInfoCollection(Request $request)
    {
        try {
            set_time_limit(180);
            ini_set('max_execution_time', '180');
            $requestData = $request->all();
            $result = (new ProductSupplierPyApiService())->requestProductInfoCollection([
                'collection_url' => $requestData['collection_url'] ?? '',
                'matched_product_pk' => $requestData['prd_idx'] ?? 0,
                'requester_user_pk' => AuthAdmin::getSession('sess_idx'),
                'requester_user_name' => AuthAdmin::getSession('sess_name'),
            ]);

            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? '정보수집을 요청했습니다.',
                'data' => $result,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * 수집 이미지 프록시.
     * 외부 사이트의 hotlink/mixed-content 차단을 피하기 위해 허용된 도메인의 이미지만 중계한다.
     */
    public function collectedProductImageProxy(Request $request)
    {
        $requestData = $request->all();
        $imageUrl = trim((string)($requestData['url'] ?? ''));
        try {
            $image = ProductImageHostingService::downloadCollectedSourceImage($imageUrl, 15);
        } catch (\InvalidArgumentException $e) {
            http_response_code(400);
            return 'Invalid image source.';
        } catch (Throwable $e) {
            http_response_code(404);
            return 'Image unavailable.';
        }

        header('Content-Type: ' . $image['content_type']);
        header('Cache-Control: private, max-age=3600');
        if (!empty($requestData['download'])) {
            $filename = basename((string)(parse_url($imageUrl, PHP_URL_PATH) ?? ''));
            $filename = preg_replace('/[^A-Za-z0-9._-]/', '_', $filename) ?: 'collected_image';
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        }
        return $image['body'];
    }

    /**
     * 수집된 원본 이미지를 ZIP으로 일괄 다운로드한다.
     */
    public function downloadCollectedProductImages(Request $request)
    {
        $requestData = $request->all();
        $prdIdx = (int)($requestData['prd_idx'] ?? 0);
        $collectionIndex = max(0, (int)($requestData['collection_index'] ?? 0));
        if ($prdIdx < 1) {
            http_response_code(400);
            return 'Invalid product.';
        }
        if (!class_exists(\ZipArchive::class)) {
            http_response_code(500);
            return 'ZIP extension is unavailable.';
        }

        $apiData = (new ProductPartnerApiService())->getMakerProductDetails([
            'matched_product_pk' => $prdIdx,
            'page' => 1,
            'limit' => 100,
        ]);
        $item = $apiData['data']['items'][$collectionIndex] ?? [];
        $imageSources = is_array($item['image_sources'] ?? null) ? $item['image_sources'] : [];
        if (empty($imageSources)) {
            http_response_code(404);
            return 'No collected images.';
        }

        $zipPath = tempnam(sys_get_temp_dir(), 'a1_collected_images_');
        $zip = new \ZipArchive();
        if ($zipPath === false || $zip->open($zipPath, \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('이미지 ZIP 파일을 만들 수 없습니다.');
        }

        $mimeExtensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];
        $collectionPageUrl = trim((string)($item['source_url'] ?? ''));
        $imageCount = 0;
        foreach (array_slice($imageSources, 0, 50) as $imageSource) {
            $imageUrl = is_array($imageSource) ? (string)($imageSource['full'] ?? $imageSource['src'] ?? '') : (string)$imageSource;
            $imageUrl = ProductImageHostingService::resolveCollectedImageUrl($imageUrl, $collectionPageUrl);
            try {
                $image = ProductImageHostingService::downloadCollectedSourceImage($imageUrl, 20);
            } catch (Throwable $e) {
                continue;
            }
            if (!isset($mimeExtensions[$image['content_type']])) {
                continue;
            }
            $imageCount++;
            $zip->addFromString(sprintf('collected_image_%02d.%s', $imageCount, $mimeExtensions[$image['content_type']]), $image['body']);
        }
        $zip->close();

        if ($imageCount === 0) {
            @unlink($zipPath);
            http_response_code(404);
            return 'No downloadable images.';
        }

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="collected_images_' . $prdIdx . '.zip"');
        header('Content-Length: ' . filesize($zipPath));
        readfile($zipPath);
        @unlink($zipPath);
        return null;
    }

    /**
     * 상품별 외부 이미지 저장소 경로를 저장한다.
     */
    public function saveProductImageStoragePath(Request $request)
    {
        try {
            $requestData = $request->all();
            $prdIdx = (int)($requestData['prd_idx'] ?? 0);
            $storagePath = trim((string)($requestData['image_storage_path'] ?? ''));
            if ($prdIdx < 1) {
                throw new \InvalidArgumentException('상품 번호가 올바르지 않습니다.');
            }

            $storagePath = preg_replace('#/+#', '/', $storagePath);
            if ($storagePath === '' || $storagePath[0] !== '/') {
                throw new \InvalidArgumentException('이미지 저장소 경로는 / 로 시작해야 합니다.');
            }
            if (substr($storagePath, -1) !== '/') {
                $storagePath .= '/';
            }
            if (strpos($storagePath, '..') !== false || !preg_match('#^/[A-Za-z0-9._-]+(?:/[A-Za-z0-9._-]+)*/$#', $storagePath)) {
                throw new \InvalidArgumentException('이미지 저장소 경로 형식이 올바르지 않습니다.');
            }

            $samePathProducts = ProductModel::query()
                ->select('CD_IDX')
                ->where('CD_IMAGE_STORAGE_PATH', '=', $storagePath)
                ->get();
            $samePathProducts = is_array($samePathProducts) ? $samePathProducts : $samePathProducts->toArray();
            foreach ($samePathProducts as $samePathProduct) {
                if ((int)($samePathProduct['CD_IDX'] ?? 0) !== $prdIdx) {
                    throw new \InvalidArgumentException('이미 다른 상품에 사용 중인 이미지 저장소 경로입니다.');
                }
            }

            ProductModel::update(['CD_IDX' => $prdIdx], [
                'CD_IMAGE_STORAGE_PATH' => $storagePath,
            ]);
            $savedProduct = ProductModel::query()
                ->select('CD_IMAGE_STORAGE_PATH')
                ->where('CD_IDX', '=', $prdIdx)
                ->first();
            if (empty($savedProduct)) {
                throw new \RuntimeException('저장한 상품 정보를 찾을 수 없습니다.');
            }
            $savedProduct = is_array($savedProduct) ? $savedProduct : $savedProduct->toArray();
            if ((string)($savedProduct['CD_IMAGE_STORAGE_PATH'] ?? '') !== $storagePath) {
                throw new \RuntimeException('이미지 저장소 경로 저장을 확인하지 못했습니다.');
            }

            return response()->json([
                'success' => true,
                'message' => '이미지 저장소 경로를 저장했습니다.',
                'image_storage_path' => $storagePath,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * 수집 이미지를 상품별 이미지 호스팅 경로로 일괄 업로드한다.
     */
    public function uploadCollectedImagesToHosting(Request $request)
    {
        $collectionItem = null;
        try {
            $requestData = $request->all();
            $prdIdx = (int)($requestData['prd_idx'] ?? 0);
            $collectionIndex = max(0, (int)($requestData['collection_index'] ?? 0));
            if ($prdIdx < 1) {
                throw new \InvalidArgumentException('상품 번호가 올바르지 않습니다.');
            }

            $productData = $this->productService->getProductDataForAdmin($prdIdx);
            $storagePath = trim((string)($productData['CD_IMAGE_STORAGE_PATH'] ?? ''));
            if ($storagePath === '') {
                throw new \InvalidArgumentException('이미지 저장소 설정을 먼저 완료해 주세요.');
            }

            $apiData = (new ProductPartnerApiService())->getMakerProductDetails([
                'matched_product_pk' => $prdIdx,
                'page' => 1,
                'limit' => 100,
            ]);
            $sourceItem = $apiData['data']['items'][$collectionIndex] ?? [];
            if (!is_array($sourceItem)) {
                throw new \RuntimeException('업로드할 수집 상품 정보를 찾을 수 없습니다.');
            }

            $sourceImageUrls = [];
            $collectionPageUrl = trim((string)($sourceItem['source_url'] ?? ''));
            foreach ((array)($sourceItem['image_sources'] ?? []) as $imageSource) {
                $sourceUrl = is_array($imageSource) ? (string)($imageSource['full'] ?? $imageSource['src'] ?? '') : (string)$imageSource;
                if ($sourceUrl !== '') {
                    $sourceImageUrls[] = ProductImageHostingService::resolveCollectedImageUrl($sourceUrl, $collectionPageUrl);
                }
            }
            if (empty($sourceImageUrls)) {
                throw new \RuntimeException('업로드할 수집 이미지가 없습니다.');
            }

            $sourceType = 'maker';
            $siteCode = (string)($sourceItem['site_code'] ?? '');
            $sourceProductPk = (string)($sourceItem['product_pk'] ?? '');
            if ($sourceProductPk === '' && !empty($sourceItem['product_code'])) {
                $sourceProductPk = (string)$sourceItem['product_code'];
            }
            $sourceCollectedAt = is_array($sourceItem['collected_at'] ?? null)
                ? (string)($sourceItem['collected_at']['date'] ?? '')
                : (string)($sourceItem['collected_at'] ?? '');
            if ($siteCode === '' || $sourceProductPk === '') {
                throw new \RuntimeException('수집 상품 식별값이 부족합니다.');
            }

            $collectionItem = ProductCollectionItemModel::query()
                ->where('matched_product_pk', '=', $prdIdx)
                ->where('source_type', '=', $sourceType)
                ->where('source_site_code', '=', $siteCode)
                ->where('source_product_pk', '=', $sourceProductPk)
                ->where('source_collected_at', '=', $sourceCollectedAt)
                ->first();
            $collectionItem = is_array($collectionItem) ? $collectionItem : ($collectionItem ? $collectionItem->toArray() : null);

            if (!empty($collectionItem) && ($collectionItem['image_upload_status'] ?? '') === 'success') {
                return response()->json([
                    'success' => true,
                    'already_uploaded' => true,
                    'message' => '이미 이미지 호스팅에 등록된 수집 데이터입니다.',
                    'hosting_image_urls' => json_decode((string)($collectionItem['hosting_image_urls_json'] ?? '[]'), true) ?: [],
                ]);
            }

            $recordData = [
                'matched_product_pk' => $prdIdx,
                'source_type' => $sourceType,
                'source_site_code' => $siteCode,
                'source_product_pk' => $sourceProductPk,
                'source_collected_at' => $sourceCollectedAt,
                'image_storage_path' => $storagePath,
                'image_upload_status' => 'uploading',
                'source_image_urls_json' => json_encode($sourceImageUrls, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'image_total_count' => count($sourceImageUrls),
                'image_success_count' => 0,
                'image_failed_count' => 0,
                'error_message' => null,
            ];
            if (!empty($collectionItem)) {
                ProductCollectionItemModel::update(['idx' => $collectionItem['idx']], $recordData);
            } else {
                $collectionItem = ProductCollectionItemModel::create($recordData);
                $collectionItem = is_array($collectionItem) ? $collectionItem : $collectionItem->toArray();
            }

            $result = (new ProductImageHostingService())->uploadCollectionImages([
                'image_storage_path' => $storagePath,
                'site_code' => $siteCode,
                'source_image_urls' => $sourceImageUrls,
            ]);
            $updateData = [
                'image_upload_status' => $result['status'],
                'hosting_image_urls_json' => json_encode($result['hosting_urls'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'image_success_count' => $result['success_count'],
                'image_failed_count' => $result['failed_count'],
                'error_message' => $result['failed_count'] > 0
                    ? implode("\n", array_filter(array_map(static function (array $upload) {
                        return $upload['error_message'] ?? null;
                    }, $result['uploads'])))
                    : null,
            ];
            ProductCollectionItemModel::update(['idx' => $collectionItem['idx']], $updateData);
            (new AdminActionLogService())->log([
                'target_type' => 'product_collection_item',
                'target_table' => 'product_collection_item',
                'target_pk' => (string)$collectionItem['idx'],
                'action_mode' => 'image_hosting_upload',
                'action_summary' => '수집 이미지 이미지호스팅 업로드 (' . $result['success_count'] . '건 성공, ' . $result['failed_count'] . '건 실패)',
                'before_json' => [
                    'image_upload_status' => $collectionItem['image_upload_status'] ?? null,
                    'hosting_image_urls_json' => $collectionItem['hosting_image_urls_json'] ?? null,
                ],
                'after_json' => $updateData,
                'diff_json' => [
                    'image_upload_status' => [
                        'before' => $collectionItem['image_upload_status'] ?? null,
                        'after' => $result['status'],
                    ],
                ],
            ]);

            return response()->json([
                'success' => $result['status'] !== 'failed',
                'message' => $result['status'] === 'success'
                    ? '수집 이미지를 이미지 호스팅에 업로드했습니다.'
                    : '일부 이미지 업로드에 실패했습니다.',
                'data' => $result,
            ]);
        } catch (Throwable $e) {
            if (!empty($collectionItem['idx'])) {
                ProductCollectionItemModel::update(['idx' => $collectionItem['idx']], [
                    'image_upload_status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * 이미지 호스팅 URL 배열의 표시 순서를 저장한다.
     */
    public function saveHostedImageOrder(Request $request)
    {
        try {
            // URL 배열 JSON의 큰따옴표가 HTML 엔티티로 변환되지 않도록 원문을 사용한다.
            $requestData = $request->all(FILTER_UNSAFE_RAW);
            $prdIdx = (int)($requestData['prd_idx'] ?? 0);
            $collectionItemIdx = (int)($requestData['collection_item_idx'] ?? 0);
            $orderedUrls = json_decode((string)($requestData['hosting_image_urls_json'] ?? ''), true);
            if ($prdIdx < 1 || $collectionItemIdx < 1 || !is_array($orderedUrls) || empty($orderedUrls)) {
                throw new \InvalidArgumentException('이미지 순서 저장 요청이 올바르지 않습니다.');
            }
            $orderedUrls = array_values(array_filter($orderedUrls, 'is_string'));

            $collectionItem = ProductCollectionItemModel::query()
                ->where('idx', '=', $collectionItemIdx)
                ->where('matched_product_pk', '=', $prdIdx)
                ->first();
            if (empty($collectionItem)) {
                throw new \RuntimeException('이미지 호스팅 수집 정보를 찾을 수 없습니다.');
            }
            $collectionItem = is_array($collectionItem) ? $collectionItem : $collectionItem->toArray();
            $savedUrls = json_decode((string)($collectionItem['hosting_image_urls_json'] ?? '[]'), true);
            $savedUrls = is_array($savedUrls) ? array_values(array_filter($savedUrls, 'is_string')) : [];
            sort($savedUrls);
            $verificationUrls = $orderedUrls;
            sort($verificationUrls);
            if ($savedUrls !== $verificationUrls) {
                throw new \InvalidArgumentException('등록된 이미지 URL만 순서를 변경할 수 있습니다.');
            }

            ProductCollectionItemModel::update(['idx' => $collectionItemIdx], [
                'hosting_image_urls_json' => json_encode($orderedUrls, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ]);

            return response()->json([
                'success' => true,
                'message' => '이미지 순서를 저장했습니다.',
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * 수집 데이터의 번역본을 저장한다.
     */
    public function saveCollectionTranslation(Request $request)
    {
        try {
            $requestData = $request->all();
            $prdIdx = (int)($requestData['prd_idx'] ?? 0);
            $collectionItemIdx = (int)($requestData['collection_item_idx'] ?? 0);
            $field = (string)($requestData['field'] ?? '');
            $translation = trim((string)($requestData['translation'] ?? ''));
            $imageKey = trim((string)($requestData['image_key'] ?? ''));
            $sourceAlt = trim((string)($requestData['source_alt'] ?? ''));
            $fieldMap = [
                'accessories' => 'translated_accessories',
                'maker_comment' => 'translated_maker_comment',
                'seller_comment' => 'translated_seller_comment',
                'image_alt' => 'translated_image_alts_json',
            ];
            $fieldLabels = [
                'accessories' => '부속품',
                'maker_comment' => '메이커 코멘트',
                'seller_comment' => '판매사 코멘트',
                'image_alt' => '이미지 설명',
            ];
            if ($prdIdx < 1 || $collectionItemIdx < 1 || !isset($fieldMap[$field])) {
                throw new \InvalidArgumentException('번역 저장 요청이 올바르지 않습니다.');
            }
            if ($field === 'image_alt') {
                $normalizedImageKey = ProductImageHostingService::collectedImageTranslationKey($imageKey);
                if ($normalizedImageKey !== '') {
                    $imageKey = $normalizedImageKey;
                }
            }
            if ($field === 'image_alt' && $imageKey === '') {
                throw new \InvalidArgumentException('이미지 번역 키가 없습니다.');
            }
            if ($translation === '') {
                throw new \InvalidArgumentException('번역 내용을 입력해 주세요.');
            }

            $collectionItem = ProductCollectionItemModel::query()
                ->where('idx', '=', $collectionItemIdx)
                ->where('matched_product_pk', '=', $prdIdx)
                ->first();
            if (empty($collectionItem)) {
                throw new \RuntimeException('매칭된 수집 데이터를 찾을 수 없습니다.');
            }
            $collectionItem = is_array($collectionItem) ? $collectionItem : $collectionItem->toArray();

            $updateData = [
                'translation_updated_at' => date('Y-m-d H:i:s'),
            ];
            $beforeValue = $collectionItem[$fieldMap[$field]] ?? null;
            $afterValue = $translation;
            if ($field === 'image_alt') {
                $imageAltMap = json_decode((string)($collectionItem['translated_image_alts_json'] ?? '{}'), true);
                if (!is_array($imageAltMap)) {
                    $imageAltMap = [];
                }
                $beforeEntry = $imageAltMap[$imageKey] ?? null;
                $imageAltMap[$imageKey] = [
                    'source_alt' => $sourceAlt,
                    'translated_alt' => $translation,
                ];
                $updateData['translated_image_alts_json'] = json_encode($imageAltMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $beforeValue = $beforeEntry;
                $afterValue = $imageAltMap[$imageKey];
            } else {
                $updateData[$fieldMap[$field]] = $translation;
            }

            ProductCollectionItemModel::update(['idx' => $collectionItemIdx], $updateData);
            (new AdminActionLogService())->log([
                'target_type' => 'product_collection_item',
                'target_table' => 'product_collection_item',
                'target_pk' => (string)$collectionItemIdx,
                'action_mode' => 'translation_' . $field,
                'action_summary' => ($fieldLabels[$field] ?? $field) . ' 번역 저장',
                'before_json' => [$fieldMap[$field] => $beforeValue],
                'after_json' => [$fieldMap[$field] => $afterValue],
                'diff_json' => [$fieldMap[$field] => [
                    'before' => $beforeValue,
                    'after' => $afterValue,
                    'image_key' => $field === 'image_alt' ? $imageKey : null,
                ]],
            ]);

            return response()->json([
                'success' => true,
                'message' => '번역 데이터를 저장했습니다.',
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }


    /**
     * 수집 데이터를 상품 DB에 일괄 반영
     */
    public function applyCollectedProductFields(Request $request)
    {
        try {
            $prdIdx = (int)($_POST['prd_idx'] ?? $request->input('prd_idx') ?? 0);
            $siteCode = strtolower(trim((string)($_POST['site_code'] ?? $request->input('site_code') ?? '')));
            $fieldsRaw = $_POST['fields'] ?? [];
            $fields = is_array($fieldsRaw) ? $fieldsRaw : json_decode((string)$fieldsRaw, true);
            if (!is_array($fields)) {
                $fields = json_decode(html_entity_decode((string)$fieldsRaw, ENT_QUOTES, 'UTF-8'), true);
            }
            $fieldMap = [
                'cd_name_og' => 'CD_NAME_OG',
                'cd_code' => 'CD_CODE',
                'cd_code2' => 'CD_CODE2',
                'cd_size' => 'CD_SIZE',
                'cd_weight_1' => 'cd_weight_fn',
                'cd_weight_2' => 'cd_weight_fn',
                'cd_spec_product_size' => 'cd_spec',
                'cd_spec_inner_length_vagina' => 'cd_spec',
                'cd_spec_inner_length_anal' => 'cd_spec',
                'cd_spec_material' => 'cd_spec',
                'cd_release_date' => 'CD_RELEASE_DATE',
            ];
            $fieldLabels = [
                'cd_name_og' => '원상품명',
                'cd_code' => '바코드',
                'cd_code2' => '상품 품번',
                'cd_size' => '패키지 사이즈',
                'cd_weight_1' => '상품중량',
                'cd_weight_2' => '전체중량',
                'cd_spec_product_size' => '상품 사이즈',
                'cd_spec_inner_length_vagina' => '내부길이 (질)',
                'cd_spec_inner_length_anal' => '내부길이 (애널)',
                'cd_spec_material' => '소재',
                'cd_release_date' => '출시일',
            ];
            if ($prdIdx < 1 || !is_array($fields) || empty($fields)) {
                throw new \InvalidArgumentException('업데이트할 항목을 선택해 주세요.');
            }

            $product = ProductModel::find($prdIdx);
            if (empty($product)) {
                throw new \RuntimeException('상품을 찾을 수 없습니다.');
            }
            $product = is_array($product) ? $product : $product->toArray();

            $updateData = [];
            $before = [];
            $after = [];
            $labels = [];
            foreach ($fields as $fieldRow) {
                if (!is_array($fieldRow)) {
                    continue;
                }
                $field = trim((string)($fieldRow['field'] ?? ''));
                if (!isset($fieldMap[$field])) {
                    continue;
                }
                $column = $fieldMap[$field];
                $value = html_entity_decode(trim((string)($fieldRow['value'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                if ($value === '') {
                    continue;
                }
                if ($field === 'cd_code') {
                    $codeFn = $product['cd_code_fn'] ?? [];
                    if (is_string($codeFn)) {
                        $decodedCodeFn = json_decode($codeFn, true);
                        $codeFn = is_array($decodedCodeFn) ? $decodedCodeFn : [];
                    }
                    if (!is_array($codeFn)) {
                        $codeFn = [];
                    }
                    $codeFn['jan'] = $value;
                    $encodedCodeFn = json_encode($codeFn, JSON_UNESCAPED_UNICODE);
                    $updateData['cd_code_fn'] = $encodedCodeFn;
                    $before['cd_code_fn'] = is_array($product['cd_code_fn'] ?? null)
                        ? json_encode($product['cd_code_fn'], JSON_UNESCAPED_UNICODE)
                        : (string)($product['cd_code_fn'] ?? '');
                    $after['cd_code_fn'] = $encodedCodeFn;
                } elseif ($field === 'cd_size') {
                    $sizeData = json_decode($value, true);
                    if (!is_array($sizeData)) {
                        continue;
                    }
                    $value = json_encode([
                        'W' => trim((string)($sizeData['W'] ?? '')),
                        'H' => trim((string)($sizeData['H'] ?? '')),
                        'D' => trim((string)($sizeData['D'] ?? '')),
                    ], JSON_UNESCAPED_UNICODE);
                } elseif ($field === 'cd_weight_1' || $field === 'cd_weight_2') {
                    $weightKey = $field === 'cd_weight_1' ? '1' : '2';
                    if (isset($updateData['cd_weight_fn'])) {
                        $decodedWeightFn = json_decode((string)$updateData['cd_weight_fn'], true);
                        $weightFn = is_array($decodedWeightFn) ? $decodedWeightFn : [];
                    } else {
                        $weightFn = $product['cd_weight_fn'] ?? [];
                        if (is_string($weightFn)) {
                            $decodedWeightFn = json_decode($weightFn, true);
                            $weightFn = is_array($decodedWeightFn) ? $decodedWeightFn : [];
                        }
                    }
                    if (!is_array($weightFn)) {
                        $weightFn = [];
                    }
                    $weightFn[$weightKey] = $value;
                    $productWeightValue = $value;
                    $value = json_encode([
                        '1' => (string)($weightFn['1'] ?? ''),
                        '2' => (string)($weightFn['2'] ?? ''),
                        '3' => (string)($weightFn['3'] ?? ''),
                        '4' => (string)($weightFn['4'] ?? ''),
                    ], JSON_UNESCAPED_UNICODE);
                    if ($field === 'cd_weight_1') {
                        $categoryCode = (string)($product['CD_CATEGORY_CODE'] ?? $product['CD_KIND_CODE'] ?? '');
                        $weightSchema = (new ProductSpecService())->getSchema($categoryCode);
                        if (isset($weightSchema['fields']['weight'])) {
                            $specWeightUnit = strtolower(trim((string)($weightSchema['fields']['weight'][1] ?? '')));
                            $specWeightValue = $productWeightValue;
                            if ($specWeightValue !== '' && ($specWeightUnit === 'kg' || str_starts_with($specWeightUnit, 'kg'))) {
                                $specWeightValue = rtrim(rtrim(sprintf('%.4F', (float)$specWeightValue / 1000), '0'), '.');
                            }
                            if (isset($updateData['cd_spec'])) {
                                $decodedSpec = json_decode((string)$updateData['cd_spec'], true);
                                $specData = is_array($decodedSpec) ? $decodedSpec : [];
                            } else {
                                $specData = $product['cd_spec'] ?? [];
                                if (is_string($specData)) {
                                    $decodedSpec = json_decode($specData, true);
                                    $specData = is_array($decodedSpec) ? $decodedSpec : [];
                                }
                            }
                            if (!is_array($specData)) {
                                $specData = [];
                            }
                            $vendorSize = (isset($specData['vendor_size']) && is_array($specData['vendor_size']))
                                ? $specData['vendor_size']
                                : [];
                            $vendorSize['weight'] = $specWeightValue;
                            $specData['vendor_size'] = $vendorSize;
                            if (!isset($specData['measured_size']) || !is_array($specData['measured_size'])) {
                                $specData['measured_size'] = [];
                            }
                            if (!isset($specData['options']) || !is_array($specData['options'])) {
                                $specData['options'] = [];
                            }
                            if (trim((string)($specData['category_code'] ?? '')) === '') {
                                $specData['category_code'] = $categoryCode;
                            }
                            $encodedSpec = json_encode($specData, JSON_UNESCAPED_UNICODE);
                            $updateData['cd_spec'] = $encodedSpec;
                            $before['cd_spec'] = is_array($product['cd_spec'] ?? null)
                                ? json_encode($product['cd_spec'], JSON_UNESCAPED_UNICODE)
                                : (string)($product['cd_spec'] ?? '');
                            $after['cd_spec'] = $encodedSpec;
                        }
                    }
                } elseif (in_array($field, ['cd_spec_product_size', 'cd_spec_inner_length_vagina', 'cd_spec_inner_length_anal', 'cd_spec_material'], true)) {
                    $vendorValues = [];
                    if ($field === 'cd_spec_product_size') {
                        $sizeData = json_decode($value, true);
                        if (!is_array($sizeData)) {
                            continue;
                        }
                        foreach (['length', 'height', 'width'] as $sizeKey) {
                            $sizeValue = trim((string)($sizeData[$sizeKey] ?? ''));
                            if ($sizeValue !== '') {
                                $vendorValues[$sizeKey] = $sizeValue;
                            }
                        }
                    } elseif ($field === 'cd_spec_inner_length_vagina') {
                        $vendorValues['inner_length_vagina'] = $value;
                    } elseif ($field === 'cd_spec_inner_length_anal') {
                        $vendorValues['inner_length_anal'] = $value;
                    } else {
                        $vendorValues['material'] = (new ProductSpecService())->mapCollectedMaterial($value);
                    }
                    if ($vendorValues === []) {
                        continue;
                    }
                    if (isset($updateData['cd_spec'])) {
                        $decodedSpec = json_decode((string)$updateData['cd_spec'], true);
                        $specData = is_array($decodedSpec) ? $decodedSpec : [];
                    } else {
                        $specData = $product['cd_spec'] ?? [];
                        if (is_string($specData)) {
                            $decodedSpec = json_decode($specData, true);
                            $specData = is_array($decodedSpec) ? $decodedSpec : [];
                        }
                    }
                    if (!is_array($specData)) {
                        $specData = [];
                    }
                    $vendorSize = (isset($specData['vendor_size']) && is_array($specData['vendor_size']))
                        ? $specData['vendor_size']
                        : [];
                    foreach ($vendorValues as $sizeKey => $sizeValue) {
                        $vendorSize[$sizeKey] = $sizeValue;
                    }
                    $specData['vendor_size'] = $vendorSize;
                    if (!isset($specData['measured_size']) || !is_array($specData['measured_size'])) {
                        $specData['measured_size'] = [];
                    }
                    if (!isset($specData['options']) || !is_array($specData['options'])) {
                        $specData['options'] = [];
                    }
                    if (trim((string)($specData['category_code'] ?? '')) === '') {
                        $specData['category_code'] = (string)($product['CD_CATEGORY_CODE'] ?? $product['CD_KIND_CODE'] ?? '');
                    }
                    $value = json_encode($specData, JSON_UNESCAPED_UNICODE);
                    if ($field === 'cd_spec_inner_length_vagina') {
                        $categoryCode = (string)($product['CD_CATEGORY_CODE'] ?? $product['CD_KIND_CODE'] ?? '');
                        if ((new ProductSpecService())->getSpecType($categoryCode) === '01000000') {
                            $updateData['CD_SIZE2'] = $vendorValues['inner_length_vagina'];
                            $before['CD_SIZE2'] = (string)($product['CD_SIZE2'] ?? '');
                            $after['CD_SIZE2'] = $vendorValues['inner_length_vagina'];
                        }
                    }
                } elseif ($field === 'cd_release_date') {
                    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                        continue;
                    }
                }
                $updateData[$column] = $value;
                $before[$column] = is_array($product[$column] ?? null)
                    ? json_encode($product[$column], JSON_UNESCAPED_UNICODE)
                    : (string)($product[$column] ?? '');
                $after[$column] = $value;
                $labels[] = $fieldLabels[$field] ?? $field;
            }
            if (empty($updateData)) {
                throw new \InvalidArgumentException('반영할 수집값이 없습니다.');
            }

            $updateData['cd_update_time'] = date('Y-m-d H:i:s');
            ProductModel::update(['CD_IDX' => $prdIdx], $updateData);
            $siteCodeNames = [
                'npg' => 'NPG 주문사이트',
                'tamatoys' => '타마토이즈 본사사이트',
                'mzakka' => '엠자카',
                'nobunaga' => '노부나가',
                'nls' => 'NLS 사이트',
                'ms' => '엠즈',
                'tis' => 'TIS',
                'ridejapan' => '라이드재팬',
                'yelolab' => '옐로랩',
            ];
            $siteName = $siteCodeNames[$siteCode] ?? '';
            $siteLabel = $siteCode === ''
                ? ''
                : ($siteName !== '' ? $siteName . ' (' . $siteCode . ')' : $siteCode);
            $actionSummary = '수집 정보 일괄 업데이트';
            if ($siteLabel !== '') {
                $actionSummary .= ' [' . $siteLabel . ']';
            }
            $actionSummary .= ' (' . implode(', ', $labels) . ')';
            (new AdminActionLogService())->log([
                'target_type' => 'product',
                'target_table' => 'COMPARISON_DB',
                'target_pk' => (string)$prdIdx,
                'action_mode' => 'collection_field_apply',
                'action_summary' => $actionSummary,
                'before_json' => $before,
                'after_json' => $after,
                'diff_json' => (new AdminActionLogService())->buildDiff($before, $after),
            ]);

            return response()->json([
                'success' => true,
                'message' => implode(', ', $labels) . '을(를) 상품에 반영했습니다.',
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
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
            $configProduct = config('admin.product');
            $purchaseTypeOptions = $configProduct['purchase_type_options'] ?? [];

            //dump($productData);

            $data = [
                'productData' => $productData,
                'purchase_type_options' => $purchaseTypeOptions,
            ];

            return view('admin.product.prd_detail_price', $data);

        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
        
    }


    /**
     * 상품 디테일 (고도몰 검수 처리)
     */
    public function prdDetailGodoInspection(Request $request)
    {
        try{

            $requestData = $request->all();
            $prdIdx = (int)($requestData['prd_idx'] ?? 0);
            $psIdx = (int)($requestData['ps_idx'] ?? 0);

            $inspectionData = $this->productService->getSingleProductGodoInspectionData($prdIdx, $psIdx);

            $godoInspectionService = new GodoInspectionService();
            $inspectionContext = $godoInspectionService->buildInspectionContext(
                (array)($inspectionData['item'] ?? []),
                GodoInspectionService::CONTEXT_PRODUCT_SINGLE
            );
            $inspectionVersion = $godoInspectionService->getInspectionVersion();
            $inspectionProcessLogService = new InspectionProcessLogService();
            $inspectionHistoryRows = $inspectionProcessLogService->getHistoryByPrdIdx($prdIdx, 30);

            $data = [
                'prd_idx' => $prdIdx,
                'inspectionVersion' => $inspectionVersion,
                'item' => $inspectionData['item'] ?? [],
                'inspectionContext' => $inspectionContext,
                'inspectionHistoryRows' => $inspectionHistoryRows,
                'godoApiErrorMessage' => $inspectionData['godoApiErrorMessage'] ?? '',
                'godoInfoLoadedAt' => $inspectionData['godoInfoLoadedAt'] ?? '',
                'godoInfoLoadMs' => $inspectionData['godoInfoLoadMs'] ?? 0,
            ];

            return view('admin.product.prd_detail_godo_inspection', $data);

        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }


    /**
     * 상품 디테일 (시리즈/연관그룹 관리)
     */
    public function prdDetailRelationGroupPage(Request $request)
    {
        try {
            $requestData = $request->all();
            $prdIdx = (int)($requestData['prd_idx'] ?? 0);
            $relationGroupData = $this->productService->getProductRelationGroupData($prdIdx);

            return view('admin.product.prd_detail_relation_group', [
                'prd_idx' => $prdIdx,
                'relation_group_data' => $relationGroupData,
            ]);
        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }


    /**
     * 상품 디테일 (상품 스펙정보)
     */
    public function prdDetailSpecInfoPage(Request $request)
    {
        try {
            $prdIdx = (int)($request->input('prd_idx') ?? 0);
            $pageData = (new ProductSpecInfoService())->getPageData($prdIdx);

            return view('admin.product.prd_detail_spec_info', $pageData);
        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }


    /**
     * 상품 스펙정보 단면도 업로드
     */
    public function uploadProductSpecInfoImage(Request $request)
    {
        try {
            $prdIdx = (int)($request->input('prd_idx') ?? 0);
            if (!$request->hasFile('spec_image')) {
                throw new Exception('이미지 파일이 없습니다.');
            }

            $spec = (new ProductSpecInfoService())->uploadImage(
                $prdIdx,
                $request->file('spec_image'),
                [
                    'idx' => AuthAdmin::getSession('sess_idx'),
                    'name' => AuthAdmin::getSession('sess_name'),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => '단면도를 업로드했습니다.',
                'data' => $spec,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }


    /**
     * 상품 스펙정보 외부 이미지 미리보기 (서버 저장 없음)
     */
    public function previewProductSpecInfoImage(Request $request)
    {
        try {
            $imageUrl = trim((string)($_GET['url'] ?? ''));
            if ($imageUrl === '') {
                $imageUrl = html_entity_decode((string)($request->input('url') ?? ''), ENT_QUOTES, 'UTF-8');
            }

            $image = (new ProductSpecInfoService())->previewRemoteImage($imageUrl);
            header('Content-Type: ' . $image['content_type']);
            header('Cache-Control: private, max-age=300');
            return $image['body'];
        } catch (Throwable $e) {
            http_response_code(400);
            return $e->getMessage();
        }
    }


    /**
     * 상품 스펙정보 측정값 저장
     */
    public function saveProductSpecInfo(Request $request)
    {
        try {
            $prdIdx = (int)($request->input('prd_idx') ?? 0);
            $itemsRaw = $_POST['psi_items'] ?? '';
            $items = is_array($itemsRaw) ? $itemsRaw : json_decode((string)$itemsRaw, true);
            if (!is_array($items)) {
                $items = json_decode(html_entity_decode((string)$itemsRaw, ENT_QUOTES, 'UTF-8'), true);
            }

            $imageUrl = trim((string)($_POST['image_url'] ?? ''));
            if ($imageUrl === '') {
                $imageUrl = html_entity_decode((string)($request->input('image_url') ?? ''), ENT_QUOTES, 'UTF-8');
            }

            $spec = (new ProductSpecInfoService())->save(
                $prdIdx,
                [
                    'psi_total_length_cm' => $_POST['psi_total_length_cm'] ?? $request->input('psi_total_length_cm'),
                    'psi_items' => is_array($items) ? $items : [],
                    'image_url' => $imageUrl,
                    'rotated_file' => $request->hasFile('rotated_image') ? $request->file('rotated_image') : [],
                ],
                [
                    'idx' => AuthAdmin::getSession('sess_idx'),
                    'name' => AuthAdmin::getSession('sess_name'),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => '측정값을 저장했습니다.',
                'data' => $spec,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }


    /**
     * 상품 디테일 (상품 컨텐츠 관리)
     */
    public function prdDetailContentPage(Request $request)
    {
        try {
            $prdPk = (int)($request->input('prd_idx') ?? $request->input('prd_pk') ?? 0);
            $pageData = (new ProductDetailContentService())->getPageData($prdPk);

            return view('admin.product.prd_detail_content', $pageData);
        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }


    /**
     * 상품 상세페이지 컨텐츠 저장
     */
    public function saveProductDetailContent(Request $request)
    {
        try {
            $prdPk = (int)($_POST['prd_pk'] ?? $_POST['prd_idx'] ?? $request->input('prd_pk') ?? $request->input('prd_idx') ?? 0);
            $summaryPoints = $this->decodePostedJsonList($_POST['summary_points'] ?? []);
            $specs = $this->decodePostedJsonList($_POST['specs'] ?? []);
            $saveMode = trim((string)($_POST['save_mode'] ?? $request->input('save_mode') ?? 'version'));
            $keepVersion = $saveMode === 'draft';

            $content = (new ProductDetailContentService())->save(
                $prdPk,
                [
                    'original_name' => $_POST['original_name'] ?? '',
                    'korean_name' => $_POST['korean_name'] ?? '',
                    'list_summary' => $_POST['list_summary'] ?? '',
                    'title' => $_POST['title'] ?? '',
                    'maker_comment' => $_POST['maker_comment'] ?? '',
                    'md_comment' => $_POST['md_comment'] ?? '',
                    'summary_points' => $summaryPoints,
                    'specs' => $specs,
                    'save_mode' => $keepVersion ? 'draft' : 'version',
                ],
                [
                    'idx' => AuthAdmin::getSession('sess_idx'),
                    'name' => AuthAdmin::getSession('sess_name'),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => $keepVersion
                    ? '임시저장했습니다. 배포버전은 그대로입니다.'
                    : '새 배포버전으로 저장했습니다.',
                'data' => $content,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }


    /**
     * 현재 저장된 상품 컨텐츠를 고도몰에 실배포
     */
    public function deployProductDetailContent(Request $request)
    {
        try {
            $prdPk = (int)($_POST['prd_pk'] ?? $_POST['prd_idx'] ?? $request->input('prd_pk') ?? $request->input('prd_idx') ?? 0);
            $result = (new ProductDetailContentService())->deployToGodo(
                $prdPk,
                [
                    'idx' => AuthAdmin::getSession('sess_idx'),
                    'name' => AuthAdmin::getSession('sess_name'),
                ]
            );

            if (empty($result['ok'])) {
                return response()->json([
                    'success' => false,
                    'message' => (string)($result['message'] ?? '고도몰 배포에 실패했습니다.'),
                    'stage' => (string)($result['stage'] ?? ''),
                    'error_code' => (string)($result['error_code'] ?? ''),
                    'debug' => $result['debug'] ?? [],
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? '고도몰에 배포했습니다.',
                'data' => $result,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => "단계: 서버 처리\n코드: SERVER_ERROR\n" . $e->getMessage(),
                'stage' => 'server',
                'error_code' => 'SERVER_ERROR',
                'debug' => [
                    'exception' => get_class($e),
                    'file' => basename($e->getFile()) . ':' . $e->getLine(),
                ],
            ], 400);
        }
    }


    /**
     * 상품 컨텐츠 스펙 추천값 생성
     */
    public function recommendProductDetailSpecs(Request $request)
    {
        try {
            $prdPk = (int)($request->input('prd_pk') ?? $request->input('prd_idx') ?? 0);
            $result = (new ProductDetailContentService())->getRecommendedSpecs($prdPk);

            return response()->json([
                'success' => !empty($result['supported']),
                'message' => $result['message'] ?? '추천값을 생성했습니다.',
                'data' => $result,
            ], !empty($result['supported']) ? 200 : 400);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }


    /**
     * POST JSON 배열 파싱
     */
    private function decodePostedJsonList($raw): array
    {
        if (is_array($raw)) {
            return $raw;
        }
        $decoded = json_decode((string)$raw, true);
        if (!is_array($decoded)) {
            $decoded = json_decode(html_entity_decode((string)$raw, ENT_QUOTES, 'UTF-8'), true);
        }
        return is_array($decoded) ? $decoded : [];
    }


    /**
     * 시리즈/연관그룹 관리 목록.
     */
    public function relationGroupManagementList(Request $request)
    {
        try {
            $requestData = $request->all();
            $mode = trim((string)($requestData['s_mode'] ?? ''));
            $useYn = trim((string)($requestData['s_use_yn'] ?? ''));
            $brandIdx = (int)($requestData['s_brand_idx'] ?? 0);
            $searchValue = trim((string)($requestData['search_value'] ?? ''));
            $page = max(1, (int)($requestData['page'] ?? 1));

            $listData = $this->productService->getProductRelationGroupManagementList([
                'mode' => $mode,
                'use_yn' => $useYn,
                'brand_idx' => $brandIdx,
                'search_value' => $searchValue,
                'page' => $page,
                'per_page' => 100,
            ]);
            $pagination = new Pagination(
                (int)($listData['total'] ?? 0),
                (int)($listData['per_page'] ?? 100),
                (int)($listData['current_page'] ?? $page),
                10
            );

            return view('admin.product.relation_group_management', [
                's_mode' => $mode,
                's_use_yn' => $useYn,
                's_brand_idx' => $brandIdx,
                'search_value' => $searchValue,
                'relationGroupList' => $listData['data'] ?? [],
                'brandOptions' => $this->productService->getProductRelationGroupBrandOptions(),
                'pagination' => $pagination->toArray(),
                'paginationHtml' => $pagination->renderLinks(),
            ])->extends('admin.layout.layout', [
                'pageGroup2' => 'prd',
                'pageNameCode' => 'product_relation_group_list',
            ]);
        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }


    /**
     * 상품 할인 로그 목록 화면
     */
    public function prdDetailSaleLogPage(Request $request)
    {

        try {

            $requestData = $request->all();
            $prdIdx = $requestData['prd_idx'] ?? null;
            $prdMode = $requestData['prd_mode'] ?? 'prdDB';

            $productStockSaleLogService = new ProductStockSaleLogService();
            $saleLogPageData = $productStockSaleLogService->getSaleLogPageData($prdIdx);
            $recentSaleLog = $productStockSaleLogService->getRecentSaleLogByPrdIdx($prdIdx);

            $productData = [];
            if ($prdMode === 'prdDB') {
                $productService = new ProductService();
                $productData = $productService->getProductDataForAdmin($prdIdx);
            }

            $data = [
                'prd_idx' => $prdIdx,
                'prd_mode' => $prdMode,
                'productData' => $productData,
                'saleLogRows' => $saleLogPageData['rows'] ?? [],
                'recentSaleLog' => $recentSaleLog,
            ];

            return view('admin.product.prd_detail_sale_log', $data);

        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }

    /**
     * 상품 디테일 (판매량/발주 요약)
     */
    public function prdDetailStockChartPage(Request $request)
    {
        try {
            $requestData = $request->all();
            $prdIdx = (int)($requestData['prd_idx'] ?? 0);
            $psIdx = (int)($requestData['ps_idx'] ?? 0);
            $showMode = trim((string)($requestData['show_mode'] ?? '연간통계'));

            if ($prdIdx <= 0 || $psIdx <= 0) {
                throw new Exception('상품 정보가 올바르지 않습니다.');
            }

            $productStockService = new ProductStockService();
            $data = $productStockService->getStockChartPageData(
                $prdIdx,
                $psIdx,
                $showMode,
                $requestData['cur_y'] ?? null,
                $requestData['cur_m'] ?? null
            );

            return view('admin.product.prd_detail_stock_chart', $data);
        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }

    /**
     * 상품 디테일 (경쟁사 판매현황)
     */
    public function prdDetailCompetitorProductPage(Request $request)
    {
        try {
            $requestData = $request->all();
            
            $prdIdx = (int)($requestData['prd_idx'] ?? 0);
            if ($prdIdx <= 0) {
                throw new Exception('prd_idx가 올바르지 않습니다.');
            }

            $productService = new ProductService();
            $productData = $productService->getProductDataForAdmin($prdIdx);

            $competitorApiService = new CompetitorApiService();
            $rowMatchesProduct = function (array $row, int $targetCdIdx): bool {
                $legacyMatchIdx = (int)($row['match_idx'] ?? 0);
                if ($legacyMatchIdx === $targetCdIdx) {
                    return true;
                }
                $primaryMatchIdx = (int)($row['primary_match_idx'] ?? 0);
                if ($primaryMatchIdx === $targetCdIdx) {
                    return true;
                }
                $matchedItems = $row['matched_items'] ?? [];
                if (!is_array($matchedItems)) {
                    return false;
                }
                foreach ($matchedItems as $matchedItem) {
                    if (!is_array($matchedItem)) {
                        continue;
                    }
                    if ((int)($matchedItem['cd_idx'] ?? 0) === $targetCdIdx) {
                        return true;
                    }
                }
                return false;
            };

            $competitorApiData = $competitorApiService->getCompetitorProducts([
                'match_idx' => $prdIdx,
                'sort_mode' => 'price_asc',
                'page' => 1,
                'limit' => 200,
            ]);

            $rows = $competitorApiData['data']['competitorProducts'] ?? [];
            if (!is_array($rows)) {
                $rows = [];
            }

            $apiStatus = strtolower(trim((string)($competitorApiData['status'] ?? '')));
            $apiMessage = (string)($competitorApiData['message'] ?? '');
            $hasMatchIdxCollationError = (
                $apiStatus === 'error'
                && stripos($apiMessage, '1267') !== false
                && stripos($apiMessage, 'collation') !== false
            );

            // 임시 우회: 운영 API에서 match_idx 조건 시 collation 오류가 발생하면,
            // 사이트별 전체 목록을 페이지 순회 조회 후 로컬에서 매칭 필터링한다.
            if ($hasMatchIdxCollationError) {
                $rows = [];
                $rowsByKey = [];
                $scanLimit = 200;
                $maxScanPagePerSite = 60;
                $scanSites = ['oname', 'freebody', 'bananamall', 'rmax', 'dingdong', 'vavoomshop'];

                foreach ($scanSites as $scanSite) {
                    $scanPage = 1;
                    $lastPage = 1;
                    while ($scanPage <= $lastPage && $scanPage <= $maxScanPagePerSite) {
                        $scanApiData = $competitorApiService->getCompetitorProducts([
                            'site' => $scanSite,
                            'sort_mode' => 'updated_at',
                            'page' => $scanPage,
                            'limit' => $scanLimit,
                        ]);
                        $scanRows = $scanApiData['data']['competitorProducts'] ?? [];
                        if (!is_array($scanRows) || empty($scanRows)) {
                            break;
                        }

                        foreach ($scanRows as $scanRow) {
                            if (!is_array($scanRow)) {
                                continue;
                            }
                            if (!$rowMatchesProduct($scanRow, $prdIdx)) {
                                continue;
                            }
                            $rowKey = (string)($scanRow['site'] ?? '') . '::' . (string)($scanRow['prd_pk'] ?? '');
                            $rowsByKey[$rowKey] = $scanRow;
                        }

                        $pagination = $scanApiData['data']['pagination'] ?? [];
                        $reportedLastPage = (int)($pagination['last_page'] ?? 0);
                        if ($reportedLastPage > 0) {
                            $lastPage = $reportedLastPage;
                        } else if (count($scanRows) < $scanLimit) {
                            $lastPage = $scanPage;
                        } else {
                            $lastPage = max($lastPage, $scanPage + 1);
                        }
                        $scanPage++;
                    }
                }

                $rows = array_values($rowsByKey);
            }
            if (!empty($rows)) {
                usort($rows, function ($a, $b) {
                    $priceA = (int)($a['price'] ?? 0);
                    $priceB = (int)($b['price'] ?? 0);
                    if ($priceA !== $priceB) {
                        return $priceA <=> $priceB;
                    }

                    $timeA = strtotime((string)($a['updated_at'] ?? '')) ?: 0;
                    $timeB = strtotime((string)($b['updated_at'] ?? '')) ?: 0;
                    if ($timeA !== $timeB) {
                        return $timeB <=> $timeA;
                    }

                    $siteA = (string)($a['site'] ?? '');
                    $siteB = (string)($b['site'] ?? '');
                    if ($siteA !== $siteB) {
                        return strcmp($siteA, $siteB);
                    }

                    $prdPkA = (int)($a['prd_pk'] ?? 0);
                    $prdPkB = (int)($b['prd_pk'] ?? 0);
                    return $prdPkB <=> $prdPkA;
                });
            }

            foreach ($rows as &$row) {
                if (!is_array($row)) {
                    continue;
                }
                $eventTags = $row['event_tags_json'] ?? [];
                if (is_string($eventTags)) {
                    $eventTags = json_decode($eventTags, true);
                }
                if (!is_array($eventTags)) {
                    $eventTags = [];
                }
                $row['event_tags_json'] = array_values(array_filter($eventTags, static function ($eventTag) {
                    return is_string($eventTag) && trim($eventTag) !== '';
                }));
            }
            unset($row);

            $configCompetitor = config('admin.competitor');
            $competitor_data = $configCompetitor['competitor_data'] ?? [];

            return view('admin.product.prd_detail_competitor_product', [
                'prd_idx' => $prdIdx,
                'productData' => $productData,
                'competitor_data' => $competitor_data,
                'rows' => $rows,
            ]);
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
     * 상품 매입정보 저장 + 고도몰 가격 업데이트
     */
    public function saveProductPriceAndGodoUpdate(Request $request)
    {
        try{

            $requestData = $request->all();

            $productService = new ProductService();
            $result = $productService->saveProductPriceAndGodoUpdate($requestData);

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
     * 상품 할인 로그 저장
     */
    public function saveProductSaleLog(Request $request)
    {
        try{
            $requestData = $request->all();
            $productStockSaleLogService = new ProductStockSaleLogService();
            $result = $productStockSaleLogService->updateRecentSaleDate($requestData);

            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? '저장되었습니다.',
                'data' => $result,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
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

                case 'set_godo_product_discontinued':
                    $result = $this->productService->setGodoProductDiscontinued($requestData);
                    break;

                case 'set_godo_product_handling_stopped':
                    $result = $this->productService->setGodoProductHandlingStopped($requestData);
                    break;

                case 'set_godo_product_special_discount':
                    $result = $this->productService->setGodoProductSpecialDiscount($requestData);
                    break;

                case 'unset_godo_product_special_discount':
                    $result = $this->productService->unsetGodoProductSpecialDiscount($requestData);
                    break;

                case 'unset_product_discontinued':
                    $result = $this->productService->unsetProductDiscontinued($requestData);
                    break;

                case 'set_product_handling_stopped':
                    $result = $this->productService->setProductHandlingStopped($requestData);
                    break;

                case 'unset_product_handling_stopped':
                    $result = $this->productService->unsetProductHandlingStopped($requestData);
                    break;

                case 'process_single_godo_inspection':
                    $result = $this->productService->processSingleProductGodoInspection($requestData);
                    break;
                
                case 'sync_godo_restock_alert_count':
                    $productActionService = new ProductActionService();
                    $result = $productActionService->syncGodoRestockAlertCount($requestData);
                    break;

                case 'update_product_category':
                    $result = $this->productService->updateProductCategory($requestData);
                    break;

                case 'update_product_memo2':
                    $result = $this->productService->updateProductMemo2($requestData);
                    break;

                case 'update_product_sale_status':
                    $result = $this->productService->updateProductSaleStatus($requestData);
                    break;

                case 'copy_product':
                    $result = $this->productService->copyProduct($requestData);
                    break;

                case 'bulk_update_product_fields':
                    $result = $this->productService->bulkUpdateProductFields($requestData);
                    break;

                case 'soft_delete_product_stock':
                    $result = $this->productService->softDeleteProductStock($requestData);
                    break;

                case 'soft_delete_product_db':
                    $result = $this->productService->softDeleteProductDb($requestData);
                    break;

                case 'restore_deleted_product':
                    $result = $this->productService->restoreDeletedProduct($requestData);
                    break;

                case 'create_product_relation_group':
                    $result = $this->productService->createProductRelationGroup($requestData);
                    break;

                case 'bulk_create_product_series':
                    $result = $this->productService->bulkCreateProductSeries($requestData);
                    break;

                case 'add_product_to_relation_group':
                    $result = $this->productService->addProductToExistingRelationGroup($requestData);
                    break;

                case 'remove_product_from_relation_group':
                    $result = $this->productService->removeProductFromRelationGroup($requestData);
                    break;

                case 'save_product_relation_group':
                    $result = $this->productService->saveProductRelationGroup($requestData);
                    break;

                case 'delete_product_relation_group':
                    $result = $this->productService->deleteProductRelationGroup($requestData);
                    break;

                // 월간할인 해제 - 고도몰 반영까지 처리
                case 'prd_release_monthly_discount':

                    $goodsNo = trim((string)($requestData['goods_no'] ?? ''));
                    $prdIdx = trim((string)($requestData['prd_idx'] ?? ''));
                    $prdStockIdx = trim((string)($requestData['prd_stock_idx'] ?? ''));
                    $fixedPrice = $requestData['fixed_price'] ?? 0;
                    $goodsPrice = $requestData['goods_price'] ?? 0;
                    $actionSource = trim((string)($requestData['action_source'] ?? ''));
                    $actionSummary = trim((string)($requestData['action_summary'] ?? ''));
                    $actionUrl = trim((string)($requestData['action_url'] ?? ($_SERVER['HTTP_REFERER'] ?? $_SERVER['REQUEST_URI'] ?? '')));

                    $payload = [
                        'goodsNo' => $goodsNo,
                        'prdIdx' => $prdIdx,
                        'prdStockIdx' => $prdStockIdx,
                        'fixedPrice' => $fixedPrice,
                        'goodsPrice' => $goodsPrice,
                        'actionSource' => $actionSource,
                        'actionSummary' => $actionSummary,
                        'actionUrl' => $actionUrl,
                    ];

                    $productActionService = new ProductActionService();
                    $result = $productActionService->prdReleaseMonthlyDiscount($payload);
                    break;

                default:
                    throw new Exception('유효하지 않은 action_mode 입니다.');

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