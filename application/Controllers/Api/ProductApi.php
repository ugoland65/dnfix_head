<?php
namespace App\Controllers\Api;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;

use App\Services\ProductService;

class ProductApi extends BaseClass 
{

    /**
     * 상품 재고 DB API
     *
     * - Apps Script 연동용: page/limit/since 지원(일단 since는 payload로만 전달)
     * - 응답 포맷 표준화: data/has_more/next_page/server_time
     */
    public function productStockApi(
        Request $request, 
        ProductService $productService
    )
    {
        try {

            $page = max(1, (int) $request->input('page', 1));

            // limit or per_page 허용 + 최대치 제한(시트/서버 보호)
            $perPage = (int) $request->input('limit', $request->input('per_page', 100));
            if ($perPage <= 0) $perPage = 100;
            $perPage = min($perPage, 200);

            $sortMode = $request->input('sort_mode', 'stock');

            // sort_mode allowlist (applySortMode에서 허용된 값만)
            $allowedSortModes = ['stock', 'idx']; // 필요하면 확장
            if (!in_array($sortMode, $allowedSortModes, true)) {
                $sortMode = 'stock';
            }

            $payload = [
                'paging' => true,
                'page' => $page,
                'per_page' => $perPage,
                'show_mode' => 'product_stock',
                'sort_mode' => $sortMode,

                'rack_code' => $request->input('rack_code'),
                's_brand' => $request->input('s_brand'),
                's_prd_kind' => $request->input('s_prd_kind'),
                's_importing_country' => $request->input('s_importing_country'),
                's_margin_group' => $request->input('s_margin_group'),
                'search_value' => $request->input('search_value'),

                // 증분 동기화용 (서비스에서 처리하도록 확장 필요)
                'since' => $request->input('since'),
            ];

            $result = $productService->getProductListForAdmin($payload);

            // paginate 구조가 어떻든 최대한 안전하게 파싱
            $data = $result['data'] ?? [];
            $currentPage = (int)($result['current_page'] ?? $page);
            $lastPage = (int)($result['last_page'] ?? 0);

            // last_page가 없을 수도 있으니 보조 로직
            $hasMore = false;
            if ($lastPage > 0) {
                $hasMore = $currentPage < $lastPage;
            } else {
                // next_page_url 같은 키가 있으면 활용 (프로젝트 paginate 구조에 맞게 필요 시 수정)
                $hasMore = !empty($result['next_page_url'] ?? null);
            }

            $nextPage = $hasMore ? ($currentPage + 1) : null;

            return response()->json([
                'data' => $data,
                'page' => $currentPage,
                'limit' => $perPage,
                'has_more' => $hasMore,
                'next_page' => $nextPage,
                'server_time' => date('Y-m-d\TH:i:sP'), // ISO 8601 형식
                // 필요하면 원본 메타도 같이 내려주기
                'meta' => [
                    'total' => $result['total'] ?? null,
                    'last_page' => $result['last_page'] ?? null,
                ],
            ]);

        } catch (Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}