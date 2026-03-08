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

            $csRequestService = new CsRequestService();

            $csRequestCount = $csRequestService->getCsRequestCount();

            //dd($csRequestCount);

            $payload = [
                'cs_status' => $cs_status,
            ];
            $csRequestList = $csRequestService->getCsRequestList($payload);
            $data = [
                's_cs_status' => $cs_status,
                'csRequestCount' => $csRequestCount,
                'csRequestList' => $csRequestList,
            ];

            return view('admin.cs.cs_list', $data)
                ->extends('admin.layout.layout',[
                    'pageGroup2' => 'order',
                    'pageNameCode' => 'godo_order_list'
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

            $data = [
                'mode' => 'create',
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
                $category = $requestData['category'] ?? null;
                $cs_body = $requestData['cs_body'] ?? null;

                if( !empty($order_no) ){
                    $godoApiService = new GodoApiService();
                    $godoGoodsInfo = $godoApiService->getGodoOrderInfo($order_no);

                    $payload = [
                        'orderNo' => $order_no,
                        'orderDate' => $godoGoodsInfo['regDt'],
                        'memNo' => $godoGoodsInfo['memNo'],
                        'memId' => $godoGoodsInfo['memId'],
                        'category' => $category,
                        'csBody' => $cs_body,
                    ];

                    $csRequest = $csRequestService->createCsRequest($payload);

                    $message = 'C/S 처리 요청 완료';

                }

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