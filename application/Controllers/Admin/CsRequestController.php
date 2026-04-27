<?php
namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Services\CsRequestService;
use App\Services\GodoApiService;
use App\Utils\Pagination;

class CsRequestController extends BaseClass
{

    /**
     * C/S 목록
     * 
     * @param Request $request
     * @return view
     */
    public function csList(Request $request)
    {
        try{

            $requestData = $request->all();

            $page = (int)($requestData['page'] ?? ($requestData['pn'] ?? 1));
            if ($page < 1) {
                $page = 1;
            }

            $cs_status = $requestData['s_cs_status'] ?? '요청+처리중';
            $keyword = $requestData['s_keyword'] ?? '';

            $csRequestService = new CsRequestService();

            $csRequestCount = $csRequestService->getCsRequestCount();

            //dd($csRequestCount);

            $payload = [
                'page' => $page,
                'per_page' => 100,
                'cs_status' => $cs_status,
                'keyword' => $keyword,
            ];
            $csRequestList = $csRequestService->getCsRequestList($payload);

            $pagination = new Pagination(
                $csRequestList['total'],
                $csRequestList['per_page'],
                $csRequestList['current_page'],
                10
            );
            $paginationHtml = $pagination->renderLinks();

            $data = [
                's_cs_status' => $cs_status,
                's_keyword' => $keyword,
                'csRequestCount' => $csRequestCount,
                'csRequestList' => $csRequestList['data'],
                'paginationHtml' => $paginationHtml,
                'pagination' => $pagination->toArray(),
            ];

            return view('admin.cs.cs_list', $data)
                ->extends('admin.layout.layout',[
                    'pageGroup2' => 'work',
                    'pageNameCode' => 'cs_list'
                ]);

        } catch (Exception $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }


    /**
     * C/S 생성페이지
     * 
     * @param Request $request
     * @return view
     */
    public function csCreate(Request $request)
    {

        try{

            $requestData = $request->all();
            $apiMode = $requestData['apiMode'] ?? 'do';
            $category = $requestData['category'] ?? '출고준비';
            $orderNo = $requestData['orderNo'] ?? null;
            $orderDate = $requestData['orderDate'] ?? null;
            $paymentDt = $requestData['paymentDt'] ?? null;
            $memNo = $requestData['memNo'] ?? null;
            $memId = $requestData['memId'] ?? null;
            $memName = $requestData['memName'] ?? null;
            $memPhone = $requestData['memPhone'] ?? null;
            $groupNm = $requestData['groupNm'] ?? null;
            $receiverName = $requestData['receiverName'] ?? null;
            $receiverPhone = $requestData['receiverPhone'] ?? null;
            $actionDate = $requestData['actionDate'] ?? null;

            $data = [
                'mode' => 'create',
                'apiMode' => $apiMode,
                'category' => $category,
                'actionDate' => $actionDate,
                'orderNo' => $orderNo,
                'orderDate' => $orderDate,
                'paymentDt' => $paymentDt,
                'memNo' => $memNo,
                'memId' => $memId,
                'memName' => $memName,
                'memPhone' => $memPhone,
                'groupNm' => $groupNm,
                'receiverName' => $receiverName,
                'receiverPhone' => $receiverPhone,
            ];

            return view('admin.cs.cs_detail', $data);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }


    /**
     * C/S 상세
     * 
     * @param Request $request
     * @param int $idx
     * @return view
     */
    public function csDetail(Request $request, $idx)
    {

        try{

            $requestData = $request->all();

            $csRequestService = new CsRequestService();
            $csRequest = $csRequestService->getCsRequestDetail($idx);

            $data = [
                'mode' => 'detail',
                'csRequest' => $csRequest,
            ];

            return view('admin.cs.cs_detail', $data);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }


    /**
     * C/S 처리 요청
     * 
     * @param Request $request
     * @return json
     */
    public function createCsRequest(Request $request)
    {
        try{

            $requestData = $request->all();

            $csRequestService = new CsRequestService();
            $csRequest = $csRequestService->createCsRequest($requestData);

            return response()->json([
                'success' => true,
                'message' => 'C/S 처리 요청 완료',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }



    //C/S 상태변경
    public function updateCsStatus(Request $request)
    {
        try{

            $requestData = $request->all();

            $mode = $requestData['mode'] ?? null;

            $csRequestService = new CsRequestService();

            if( $mode == 'create' ){

                $order_no = $requestData['order_no'] ?? null; 
                $order_date = $requestData['order_date'] ?? null;
                $payment_dt = $requestData['payment_dt'] ?? null;
                $category = $requestData['category'] ?? null;
                $mem_no = $requestData['mem_no'] ?? null;
                $mem_id = $requestData['mem_id'] ?? null;
                $mem_name = $requestData['mem_name'] ?? null;
                $mem_phone = $requestData['mem_phone'] ?? null;
                $group_nm = $requestData['group_nm'] ?? null;
                $receiver_name = $requestData['receiver_name'] ?? null;
                $receiver_phone = $requestData['receiver_phone'] ?? null;
                $cs_body = $requestData['cs_body'] ?? null;
                $action_date = $requestData['action_date'] ?? null;

                if ($category !== '출고지정일') {
                    $action_date = null;
                }

                if( !empty($order_no) ){
                    
                    $godoApiService = new GodoApiService();
                    $godoGoodsInfo = $godoApiService->getGodoOrderInfo($order_no);

                    $payload = [
                        'orderNo' => $order_no,
                        'orderDate' => $godoGoodsInfo['regDt'],
                        'paymentDt' => $godoGoodsInfo['paymentDt'],
                        'memNo' => $godoGoodsInfo['memNo'],
                        'memId' => $godoGoodsInfo['memId'],
                        'memName' => $godoGoodsInfo['memNm'],
                        'memPhone' => $godoGoodsInfo['cellPhone'],
                        'receiverName' => $godoGoodsInfo['receiverName'],
                        'receiverPhone' => $godoGoodsInfo['receiverCellPhone'],
                        'category' => $category,
                        'actionDate' => $action_date,
                        'csBody' => $cs_body,
                    ];

                }else{
                    $payload = [
                        'orderNo' => $order_no,
                        'orderDate' => $order_date,
                        'paymentDt' => $payment_dt,
                        'memNo' => $mem_no,
                        'memId' => $mem_id,
                        'memName' => $mem_name,
                        'memPhone' => $mem_phone,
                        'groupNm' => $group_nm,
                        'receiverName' => $receiver_name,
                        'receiverPhone' => $receiver_phone,
                        'category' => $category,
                        'actionDate' => $action_date,
                        'csBody' => $cs_body,
                    ];

                }

                $csRequest = $csRequestService->createCsRequest($payload);

                $message = 'C/S 처리 요청 완료';


            }else{

                $payload = [
                    'idx' => $requestData['idx'],
                    'category' => $requestData['category'] ?? null,
                    'action_date' => $requestData['action_date'] ?? null,
                    'cs_status' => $requestData['cs_status'],
                    'process_action' => $requestData['process_action'],
                ];

                $csRequest = $csRequestService->updateCsStatus($payload);

                $message = 'C/S 상태변경 완료';

            }

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

}

?>