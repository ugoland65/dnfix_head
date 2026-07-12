<?php
namespace App\Models;

use App\Core\BaseModel;

class GodoOrderModel extends BaseModel
{
    protected $table = 'godo_orders';
    protected $primaryKey = 'idx';

    protected $fillable = [
        'order_no',
        'order_status',
        'settle_kind',
        'settle_price',
        'payment_dt',
        'order_payment_dt',
        'order_reg_dt',
        'order_name',
        'order_phone',
        'order_cell_phone',
        'receiver_name',
        'receiver_phone',
        'receiver_cell_phone',
        'receiver_zipcode',
        'receiver_zonecode',
        'receiver_address',
        'receiver_address_sub',
        'order_memo',
        'member_no',
        'member_id',
        'member_name',
        'member_group_name',
        'refund_price',
        'goods_count',
        'total_goods_count',
        'is_member',
        'is_refunded',
        'is_cancelled',
        'api_sync_status',
        'api_sync_message',
        'api_synced_at',
        'godo_updated_at',
        'raw_data',
        'created_at',
        'updated_at',
    ];

    /**
     * 주문에 포함된 주문상품 목록
     * @return \App\Core\HasManyRelation
     */
    public function goods()
    {
        return $this->hasMany(\App\Models\GodoOrderGoodsModel::class, 'godo_order_idx', 'idx');
    }
}
