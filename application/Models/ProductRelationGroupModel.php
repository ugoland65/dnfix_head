<?php
namespace App\Models;

use App\Core\BaseModel;

class ProductRelationGroupModel extends BaseModel
{
    protected $table = 'prd_relation_group';
    protected $primaryKey = 'prg_idx';

    protected $fillable = [
        'prg_mode',
        'prg_brand_idx',
        'prg_name',
        'prg_memo',
        'prg_use_yn',
        'prg_reg_admin_idx',
        'prg_reg_admin_name',
        'prg_reg_at',
        'prg_updated_at',
    ];
}
