<?php
namespace App\Models;

use App\Core\BaseModel;

class ProductStockModel extends BaseModel 
{

    protected $table = 'prd_stock';
    protected $primaryKey = 'ps_idx';  //기본값 idx

    protected $fillable = [
        'ps_prd_idx',
        'ps_rack_code',
        'ps_stock',
        'ps_stock_hold',
        'ps_stock_all',
        'ps_income',
        'ps_last_in',
        'ps_update_date',
        'ps_in_date',
        'ps_last_date',
        'ps_soldout_date',
        'ps_sale_date',
        'ps_sale_log',
        'ps_sale_data',
        'ps_in_sale_s',
        'ps_in_sale_e',
        'ps_in_sale_data',
        'ps_stock_object',
        'ps_alarm_count',
        'ps_alarm_message',
        'ps_mode',
        'ps_kind',
        'ps_name',
        'ps_set_value',
        'ps_alarm_yn',
        'ps_cafe24_sms',
        'is_sale_month',
        'is_sale_special',

        'coupang_seller_product_id', // BIGINT  | 쿠팡 판매자 상품ID
        'is_rocket', // CHAR(1) | 로켓 여부 (Y/N)
    ];

}

