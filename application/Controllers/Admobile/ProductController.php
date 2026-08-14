<?php

namespace App\Controllers\Admobile;

use App\Auth\AdmobileSession;
use App\Classes\Request;
use App\Core\BaseClass;
use App\Services\ProductService;
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
            'sort_mode' => 'idx',
        ]);

        $pagination = new Pagination(
            (int)($productList['total'] ?? 0),
            (int)($productList['per_page'] ?? 20),
            (int)($productList['current_page'] ?? $page),
            5
        );
        $productConfig = config('admin.product');

        return view('admobile.product.list', [
            'productList' => $productList['data'] ?? [],
            'totalCount' => (int)($productList['total'] ?? 0),
            'inStock' => $inStock,
            'saleStatus' => trim((string)($requestData['s_sale_status'] ?? '')),
            'searchValue' => trim((string)($requestData['search_value'] ?? '')),
            'saleStatusOptions' => $productConfig['sale_status_options'] ?? [],
            'paginationHtml' => $pagination->renderLinks(),
        ])->extends('admobile.layout.layout', [
            'pageTitle' => '상품관리',
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
        if (strpos($returnTo, '/admobile/order/sheet/stock?') !== 0) {
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
}
