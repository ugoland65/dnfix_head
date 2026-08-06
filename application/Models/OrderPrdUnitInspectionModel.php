<?php

namespace App\Models;

use App\Core\BaseModel;

class OrderPrdUnitInspectionModel extends BaseModel
{
    protected $table = 'order_prd_unit_inspection';
    protected $primaryKey = 'idx';

    protected $fillable = [
        'order_prd_unit_idx',
        'checked_qty',
        'inspector_admin_idx',
        'inspector_admin_id',
        'inspector_admin_name',
        'inspection_memo',
    ];

    protected $casts = [
        'idx' => 'int',
        'order_prd_unit_idx' => 'int',
        'checked_qty' => 'int',
        'inspector_admin_idx' => 'int',
    ];
}
