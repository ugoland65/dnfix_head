<?php
namespace App\Models;

use App\Core\BaseModel;

class PersonalPaymentSettlementFileModel extends BaseModel
{
    protected $table = 'personal_payment_settlement_file';

    protected $fillable = [
        'settlement_idx',
        'file_path',
        'original_name',
        'created_at',
    ];
}
