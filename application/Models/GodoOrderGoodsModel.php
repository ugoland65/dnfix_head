<?php
namespace App\Models;

use App\Core\BaseModel;

class GodoOrderGoodsModel extends BaseModel
{
    protected $table = 'godo_order_goods';
    protected $primaryKey = 'idx';

    protected $fillable = [
        'godo_order_idx',
        'order_no',
        'order_goods_sno',
        'goods_no',
        'goods_cd',
        'goods_name',
        'order_status',
        'goods_count',
        'goods_price',
        'goods_total_price',
        'option_price',
        'discount_price',
        'refund_price',
        'option_info',
        'thumb_image_url',
        'scm_no',
        'scm_name',
        'product_partner_id',
        'intranet_goods_id',
        'purchase_status',
        'purchase_order_idx',
        'purchase_order_date',
        'purchase_order_admin',
        'purchase_order_admin_name',
        'user_handle_mode',
        'user_handle_fl',
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
     * 주문상품이 속한 주문
     * @return \App\Core\BelongsToRelation
     */
    public function order()
    {
        return $this->belongsTo(\App\Models\GodoOrderModel::class, 'godo_order_idx', 'idx');
    }
}
