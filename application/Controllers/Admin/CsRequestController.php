<?php
namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Services\CsRequestService;

class CsRequestController extends BaseClass
{

    //C/S 목록
    public function csList(Request $request)
    {
        try{

            $requestData = $request->all();

            $csRequestService = new CsRequestService();
            $csRequestList = $csRequestService->getCsRequestList();
            $data = [
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

    //C/S 처리 요청
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

    //C/S 상세
    public function csDetail(Request $request, $idx)
    {

        try{

            $requestData = $request->all();

            $csRequestService = new CsRequestService();
            $csRequest = $csRequestService->getCsRequestDetail($idx);

            $data = [
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


    //C/S 상태변경
    public function updateCsStatus(Request $request)
    {
        try{

            $requestData = $request->all();

            $payload = [
                'idx' => $requestData['idx'],
                'cs_status' => $requestData['cs_status'],
                'process_action' => $requestData['process_action'],
            ];

            $csRequestService = new CsRequestService();
            $csRequest = $csRequestService->updateCsStatus($payload);

            return response()->json([
                'success' => true,
                'message' => 'C/S 상태변경 완료',
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