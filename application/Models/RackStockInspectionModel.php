<?php

namespace App\Models;

use App\Core\BaseModel;

class RackStockInspectionModel extends BaseModel
{
    protected $table = 'prd_rack_stock_inspection';
    protected $primaryKey = 'idx';

    protected $fillable = [
        'rack_group',
        'title',
        'inspect_date',
        'seq_no',
        'created_by_idx',
        'created_by_id',
        'created_by_name',
        'created_at',
    ];
}
