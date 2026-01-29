<?php
namespace App\Models;

use App\Core\BaseModel;

class WorkLogHistoryModel extends BaseModel
{
    protected $table = 'work_log_history';
    protected $primaryKey = 'idx';

    // 대량할당 허용 컬럼
    protected $fillable = [
        'mode',
        'category',
        'target_pk',
        'action_mode',
        'action_summary',
        'action_body',
        'action_date',
        'action_id',
        'action_idx',
        'action_name',
        'before_json',
        'after_json',
        'diff_json',
    ];

}
