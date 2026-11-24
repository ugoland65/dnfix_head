<?php

namespace App\Models;

use App\Core\BaseModel;

class RackStockInspectionItemModel extends BaseModel
{
    protected $table = 'prd_rack_stock_inspection_item';
    protected $primaryKey = 'idx';

    protected $fillable = [
        'inspection_idx',
        'prd_idx',
        'ps_idx',
        'rack_code',
        'stock_qty',
        'checked_at',
        'checked_by_idx',
        'checked_by_id',
        'checked_by_name',
    ];
}
