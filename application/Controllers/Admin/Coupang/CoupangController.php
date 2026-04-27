<?php
namespace App\Controllers\Admin\Coupang;

use Exception;
use App\Classes\Request;
use App\Core\BaseClass;
use App\Services\Coupang\CoupangApiService;
use App\Services\Coupang\CoupangService;
use App\Utils\Pagination;

class CoupangController extends BaseClass
{

    /** 
     * 쿠팡 상품 목록
     * 
     * @param Request $request
     * @return view
     */
    public function coupangProductList(Request $request)
    {
        /*
        $payload = [
        ];

        $coupangApiService = new CoupangApiService();
        $data = $coupangApiService->getCoupangPrdList( $payload );
        
        dd($data);
        */

        
        try{

            $requestData = $request->all();

            $payload = [
                'page' => $requestData['page'] ?? 1,
                'per_page' => $requestData['per_page'] ?? 100,
            ];
            $coupangService = new CoupangService();
            $coupangProductList = $coupangService->getCoupangProductList($payload);

            $pagination = new Pagination(
                $coupangProductList['total'],
                $coupangProductList['per_page'],
                $coupangProductList['current_page'],
                10
            );
            $paginationHtml = $pagination->renderLinks();

            $data = [
                'coupangProductList' => $coupangProductList['data'],
                'paginationHtml' => $paginationHtml,
                'pagination' => $pagination->toArray(),
            ];

            return view('admin.coupang.product_list', $data)
                ->extends('admin.layout.layout',[
                    'pageGroup2' => 'coupang',
                    'pageNameCode' => 'product_list'
                ]);

        } catch (Exception $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }


    }

    /*
        쿠팡 처리 액션
     * 
     * @param Request $request
     * @return view
     */
    public function coupangAction(Request $request)
    {
        try{        

            $requestData = $request->all();
            $actionMode = $requestData['action_mode'] ?? '';

            $coupangApiService = new CoupangApiService();
            $coupangService = new CoupangService();

            // 상품 동기화
            if( $actionMode == 'product_sync' ){

                $result = $coupangApiService->productSync($requestData);
                $message = $result['message'] ?? $result['msg'] ?? "상품 동기화 처리되었습니다.";

            // 상품 상세 동기화
            }elseif( $actionMode == 'product_detail_sync' ){
                $result = $coupangApiService->productDetailSync($requestData);
                $message = $result['message'] ?? $result['msg'] ?? "상품 상세 동기화 처리되었습니다.";

            // 상품 매칭
            }elseif( $actionMode == 'product_matching' ){
                $result = $coupangService->matchProductStock($requestData);
                $message = $result['message'] ?? "상품 매칭 처리되었습니다.";

            // 상품 매칭 해제
            }elseif( $actionMode == 'product_matching_cancel' ){
                $result = $coupangService->cancelProductStockMatch($requestData);
                $message = $result['message'] ?? "상품 매칭 해제 처리되었습니다.";

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

        } catch (Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
}