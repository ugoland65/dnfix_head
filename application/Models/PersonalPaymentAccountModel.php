<?php
namespace App\Models;

use App\Core\BaseModel;

class PersonalPaymentAccountModel extends BaseModel
{
    protected $table = 'personal_payment_account';

    protected $fillable = [
        'ad_pk',
        'bank',
        'bank_account',
        'depositor',
        'updated_at',
    ];
}
