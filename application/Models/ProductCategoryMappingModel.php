<?php

namespace App\Models;

use App\Core\BaseModel;

class ProductCategoryMappingModel extends BaseModel
{
    protected $table = 'product_category_mappings';
    protected $primaryKey = 'idx';

    protected $fillable = [
        'product_type',
        'product_idx',
        'category_type',
        'category_code',
        'display_order',
        'created_at',
        'updated_at',
    ];
}
