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
use App\Models\ProductStockModel;
use App\Services\CompetitorApiService;
use App\Services\CompetitorPopupTicketService;
use App\Services\ProductService;

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
            $s_keyword_mode = $requestData['s_keyword_mode'] ?? 'name';
            $s_keyword = $requestData['s_keyword'] ?? '';
            $page = $requestData['page'] ?? 1;
            $s_status = $requestData['s_status'] ?? '';
            $s_sort_mode = $requestData['s_sort_mode'] ?? 'last_status_changed_at';
            $s_limit = (int)($requestData['s_limit'] ?? 100);
            if (!in_array($s_limit, [100, 200, 300, 500], true)) {
                $s_limit = 100;
            }
            $matchedProductMap = [];
            $brandForSelect = BrandModel::query()
                ->select('BD_IDX', 'BD_NAME')
                ->orderBy('BD_NAME', 'ASC')
                ->get();
            $brandForSelect = is_array($brandForSelect) ? $brandForSelect : $brandForSelect->toArray();

            $configCompetitor = config('admin.competitor');
            $competitor_data = $configCompetitor['competitor_data'] ?? [];

            if( $site ){
                $competitorApiService = new CompetitorApiService();
                $CompetitorProductApiData = $competitorApiService->getCompetitorProducts([
                    'site' => $site,
                    'status' => $s_status,
                    'match_status' => $s_match_status,
                    'keyword_mode' => $s_keyword_mode,
                    'keyword' => $s_keyword,
                    'sort_mode' => $s_sort_mode,
                    'page' => $page,
                    'limit' => $s_limit,
                ]);

                $competitorRows = $CompetitorProductApiData['data']['competitorProducts'] ?? [];
                foreach ($competitorRows as &$competitorRow) {
                    if (!is_array($competitorRow)) {
                        continue;
                    }

                    $eventTags = $competitorRow['event_tags_json'] ?? [];
                    if (is_string($eventTags)) {
                        $eventTags = json_decode($eventTags, true);
                    }
                    if (!is_array($eventTags)) {
                        $eventTags = [];
                    }

                    $competitorRow['event_tags_json'] = array_values(array_filter($eventTags, static function ($eventTag) {
                        return is_string($eventTag) && trim($eventTag) !== '';
                    }));
                }
                unset($competitorRow);
                $CompetitorProductApiData['data']['competitorProducts'] = $competitorRows;
                $matchIdxs = [];
                foreach ($competitorRows as $competitorRow) {
                    $primaryMatchIdx = $competitorRow['primary_match_idx'] ?? ($competitorRow['match_idx'] ?? null);
                    if ($primaryMatchIdx !== null && $primaryMatchIdx !== '' && is_numeric($primaryMatchIdx)) {
                        $matchIdxs[] = (int)$primaryMatchIdx;
                    }
                    $matchIdx = $competitorRow['match_idx'] ?? null;
                    if ($matchIdx !== null && $matchIdx !== '' && is_numeric($matchIdx)) {
                        $matchIdxs[] = (int)$matchIdx;
                    }
                    $matchedItems = $competitorRow['matched_items'] ?? [];
                    if (is_array($matchedItems)) {
                        foreach ($matchedItems as $matchedItem) {
                            if (!is_array($matchedItem)) {
                                continue;
                            }
                            $matchedCdIdx = $matchedItem['cd_idx'] ?? null;
                            if ($matchedCdIdx !== null && $matchedCdIdx !== '' && is_numeric($matchedCdIdx)) {
                                $matchIdxs[] = (int)$matchedCdIdx;
                            }
                        }
                    }
                }
                $matchIdxs = array_values(array_unique($matchIdxs));

                if (!empty($matchIdxs)) {
                    $matchedProducts = ProductModel::query()
                        ->select('CD_IDX', 'CD_NAME', 'CD_BRAND_IDX', 'cd_sale_price', 'cd_cost_price', 'cd_godo_code', 'delivery_type', 'CD_IMG')
                        ->whereIn('CD_IDX', $matchIdxs)
                        ->get();
                    $matchedProducts = is_array($matchedProducts) ? $matchedProducts : $matchedProducts->toArray();
                    $stockQtyByCdIdx = [];
                    $stockRows = ProductStockModel::query()
                        ->select('ps_prd_idx', 'ps_stock')
                        ->whereIn('ps_prd_idx', $matchIdxs)
                        ->get();
                    $stockRows = is_array($stockRows) ? $stockRows : $stockRows->toArray();
                    foreach ($stockRows as $stockRow) {
                        $prdIdx = (int)($stockRow['ps_prd_idx'] ?? 0);
                        if ($prdIdx <= 0) {
                            continue;
                        }
                        $stockQtyByCdIdx[$prdIdx] = (int)($stockRow['ps_stock'] ?? 0);
                    }

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
                        $salePrice = (int)($matchedProduct['cd_sale_price'] ?? 0);
                        $costPrice = (int)($matchedProduct['cd_cost_price'] ?? 0);
                        $deliveryType = trim((string)($matchedProduct['delivery_type'] ?? 'small'));
                        if ($deliveryType === 'tiny_80') {
                            $deliveryType = 'tiny';
                        }
                        $deliveryFeeMap = [
                            'tiny' => 2300,
                            'small' => 2800,
                            'medium' => 3300,
                            'large' => 5000,
                            'xlarge' => 5400,
                        ];
                        $deliveryFee = (int)($deliveryFeeMap[$deliveryType] ?? 2800);

                        $marginAmount = $salePrice - $costPrice;
                        if ($salePrice > 29999) {
                            $marginAmount = $salePrice - ($costPrice + $deliveryFee);
                        }
                        $marginRate = 0;
                        if ($salePrice > 0) {
                            $marginRate = round(($marginAmount / $salePrice) * 100, 2);
                        }
                        $marginGrade = '';
                        if ($marginRate > 39) $marginGrade = 'A';
                        else if ($marginRate >= 35) $marginGrade = 'B';
                        else if ($marginRate >= 30) $marginGrade = 'C';
                        else if ($marginRate >= 25) $marginGrade = 'D';
                        else if ($marginRate >= 20) $marginGrade = 'E';
                        else if ($marginRate >= 15) $marginGrade = 'F';
                        else if ($marginRate >= 10) $marginGrade = 'G';
                        else if ($marginRate >= 5) $marginGrade = 'H';
                        else if ($marginRate > 0) $marginGrade = 'I';

                        $matchedProductMap[$cdIdx] = [
                            'CD_IDX' => $cdIdx,
                            'CD_NAME' => (string)($matchedProduct['CD_NAME'] ?? ''),
                            'CD_BRAND_IDX' => $brandIdx,
                            'brand_name' => (string)($brandNameByIdx[$brandIdx] ?? ''),
                            'cd_sale_price' => $salePrice,
                            'cd_cost_price' => $costPrice,
                            'cd_godo_code' => (string)($matchedProduct['cd_godo_code'] ?? ''),
                            'margin_grade' => $marginGrade,
                            'delivery_type' => $deliveryType,
                            'stock_qty' => (int)($stockQtyByCdIdx[$cdIdx] ?? 0),
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
                's_keyword_mode' => $s_keyword_mode,
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
                'brandForSelect' => $brandForSelect,
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
            $brandIdx = (int)($requestData['brand_idx'] ?? 0);
            $page = (int)($requestData['page'] ?? 1);
            if ($page <= 0) {
                $page = 1;
            }
            $limit = (int)($requestData['limit'] ?? 30);
            if ($limit <= 0) {
                $limit = 30;
            }
            if ($limit > 100) {
                $limit = 100;
            }

            if ($keyword === '' && $brandIdx <= 0) {
                return response()->json([
                    'success' => true,
                    'message' => '검색 조건이 비어있습니다.',
                    'data' => [
                        'items' => [],
                    ],
                ]);
            }

            $query = ProductModel::query()
                ->select('CD_IDX', 'CD_NAME', 'CD_BRAND_IDX', 'CD_IMG')
                ->orderBy('CD_IDX', 'DESC');

            if ($keyword !== '') {
                $query->where('CD_NAME', 'LIKE', '%' . $keyword . '%');
            }
            if ($brandIdx > 0) {
                $query->where('CD_BRAND_IDX', $brandIdx);
            }

            $paginationResult = $query->paginate($limit, $page);
            $productRows = $paginationResult['data'] ?? [];

            $brandIdxs = [];
            foreach ($productRows as $row) {
                $itemBrandIdx = (int)($row['CD_BRAND_IDX'] ?? 0);
                if ($itemBrandIdx > 0) {
                    $brandIdxs[] = $itemBrandIdx;
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
                    'pagination' => [
                        'total' => (int)($paginationResult['total'] ?? 0),
                        'per_page' => (int)($paginationResult['per_page'] ?? $limit),
                        'current_page' => (int)($paginationResult['current_page'] ?? $page),
                        'last_page' => (int)($paginationResult['last_page'] ?? 1),
                    ],
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
     * 경쟁사 가격 기준으로 상품 판매가와 고도몰 가격을 갱신한다.
     *
     * @param Request $request
     * @return \App\Core\Response
     */
    public function adjustMatchedProductPrice(Request $request)
    {
        try {
            $productService = new ProductService();
            $result = $productService->adjustProductSalePriceAndGodoUpdate($request->all());

            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? '판매가 및 고도몰 가격을 업데이트했습니다.',
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
     * B 경쟁사 상세 팝업용 단기 서명 티켓을 발급한다.
     */
    public function issueCompetitorPopupTicket(Request $request)
    {
        $requestId = bin2hex(random_bytes(12));
        try {
            $site = trim((string)$request->input('site', ''));
            $productId = (int)$request->input('product_id', 0);
            $userId = (int)(AuthAdmin::getSession('sess_idx') ?? 0);
            $userLoginId = trim((string)(AuthAdmin::getSession('sess_id') ?? ''));
            if ($userId <= 0 || $userLoginId === '') {
                throw new \RuntimeException('unauthenticated');
            }

            // 세션 값만 신뢰하지 않고 현재 A DB의 관리자 계정을 재확인한다.
            $stmt = $this->db->prepare('SELECT idx FROM admin WHERE idx = :idx AND ad_id = :ad_id LIMIT 1');
            $stmt->execute(['idx' => $userId, 'ad_id' => $userLoginId]);
            if (!$stmt->fetch()) {
                throw new \RuntimeException('unauthenticated');
            }

            $service = new CompetitorPopupTicketService($this->db);
            $result = $service->issue($userId, $site, $productId, $requestId, AuthAdmin::getIp());
            return response()->json([
                'success' => true,
                'popup_url' => $result['popup_url'],
                'expires_at' => $result['expires_at'],
            ]);
        } catch (Throwable $e) {
            error_log(json_encode([
                'event' => 'competitor_popup_ticket_denied',
                'reason' => $e->getMessage(),
                'request_id' => $requestId,
            ], JSON_UNESCAPED_SLASHES));
            return response()->json([
                'success' => false,
                'message' => '팝업 접근 권한이 없거나 요청을 처리할 수 없습니다.',
            ], 403);
        }
    }

    /**
     * noopener 팝업 내부에서 ticket을 발급한 뒤 B launch URL로 이동한다.
     */
    public function competitorPopupLaunchPage(Request $request)
    {
        $site = trim((string)$request->input('site', ''));
        $productId = (int)$request->input('product_id', 0);
        $payload = json_encode([
            'site' => $site,
            'product_id' => $productId,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return '<!doctype html><html lang="ko"><head><meta charset="utf-8">'
            . '<meta name="referrer" content="no-referrer"><title>경쟁사 상품 상세</title></head><body>'
            . '<p id="message">보안 연결을 준비하고 있습니다...</p><script>'
            . '(function(){var data=' . $payload . ';'
            . 'fetch("/admin/competitor/popup-ticket",{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded;charset=UTF-8","Accept":"application/json"},body:new URLSearchParams(data)})'
            . '.then(function(response){return response.json().then(function(body){if(!response.ok||!body.success||!body.popup_url){throw new Error(body.message||"팝업 접근 권한이 없습니다.");}return body;});})'
            . '.then(function(body){window.location.replace(body.popup_url);})'
            . '.catch(function(error){document.getElementById("message").textContent=error.message||"팝업 접근 권한이 없습니다.";});'
            . '})();</script></body></html>';
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
            $actionMode = trim((string)($requestData['action_mode'] ?? ''));
            $matchIdx = (int)($requestData['match_idx'] ?? 0);
            $primaryCdIdx = (int)($requestData['primary_cd_idx'] ?? 0);
            $matchIdxListRaw = $requestData['match_idx_list'] ?? [];
            $matchIdxList = [];

            if ($site === '') {
                throw new \Exception('site is required');
            }
            if ($prdPk <= 0) {
                throw new \Exception('prd_pk must be numeric');
            }
            if (!is_array($matchIdxListRaw)) {
                $matchIdxListRaw = [];
            }
            foreach ($matchIdxListRaw as $listIdx) {
                if ($listIdx !== null && $listIdx !== '' && is_numeric($listIdx) && (int)$listIdx > 0) {
                    $matchIdxList[] = (int)$listIdx;
                }
            }
            $matchIdxList = array_values(array_unique($matchIdxList));

            // 하위호환: action_mode 미지정 + match_idx 단건 호출
            if ($actionMode === '') {
                if ($matchIdx <= 0) {
                    throw new \Exception('match_idx must be numeric');
                }
            } elseif ($actionMode === 'upsert_many') {
                if (empty($matchIdxList)) {
                    throw new \Exception('match_idx_list is required');
                }
                if ($primaryCdIdx > 0 && !in_array($primaryCdIdx, $matchIdxList, true)) {
                    throw new \Exception('primary_cd_idx must be in match_idx_list');
                }
            } elseif ($actionMode === 'set_primary') {
                if ($matchIdx <= 0) {
                    throw new \Exception('match_idx must be numeric');
                }
            } else {
                throw new \Exception('invalid action_mode');
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
                'processor_pk' => $processorPk,
                'processor_id' => $processorId,
                'processor_name' => $processorName,
                'match_processed_at' => date('Y-m-d H:i:s'),
            ];
            if ($actionMode !== '') {
                $payload['action_mode'] = $actionMode;
            }
            if ($actionMode === 'upsert_many') {
                $payload['match_idx_list'] = $matchIdxList;
                if ($primaryCdIdx > 0) {
                    $payload['primary_cd_idx'] = $primaryCdIdx;
                }
            } else {
                $payload['match_idx'] = $matchIdx;
            }

            $headers = [
                'Content-Type: application/json',
                'X-API-KEY: DNP_2024_SUPPLIER_API_KEY_v1_8f9e2c7b4a1d6e3f',
            ];

            $apiResult = HttpClient::postDataWithMeta('https://dnetc01.mycafe24.com/api/CompetitorProductMatch', $payload, $headers);
            $responseRaw = (string)($apiResult['response'] ?? '');
            $httpCode = (int)($apiResult['http_code'] ?? 0);
            $curlError = trim((string)($apiResult['curl_error'] ?? ''));
            if ($responseRaw === '') {
                throw new \Exception($this->buildExternalApiEmptyResponseMessage($httpCode, $curlError));
            }

            $responseData = json_decode($responseRaw, true);
            if (!is_array($responseData)) {
                throw new \Exception($this->buildExternalApiParseFailMessage($httpCode, $responseRaw));
            }

            $status = (string)($responseData['status'] ?? '');
            if ($status !== 'success') {
                $errorMessage = (string)($responseData['message'] ?? '매칭 저장 실패');
                if ($httpCode >= 400) {
                    $errorMessage .= ' (HTTP ' . $httpCode . ')';
                }
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

    /**
     * 경쟁사 상품 매칭 해지
     * 외부 API(/api/CompetitorProductMatch)로 매칭 해지 정보 전달
     *
     * @param Request $request
     * @return \App\Core\Response
     */
    public function unmatchCompetitorProduct(Request $request)
    {
        try {
            $requestData = $request->all();

            $site = trim((string)($requestData['site'] ?? ''));
            $prdPk = (int)($requestData['prd_pk'] ?? 0);
            $actionMode = trim((string)($requestData['action_mode'] ?? ''));
            $matchIdx = (int)($requestData['match_idx'] ?? 0);

            if ($site === '') {
                throw new \Exception('site is required');
            }
            if ($prdPk <= 0) {
                throw new \Exception('prd_pk must be numeric');
            }
            if ($actionMode === 'unmatch_one' && $matchIdx <= 0) {
                throw new \Exception('match_idx must be numeric');
            }
            if ($actionMode !== '' && !in_array($actionMode, ['unmatch_one', 'unmatch_all'], true)) {
                throw new \Exception('invalid action_mode');
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
                'action_mode' => $actionMode !== '' ? $actionMode : 'unmatch_all',
                'processor_pk' => $processorPk,
                'processor_id' => $processorId,
                'processor_name' => $processorName,
                'match_processed_at' => date('Y-m-d H:i:s'),
            ];
            if ($payload['action_mode'] === 'unmatch_one') {
                $payload['match_idx'] = $matchIdx;
            }

            $headers = [
                'Content-Type: application/json',
                'X-API-KEY: DNP_2024_SUPPLIER_API_KEY_v1_8f9e2c7b4a1d6e3f',
            ];

            $apiResult = HttpClient::postDataWithMeta('https://dnetc01.mycafe24.com/api/CompetitorProductMatch', $payload, $headers);
            $responseRaw = (string)($apiResult['response'] ?? '');
            $httpCode = (int)($apiResult['http_code'] ?? 0);
            $curlError = trim((string)($apiResult['curl_error'] ?? ''));
            if ($responseRaw === '') {
                throw new \Exception($this->buildExternalApiEmptyResponseMessage($httpCode, $curlError));
            }

            $responseData = json_decode($responseRaw, true);
            if (!is_array($responseData)) {
                throw new \Exception($this->buildExternalApiParseFailMessage($httpCode, $responseRaw));
            }

            $status = (string)($responseData['status'] ?? '');
            if ($status !== 'success') {
                $errorMessage = (string)($responseData['message'] ?? '매칭 해지 실패');
                if ($httpCode >= 400) {
                    $errorMessage .= ' (HTTP ' . $httpCode . ')';
                }
                throw new \Exception($errorMessage);
            }

            return response()->json([
                'success' => true,
                'message' => '매칭 해지 완료',
                'data' => $responseData['data'] ?? [],
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    private function buildExternalApiEmptyResponseMessage(int $httpCode, string $curlError): string
    {
        if ($curlError !== '') {
            return '외부 API 호출 실패(cURL): ' . $curlError;
        }
        if ($httpCode > 0) {
            return '외부 API 응답이 비어있습니다. (HTTP ' . $httpCode . ')';
        }
        return '외부 API 응답이 비어있습니다.';
    }

    private function buildExternalApiParseFailMessage(int $httpCode, string $responseRaw): string
    {
        $message = '외부 API 응답 파싱에 실패했습니다.';
        if ($httpCode > 0) {
            $message .= ' (HTTP ' . $httpCode . ')';
        }

        $preview = $this->createResponsePreview($responseRaw);
        if ($preview !== '') {
            $message .= ' 응답 미리보기: ' . $preview;
        }

        return $message;
    }

    private function createResponsePreview(string $responseRaw): string
    {
        $text = trim((string)preg_replace('/\s+/u', ' ', $responseRaw));
        if ($text === '') {
            return '';
        }

        if (strlen($text) > 180) {
            return substr($text, 0, 180) . '...';
        }

        return $text;
    }

}