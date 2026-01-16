<?php

namespace App\Controllers\Admin;

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