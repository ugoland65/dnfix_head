<?php

namespace App\Services;

use App\Models\WorkLogHistoryModel;

class WorkLogHistoryService
{

    /**
     * 업무 로그 기록
     * 
     * @param array $data
     * @return int
     */
    public function writeWorkLogHistory($data)
    {

        $input_data = [
            'mode' => $data['mode'] ?? 'hand',
            'category' => $data['category'],
            'target_pk' => $data['target_pk'],
            'action_mode' => $data['action_mode'],
            'action_summary' => $data['action_summary'],
            'action_body' => $data['action_body'] ?? '',
            'action_date' => $data['action_date'],
            'action_id' => $data['action_id'],
            'action_idx' => $data['action_idx'],
            'action_name' => $data['action_name'],
            'before_json' => $this->normalizeJson($data['before_json'] ?? null),
            'after_json' => $this->normalizeJson($data['after_json'] ?? null),
            'diff_json' => $this->normalizeJson($data['diff_json'] ?? null),
        ];

        return WorkLogHistoryModel::insert($input_data);
    }

    private function normalizeJson($value)
    {
        if ($value === null || $value === '') {
            return json_encode([], JSON_UNESCAPED_UNICODE);
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            if ($trimmed !== '') {
                json_decode($trimmed);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $trimmed;
                }
            }

            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 업무 로그 기록 목록 조회
     * 
     * @param int $idx
     * @return array
     */
    public function getWorkLogHistoryList($idx)
    {
        $query = WorkLogHistoryModel::query()
            ->where('target_pk', $idx)
            ->orderBy('idx', 'desc')
            ->get()
            ->toArray();

        foreach($query as &$row){
            $row['before_json'] = json_decode($row['before_json'] ?? '{}', true);
            $row['after_json'] = json_decode($row['after_json'] ?? '{}', true);
            $row['diff_json'] = json_decode($row['diff_json'] ?? '{}', true);
        }

        unset($row);

        return $query;
    }

}