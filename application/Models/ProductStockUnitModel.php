<?php
namespace App\Models;

use App\Core\BaseModel;

class ProductStockUnitModel extends BaseModel 
{

    protected $table = 'prd_stock_unit';
    protected $primaryKey = 'psu_idx';  //기본값 idx

    protected $fillable = [
        'psu_stock_idx',
        'psu_day',
        'psu_mode',
        'psu_qry',
        'psu_stock',
        'psu_kind',
        'psu_memo',
        'psu_token',
        'psu_id',
        'psu_date',
        'reg',
    ];

}