<?php

namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Classes\Request;
use App\Core\BaseClass;
use App\Services\OrderSheetService;
use App\Services\OnaOrderGroupService;

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

            $data = [
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

        $onaOrderGroupService = new OnaOrderGroupService();
        $onaOrderGroupList = $onaOrderGroupService->getOnaOrderGroupForSelect();

        try{

            return view('admin.order_sheet.info', [
                'mode' => 'create',
                'onaOrderGroupList' => $onaOrderGroupList
            ])->extends('admin.layout.layout',['pageGroup2' => 'order']);

        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }

    }


    public function orderSheetInfo(Request $request, int $idx)
    {

        $orderSheetService = new OrderSheetService();
        $orderSheetInfo = $orderSheetService->getOrderSheetInfo($idx);

        $onaOrderGroupService = new OnaOrderGroupService();
        $onaOrderGroupList = $onaOrderGroupService->getOnaOrderGroupForSelect();
        
        try {

            return view('admin.order_sheet.info', [
                'idx' => $idx,
                'mode' => 'modify',
                'orderSheetInfo' => $orderSheetInfo,
                'onaOrderGroupList' => $onaOrderGroupList
            ])->extends('admin.layout.layout',['pageGroup2' => 'order']);

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

            $orderSheetService = new OrderSheetService();
            $orderSheetInfo = $orderSheetService->saveOrderSheet($payload);

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

            // 주문서 상태 변경
            if( $actionMode == 'order_sheet_state' ){

                $orderSheetService = new OrderSheetService();
                $result = $orderSheetService->orderSheetState($requestData);
                $message = $result['message'] ?? $result['msg'] ?? "사용자가 주문서 상태를 변경했습니다.";

            // 주문서 파일 등록
            }elseif( $actionMode == 'orderSheetFile' ){

                $orderSheetService = new OrderSheetService();
                $result = $orderSheetService->orderSheetFile($requestData, $files);
                $message = $result['message'] ?? $result['msg'] ?? "주문서 파일을 등록했습니다.";

            // 주문서 파일 삭제
            }elseif( $actionMode == 'orderSheetFileDelete' ){

                $orderSheetService = new OrderSheetService();
                $result = $orderSheetService->orderSheetFileDelete($requestData);
                $message = $result['message'] ?? $result['msg'] ?? "주문서 파일을 삭제했습니다.";

            // 캘린더 결제기한 등록/수정
            }elseif( $actionMode == 'ApprovalPayment' ){

                $orderSheetService = new OrderSheetService();
                $result = $orderSheetService->approvalPayment($requestData);
                $message = $result['message'] ?? $result['msg'] ?? "결제기한을 등록/수정했습니다.";

            // 캘린더 완료처리
            }elseif( $actionMode == 'calendarOk' ){

                $orderSheetService = new OrderSheetService();
                $result = $orderSheetService->calendarOk($requestData);
                $message = $result['message'] ?? $result['msg'] ?? "캘린더 완료처리되었습니다.";

            // 캘린더 삭제
            }elseif( $actionMode == 'calendarDel' ){

                $orderSheetService = new OrderSheetService();
                $result = $orderSheetService->calendarDel($requestData);
                $message = $result['message'] ?? $result['msg'] ?? "캘린더가 삭제되었습니다.";

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