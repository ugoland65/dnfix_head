<?php

namespace App\Models;

use App\Core\BaseModel;

class ProductDetailContentModel extends BaseModel
{
    protected $table = 'prd_detail_content';
    protected $primaryKey = 'idx';

    protected $fillable = [
        'prd_pk',
        'original_name',
        'korean_name',
        'title',
        'maker_comment',
        'md_comment',
        'summary_points',
        'specs',
        'deploy_version',
        'deploy_version_code',
        'admin_idx',
        'admin_name',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'idx' => 'int',
        'prd_pk' => 'int',
        'summary_points' => 'array',
        'specs' => 'array',
        'deploy_version' => 'int',
        'admin_idx' => 'int',
    ];

    /**
     * 상품
     * @return \App\Core\BelongsToRelation
     */
    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'prd_pk', 'CD_IDX')
            ->select(['CD_IDX', 'CD_NAME', 'CD_NAME_OG']);
    }
}
