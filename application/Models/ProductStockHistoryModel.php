<?php
namespace App\Models;

use App\Core\BaseModel;

class ProductStockHistoryModel extends BaseModel 
{

    protected $table = 'prd_stock_history';
    protected $primaryKey = 'uid';  //기본값 idx

    protected $fillable = [
        'file_name',
        'source_type',
        'meta_data',
        'reg_time',
        'end_time',
        'reg_id',
        'data',
        'step',
        'info',
        'error',
    ];

    /**
     * 일일재고 임시 데이터를 저장하고 생성된 uid를 반환
     *
     * @param array $payload
     * @return int
     */
    public static function createDailyStockTemp(array $payload): int
    {
        $created = self::create($payload);
        $createdArray = $created ? $created->toArray() : [];

        return (int)($createdArray['uid'] ?? 0);
    }

}