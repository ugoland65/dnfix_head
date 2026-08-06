<?php

namespace App\Models;

use App\Core\BaseModel;

class OrderPrdUnitModel extends BaseModel
{
    protected $table = 'order_prd_unit';
    protected $primaryKey = 'idx';

    protected $fillable = [
        'order_idx',
        'bidx',
        'pidx',
        'order_unit_price',
        'order_qty',
        'is_order_failed',
        'stock_inspection_data',
        'stock_inspection_memo',
    ];

    protected $casts = [
        'idx' => 'int',
        'order_idx' => 'int',
        'bidx' => 'int',
        'pidx' => 'int',
        'order_unit_price' => 'decimal:2',
        'order_qty' => 'int',
        'is_order_failed' => 'bool',
        'stock_inspection_data' => 'json',
    ];
}
