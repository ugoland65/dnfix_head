<?php
namespace App\Models;

use App\Core\BaseModel;

class PersonalPaymentSettlementModel extends BaseModel
{
    protected $table = 'personal_payment_settlement';

    protected $fillable = [
        'applicant_ad_pk',
        'applicant_name',
        'use_date',
        'vendor',
        'purpose',
        'pay_method',
        'payment_amount',
        'request_amount',
        'bank',
        'bank_account',
        'depositor',
        'account_subject',
        'status',
        'paid_amount',
        'paid_date',
        'paid_ad_pk',
        'paid_ad_name',
        'paid_memo',
        'payout_idx',
        'ad_pk',
        'ad_name',
        'created_at',
        'updated_at',
    ];
}
