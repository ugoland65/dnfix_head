<?php
namespace App\Models;

use App\Core\BaseModel;

class ProductLabelModel extends BaseModel
{
    protected $table = 'product_labels';
    protected $primaryKey = 'idx';

    protected $fillable = [
        'label_code',
        'label_name',
        'icon_path',
        'display_order',
        'is_active',
        'created_at',
        'updated_at',
    ];

    /**
     * 라벨에 연결된 상품 목록
     * @return \App\Core\HasManyRelation
     */
    public function mappings()
    {
        return $this->hasMany(\App\Models\ProductLabelMappingModel::class, 'label_idx', 'idx');
    }
}
