<?php
namespace App\Models;

use App\Core\BaseModel;

class PurchaseOrderModel extends BaseModel
{
    protected $table = 'purchase_orders';
    protected $primaryKey = 'idx';

    protected $fillable = [
        'order_name',
        'po_code',
        'supplier_no',
        'supplier_name',
        'item_count',
        'total_quantity',
        'total_amount',
        'status',
        'memo',
        'created_by',
        'created_name',
        'created_at',
        'updated_at',
    ];
}
