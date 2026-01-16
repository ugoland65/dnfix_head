<?php

namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Classes\DB;
use App\Services\ProductStockHistoryService;
use App\Services\GodoApiService;

class SalesController extends BaseClass 
{

    /**
     * 피킹리스트 화면
     * 
     * @param Request $request
     * @param int $idx
     * @return view
     */
    public function pickingList(
        Request $request,
        int $idx
    ) 
    {

        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        try{

            $productStockHistoryService = new ProductStockHistoryService();
            $pickingList = $productStockHistoryService->getPickingList($idx);

            $data = [
                'pickingList' => $pickingList['pickingList'],
                'error' => $pickingList['error'],
            ];

            return view('admin.sales.picking_list', $data)
                ->extends('admin.layout.popup_layout', ['headTitle' => '피킹리스트']);

        } catch (Exception $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }

    
    /**
     * 패킹리스트 출력
     * 
     * @param Request $request
     * @return view
     */
    public function packingList(Request $request)
    {

        try{

            $requestData = $request->all();

            // 기본값 설정
            $today = date('Y-m-d');
            $dayOfWeek = date('N'); // 1(월요일) ~ 7(일요일)
            
            // end_date 기본값: 오늘
            $default_end_date = $today;
            
            // start_date 기본값: 오늘이 월요일이면 금요일(-3일), 그 외에는 어제(-1일)
            if ($dayOfWeek == 1) { // 월요일
                $default_start_date = date('Y-m-d', strtotime('-3 days'));
            } else {
                $default_start_date = date('Y-m-d', strtotime('-1 day'));
            }


            $startDate = $requestData['start_date'] ?? $default_start_date;
            $endDate = $requestData['end_date'] ?? $default_end_date;

            $mode = $requestData['mode'] ?? 'b';

            $payload = [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'mode' => $mode,
            ];

            $godoApiService = new GodoApiService();
            $packingList = $godoApiService->getOrderPackingList($payload);

            //dump($packingList);

            $data = [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'packingList' => $packingList,
            ];

            return view('admin.sales.packing_list', $data)
                ->extends('admin.layout.popup_layout', ['headTitle' => '패킹리스트']);

        } catch (Exception $e) {

            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);

        }
    }

}