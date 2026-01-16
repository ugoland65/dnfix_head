<?php
namespace App\Models;

use App\Core\BaseModel;

class ProductStockHistoryModel extends BaseModel 
{

    protected $table = 'prd_stock_history';
    protected $primaryKey = 'uid';  //기본값 idx

    protected $fillable = [
        'file_name',
        'reg_time',
        'end_time',
        'reg_id',
        'data',
        'step',
        'info',
        'error',
    ];

}