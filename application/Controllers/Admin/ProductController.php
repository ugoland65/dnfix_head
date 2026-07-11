<?php

namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;

use App\Services\ProductService;
use App\Services\ProductActionService;
use App\Services\BrandService;
use App\Services\ProductPartnerService;
use App\Services\PartnersService;
use App\Services\ProductStockSaleLogService;
use App\Services\GodoInspectionService;
use App\Services\InspectionProcessLogService;
use App\Services\CompetitorApiService;
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

            $data = [
                's_brand' => $s_brand,
                's_prd_kind' => $s_prd_kind,
                's_prd_kind_second' => $s_prd_kind_second,
                's_importing_country' => $s_importing_country,
                's_margin_group' => $s_margin_group,
                's_sale_mode' => $s_sale_mode,
                's_sale_status' => $s_sale_status,
                's_discontinued' => $s_discontinued,
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

            $data = [
                's_brand' => $s_brand,
                's_prd_kind' => $s_prd_kind,
                's_prd_kind_second' => $s_prd_kind_second,
                's_importing_country' => $s_importing_country,
                's_margin_group' => $s_margin_group,
                's_sale_mode' => $s_sale_mode,
                's_sale_status' => $s_sale_status,
                's_discontinued' => $s_discontinued,
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
                'cd_site_show' => 'Y',
                'cd_reference_links' => [],
                'work_check_list' => [],
                'is_sale_month' => 0,
                'is_sale_special' => 0,
                'is_discontinued' => 0,
            ];

            $data = [
                'mode' => 'new',
                'prd_idx' => null,
                'productData' => $productData,
                'prd_kind_name' => $prd_kind_name,
                'categories' => $categories,
                'brandForSelect' => $brandForSelect,
                'sale_status_options' => $saleStatusOptions,
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

            $config_product = config('admin.product');
            $prd_kind_name = $config_product['prd_kind_name'] ?? [];
            $categories = $config_product['categories'] ?? [];
            $saleStatusOptions = $config_product['sale_status_options'] ?? [];

            // 브랜드 셀렉트바를 위한 조회
            $brandService = new BrandService();
            $brandForSelect = $brandService->getBrandForSelect();

            $data = [
                'mode' => 'edit',
                'prd_idx' => $prdIdx,
                'productData' => $productData,
                'prd_kind_name' => $prd_kind_name,
                'categories' => $categories,
                'brandForSelect' => $brandForSelect,
                'sale_status_options' => $saleStatusOptions,
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

            //dump($productData);

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

                case 'unset_product_discontinued':
                    $result = $this->productService->unsetProductDiscontinued($requestData);
                    break;

                case 'process_single_godo_inspection':
                    $result = $this->productService->processSingleProductGodoInspection($requestData);
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