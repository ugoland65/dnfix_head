<?php
namespace App\Models;

use App\Core\BaseModel;

class ProductWorkCheckItemModel extends BaseModel
{
    protected $table = 'prd_work_check_item';
    protected $primaryKey = 'id';

    protected $fillable = [
        'task_code',
        'task_label',
        'target_kind_codes',
        'is_active',
        'sort_no',
    ];
}

