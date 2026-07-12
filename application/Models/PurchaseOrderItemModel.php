<?php
namespace App\Models;

use App\Core\BaseModel;

class PurchaseOrderItemModel extends BaseModel
{
    protected $table = 'purchase_order_items';
    protected $primaryKey = 'idx';

    protected $fillable = [
        'purchase_order_idx',
        'godo_order_goods_id',
        'order_goods_sno',
        'order_no',
        'goods_no',
        'goods_name',
        'option_info',
        'scm_no',
        'scm_name',
        'goods_count',
        'goods_price',
        'goods_total_price',
        'receiver_name',
        'receiver_phone',
        'receiver_cell_phone',
        'receiver_zonecode',
        'receiver_address',
        'receiver_address_sub',
        'order_memo',
        'created_by',
        'created_name',
        'created_at',
        'updated_at',
    ];
}
