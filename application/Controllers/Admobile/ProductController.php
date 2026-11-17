<?php

namespace App\Controllers\Admobile;

use App\Auth\AdmobileSession;
use App\Classes\Request;
use App\Core\BaseClass;
use App\Models\ProductStockModel;
use App\Services\ProductService;
use App\Services\ProductStockService;
use App\Services\RackService;
use App\Utils\Pagination;

class ProductController extends BaseClass
{
    /**
     * 모바일 상품관리 목록.
     */
    public function list(Request $request)
    {
        if (!AdmobileSession::isAuthenticated()) {
            return redirect('/admobile/login');
        }

        $requestData = $request->all();
        $page = max(1, (int)($requestData['page'] ?? 1));
        $inStock = (string)($requestData['in_stock'] ?? 'all');
        if (!in_array($inStock, ['all', 'have', 'no'], true)) {
            $inStock = 'all';
        }

        $productList = (new ProductService())->getProductListForAdmin([
            'paging' => true,
            'page' => $page,
            'per_page' => 20,
            'in_stock' => $inStock,
            's_sale_status' => trim((string)($requestData['s_sale_status'] ?? '')),
            'search_value' => trim((string)($requestData['search_value'] ?? '')),
            'rack_code' => trim((string)($requestData['rack_code'] ?? '')),
            'sort_mode' => 'idx',
        ]);

        $pagination = new Pagination(
            (int)($productList['total'] ?? 0),
            (int)($productList['per_page'] ?? 20),
            (int)($productList['current_page'] ?? $page),
            5
        );
        $productConfig = config('admin.product');
        $rackCode = trim((string)($requestData['rack_code'] ?? ''));

        return view('admobile.product.list', [
            'productList' => $productList['data'] ?? [],
            'totalCount' => (int)($productList['total'] ?? 0),
            'inStock' => $inStock,
            'saleStatus' => trim((string)($requestData['s_sale_status'] ?? '')),
            'searchValue' => trim((string)($requestData['search_value'] ?? '')),
            'rackCode' => $rackCode,
            'rackGroups' => $this->rackGroupsForLookup(),
            'saleStatusOptions' => $productConfig['sale_status_options'] ?? [],
            'paginationHtml' => $pagination->renderLinks(),
        ])->extends('admobile.layout.layout', [
            'pageTitle' => '상품관리',
        ]);
    }

    /**
     * 랙 묶음별 상품 재고.
     * 랙코드 A10-2-1, B01-4-4는 A10, B01 묶음으로 나누고 랙코드 내림차순으로 보여준다.
     */
    public function rackStock(Request $request)
    {
        if (!AdmobileSession::isAuthenticated()) {
            return redirect('/admobile/login');
        }

        $requestData = $request->all();
        $group = strtoupper(trim((string)($requestData['group'] ?? '')));
        $searchValue = trim((string)($requestData['search_value'] ?? ''));
        $rackGroups = $this->rackStockGroups();
        $productGroups = [];

        if ($group !== '' && !isset($rackGroups[$group])) {
            $group = '';
        }
        if ($group !== '' || $searchValue !== '') {
            $productGroups = $this->rackStockProducts($group, $searchValue);
        }

        return view('admobile.product.rack_stock', [
            'rackGroups' => $rackGroups,
            'productGroups' => $productGroups,
            'group' => $group,
            'searchValue' => $searchValue,
        ])->extends('admobile.layout.layout', [
            'pageTitle' => '랙별 상품 재고관리',
        ]);
    }

    /**
     * 모바일 상품 정보 조회.
     */
    public function detail(Request $request)
    {
        if (!AdmobileSession::isAuthenticated()) {
            return redirect('/admobile/login');
        }

        $prdIdx = (int)($request->all()['prd_idx'] ?? 0);
        $productData = $prdIdx > 0
            ? (new ProductService())->getProductDataForAdmin($prdIdx)
            : [];
        if (empty($productData)) {
            return view('admin.errors.404', [
                'message' => '상품 정보를 찾을 수 없습니다.',
            ])->response(404);
        }

        $productConfig = config('admin.product');
        $returnTo = (string)($request->all()['return_to'] ?? '');
        $allowedReturnPrefixes = [
            '/admobile/order/sheet/stock?',
            '/admobile/product/grouping/view/',
            '/admobile/product/rack',
        ];
        $isAllowedReturn = false;
        foreach ($allowedReturnPrefixes as $prefix) {
            if (strpos($returnTo, $prefix) === 0) {
                $isAllowedReturn = true;
                break;
            }
        }
        if (!$isAllowedReturn) {
            $returnTo = '/admobile/product/list';
        }

        return view('admobile.product.detail', [
            'productData' => $productData,
            'prdKindNames' => $productConfig['prd_kind_name'] ?? [],
            'returnTo' => $returnTo,
        ])->extends('admobile.layout.layout', [
            'pageTitle' => '상품정보',
        ]);
    }

    /**
     * 모바일 상품 이미지 액션.
     */
    public function action(Request $request)
    {
        if (!AdmobileSession::isAuthenticated()) {
            return response()->json(['success' => false, 'message' => '로그인이 필요합니다.'], 401);
        }

        try {
            $requestData = $request->all();
            AdmobileSession::start();
            $actor = [
                'idx' => (int)($_SESSION['sess_idx'] ?? 0),
                'id' => (string)($_SESSION['sess_id'] ?? ''),
                'name' => (string)($_SESSION['sess_name'] ?? ''),
            ];
            $service = new ProductService();
            $actionMode = (string)($requestData['action_mode'] ?? '');

            if ($actionMode === 'inspection_image_upload') {
                $imageUrl = $service->updateProductInspectionImage(
                    (int)($requestData['prd_idx'] ?? 0),
                    (string)($requestData['image_type'] ?? ''),
                    $_FILES['image'] ?? [],
                    $actor
                );

                return response()->json([
                    'success' => true,
                    'message' => '상품 이미지를 변경했습니다.',
                    'image_url' => $imageUrl,
                ]);
            }
            if ($actionMode === 'rack_code_update') {
                $rackCode = $service->updateProductRackCode(
                    (int)($requestData['prd_idx'] ?? 0),
                    (string)($requestData['rack_code'] ?? ''),
                    $actor
                );

                return response()->json([
                    'success' => true,
                    'message' => '랙 코드를 변경했습니다.',
                    'rack_code' => $rackCode,
                ]);
            }
            if ($actionMode === 'barcode_update') {
                $barcode = $service->updateProductBarcode(
                    (int)($requestData['prd_idx'] ?? 0),
                    (string)($requestData['barcode'] ?? ''),
                    $actor
                );

                return response()->json([
                    'success' => true,
                    'message' => '바코드를 변경했습니다.',
                    'barcode' => $barcode,
                ]);
            }
            if ($actionMode === 'register_stock_change') {
                $prdIdx = (int)($requestData['prd_idx'] ?? 0);
                $psIdx = (int)($requestData['ps_idx'] ?? 0);
                if ($prdIdx < 1) {
                    throw new \Exception('상품 번호가 올바르지 않습니다.');
                }

                $stockService = new ProductStockService();
                $stockQuery = ProductStockModel::query()
                    ->select(['ps_idx'])
                    ->where('ps_prd_idx', '=', $prdIdx);
                if ($psIdx > 0) {
                    $stockQuery->where('ps_idx', '=', $psIdx);
                }
                $stock = $stockQuery->first();
                if (empty($stock)) {
                    if ($psIdx > 0) {
                        throw new \Exception('재고코드가 올바르지 않습니다.');
                    }
                    $stockService->createStockCode(['prd_idx' => $prdIdx]);
                    $stock = ProductStockModel::query()
                        ->select(['ps_idx'])
                        ->where('ps_prd_idx', '=', $prdIdx)
                        ->first();
                }
                $stock = is_array($stock) ? $stock : ($stock ? $stock->toArray() : []);
                $psIdx = (int)($stock['ps_idx'] ?? 0);

                $result = $stockService->registerStockChange([
                    'ps_idx' => $psIdx,
                    'stock_mode' => $requestData['stock_mode'] ?? '',
                    'stock_kind' => $requestData['stock_kind'] ?? '',
                    'stock_day' => $requestData['stock_day'] ?? '',
                    'stock_qty' => $requestData['stock_qty'] ?? '',
                    'stock_memo' => $requestData['stock_memo'] ?? '',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => (string)($result['message'] ?? '재고 변경이 완료되었습니다.'),
                    'stock' => (int)($result['stock'] ?? 0),
                    'stock_hold' => (int)($result['stock_hold'] ?? 0),
                    'ps_idx' => $psIdx,
                ]);
            }
            if ($actionMode === 'measured_weight_update') {
                $weights = $service->updateProductMeasuredWeights(
                    (int)($requestData['prd_idx'] ?? 0),
                    $requestData['product_weight'] ?? '',
                    $requestData['total_weight'] ?? '',
                    $actor
                );

                return response()->json([
                    'success' => true,
                    'message' => '실측 중량을 변경했습니다.',
                    'weights' => $weights,
                ]);
            }

            throw new \Exception('유효하지 않은 action_mode입니다.');
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * 랙조회 모달용 그룹. 첫 글자 A/B/C, 나머지는 기타.
     *
     * @return array{A: string[], B: string[], C: string[], ETC: string[]}
     */
    private function rackGroupsForLookup(): array
    {
        $groups = [
            'A' => [],
            'B' => [],
            'C' => [],
            'ETC' => [],
        ];

        try {
            $rackList = (new RackService())->getRackList();
        } catch (\Throwable $e) {
            return $groups;
        }

        $codes = [];
        foreach ($rackList as $rack) {
            $code = trim((string)($rack['code'] ?? ''));
            if ($code !== '') {
                $codes[$code] = $code;
            }
        }

        $codes = array_values($codes);
        usort($codes, 'strnatcasecmp');

        foreach ($codes as $code) {
            $first = function_exists('mb_substr')
                ? mb_strtoupper(mb_substr($code, 0, 1, 'UTF-8'), 'UTF-8')
                : strtoupper(substr($code, 0, 1));
            if ($first === 'A' || $first === 'B' || $first === 'C') {
                $groups[$first][] = $code;
            } else {
                $groups['ETC'][] = $code;
            }
        }

        return $groups;
    }

    /**
     * 랙코드 앞자리(A10-2-1 → A10)별 상품 수와 재고 합계.
     *
     * @return array<string, array{group: string, product_count: int, stock_sum: int}>
     */
    private function rackStockGroups(): array
    {
        $rows = ProductStockModel::query()
            ->select(['ps_rack_code', 'ps_stock'])
            ->whereRaw("ps_rack_code IS NOT NULL AND TRIM(ps_rack_code) <> ''")
            ->get()
            ->toArray();

        $groups = [];
        foreach ($rows as $row) {
            $stock = (int)($row['ps_stock'] ?? 0);
            $seenGroups = [];
            foreach ($this->rackCodeParts((string)($row['ps_rack_code'] ?? '')) as $part) {
                $group = $this->rackGroupKey($part);
                if ($group === '' || isset($seenGroups[$group])) {
                    continue;
                }
                $seenGroups[$group] = true;
                if (!isset($groups[$group])) {
                    $groups[$group] = [
                        'group' => $group,
                        'product_count' => 0,
                        'stock_sum' => 0,
                    ];
                }
                $groups[$group]['product_count']++;
                $groups[$group]['stock_sum'] += $stock;
            }
        }

        uksort($groups, static function ($left, $right) {
            return strnatcasecmp((string)$right, (string)$left);
        });

        return $groups;
    }

    /**
     * 선택한 랙 묶음 또는 검색어에 해당하는 상품을 랙코드 내림차순으로 묶는다.
     *
     * @return array<string, array<int, array{prd_idx: int, ps_idx: int, name: string, barcode: string, rack_code: string, stock: int, image: string}>>
     */
    private function rackStockProducts(string $group, string $searchValue): array
    {
        $query = ProductStockModel::query()
            ->select([
                'prd_stock.ps_idx',
                'prd_stock.ps_prd_idx',
                'prd_stock.ps_rack_code',
                'prd_stock.ps_stock',
                'COMPARISON_DB.CD_NAME',
                'COMPARISON_DB.CD_IMG',
                'COMPARISON_DB.img_mode',
                'COMPARISON_DB.cd_code_fn',
            ])
            ->leftJoin('COMPARISON_DB', 'prd_stock.ps_prd_idx', '=', 'COMPARISON_DB.CD_IDX')
            ->whereRaw("prd_stock.ps_rack_code IS NOT NULL AND TRIM(prd_stock.ps_rack_code) <> ''");

        if ($group !== '') {
            $query->whereRaw(
                "(UPPER(TRIM(SUBSTRING_INDEX(TRIM(prd_stock.ps_rack_code), '-', 1))) = ?
                  OR CONCAT('/', REPLACE(REPLACE(UPPER(prd_stock.ps_rack_code), ' ', ''), '／', '/'), '/') LIKE CONCAT('%/', ?, '-%')
                  OR CONCAT('/', REPLACE(REPLACE(UPPER(prd_stock.ps_rack_code), ' ', ''), '／', '/'), '/') LIKE CONCAT('%/', ?, '/%'))",
                [$group, $group, $group]
            );
        }
        if ($searchValue !== '') {
            $query->whereRaw(
                '(INSTR(prd_stock.ps_rack_code, ?) > 0 OR INSTR(COMPARISON_DB.CD_NAME, ?) > 0 OR INSTR(COMPARISON_DB.cd_code_fn, ?) > 0)',
                [$searchValue, $searchValue, $searchValue]
            );
        }

        $rows = $query->get()->toArray();
        $groups = [];
        foreach ($rows as $row) {
            $rackCode = trim((string)($row['ps_rack_code'] ?? ''));
            $codeData = json_decode((string)($row['cd_code_fn'] ?? ''), true);
            $product = [
                'prd_idx' => (int)($row['ps_prd_idx'] ?? 0),
                'ps_idx' => (int)($row['ps_idx'] ?? 0),
                'name' => trim((string)($row['CD_NAME'] ?? '')),
                'barcode' => trim((string)(is_array($codeData) ? ($codeData['jan'] ?? '') : '')),
                'rack_code' => $rackCode,
                'stock' => (int)($row['ps_stock'] ?? 0),
                'image' => $this->rackProductImageUrl((string)($row['CD_IMG'] ?? ''), (string)($row['img_mode'] ?? '')),
            ];
            $nameHit = $searchValue !== '' && (
                mb_stripos($product['name'], $searchValue) !== false
                || mb_stripos($product['barcode'], $searchValue) !== false
            );
            $rackParts = $this->rackCodeParts($rackCode);
            if ($rackParts === []) {
                continue;
            }
            $rackHit = $searchValue !== '' && mb_stripos($rackCode, $searchValue) !== false;

            foreach ($rackParts as $part) {
                $partGroup = $this->rackGroupKey($part);
                if ($partGroup === '' || ($group !== '' && $partGroup !== $group)) {
                    continue;
                }
                if ($searchValue !== '' && $rackHit && !$nameHit && mb_stripos($part, $searchValue) === false) {
                    continue;
                }
                $itemKey = $product['ps_idx'] . ':' . $product['prd_idx'];
                if (isset($groups[$partGroup][$itemKey])) {
                    continue;
                }
                $product['sort_rack'] = $part;
                $groups[$partGroup][$itemKey] = $product;
            }
        }

        foreach ($groups as &$products) {
            $products = array_values($products);
            usort($products, static function ($left, $right) {
                $rackCompare = strnatcasecmp((string)($right['sort_rack'] ?? $right['rack_code']), (string)($left['sort_rack'] ?? $left['rack_code']));
                if ($rackCompare !== 0) {
                    return $rackCompare;
                }

                $stockCompare = (int)$right['stock'] <=> (int)$left['stock'];
                if ($stockCompare !== 0) {
                    return $stockCompare;
                }

                return strnatcasecmp((string)$left['barcode'], (string)$right['barcode']);
            });
        }
        unset($products);

        uksort($groups, static function ($left, $right) {
            return strnatcasecmp((string)$right, (string)$left);
        });

        return $groups;
    }

    /**
     * 상품 목록과 같은 규칙으로 대표 이미지 주소를 만든다.
     */
    private function rackProductImageUrl(string $image, string $imgMode): string
    {
        $image = trim($image);
        if ($image === '') {
            return '';
        }
        if ($imgMode === 'out' || strpos($image, '/') === 0 || preg_match('#^https?://#i', $image)) {
            return $image;
        }

        return '/data/comparion/' . $image;
    }

    /**
     * B01-4-0 / P01-1-0처럼 한 칸에 여러 랙이 있으면 각각 분리한다.
     *
     * @return string[]
     */
    private function rackCodeParts(string $rackCode): array
    {
        $rackCode = trim($rackCode);
        if ($rackCode === '') {
            return [];
        }

        $parts = preg_split('/\s*[\/／]\s*/u', $rackCode) ?: [];
        $codes = [];
        foreach ($parts as $part) {
            $part = trim((string)$part);
            if ($part !== '') {
                $codes[] = $part;
            }
        }

        return $codes;
    }

    /**
     * A10-2-1, B01-4-4 형태의 랙코드에서 묶음 키를 뽑는다.
     */
    private function rackGroupKey(string $rackCode): string
    {
        $rackCode = trim($rackCode);
        if ($rackCode === '') {
            return '';
        }

        $parts = explode('-', $rackCode, 2);
        return strtoupper(trim((string)$parts[0]));
    }
}
