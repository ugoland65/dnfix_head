<?php
namespace App\Models;

use App\Core\BaseModel;

class ProductStockUnitModel extends BaseModel 
{

    protected $table = 'prd_stock_unit';
    protected $primaryKey = 'psu_idx';  //기본값 idx

}