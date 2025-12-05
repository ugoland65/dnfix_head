<?php

namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Classes\DB;
use App\Services\RackService;

class RackController extends BaseClass 
{

    /**
     * 랙목록
     * 
     * @param Request $request
     * @return array
     */
    public function rackList(Request $request)
    {

        try {

            $rackService = new RackService();
            $rackList = $rackService->getRackList(['showMode' => 'withPrdCount']);

            $data = [
                'title' => '랙목록',
                'description' => '랙목록',
                'rackList' => $rackList,
            ];

            return view('admin.stock.rack_list', $data)
                ->extends('admin.layout.layout',['pageGroup2' => 'order']);

        } catch (Throwable $e) {

            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);

        }

    }


    /**
     * 랙신규등록
     * 
     * @param Request $request
     * @return array
     */
    public function rackCreate(Request $request)
    {
        return view('admin.stock.rack_info');
    }


    /**
     * 랙상세
     * 
     * @param Request $request
     * @return array
     */
    public function rackInfo(
        Request $request, 
        int $idx
    )
    {
        
        try{

            //$idx = $request->input('idx');

            if( empty($idx) ){
                throw new Exception('랙 고유번호가 없습니다.');
            }

            $rackService = new RackService();
            $rackInfo = $rackService->getRackInfo($idx);

            $data = [
                'title' => '랙상세',
                'rackInfo' => $rackInfo,
            ];

            return view('admin.stock.rack_info', $data);

        } catch (Throwable $e) {

            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);

        }

    }


    /**
     * 랙 등록
     * 
     * @param Request $request
     * @return array
     */
    public function saveRack(Request $request)
    {

        try{

            $requestData = $request->all();

            $idx = $requestData['idx'] ?? null;
            $code = $requestData['code'] ?? '';

            $isModify = !empty($idx) ? true : false;

            if( $isModify ){
                $successMessage = '랙 수정 완료';
            }else{
                $successMessage = '랙 등록 완료';
            }

            $rackService = new RackService();
            $result = $rackService->saveRack($requestData);

            return response()->json([
                'success' => true,
                'message' => $successMessage,
            ]);

        } catch (Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);

        }

    }


    /**
     * 랙 삭제
     * 
     * @param Request $request
     * @return array
     */
    public function deleteRack(Request $request)
    {

        try{

            $requestData = $request->all();

            $code = $requestData['code'] ?? '';

            $payload = [
                'idx' => $requestData['idx'] ?? null,
            ];

            $rackService = new RackService();
            $result = $rackService->deleteRack($payload);

            return response()->json([
                'success' => true,
                'message' => '랙 삭제 완료',
            ]);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

    }

    /**
     * 랙 그룹 변경 페이지
     * 
     * @param Request $request
     * @return array
     */
    public function rackChange(Request $request)
    {

        try{

            $requestData = $request->all();

            $mode = $requestData['mode'] ?? '';
            $code = $requestData['code'] ?? '';

            if( $mode == 'group' ){
                $title = '랙 그룹명 변경';
            } else if( $mode == 'move' ){
                $title = '랙 이동';
            }

            $data = [
                'title' => $title,
                'mode' => $mode,
                'code' => $code,
            ];

            return view('admin.stock.rack_change', $data);

        } catch (Throwable $e) {

            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);

        }

    }

    /**
     * 랙 그룹 변경 저장
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function saveRackChange(
        Request $request,
        RackService $rackService
    )
    {

        try{

            DB::beginTransaction();

            $requestData = $request->all();

            $mode = $requestData['mode'] ?? '';
            $code = $requestData['code'] ?? '';
            $changeCode = $requestData['change_code'] ?? '';

            if( empty($changeCode) ){
                throw new Exception('변경할 랙 코드를 입력해주세요.');
            }

            //랙 그룹명 변경
            if( $mode == 'group' ){

                $payload = [
                    'code_group' => $code,
                    'change_code' => $changeCode,

                ];
                $rackService->changeRackGroup($payload);

                $successMessage = '랙 그룹명 변경 완료';

            //랙 상품 이동
            } else if( $mode == 'move' ){

                $payload = [
                    'code' => $code,
                    'change_code' => $changeCode,
                ];
                $rackService->moveRackProduct($payload);

                $successMessage = '랙 상품 이동 완료';

            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $successMessage,
            ]);

        } catch (Throwable $e) {

            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

    }

}