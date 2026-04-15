<?php

namespace App\Controllers\Admin;

use App\Classes\Request;
use App\Core\BaseClass;
use App\Services\PartnersService;
use App\Services\GodoApiService;

class OrderController extends BaseClass {

    private $partnersService;
    private $godoApiService;

    public function __construct() {
        parent::__construct();
        $this->partnersService = new PartnersService();
        $this->godoApiService = new GodoApiService();
    }

    /**
     * 고도몰 주문내역 가져오기
     */
    public function getGodoOrderList(Request $request)
    {
        try{

            $requestData = $request->all();

            $today = date('Y-m-d');
            $dayOfWeek = date('N'); // 1(월)~7(일)
            // 월요일이면 지난주 금요일(-3일), 그 외는 전날(-1일)
            $default_start_date = ($dayOfWeek == 1)
                ? date('Y-m-d', strtotime('-3 days'))
                : date('Y-m-d', strtotime('-1 day'));

            $start_date = $requestData['start_date'] ?? $default_start_date;
            $end_date = $requestData['end_date'] ?? $today;
            $mode = $requestData['mode'] ?? 'p';
     
            $payload = [
                'start_date' => $start_date,
                'end_date' => $end_date,
                'mode' => $mode,
            ];
            $godoApiService = new GodoApiService();
            $orderList = $godoApiService->getOrderGoodsSummary($payload);

            //dd($orderList);

            $data = [
                'start_date' => $start_date,
                'end_date' => $end_date,
                'mode' => $mode,
                'orderList' => $orderList,
            ];

            return view('admin.order.godo_order', $data)
                ->extends('admin.layout.layout',['pageGroup2' => 'order', 'pageNameCode' => 'godo_order']);

        } catch (Throwable $e) {
            dump($e->getMessage());
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }


    /**
     * 거래처 목록 화면
     * @skin : skin.partners.php
     * @return array
     */
    public function partnersIndex() {

        $getData = $this->requestHandler->getAll(); // GET 데이터 받기

        $extraData = [];

        $result = $this->partnersService->getPartnersList($getData, $extraData);

        $data = [   
            'partnersList' => $result,
        ];

        return $data;
    }




    /**
     * @deprecated 2026-01-11
     * 
     * 고도몰 상품준비중 가져오기
     * @skin : skin.godo_order_list.php
     * @return array
     */
    public function godoOrderList() {

        $getData = $this->requestHandler->getAll(); // GET 데이터 받기
        $extraData = [];

        $requestData =  $getData;

        $result = $this->godoApiService->getOrderList($requestData);

        /*
        $data = [
            'orderList' => $result,
        ];
        */

        return $result;

    }

}