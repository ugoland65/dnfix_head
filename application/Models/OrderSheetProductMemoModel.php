<?php

namespace App\Models;

use App\Core\BaseModel;

class OrderSheetProductMemoModel extends BaseModel
{
    protected $table = 'order_sheet_product_memos';
    protected $primaryKey = 'idx';

    protected $fillable = [
        'oo_idx',
        'oop_idx',
        'pidx',
        'memo',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'idx' => 'int',
        'oo_idx' => 'int',
        'oop_idx' => 'int',
        'pidx' => 'int',
        'updated_by' => 'int',
    ];
}
