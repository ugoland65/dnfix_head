<?php

namespace App\Controllers\Onadb;

use App\Core\BaseClass;
use App\Services\UserServices;
use App\Services\ProductCommentService;
use App\Utils\Pagination;
use App\Classes\Request;

class CommentController extends BaseClass
{

    /**
     * 사용자 코멘트 메인
     */
    public function userCommentMainPage( Request $request, $nickname )
    {
        try{

            $requestData = $request->all();

            $page = $requestData['page'] ?? 1;
            $per_page = $requestData['per_page'] ?? 10;

            // URL 인코딩된 닉네임을 디코딩
            $nickname = urldecode($nickname);

            $userService = new UserServices();
            $user = $userService->getUserNick($nickname);

            if( empty($user) ){
                throw new Exception('사용자를 찾을 수 없습니다.');
            }

            $productCommentService = new ProductCommentService();
            $payload = [
                'user_idx' => $user['user_idx'],
                'paging' => true,
                'per_page' => 10,
                'page' => $page,
            ];
            $data_comment = $productCommentService->getUserCommentList($payload);

            $pagination = new Pagination(
                $data_comment['total'],
                $data_comment['per_page'],
                $data_comment['current_page'],
                10
            );
            $paginationHtml = $pagination->renderLinks();

            $paginationArray = $pagination->toArray();

            $data = [
                'nickname' => $nickname,
                'user' => $user,
                'data_comment' => $data_comment ?? [],
                'paginationHtml' => $paginationHtml,
                'paginationArray' => $paginationArray,
            ];

            return view('onadb.comment.user_comment', $data)
                ->extends('onadb.layout.layout');

        }catch( Throwable $e ){

            return view('onadb.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);

        }

    }

}