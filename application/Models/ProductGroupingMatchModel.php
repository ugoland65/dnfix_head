<?php
namespace App\Models;

use App\Core\BaseModel;

class ProductGroupingMatchModel extends BaseModel
{

    protected $table = 'product_grouping_match';
    protected $primaryKey = 'idx';  //기본값 idx

    protected $fillable = [
        'grouping_idx',
        'prd_mode',
        'target_idx',
        'sort_no',
        'is_active',
        'memo',
        'reg_id',
        'created_at',
        'updated_at',
    ];

}
