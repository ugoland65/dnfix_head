<?php

namespace App\Models;

use App\Core\BaseModel;

class OrderGroupProductModel extends BaseModel
{
    protected $table = 'ona_order_prd';
    protected $primaryKey = 'oop_idx';

    protected $fillable = [
        'oop_name',
        'oop_code',
        'oop_data'
    ];
}
