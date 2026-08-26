<?php

namespace App\Controllers\Admin;

use Throwable;
use App\Classes\Request;
use App\Core\BaseClass;
use App\Models\PartnersModel;
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
            $supplierOptions = PartnersModel::query()
                ->select(['idx', 'name'])
                ->orderBy('name', 'asc')
                ->get()
                ->toArray();

            $data = [
                'purchaseOrderList' => $listResult['data'],
                'purchaseOrderSummary' => $listResult['summary'] ?? [],
                'paginationHtml' => $pagination->renderLinks(),
                'pagination' => $pagination->toArray(),
                'status' => $requestData['status'] ?? 'all',
                'supplier_name' => $requestData['supplier_name'] ?? '',
                'search_value' => $requestData['search_value'] ?? '',
                'supplierOptions' => $supplierOptions,
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
                'amountCalculation' => $detailResult['amountCalculation'] ?? [],
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
     * 주문번호로 고도몰 주문상품 조회 및 필요 시 동기화
     *
     * @param Request $request
     * @return \App\Core\JsonResponse
     */
    public function purchaseOrderGoodsLookup(Request $request)
    {
        try {
            $requestData = $request->all();
            $purchaseOrderIdx = (int)($requestData['purchase_order_idx'] ?? 0);
            $orderNo = trim((string)($requestData['order_no'] ?? ''));

            $result = (new PurchaseService())->getGodoOrderGoodsForPurchase($purchaseOrderIdx, $orderNo);

            return response()->json([
                'success' => true,
                'message' => !empty($result['was_synced'])
                    ? '고도몰 최신 주문정보를 불러와 동기화했습니다.'
                    : '저장된 주문을 불러왔습니다.',
                'order_no' => (string)($result['order_no'] ?? ''),
                'goods' => $result['goods'] ?? [],
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 선택한 주문상품을 기존 발주서에 추가
     *
     * @param Request $request
     * @return \App\Core\JsonResponse
     */
    public function purchaseOrderGoodsAdd(Request $request)
    {
        try {
            $requestData = $request->all();
            $purchaseOrderIdx = (int)($requestData['purchase_order_idx'] ?? 0);
            $goodsIds = $requestData['goods_ids'] ?? [];
            if (is_string($goodsIds)) {
                $goodsIds = array_filter(array_map('trim', explode(',', $goodsIds)), function ($value) {
                    return $value !== '';
                });
            }
            if (!is_array($goodsIds)) {
                $goodsIds = [];
            }

            $result = (new PurchaseService())->addGodoOrderGoodsToPurchase($purchaseOrderIdx, $goodsIds);

            return response()->json([
                'success' => true,
                'message' => (string)($result['message'] ?? '상품을 발주서에 추가했습니다.'),
                'added_count' => (int)($result['added_count'] ?? 0),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 기존 발주서에서 주문상품 한 건 삭제
     *
     * @param Request $request
     * @return \App\Core\JsonResponse
     */
    public function purchaseOrderGoodsDelete(Request $request)
    {
        try {
            $requestData = $request->all();
            $purchaseOrderIdx = (int)($requestData['purchase_order_idx'] ?? 0);
            $purchaseOrderItemIdx = (int)($requestData['purchase_order_item_idx'] ?? 0);

            $result = (new PurchaseService())->deletePurchaseOrderItem(
                $purchaseOrderIdx,
                $purchaseOrderItemIdx
            );

            return response()->json([
                'success' => true,
                'message' => (string)($result['message'] ?? '발주상품을 삭제했습니다.'),
                'deleted_item_idx' => (int)($result['deleted_item_idx'] ?? 0),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 발주서 금액 산정 임시저장
     *
     * @param Request $request
     * @return \App\Core\JsonResponse
     */
    public function purchaseOrderAmountSave(Request $request)
    {
        try {
            $requestData = $request->all();
            $purchaseOrderIdx = (int)($requestData['purchase_order_idx'] ?? 0);
            $baseAmount = $requestData['base_amount'] ?? '';
            $additionalCosts = $requestData['additional_costs'] ?? [];

            $result = (new PurchaseService())->savePurchaseOrderAmountCalculation(
                $purchaseOrderIdx,
                $baseAmount,
                $additionalCosts
            );

            return response()->json([
                'success' => true,
                'message' => (string)($result['message'] ?? '발주서 금액을 저장했습니다.'),
                'base_amount' => (float)($result['base_amount'] ?? 0),
                'additional_cost_total' => (float)($result['additional_cost_total'] ?? 0),
                'final_amount' => (float)($result['final_amount'] ?? 0),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
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

