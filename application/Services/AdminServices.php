<?php

namespace App\Services;

use App\Models\AdminModel;
use App\Auth\AuthService;

class AdminServices
{
    
    /**
     * 직원 목록 조회
     * 
     * @param array $payload 파라미터
     * @return array 직원 목록 데이터
     */
    public function getAdminList($criteria)
    {

        $ad_work_status = $criteria['ad_work_status'] ?? null;

        $query = AdminModel::query();

        if( $ad_work_status ){
            $query->where('ad_work_status', $ad_work_status);
        }

        $result = $query->get()->toArray();

        foreach($result as &$row){
            $row['ad_data'] = json_decode($row['ad_data'], true);
        }

        unset($row);

        return $result;

    }


    /**
     * 직원 상세 조회
     * 
     * @param array $payload 파라미터
     * @return array 직원 상세 데이터
     */
    public function getAdmin($payload)
    {
        $idx = $payload['idx'] ?? null;

        $query = AdminModel::query();
        $query->where('idx', $idx);
        $result = $query->first()->toArray();

        $result['ad_data'] = json_decode($result['ad_data'], true);

        return $result;

    }


    /**
     * 직원 생성
     * 
     * @param array $data 직원 데이터
     * @return array
     */
    public function createAdmin($data)
    {

        $ad_id = $data['ad_id'] ?? null;
        $ad_pw = $data['ad_pw'] ?? null;
        $ad_employee_id = $data['ad_employee_id'] ?? null; //사번
        $ad_role = $data['ad_role'] ?? null; //직책
        $ad_title = $data['ad_title'] ?? null; //직함
        $ad_nick = $data['ad_nick'] ?? null; //닉네임
        $ad_name = $data['ad_name'] ?? null; //이름
        $ad_name_en = $data['ad_name_en'] ?? null;
        $ad_level = $data['ad_level'] ?? 0; //등급
        $ad_department = $data['ad_department'] ?? null; //부서
        $ad_work_status = $data['ad_work_status'] ?? '재직중'; //재직상태
        $ad_job_type = $data['ad_job_type'] ?? '정직원'; //고용형태
        $ad_birth = $data['ad_birth'] ?? null; //생년월일
        $ad_joining = $data['ad_joining'] ?? null; //입사일
        $ad_data = $data['ad_data'] ?? null;
        $ad_address = $data['ad_address'] ?? '';
        $ad_tel = $data['ad_tel'] ?? '';
        $ad_contact_name = $data['ad_contact_name'] ?? '';
        $ad_contact_relationship = $data['ad_contact_relationship'] ?? '';
        $ad_contact_tel = $data['ad_contact_tel'] ?? '';
        $ad_google = $data['ad_google'] ?? ''; //구글 아이디
        $ad_image = $data['ad_image'] ?? null;
        $ad_telegram_token = $data['ad_telegram_token'] ?? null;
        $ad_line_token = $data['ad_line_token'] ?? null;
        $active = $data['active'] ?? 'Y';

        if( empty($ad_id) ){
            throw new Exception('아이디를 입력해주세요.');
        }

        if( empty($ad_pw) ){
            throw new Exception('패스워드를 입력해주세요.');
        }

        $ad_pw = AuthService::getLegacyPassword($ad_pw);

        $ad_data_array = [
            'address' => $ad_address,
            'tel' => $ad_tel,
            'contact' => [
                'name' => $ad_contact_name,
                'relationship' => $ad_contact_relationship,
                'tel' => $ad_contact_tel,
            ],
        ];

        $ad_data = json_encode($ad_data_array, JSON_UNESCAPED_UNICODE);

        $currentTimestamp = time();
        
        $input_data = [
            'ad_id' => $ad_id,
            'ad_pw' => $ad_pw,
            'ad_employee_id' => $ad_employee_id ?: '', //사번
            'ad_role' => $ad_role ?: '', //직책
            'ad_title' => $ad_title ?: '', //직함
            'ad_nick' => $ad_nick ?: '', //닉네임
            'ad_name' => $ad_name ?: '', //이름
            'ad_name_en' => $ad_name_en ?: '', //이름(영문)
            'ad_level' => $ad_level ?: 0, //등급
            'ad_department' => $ad_department ?: '', //부서
            'ad_work_status' => $ad_work_status ?: '재직중', //재직상태
            'ad_job_type' => $ad_job_type ?: '정직원', //고용형태
            'ad_birth' => !empty($ad_birth) ? $ad_birth : null, //생년월일
            'ad_joining' => !empty($ad_joining) ? $ad_joining : null, //입사일
            'ad_data' => $ad_data, //추가 데이터
            'ad_google' => $ad_google ?: '', //구글 아이디
            'ad_image' => '', //프로필 이미지
            'ad_telegram_token' => $ad_telegram_token ?: '', //텔레그램 토큰
            'ad_line_token' => $ad_line_token ?: '', //라인 토큰
            'active' => $active ?: 'Y', //활성화 여부
            'AD_REG_DATE' => $currentTimestamp, //등록일
            'AD_UP_DATE' => $currentTimestamp, //수정일
        ];

        $result = AdminModel::create(
            $input_data
        );

        return $result;

    }

    /**
     * 직원 수정
     * 
     * @param array $data 직원 데이터
     * @return array
     */
    public function updateAdmin($data)
    {

        $idx = $data['idx'] ?? null;

        $new_pw_change = $data['new_pw_change'] ?? null;
        $new_ad_pw = $data['new_ad_pw'] ?? null;

        $ad_id = $data['ad_id'] ?? null;
        $ad_employee_id = $data['ad_employee_id'] ?? null; //사번
        $ad_role = $data['ad_role'] ?? null; //직책
        $ad_title = $data['ad_title'] ?? null; //직함
        $ad_nick = $data['ad_nick'] ?? null; //닉네임
        $ad_name = $data['ad_name'] ?? null; //이름
        $ad_name_en = $data['ad_name_en'] ?? null;
        $ad_level = $data['ad_level'] ?? 0; //등급
        $ad_department = $data['ad_department'] ?? null; //부서
        $ad_work_status = $data['ad_work_status'] ?? '재직중'; //재직상태
        $ad_job_type = $data['ad_job_type'] ?? '정직원'; //고용형태
        $ad_birth = $data['ad_birth'] ?? null; //생년월일
        $ad_joining = $data['ad_joining'] ?? null; //입사일
        $ad_data = $data['ad_data'] ?? null;
        $ad_address = $data['ad_address'] ?? '';
        $ad_tel = $data['ad_tel'] ?? '';
        $ad_contact_name = $data['ad_contact_name'] ?? '';
        $ad_contact_relationship = $data['ad_contact_relationship'] ?? '';
        $ad_contact_tel = $data['ad_contact_tel'] ?? '';
        $ad_google = $data['ad_google'] ?? ''; //구글 아이디
        $ad_image = $data['ad_image'] ?? null;
        $ad_telegram_token = $data['ad_telegram_token'] ?? null;
        $ad_line_token = $data['ad_line_token'] ?? null;
        $active = $data['active'] ?? 'Y';

        if( empty($idx) ){
            throw new Exception('직원 식별 번호를 입력해주세요.');
        }

        $admin = AdminModel::find($idx);

        if( empty($admin) ){
            throw new Exception('직원을 찾을 수 없습니다.');
        }

        $ad_data_array = [
            'address' => $ad_address,
            'tel' => $ad_tel,
            'contact' => [
                'name' => $ad_contact_name,
                'relationship' => $ad_contact_relationship,
                'tel' => $ad_contact_tel,
            ],
        ];

        $ad_data = json_encode($ad_data_array, JSON_UNESCAPED_UNICODE);

        $currentTimestamp = time();

        $input_data = [
            'ad_employee_id' => $ad_employee_id ?: '', //사번
            'ad_role' => $ad_role ?: '', //직책
            'ad_title' => $ad_title ?: '', //직함
            'ad_nick' => $ad_nick ?: '', //닉네임
            'ad_name' => $ad_name ?: '', //이름
            'ad_name_en' => $ad_name_en ?: '', //이름(영문)
            'ad_level' => $ad_level ?: 0, //등급
            'ad_department' => $ad_department ?: '', //부서
            'ad_work_status' => $ad_work_status ?: '재직중', //재직상태
            'ad_job_type' => $ad_job_type ?: '정직원', //고용형태
            'ad_birth' => !empty($ad_birth) ? $ad_birth : null, //생년월일
            'ad_joining' => !empty($ad_joining) ? $ad_joining : null, //입사일
            'ad_data' => $ad_data, //추가 데이터
            'ad_google' => $ad_google ?: '', //구글 아이디
            'ad_telegram_token' => $ad_telegram_token ?: '', //텔레그램 토큰
            'ad_line_token' => $ad_line_token ?: '', //라인 토큰
            'active' => $active ?: 'Y', //활성화 여부
            'AD_UP_DATE' => $currentTimestamp, //수정일
        ];

        //패스워드 수정
        if( $new_pw_change == 'ok' ){
            $ad_pw = AuthService::getLegacyPassword($new_ad_pw);
            $input_data['ad_pw'] = $ad_pw;
        }

        $admin->update($input_data);

        return $admin;
    }

}