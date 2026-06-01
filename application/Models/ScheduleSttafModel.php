<?php
namespace App\Models;

use App\Core\BaseModel;

class ScheduleSttafModel extends BaseModel
{
    protected $table = 'schedule_sttaf';
    protected $primaryKey = 'idx';

    protected $fillable = [
        'idx',
        'tidx',
        'mode',
        'date_s',
        'date_e',
        'data',
        'memo',
        'state',
    ];
}
