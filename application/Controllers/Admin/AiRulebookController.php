<?php

namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Classes\DB;
use App\Core\AuthAdmin;
use App\Services\AiRulebookService;

class AiRulebookController extends BaseClass
{
    public function rulebookList()
    {
        return view('admin.ai_rulebook.rulebook_list')
        ->extends('admin.layout.layout',[
            'pageGroup2' => 'ai',
            'pageNameCode' => 'rulebook_list'
        ]);
    }


    /**
     * AI 규칙북 상세
     * 
     * @param Request $request
     * @return View
     */
    public function rulebookDetail(Request $request, int $idx)
    {
        try{

            $rulebookService = new AiRulebookService();
            $rulebook = $rulebookService->getRulebookDetail($idx);

            $data = [
                'rulebook' => $rulebook
            ];

            return view('admin.ai_rulebook.rulebook_detail', $data)
                ->extends('admin.layout.layout',[
                    'pageGroup2' => 'ai',
                    'pageNameCode' => 'rulebook_detail'
                ]);

        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }


    /**
     * AI 규칙북 저장
     * 
     * @param Request $request
     * @return View
     */
    public function rulebookSave(Request $request )
    {
        try{

            // JSON 문자열(payload 내 *_json, output_format, rules_json)은 필터링 없이 원문 수신
            $payload = $request->all(FILTER_UNSAFE_RAW);
            // textarea 긴 본문은 환경에 따라 필터/파싱 누락될 수 있어 raw POST를 한 번 더 보정
            if (!array_key_exists('schema_help', $payload) && isset($_POST['schema_help'])) {
                $payload['schema_help'] = $_POST['schema_help'];
            }
            $payload['updated_by'] = (int)(AuthAdmin::getSession('sess_idx') ?? 0);

            DB::beginTransaction();
            
            $rulebookService = new AiRulebookService();
            $result = $rulebookService->saveRulebook($payload);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'AI 규칙북 저장 완료',
                'data' => $result,
            ]);

        } catch (Throwable $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
