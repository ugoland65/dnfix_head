<?php

namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Classes\Request;
use App\Core\BaseClass;
use App\Services\OrderSheetService;
use App\Services\OrderGroupService;
use App\Utils\Pagination;

class OrderSheetController extends BaseClass 
{

    /**
     * 주문서 목록
     * 
     * @param Request $request
     * @return View
     */
    public function orderSheetList(Request $request)
    {
        try{

            $requestData = $request->all();

            $page = (int)($requestData['page'] ?? ($requestData['pn'] ?? 1));
            if ($page < 1) {
                $page = 1;
            }

            $oo_state = $requestData['oo_state'] ?? 'ing';

            $orderSheetService = new OrderSheetService();
            $orderSheetListResult = $orderSheetService->getOrderSheetList([
                'page' => $page,
                'per_page' => 100,
                'oo_import' => $requestData['oo_import'] ?? 'all',
                'oo_state' => $oo_state,
                'oo_form_idx' => $requestData['oo_form_idx'] ?? 0,
                'search_value' => $requestData['search_value'] ?? '',
            ]);

            $pagination = new Pagination(
                $orderSheetListResult['total'],
                $orderSheetListResult['per_page'],
                $orderSheetListResult['current_page'],
                10
            );
            $paginationHtml = $pagination->renderLinks();

            $OrderGroupService = new OrderGroupService();
            $onaOrderGroupList = $OrderGroupService->getOnaOrderGroupForSelect();

            $data = [
                'orderSheetList' => $orderSheetListResult['data'],
                'totalCount' => $orderSheetListResult['total'],
                'paginationHtml' => $paginationHtml,
                'pagination' => $pagination->toArray(),
                'onaOrderGroupList' => $onaOrderGroupList,
                'oo_import' => $requestData['oo_import'] ?? 'all',
                'oo_state' => $oo_state,
                'oo_form_idx' => $requestData['oo_form_idx'] ?? '',
                'search_value' => $requestData['search_value'] ?? '',
            ];

            return view('admin.order_sheet.order_sheet_list', $data)
                ->extends('admin.layout.layout',[
                    'pageGroup2' => 'order',
                    'pageNameCode' => 'order_sheet_list'
                ]);

        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }

    
    /**
     * 주문서 생성 페이지
     * 
     * @param Request $request
     * @return View
     */
    public function orderSheetCreate(Request $request)
    {

        $OrderGroupService = new OrderGroupService();
        $onaOrderGroupList = $OrderGroupService->getOnaOrderGroupForSelect();

        try{

            return view('admin.order_sheet.info', [
                'mode' => 'create',
                'onaOrderGroupList' => $onaOrderGroupList
            ]);

        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }

    }


    /**
     * 주문서 상세 페이지
     * 
     * @param Request $request
     * @return View
     */
    public function orderSheetInfo(Request $request)
    {

        $requestData = $request->all();
        $idx = $requestData['idx'] ?? 0;

        if ($idx <= 0) {
            throw new Exception('주문서 번호가 없습니다.');
        }

        $orderSheetService = new OrderSheetService();
        $orderSheetInfo = $orderSheetService->getOrderSheetInfo($idx);

        $OrderGroupService = new OrderGroupService();
        $onaOrderGroupList = $OrderGroupService->getOnaOrderGroupForSelect();

        if( $orderSheetInfo['oo_form_idx'] > 0 ){
            $orderGroupInfo = $OrderGroupService->getOrderGroupInfo($orderSheetInfo['oo_form_idx']);
        }
        
        try {

            return view('admin.order_sheet.info', [
                'idx' => $idx,
                'mode' => 'modify',
                'orderSheetInfo' => $orderSheetInfo,
                'onaOrderGroupList' => $onaOrderGroupList,
                'orderGroupInfo' => $orderGroupInfo ?? []
            ]);

        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }


    /**
     * 주문서 저장
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function orderSheetSave(Request $request)
    {

        try{

            $postData = $request->all();
            $payload = $postData;
            $mode = $payload['mode'] ?? '';

            $orderSheetService = new OrderSheetService();
            if ($mode === 'create') {
                $orderSheetInfo = $orderSheetService->createOrderSheet($payload);
            } else {
                $orderSheetInfo = $orderSheetService->saveOrderSheet($payload);
            }

            return response()->json(
                [
                    'success' => true,
                    'message' => '주문서 저장 완료',
                    'order_sheet_idx' => $orderSheetInfo['order_sheet_idx'],
                ]
            );

        } catch (Throwable $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ]
            );
        }
    }


    /**
     * 주문서 주문그룹 상품목록
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function orderSheetDeleteProduct(Request $request)
    {
        try{

            $requestData = $request->all();

            $oo_idx = $requestData['oo_idx'] ?? 0;
            $oop_idx = $requestData['oop_idx'] ?? 0;
            $form_view = $requestData['form_view'] ?? 'hidden';

            $orderSheetService = new OrderSheetService();
            $payload = [
                'oo_idx' => $oo_idx,
                'oop_idx' => $oop_idx,
                'form_view' => $form_view,
            ];

            $orderSheetDeleteProduct = $orderSheetService->getOrderSheetDeleteProduct($payload);

            $data = [
                'oo_idx' => $oo_idx,
                'oop_idx' => $oop_idx,
                'form_view' => $form_view,
                'orderSheet' => $orderSheetDeleteProduct['orderSheet'],
                'orderGroup' => $orderSheetDeleteProduct['orderGroup'],
                'orderGroupProduct' => $orderSheetDeleteProduct['orderGroupProduct'],
            ];

            //dump($orderSheetDeleteProduct);

            return view('admin.order_sheet.order_sheet_delete_product', $data);

        }
        catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }


    /**
     * 주문서 주문그룹 상품 저장
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function orderSheetSaveGroupProduct(Request $request)
    {
        try{

            $requestData = $request->all();

            $orderSheetService = new OrderSheetService();
            $result = $orderSheetService->orderSheetSaveGroupProduct($requestData);

            return response()->json($result);

        }
        catch (Throwable $e) {
            return response()->json(
                [
                    'success' => false,
                    'msg' => $e->getMessage(),
                    'message' => $e->getMessage(),
                ]
            );
        }
    }


    /**
     * 주문서 액션
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function orderSheetAction(Request $request)
    {
        try{

            $requestData = $request->all();
            $files = $request->allFiles();

            $actionMode = $requestData['action_mode'] ?? '';

            $orderSheetService = new OrderSheetService();

            // 주문서 상태 변경
            if( $actionMode == 'order_sheet_state' ){

                $result = $orderSheetService->orderSheetState($requestData);
                $message = $result['message'] ?? $result['msg'] ?? "사용자가 주문서 상태를 변경했습니다.";

            // 주문서 파일 등록
            }elseif( $actionMode == 'orderSheetFile' ){

                $result = $orderSheetService->orderSheetFile($requestData, $files);
                $message = $result['message'] ?? $result['msg'] ?? "주문서 파일을 등록했습니다.";

            // 주문서 파일 삭제
            }elseif( $actionMode == 'orderSheetFileDelete' ){

                $result = $orderSheetService->orderSheetFileDelete($requestData);
                $message = $result['message'] ?? $result['msg'] ?? "주문서 파일을 삭제했습니다.";

            // 캘린더 결제기한 등록/수정
            }elseif( $actionMode == 'ApprovalPayment' ){

                $result = $orderSheetService->approvalPayment($requestData);
                $message = $result['message'] ?? $result['msg'] ?? "결제기한을 등록/수정했습니다.";

            // 캘린더 완료처리
            }elseif( $actionMode == 'calendarOk' ){

                $result = $orderSheetService->calendarOk($requestData);
                $message = $result['message'] ?? $result['msg'] ?? "캘린더 완료처리되었습니다.";

            // 캘린더 삭제
            }elseif( $actionMode == 'calendarDel' ){

                $result = $orderSheetService->calendarDel($requestData);
                $message = $result['message'] ?? $result['msg'] ?? "캘린더가 삭제되었습니다.";

            // 주문서 상품 실패 처리
            }elseif( $actionMode == 'orderSheetProductUnitFalse' ){

                $result = $orderSheetService->orderSheetProductFalse($requestData);
                $message = $result['message'] ?? $result['msg'] ?? "주문서 상품 실패 처리되었습니다.";

            // 주문서 상품 주문가격 등록
            }elseif( $actionMode == 'orderSheetProductNewPrice' ){

                $result = $orderSheetService->orderSheetProductNewPrice($requestData);
                $message = $result['message'] ?? $result['msg'] ?? "주문서 상품 주문가격 등록되었습니다.";

            // 주문서 상품 가격 변경
            }elseif( $actionMode == 'orderSheetProductPriceChange' ){

                $result = $orderSheetService->orderSheetProductPriceChange($requestData);
                $message = $result['message'] ?? $result['msg'] ?? "주문서 상품 가격 변경되었습니다.";

            // 주문서 상품 단종처리
            }elseif( $actionMode == 'orderSheetProductSoldOut' ){

                $result = $orderSheetService->orderSheetProductSoldOut($requestData);
                $message = $result['message'] ?? $result['msg'] ?? "주문서 상품 단종 처리되었습니다.";

            }else{
                throw new Exception('유효하지 않은 action_mode 입니다.');
            }

            return response()->json(array_merge(
                $result ?? [],
                [
                    'success' => true,
                    'message' => $message,
                ]
            ));

        }
        catch (Throwable $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ]
            );
        }
    }


}