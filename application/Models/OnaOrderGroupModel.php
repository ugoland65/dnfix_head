<?php

namespace App\Models;

use App\Core\BaseModel;

class OnaOrderGroupModel extends BaseModel
{
    protected $table = 'ona_order_group';
    protected $primaryKey = 'oog_idx';

    protected $fillable = [
        'oog_name',
        'oog_import',
        'oog_code',
        'oog_brand',
        'oog_group',
        'memo',
    ];
}
