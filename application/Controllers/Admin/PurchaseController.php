<?php

namespace App\Controllers\Admin;

use Throwable;
use App\Classes\Request;
use App\Core\BaseClass;
use App\Services\PurchaseService;
use App\Utils\Pagination;

class PurchaseController extends BaseClass
{
    /**
     * 구매대행 발주서 목록
     *
     * @param Request $request
     * @return \App\Core\View
     */
    public function purchaseOrderList(Request $request)
    {
        try {
            $requestData = $request->all();

            $page = (int)($requestData['page'] ?? ($requestData['pn'] ?? 1));
            if ($page < 1) {
                $page = 1;
            }

            $purchaseService = new PurchaseService();
            $listResult = $purchaseService->getPurchaseOrderList([
                'page' => $page,
                'per_page' => 50,
                'status' => $requestData['status'] ?? 'all',
                'supplier_name' => $requestData['supplier_name'] ?? '',
                'search_value' => $requestData['search_value'] ?? '',
            ]);

            $pagination = new Pagination(
                $listResult['total'],
                $listResult['per_page'],
                $listResult['current_page'],
                10
            );

            $data = [
                'purchaseOrderList' => $listResult['data'],
                'purchaseOrderSummary' => $listResult['summary'] ?? [],
                'paginationHtml' => $pagination->renderLinks(),
                'pagination' => $pagination->toArray(),
                'status' => $requestData['status'] ?? 'all',
                'supplier_name' => $requestData['supplier_name'] ?? '',
                'search_value' => $requestData['search_value'] ?? '',
            ];

            return view('admin.order.purchase_list', $data)
                ->extends('admin.layout.layout', [
                    'pageGroup2' => 'order',
                    'pageNameCode' => 'purchase_list',
                ]);
        } catch (Throwable $e) {
            return view('onadb.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }

    /**
     * 구매대행 발주서 상세
     *
     * @param Request $request
     * @return \App\Core\View
     */
    public function purchaseOrderDetail(Request $request)
    {
        try {
            $requestData = $request->all();
            $purchaseOrderIdx = (int)($requestData['idx'] ?? 0);

            $purchaseService = new PurchaseService();
            $detailResult = $purchaseService->getPurchaseOrderDetail($purchaseOrderIdx);

            return view('admin.order.purchase_detail', [
                'purchaseOrder' => $detailResult['purchaseOrder'],
                'purchaseOrderItems' => $detailResult['purchaseOrderItems'],
                'summary' => $detailResult['summary'],
            ])->extends('admin.layout.layout', [
                'pageGroup2' => 'order',
                'pageNameCode' => 'purchase_list',
            ]);
        } catch (Throwable $e) {
            return view('onadb.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }

    /**
     * 구매대행 발주서 삭제
     *
     * @param Request $request
     * @return \App\Core\JsonResponse
     */
    public function purchaseOrderDelete(Request $request)
    {
        try {
            $requestData = $request->all();
            $purchaseOrderIdx = (int)($requestData['idx'] ?? 0);

            $purchaseService = new PurchaseService();
            $result = $purchaseService->deletePurchaseOrder($purchaseOrderIdx);

            return response()->json([
                'success' => true,
                'message' => (string)($result['message'] ?? '발주서가 삭제되었습니다.'),
                'deleted_idx' => (int)($result['deleted_idx'] ?? 0),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 구매대행 발주서 병합
     *
     * @param Request $request
     * @return \App\Core\JsonResponse
     */
    public function purchaseOrderMerge(Request $request)
    {
        try {
            $requestData = $request->all();
            $purchaseOrderIdxs = $requestData['purchase_order_idxs'] ?? [];
            if (is_string($purchaseOrderIdxs)) {
                $purchaseOrderIdxs = array_filter(array_map('trim', explode(',', $purchaseOrderIdxs)), function ($value) {
                    return $value !== '';
                });
            }
            if (!is_array($purchaseOrderIdxs)) {
                $purchaseOrderIdxs = [];
            }

            $purchaseService = new PurchaseService();
            $result = $purchaseService->mergePurchaseOrders($purchaseOrderIdxs);

            return response()->json([
                'success' => true,
                'message' => (string)($result['message'] ?? '발주서가 병합되었습니다.'),
                'target_idx' => (int)($result['target_idx'] ?? 0),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}

