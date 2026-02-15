<?php

namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Services\AdminActionLogService;

class AdminActionLogController extends BaseClass
{

    /**
     * 어드민 공용 액션 로그 목록 조회
     * 
     * @param Request $request
     * @return view
     */
    public function adminActionLogList(Request $request)
    {
        try{

            $requestData = $request->all();

            $target_type = $requestData['target_type'] ?? null;
            $target_pk = $requestData['prd_idx'] ?? null;

            $payload = [
                'target_type' => $target_type,
                'target_pk' => $target_pk,
            ];

            $adminActionLogService = new AdminActionLogService();
            $adminActionLogList = $adminActionLogService->getAdminActionLogList($payload);

            //dd($adminActionLogList);
            $data = [
                'adminActionLogList' => $adminActionLogList,
            ];
            return view('admin.admin_action_log.provider_log_list', $data);

        }
        catch(Throwable $e){
            dump($e->getMessage());
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }

}