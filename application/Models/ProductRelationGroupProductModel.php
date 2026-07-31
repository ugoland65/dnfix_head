<?php
namespace App\Models;

use App\Core\BaseModel;

class ProductRelationGroupProductModel extends BaseModel
{
    protected $table = 'prd_relation_group_product';
    protected $primaryKey = 'prgp_idx';

    protected $fillable = [
        'prgp_group_idx',
        'prgp_prd_idx',
        'prgp_sort_no',
        'prgp_reg_admin_idx',
        'prgp_reg_admin_name',
        'prgp_reg_at',
    ];
}
