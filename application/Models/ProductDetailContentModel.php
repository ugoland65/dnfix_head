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
        'list_summary',
        'title',
        'maker_comment',
        'md_comment',
        'summary_points',
        'specs',
        'bottom_items',
        'bottom_position',
        'deploy_version',
        'deploy_version_code',
        'bottom_deploy_version',
        'bottom_deploy_version_code',
        'admin_idx',
        'admin_name',
        'bottom_admin_idx',
        'bottom_admin_name',
        'created_at',
        'updated_at',
        'bottom_updated_at',
    ];

    protected $casts = [
        'idx' => 'int',
        'prd_pk' => 'int',
        'summary_points' => 'array',
        'specs' => 'array',
        'bottom_items' => 'array',
        'deploy_version' => 'int',
        'bottom_deploy_version' => 'int',
        'admin_idx' => 'int',
        'bottom_admin_idx' => 'int',
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
