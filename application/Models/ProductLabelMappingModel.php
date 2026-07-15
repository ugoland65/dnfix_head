<?php
namespace App\Models;

use App\Core\BaseModel;

class ProductLabelMappingModel extends BaseModel
{
    protected $table = 'product_label_mappings';
    protected $primaryKey = 'idx';

    protected $fillable = [
        'product_type',
        'product_idx',
        'label_idx',
        'started_at',
        'ended_at',
        'display_order',
        'created_at',
        'updated_at',
    ];

    /**
     * 매핑이 참조하는 라벨
     * @return \App\Core\BelongsToRelation
     */
    public function label()
    {
        return $this->belongsTo(ProductLabelModel::class, 'label_idx', 'idx');
    }
}
