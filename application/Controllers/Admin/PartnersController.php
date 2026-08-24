<?php

namespace App\Controllers\Admin;

use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Services\PartnersService;

class PartnersController extends BaseClass
{
    public function partnerInfo(Request $request)
    {
        try {
            $idx = (int)($request->all()['idx'] ?? 0);
            $partnersService = new PartnersService();
            $data = $partnersService->getPartnerInfo($idx);

            return view('admin.partner.partner_info', [
                'idx' => $idx,
                'partner' => $data['partner'] ?? [],
                'partnerInfo' => $data['info'] ?? [],
                'categories' => $data['categories'] ?? [],
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function savePartner(Request $request)
    {
        try {
            $partnersService = new PartnersService();
            $result = $partnersService->savePartner($request->all());

            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? '저장되었습니다.',
                'data' => $result,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
