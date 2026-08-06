<?php

namespace App\Controllers\Admobile;

use App\Auth\AdmobileSession;
use App\Classes\Request;
use App\Core\BaseClass;
use App\Models\OrderSheetModel;
use App\Services\OrderPrdUnitService;
use App\Services\OrderSheetService;
use App\Utils\Pagination;

class OrderSheetController extends BaseClass
{
    /**
     * 모바일 주문(발주) 목록.
     */
    public function list(Request $request)
    {
        if (!AdmobileSession::isAuthenticated()) {
            return redirect('/admobile/login');
        }

        $requestData = $request->all();
        $page = max(1, (int)($requestData['page'] ?? 1));
        $ooState = (string)($requestData['oo_state'] ?? '4');
        $allowedStates = ['all', 'ing', '1', '2', '4', '5', '7'];
        if (!in_array($ooState, $allowedStates, true)) {
            $ooState = '4';
        }

        $orderSheetListResult = (new OrderSheetService())->getOrderSheetList([
            'page' => $page,
            'per_page' => 20,
            'oo_import' => 'all',
            'oo_state' => $ooState,
            'oo_form_idx' => 0,
            'search_value' => '',
        ]);

        $pagination = new Pagination(
            $orderSheetListResult['total'],
            $orderSheetListResult['per_page'],
            $orderSheetListResult['current_page'],
            5
        );

        return view('admobile.order_sheet.list', [
            'orderSheetList' => $orderSheetListResult['data'],
            'totalCount' => $orderSheetListResult['total'],
            'ooState' => $ooState,
            'paginationHtml' => $pagination->renderLinks(),
        ])->extends('admobile.layout.layout', [
            'pageTitle' => '주문(발주) 리스트',
        ]);
    }

    /**
     * 모바일 입고수량 검수 조회 화면.
     */
    public function stock(Request $request)
    {
        if (!AdmobileSession::isAuthenticated()) {
            return redirect('/admobile/login');
        }

        $orderIdx = (int)($request->all()['idx'] ?? 0);
        if ($orderIdx <= 0) {
            return view('admin.errors.404', [
                'message' => '주문서 번호가 없습니다.',
            ])->response(404);
        }

        $orderSheet = OrderSheetModel::query()
            ->select(['oo_idx', 'oo_name'])
            ->where('oo_idx', '=', $orderIdx)
            ->first();
        $orderSheet = $orderSheet ? $orderSheet->toArray() : [];
        if (empty($orderSheet)) {
            return view('admin.errors.404', [
                'message' => '주문서를 찾을 수 없습니다.',
            ])->response(404);
        }

        $stockInspectionData = (new OrderPrdUnitService())->getStockInspectionUnits($orderIdx);

        return view('admobile.order_sheet.stock', [
            'orderIdx' => $orderIdx,
            'orderName' => (string)($orderSheet['oo_name'] ?? ''),
            'stockUnits' => $stockInspectionData['units'],
            'totalUnitCount' => $stockInspectionData['total_count'],
            'failedUnitCount' => $stockInspectionData['failed_count'],
        ])->extends('admobile.layout.layout', [
            'pageTitle' => '입고 수량검수',
        ]);
    }

    /**
     * 모바일 개별 상품 수량검수 화면.
     */
    public function stockUnit(Request $request)
    {
        if (!AdmobileSession::isAuthenticated()) {
            return redirect('/admobile/login');
        }

        $requestData = $request->all();
        $orderIdx = (int)($requestData['idx'] ?? 0);
        $bidx = (int)($requestData['bidx'] ?? 0);
        $pidx = (int)($requestData['pidx'] ?? 0);

        try {
            $inspectionHistory = (new OrderPrdUnitService())->getInspectionHistory(
                $orderIdx,
                $bidx,
                $pidx,
                $this->getCurrentActor()['idx']
            );

            return view('admobile.order_sheet.stock_unit', [
                'orderIdx' => $orderIdx,
                'unit' => $inspectionHistory['unit'],
                'inspectionRecords' => $inspectionHistory['records'],
                'checkedTotalQty' => $inspectionHistory['checked_total_qty'],
                'remainingQty' => $inspectionHistory['remaining_qty'],
            ])->extends('admobile.layout.layout', [
                'pageTitle' => '개별 수량검수',
            ]);
        } catch (\Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }

    /**
     * 모바일 주문상품 액션 라우터.
     */
    public function action(Request $request)
    {
        if (!AdmobileSession::isAuthenticated()) {
            return response()->json(['success' => false, 'message' => '로그인이 필요합니다.'], 401);
        }

        try {
            $requestData = $request->all();
            $actionMode = (string)($requestData['action_mode'] ?? '');
            $service = new OrderPrdUnitService();

            if ($actionMode === 'inspection_add') {
                $service->addInspectionRecord(
                    (int)($requestData['idx'] ?? 0),
                    (int)($requestData['bidx'] ?? 0),
                    (int)($requestData['pidx'] ?? 0),
                    (int)($requestData['checked_qty'] ?? 0),
                    $this->getCurrentActor()
                );
                $message = '검수수량을 저장했습니다.';
            } elseif ($actionMode === 'inspection_complete') {
                $service->completeInspection(
                    (int)($requestData['idx'] ?? 0),
                    (int)($requestData['bidx'] ?? 0),
                    (int)($requestData['pidx'] ?? 0),
                    $this->getCurrentActor()
                );
                $message = '주문수량 기준으로 수량체크를 완료했습니다.';
            } elseif ($actionMode === 'inspection_record_update') {
                $service->updateInspectionRecord(
                    (int)($requestData['inspection_idx'] ?? 0),
                    (int)($requestData['checked_qty'] ?? 0),
                    $this->getCurrentActor()['idx']
                );
                $message = '검수수량을 수정했습니다.';
            } elseif ($actionMode === 'inspection_record_delete') {
                $service->deleteInspectionRecord(
                    (int)($requestData['inspection_idx'] ?? 0),
                    $this->getCurrentActor()['idx']
                );
                $message = '검수 이력을 삭제했습니다.';
            } elseif ($actionMode === 'inspection_record_memo_save') {
                $memo = $service->saveInspectionRecordMemo(
                    (int)($requestData['inspection_idx'] ?? 0),
                    (string)($requestData['memo'] ?? ''),
                    $this->getCurrentActor()['idx']
                );
                return response()->json([
                    'success' => true,
                    'message' => $memo === '' ? '검수 이력 메모를 삭제했습니다.' : '검수 이력 메모를 저장했습니다.',
                    'memo' => $memo,
                ]);
            } elseif ($actionMode === 'inspection_memo_save') {
                $memo = $service->saveInspectionMemo(
                    (int)($requestData['idx'] ?? 0),
                    (int)($requestData['bidx'] ?? 0),
                    (int)($requestData['pidx'] ?? 0),
                    (string)($requestData['memo'] ?? '')
                );
                return response()->json([
                    'success' => true,
                    'message' => $memo === '' ? '메모를 삭제했습니다.' : '메모를 저장했습니다.',
                    'memo' => $memo,
                ]);
            } else {
                throw new \Exception('유효하지 않은 action_mode입니다.');
            }

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    private function getCurrentActor(): array
    {
        AdmobileSession::start();

        return [
            'idx' => (int)($_SESSION['sess_idx'] ?? 0),
            'id' => (string)($_SESSION['sess_id'] ?? ''),
            'name' => (string)($_SESSION['sess_name'] ?? ''),
        ];
    }
}
