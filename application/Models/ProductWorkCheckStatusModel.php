<?php
namespace App\Models;

use App\Core\BaseModel;

class ProductWorkCheckStatusModel extends BaseModel
{
    protected $table = 'prd_work_check_status';
    protected $primaryKey = 'id';

    protected $fillable = [
        'prd_idx',
        'task_code',
        'is_checked',
        'checked_at',
        'checked_by',
    ];
}

