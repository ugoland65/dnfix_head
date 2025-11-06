<?php
namespace App\Models;

use App\Core\BaseModel;

class ProductStockModel extends BaseModel {

    protected $table = 'prd_stock';
    protected $primaryKey = 'ps_idx';  //기본값 idx

}

