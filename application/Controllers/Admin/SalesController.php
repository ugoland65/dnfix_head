<?php

namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Classes\DB;
use App\Services\ProductStockHistoryService;

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

}