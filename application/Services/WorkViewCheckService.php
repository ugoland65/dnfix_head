<?php
namespace App\Services;

use App\Models\WorkViewCheckModel;
use App\Core\AuthAdmin;

class WorkViewCheckService
{

    /**
     * 체크를 했는지 안했는지 확인
     * 
     * @param string $mode
     * @param int $tidx
     * @param int $mb_idx
     * @param int $write_mb_idx 작성자 인덱스
     * @return bool
     */
    public function isViewCheck($mode, $tidx, $mb_idx, $write_mb_idx = null)
    {
        
        return WorkViewCheckModel::query()
            ->where('mode', '=', $mode)
            ->where('tidx', '=', $tidx)
            ->where('mb_idx', '=', $mb_idx)
            ->exists();
    }

    /**
     * 업무 로그 확인 처리
     *
     * @param string $mode
     * @param int $tidx
     * @param int $mb_idx
     * @return bool
     */
    public function addViewCheck($payload)
    {
        $mode = $payload['mode'] ?? null;
        $tidx = $payload['tidx'] ?? null;
        $mb_idx = $payload['mb_idx'] ?? null;

        $result = WorkViewCheckModel::insert([
            'mode' => $mode,
            'tidx' => $tidx,
            'mb_idx' => $mb_idx,
            'reg' => json_encode(AuthAdmin::getConnectionInfo(), JSON_UNESCAPED_UNICODE) ?? null,
            'reg_date' => date('Y-m-d H:i:s'),
        ]);

        return (bool) $result;
    }

    /**
     * 업무 로그 확인 목록 조회
     *
     * @param array $payload
     */
    public function getViewCheckList($payload)
    {
        $mode = $payload['mode'] ?? null;
        $tidx = $payload['tidx'] ?? null;

        $result = WorkViewCheckModel::with('admin')
            ->where('mode', $mode)
            ->where('tidx', $tidx)
            ->orderBy('reg_date', 'asc')
            ->get()
            ->toArray();

        return $result;
    }
}