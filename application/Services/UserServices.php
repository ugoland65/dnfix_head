<?php

namespace App\Services;

use Exception;
use App\Auth\AuthService;
use App\Models\UserModel;
use App\Auth\OnadbAuth;

class UserServices
{

    /**
     * 중복확인
     * 
     * @param {string} mode - 중복확인 모드 (ID, NICK, EMAIL)
     * @param {string} value - 중복확인 값
     */
    public function checkAvailability(string $mode, string $value)
    {

        $column = match($mode) {
            'ID' => 'user_id',
            'NICK' => 'user_nick',
            'EMAIL' => 'user_email',
            default => throw new \InvalidArgumentException('Invalid mode'),
        };

        return UserModel::where($column, $value)->exists();
    }


    /**
     * 회원가입
     * 
     * @param {array} data - 회원가입 데이터
     */
    public function register(array $data)
    {

        $user_id = $data['join_id'] ?? null;
        $user_nick = $data['join_nick'] ?? null;
        $user_email = $data['join_email'] ?? null;
        $pw = $data['password'] ?? null;

        if( empty($user_id) || empty($user_nick) || empty($user_email) || empty($pw) ){
            throw new Exception('모든 필수 필드를 입력해주세요.');
        }

        $user_id_check = $this->checkAvailability('ID', $user_id);
        if( $user_id_check ){
            throw new Exception('이미 사용중인 아이디 입니다.');
        }

        $user_nick_check = $this->checkAvailability('NICK', $user_nick);
        if( $user_nick_check ){
            throw new Exception('이미 사용중인 닉네임 입니다.');
        }

        $user_email_check = $this->checkAvailability('EMAIL', $user_email);
        if( $user_email_check ){
            throw new Exception('이미 사용중인 이메일 입니다.');
        }

        $user_pw = AuthService::getLegacyPassword($pw);

        $join_data = [
            "pw" => $pw,
            "reg_date" => date('Y-m-d H:i:s'),
            "ip" => OnadbAuth::getIp(),
            "domain" => OnadbAuth::getDomain(),
            "user_agent" => $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN'
        ];
        $user_join_data = json_encode($join_data);

        $input_data = [
            'user_id' => $user_id,
            'user_pw' => $user_pw,
            'user_nick' => $user_nick,
            'user_email' => $user_email,
            'user_mode' => 'BG',
            'user_level' => 1,
            'user_kind' => 'onadb',
            'user_join_data' => $user_join_data,
        ];

        $result = UserModel::create($input_data);
        
        return $result;

    }


    /**
     * 마이페이지 수정
     * 
     * @param {array} data - 마이페이지 수정 데이터
     */
    public function mypageModify(array $data)
    {
        
        $user_id = $data['user_id'];
        $user_nick = $data['user_nick'] ?? null;
        $new_pw = $data['new_pw'] ?? null;
        $old_pw = $data['old_pw'] ?? null;

        $user = UserModel::where('user_id', $user_id)->first();

        if( !$user ){
            throw new Exception('존재하지 않는 아이디입니다.');
        }

        if( !empty($old_pw) ){
            $old_pw_check = AuthService::getLegacyPassword($old_pw);
            if( $old_pw_check != $user->user_pw ){
                throw new Exception('현재 패스워드가 일치하지 않습니다.');
            }
        }

        $update_data = [];
        if( $user_nick != $user->user_nick ){
            $user_nick_check = $this->checkAvailability('NICK', $user_nick);
            if( $user_nick_check ){
                throw new Exception('이미 사용중인 닉네임 입니다.');
            }
            $update_data['user_nick'] = $user_nick;
        }

        if( !empty($new_pw) ){
            $new_pw_check = AuthService::getLegacyPassword($new_pw);
            $update_data['user_pw'] = $new_pw_check;
        }


        $result = UserModel::where('user_idx', $user->user_idx)->update($update_data);

        OnadbAuth::update($update_data);

        if( $result ){
            return true;

        }else{
            throw new Exception('정보 변경에 실패했습니다.');
        }

    }

    
    /**
     * 상품코멘트 포인트 / 점수 지급
     * 
     * @param int|null $userId
     * @param string $scoreMode
     * @param int $commentId
     * @param int $productId
     * @return array
     */
    public function giveRewardForComment(?int $userId, string $scoreMode, int $commentId, int $productId): array
    {
        if (!$userId) {
            return ['point' => 0, 'score' => 0, 'level_up' => 0];
        }

        $reward = [
            'point' => $scoreMode === 'after' ? $_u_point_prd_comment_after : $_u_point_prd_comment_before,
            'score' => $scoreMode === 'after' ? $_u_score_prd_comment_after : $_u_score_prd_comment_before
        ];

        if ($reward['point'] > 0) {
            $this->userModel->addPoint($userId, $reward['point'], '상품코맨트', [
                'pc_idx' => $commentId,
                'pd_idx' => $productId
            ]);
        }

        $levelUp = 0;
        if ($reward['score'] > 0) {
            $result = $this->userModel->addScore($userId, $reward['score'], '상품코맨트', [
                'pc_idx' => $commentId,
                'pd_idx' => $productId
            ]);
            $levelUp = $result['level_up'] ?? 0;
        }

        return [
            'point' => $reward['point'],
            'score' => $reward['score'],
            'level_up' => $levelUp,
        ];
    }


    /**
     * 사용자 닉네임 조회
     * 
     * @param string $nickname
     * @return array
     */
    public function getUserNick(string $nickname)
    {
        $user = UserModel::where('user_nick', $nickname)->first();

        if( $user ){
            return $user;
        }else{
            return null;
        }

    }


}