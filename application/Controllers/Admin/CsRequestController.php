<?php
namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Services\CsRequestService;
use App\Services\GodoApiService;

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

            $cs_status = $requestData['s_cs_status'] ?? '요청+처리중';
            $keyword = $requestData['s_keyword'] ?? '';

            $csRequestService = new CsRequestService();

            $csRequestCount = $csRequestService->getCsRequestCount();

            //dd($csRequestCount);

            $payload = [
                'cs_status' => $cs_status,
                'keyword' => $keyword,
            ];
            $csRequestList = $csRequestService->getCsRequestList($payload);
            $data = [
                's_cs_status' => $cs_status,
                's_keyword' => $keyword,
                'csRequestCount' => $csRequestCount,
                'csRequestList' => $csRequestList,
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

            $data = [
                'mode' => 'create',
                'apiMode' => $apiMode,
                'category' => $category,
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
                        'csBody' => $cs_body,
                    ];

                }else{

                    $payload[]['orderNo'] = $order_no;
                    if( !empty($order_date) ){
                        $payload[]['orderDate'] = $order_date;
                    }
                    if( !empty($payment_dt) ){
                        $payload[]['paymentDt'] = $payment_dt;
                    }
                    if( !empty($mem_no) ){
                        $payload[]['memNo'] = $mem_no;
                    }
                    if( !empty($mem_id) ){
                        $payload[]['memId'] = $mem_id;
                    }
                    if( !empty($mem_name) ){
                        $payload[]['memName'] = $mem_name;
                    }
                    if( !empty($mem_phone) ){
                        $payload[]['memPhone'] = $mem_phone;
                    }
                    if( !empty($group_nm) ){
                        $payload[]['groupNm'] = $group_nm;
                    }
                    if( !empty($receiver_name) ){
                        $payload[]['receiverName'] = $receiver_name;
                    }
                    if( !empty($receiver_phone) ){
                        $payload[]['receiverPhone'] = $receiver_phone;
                    }
                    $payload[]['category'] = $category;
                    $payload[]['csBody'] = $cs_body;

                }

                $csRequest = $csRequestService->createCsRequest($payload);

                $message = 'C/S 처리 요청 완료';


            }else{

                $payload = [
                    'idx' => $requestData['idx'],
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