<?php
namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Core\AuthAdmin;
use App\Utils\HttpClient; 
use App\Classes\Request;
use App\Utils\Pagination;
use App\Models\ProductModel;
use App\Models\BrandModel;
use App\Services\CompetitorApiService;

class CompetitorController extends BaseClass
{

    /**
     * 경쟁사 사이트 상품DB 목록 조회
     * 
     * @param Request $request
     * @return view
     */
    public function getCompetitorProductDb(Request $request)
    {
        try{

            $requestData = $request->all();

            $s_match_status = $requestData['s_match_status'] ?? 'unmatched';
            $site = $requestData['s_site'] ?? null;
            $s_keyword = $requestData['s_keyword'] ?? '';
            $page = $requestData['page'] ?? 1;
            $s_status = $requestData['s_status'] ?? '';
            $s_sort_mode = $requestData['s_sort_mode'] ?? 'code';
            $s_limit = (int)($requestData['s_limit'] ?? 100);
            if (!in_array($s_limit, [100, 200, 300, 500], true)) {
                $s_limit = 100;
            }
            $matchedProductMap = [];

            $competitor_data = [
                'oname' => ['name' => "오나미몰", 'code' => 'oname'],
                'freebody' => ['name' => "프리바디", 'code' => 'freebody'],
                'bananamall' => ['name' => "바나나몰", 'code' => 'bananamall'],
                'rmax' => ['name' => "리얼맥스", 'code' => 'rmax'],
                'dingdong' => ['name' => "딩동몰", 'code' => 'dingdong'],
                'vavoomshop' => ['name' => "바붐샵", 'code' => 'vavoomshop'],
            ];

            if( $site ){
                $competitorApiService = new CompetitorApiService();
                $CompetitorProductApiData = $competitorApiService->getCompetitorProducts([
                    'site' => $site,
                    'status' => $s_status,
                    'match_status' => $s_match_status,
                    'keyword' => $s_keyword,
                    'sort_mode' => $s_sort_mode,
                    'page' => $page,
                    'limit' => $s_limit,
                ]);

                $competitorRows = $CompetitorProductApiData['data']['competitorProducts'] ?? [];
                $matchIdxs = [];
                foreach ($competitorRows as $competitorRow) {
                    $matchIdx = $competitorRow['match_idx'] ?? null;
                    if ($matchIdx !== null && $matchIdx !== '' && is_numeric($matchIdx)) {
                        $matchIdxs[] = (int)$matchIdx;
                    }
                }
                $matchIdxs = array_values(array_unique($matchIdxs));

                if (!empty($matchIdxs)) {
                    $matchedProducts = ProductModel::query()
                        ->select('CD_IDX', 'CD_NAME', 'CD_BRAND_IDX', 'cd_sale_price', 'CD_IMG')
                        ->whereIn('CD_IDX', $matchIdxs)
                        ->get();
                    $matchedProducts = is_array($matchedProducts) ? $matchedProducts : $matchedProducts->toArray();

                    $brandIdxs = [];
                    foreach ($matchedProducts as $matchedProduct) {
                        $brandIdx = (int)($matchedProduct['CD_BRAND_IDX'] ?? 0);
                        if ($brandIdx > 0) {
                            $brandIdxs[] = $brandIdx;
                        }
                    }
                    $brandIdxs = array_values(array_unique($brandIdxs));

                    $brandNameByIdx = [];
                    if (!empty($brandIdxs)) {
                        $brands = BrandModel::query()
                            ->select('BD_IDX', 'BD_NAME')
                            ->whereIn('BD_IDX', $brandIdxs)
                            ->get();
                        $brands = is_array($brands) ? $brands : $brands->toArray();
                        foreach ($brands as $brand) {
                            $bdIdx = (int)($brand['BD_IDX'] ?? 0);
                            if ($bdIdx > 0) {
                                $brandNameByIdx[$bdIdx] = (string)($brand['BD_NAME'] ?? '');
                            }
                        }
                    }

                    foreach ($matchedProducts as $matchedProduct) {
                        $cdIdx = (int)($matchedProduct['CD_IDX'] ?? 0);
                        if ($cdIdx <= 0) {
                            continue;
                        }
                        $brandIdx = (int)($matchedProduct['CD_BRAND_IDX'] ?? 0);
                        $img = trim((string)($matchedProduct['CD_IMG'] ?? ''));
                        $matchedProductMap[$cdIdx] = [
                            'CD_IDX' => $cdIdx,
                            'CD_NAME' => (string)($matchedProduct['CD_NAME'] ?? ''),
                            'CD_BRAND_IDX' => $brandIdx,
                            'brand_name' => (string)($brandNameByIdx[$brandIdx] ?? ''),
                            'cd_sale_price' => (int)($matchedProduct['cd_sale_price'] ?? 0),
                            'img_path' => $img !== '' ? ('/data/comparion/' . $img) : '',
                        ];
                    }
                }

                $pagination_total = $CompetitorProductApiData['data']['pagination']['total'];
                $pagination_per_page = $CompetitorProductApiData['data']['pagination']['per_page'];
                $pagination_current_page = $CompetitorProductApiData['data']['pagination']['current_page'];
            
                $pagination = new Pagination($pagination_total, $pagination_per_page, $pagination_current_page, 10);
                $paginationHtml = $pagination->renderLinks();

            }else{

                $CompetitorProductApiData = [];
                $pagination_total = 0;
                $pagination_per_page = 0;
                $pagination_current_page = 0;
                $paginationHtml = '';

            }

            $data = [
                'competitor_data' => $competitor_data,
                'CompetitorProductApiData' => $CompetitorProductApiData,
                's_match_status' => $s_match_status,
                'site' => $site,
                's_keyword' => $s_keyword,
                's_sort_mode' => $s_sort_mode,
                's_limit' => $s_limit,
                'page' => $page,
                's_status' => $s_status,
                'pagination_total' => $pagination_total,
                'pagination_per_page' => $pagination_per_page,
                'pagination_current_page' => $pagination_current_page,
                'paginationHtml' => $paginationHtml,
                'matchedProductMap' => $matchedProductMap,
            ];

            return view('admin.competitor.competitor_product_db', $data)
                ->extends('admin.layout.layout',[
                    'pageGroup2' => 'provider',
                    'pageNameCode' => 'competitor_product_db'
                ]);

        } catch (Throwable $e) {
            dump($e->getMessage());
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }

    /**
     * 경쟁사 상품 매칭용 상품DB 검색 (COMPARISON_DB)
     *
     * @param Request $request
     * @return \App\Core\Response
     */
    public function searchComparisonProducts(Request $request)
    {
        try {
            $requestData = $request->all();
            $keyword = trim((string)($requestData['keyword'] ?? ''));
            $limit = (int)($requestData['limit'] ?? 30);
            if ($limit <= 0) {
                $limit = 30;
            }
            if ($limit > 100) {
                $limit = 100;
            }

            if ($keyword === '') {
                return response()->json([
                    'success' => true,
                    'message' => '검색어가 비어있습니다.',
                    'data' => [
                        'items' => [],
                    ],
                ]);
            }

            $productRows = ProductModel::query()
                ->select('CD_IDX', 'CD_NAME', 'CD_BRAND_IDX', 'CD_IMG')
                ->where('CD_NAME', 'LIKE', '%' . $keyword . '%')
                ->orderBy('CD_IDX', 'DESC')
                ->limit($limit)
                ->get();
            $productRows = is_array($productRows) ? $productRows : $productRows->toArray();

            $brandIdxs = [];
            foreach ($productRows as $row) {
                $brandIdx = (int)($row['CD_BRAND_IDX'] ?? 0);
                if ($brandIdx > 0) {
                    $brandIdxs[] = $brandIdx;
                }
            }
            $brandIdxs = array_values(array_unique($brandIdxs));

            $brandNameByIdx = [];
            if (!empty($brandIdxs)) {
                $brandRows = BrandModel::query()
                    ->select('BD_IDX', 'BD_NAME')
                    ->whereIn('BD_IDX', $brandIdxs)
                    ->get();
                $brandRows = is_array($brandRows) ? $brandRows : $brandRows->toArray();
                foreach ($brandRows as $brandRow) {
                    $bdIdx = (int)($brandRow['BD_IDX'] ?? 0);
                    if ($bdIdx > 0) {
                        $brandNameByIdx[$bdIdx] = (string)($brandRow['BD_NAME'] ?? '');
                    }
                }
            }

            $items = [];
            foreach ($productRows as $row) {
                $cdIdx = (int)($row['CD_IDX'] ?? 0);
                $brandIdx = (int)($row['CD_BRAND_IDX'] ?? 0);
                $img = trim((string)($row['CD_IMG'] ?? ''));
                $items[] = [
                    'cd_idx' => $cdIdx,
                    'cd_name' => (string)($row['CD_NAME'] ?? ''),
                    'brand_name' => (string)($brandNameByIdx[$brandIdx] ?? ''),
                    'thumbnail_url' => $img !== '' ? ('/data/comparion/' . $img) : '',
                ];
            }

            return response()->json([
                'success' => true,
                'message' => '검색 완료',
                'data' => [
                    'items' => $items,
                ],
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * 경쟁사 상품 매칭 저장
     * 외부 API(/api/CompetitorProductMatch)로 매칭 정보 전달
     *
     * @param Request $request
     * @return \App\Core\Response
     */
    public function matchCompetitorProduct(Request $request)
    {
        try {
            $requestData = $request->all();

            $site = trim((string)($requestData['site'] ?? ''));
            $prdPk = (int)($requestData['prd_pk'] ?? 0);
            $matchIdx = (int)($requestData['match_idx'] ?? 0);

            if ($site === '') {
                throw new \Exception('site is required');
            }
            if ($prdPk <= 0) {
                throw new \Exception('prd_pk must be numeric');
            }
            if ($matchIdx <= 0) {
                throw new \Exception('match_idx must be numeric');
            }

            $processorPk = (int)(AuthAdmin::getSession('sess_idx') ?? 0);
            $processorId = trim((string)(AuthAdmin::getSession('sess_id') ?? ''));
            $processorName = trim((string)(AuthAdmin::getSession('sess_name') ?? ''));

            if ($processorPk <= 0 || $processorId === '' || $processorName === '') {
                throw new \Exception('처리자 정보가 유효하지 않습니다. 다시 로그인 후 시도해주세요.');
            }

            $payload = [
                'site' => $site,
                'prd_pk' => $prdPk,
                'match_idx' => $matchIdx,
                'processor_pk' => $processorPk,
                'processor_id' => $processorId,
                'processor_name' => $processorName,
                'match_processed_at' => date('Y-m-d H:i:s'),
            ];

            $headers = [
                'Content-Type: application/json',
                'X-API-KEY: DNP_2024_SUPPLIER_API_KEY_v1_8f9e2c7b4a1d6e3f',
            ];

            $responseRaw = HttpClient::postData('https://dnetc01.mycafe24.com/api/CompetitorProductMatch', $payload, $headers);
            if ($responseRaw === '') {
                throw new \Exception('외부 API 응답이 비어있습니다.');
            }

            $responseData = json_decode($responseRaw, true);
            if (!is_array($responseData)) {
                throw new \Exception('외부 API 응답 파싱에 실패했습니다.');
            }

            $status = (string)($responseData['status'] ?? '');
            if ($status !== 'success') {
                $errorMessage = (string)($responseData['message'] ?? '매칭 저장 실패');
                throw new \Exception($errorMessage);
            }

            return response()->json([
                'success' => true,
                'message' => '매칭 처리 완료',
                'data' => $responseData['data'] ?? [],
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

}