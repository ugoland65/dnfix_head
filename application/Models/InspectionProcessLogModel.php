<?php

namespace App\Models;

use App\Core\BaseModel;

class InspectionProcessLogModel extends BaseModel
{
    protected $table = 'inspection_process_log';
    protected $primaryKey = 'ipl_idx';

    public $timestamps = false;

    protected $fillable = [
        'inspection_version',
        'location_code',
        'relation_pk',
        'prd_idx',
        'ps_idx',
        'godo_goods_no',
        'process_content',
        'result_content',
        'executor_admin_idx',
        'executor_admin_id',
        'executor_admin_name',
        'executed_at',
        'is_stock_qty_sent', // 고도몰 API 호출 시 재고수량(stockQty) 전송 여부
    ];
}
