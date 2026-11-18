<?php
namespace App\Models;

use App\Core\BaseModel;

class PersonalPaymentStatusLogModel extends BaseModel
{
    protected $table = 'personal_payment_settlement_status_log';

    protected $fillable = [
        'settlement_idx',
        'status',
        'memo',
        'ad_pk',
        'ad_name',
        'created_at',
    ];
}
