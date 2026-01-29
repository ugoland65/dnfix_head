<?php

namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Utils\TelegramUtils;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Utils\Pagination;
use App\Core\AuthAdmin;
use App\Services\WorkService;
use App\Services\CommentService;
use App\Services\AdminServices;
use App\Services\WorkLogHistoryService;
use App\Services\WorkViewCheckService;

class WorkController extends BaseClass
{

    /**
     * 업무 요청 목록 조회
     * 
     * @param Request $request
     * @return view
     */
    public function taskRequest(Request $request)
    {
        try{

            $requestData = $request->all();

            $category = $requestData['category'] ?? '업무요청';
            $s_state = $requestData['s_state'] ?? '대기/확인';
            $s_keyword = $requestData['s_keyword'] ?? null;
            $s_scope = $requestData['s_scope'] ?? null;

            if( $category == '공지사항' ){
                $s_state = '전체보기';
                $s_scope = 'my_all';
            }

            $page = $requestData['page'] ?? 1;
            $perPage = $requestData['per_page'] ?? 50;

            $workService = new WorkService();

            $payload = [
                'category' => $category,
                'state' => $s_state,
                'keyword' => $s_keyword,
                'paging' => true,
                'page' => $page,
                'per_page' => $perPage,
                'scope' => $s_scope,
            ];
            $workLogList = $workService->getWorkLogList($payload);

            $pagination = new Pagination(
                $workLogList['total'],
                $workLogList['per_page'],
                $workLogList['current_page'],
                10
            );

            $paginationHtml = $pagination->renderLinks();
            $paginationArray = $pagination->toArray();

            $myDashboardCounts = $workService->getMyDashboardCounts(AuthAdmin::getSession('sess_idx'));

            //dd($myDashboardCounts);
            
            $data = [
                'category' => $category,
                's_state' => $s_state,
                's_keyword' => $s_keyword,
                'workLogList' => $workLogList,
                'myDashboardCounts' => $myDashboardCounts,
                's_scope' => $s_scope,
                'paginationHtml' => $paginationHtml,
                'paginationArray' => $paginationArray,
            ];

            return view('admin.work.work_board', $data)
                ->extends('admin.layout.layout', ['pageGroup2' => 'staff', 'pageNameCode' => 'work_board']);

        } catch (Exception $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }


    /**
     * 업무 요청 신규생성 페이지
     * 
     * @param Request $request
     * @return view
     */
    public function taskRequestCreate(Request $request)
    {
        try{

            $requestData = $request->all();
            $category = $requestData['category'] ?? "업무요청";

            $workConfig = config('admin.workConfig');
            $workLogCate = $workConfig['work_log_cate'];

            $CommentService = new CommentService();
            $mentionTarget = $CommentService->getMentionTarget();

            $data = [
                'title' => '업무 게시판 등록',
                'mode' => 'create',
                'category' => $category,
                'workLogCate' => $workLogCate,
                'mentionTarget' => $mentionTarget,
            ];

            return view('admin.work.work_board_create', $data)
                ->extends('admin.layout.layout', ['pageGroup2' => 'staff', 'pageNameCode' => 'work_board']);

        } catch (Exception $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }


    /**
     * 업무 요청 수정 페이지
     */
    public function taskRequestModify(Request $request, $idx)
    {
        try{

            $workService = new WorkService();
            $workInfo = $workService->getWorkLog($idx);

            $workConfig = config('admin.workConfig');
            $workLogCate = $workConfig['work_log_cate'];

            $CommentService = new CommentService();
            $mentionTarget = $CommentService->getMentionTarget();

            $category = $workInfo['category'] ?? '';

            $data = [
                'title' => '업무 게시판 수정',
                'mode' => 'modify',
                'category' => $category,
                'workInfo' => $workInfo,
                'workLogCate' => $workLogCate,
                'mentionTarget' => $mentionTarget,
            ];

            return view('admin.work.work_board_create', $data)
                ->extends('admin.layout.layout', ['pageGroup2' => 'staff', 'pageNameCode' => 'work_board']);

        }
        catch (Exception $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }


    /**
     * 업무 요청 상세 조회
     * 
     * @param Request $request
     * @return view
     */
    public function taskRequestDetail(Request $request, $idx)
    {
        try{

            $workService = new WorkService();

            $workService->checkWorkLogRead($idx); // 읽음 체크

            $workLog = $workService->getWorkLog($idx);

            $CommentService = new CommentService();
            $mentionTarget = $CommentService->getMentionTarget();

            $workLogHistoryService = new WorkLogHistoryService();
            $workLogHistoryList = $workLogHistoryService->getWorkLogHistoryList($idx);

            $WorkViewCheckService = new WorkViewCheckService();

            if( $workLog['reg_idx'] == AuthAdmin::getSession('sess_idx') ){
                $isViewCheck = true;
            }else{
                //읽음 체크 여부 확인
                $isViewCheck = $WorkViewCheckService->isViewCheck('log', $idx, AuthAdmin::getSession('sess_idx'), $workLog['reg_idx']);
            }

            $viewCheckList = $WorkViewCheckService->getViewCheckList([
                'mode' => 'log',
                'tidx' => $idx,
            ]);

            $data = [
                'workLog' => $workLog,
                'workLogHistoryList' => $workLogHistoryList,
                'mentionTarget' => $mentionTarget,
                'isViewCheck' => $isViewCheck,
                'viewCheckList' => $viewCheckList ?? [],
            ];

            return view('admin.work.work_board_detail', $data)
                ->extends('admin.layout.layout', ['pageGroup2' => 'staff', 'pageNameCode' => 'work_board']);

        } catch (Exception $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }


    /**
     * 업무 요청 신규생성 저장
     * 
     * @param Request $request+
     * @return array
     */
    public function saveTaskRequest(Request $request)
    {
        try{

            $payload = $request->all();
            $files = $request->allFiles();

            $workService = new WorkService();
            $workInfo = $workService->saveTaskRequest($payload, $files);


            //dd($workInfo);
            return redirect()->to('/admin/work/TaskRequestDetail/'.$workInfo['idx'])->with('success', '업무 요청 저장 완료');

        } catch (Exception $e) {
            dd($e);
            //return redirect()->back()->with('error', $e->getMessage());
        }
    }


    /**
     * 업무 요청 액션
     * 
     * @param Request $request
     * @return array
     */
    public function taskRequestAction(Request $request)
    {
        try{

            $requestData = $request->all();
            $actionMode = $requestData['action_mode'] ?? '';

            $workService = new WorkService();

            //참여자 추가
            if( $actionMode == 'add_participant' ){
                $payload = [
                    'idx' => $requestData['idx'] ?? null,
                    'target_mb_idxs' => $requestData['target_mb_idxs'] ?? [],
                ];
                $result = $workService->addParticipant($payload);
            }

            //참여자 제거
            elseif( $actionMode == 'remove_participant' ){
                $payload = [
                    'idx' => $requestData['idx'] ?? null,
                    'target_mb_idx' => $requestData['target_mb_idx'] ?? null,
                ];
                $result = $workService->removeParticipant($payload);
            }
            //체크 처리
            elseif( $actionMode == 'view_check' ){
                $idx = $requestData['idx'] ?? null;
                if (empty($idx)) {
                    throw new Exception('필수 값이 누락되었습니다.');
                }
                $WorkViewCheckService = new WorkViewCheckService();

                $payload = [
                    'mode' => 'log',
                    'tidx' => $idx,
                    'mb_idx' => AuthAdmin::getSession('sess_idx'),
                ];
                $result = $WorkViewCheckService->addViewCheck($payload);
            }
            //상태 변경
            elseif( $actionMode == 'change_state' ){
                $payload = [
                    'idx' => $requestData['idx'] ?? null,
                    'state' => $requestData['state'] ?? null,
                ];
                $result = $workService->changeState($payload);
            }

            return response()->json([
                'success' => true,
                'message' => $actionMode === 'view_check' ? '확인 처리 완료' : ($actionMode === 'change_state' ? '상태 변경 완료' : '처리 완료'),
                'data' => $result ?? null,
            ]);

        }
        catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}