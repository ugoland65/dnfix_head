<?php

namespace App\Models;

use App\Core\BaseModel;

class ProductSpecInfoModel extends BaseModel
{
    protected $table = 'prd_spec_info';
    protected $primaryKey = 'psi_idx';

    protected $fillable = [
        'psi_cd_idx',
        'psi_image_path',
        'psi_total_length_cm',
        'psi_px_per_cm',
        'psi_items',
        'psi_admin_idx',
        'psi_admin_name',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'psi_idx' => 'int',
        'psi_cd_idx' => 'int',
        'psi_total_length_cm' => 'decimal:2',
        'psi_px_per_cm' => 'decimal:4',
        'psi_items' => 'array',
        'psi_admin_idx' => 'int',
    ];

    /**
     * 상품
     * @return \App\Core\BelongsToRelation
     */
    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'psi_cd_idx', 'CD_IDX')
            ->select(['CD_IDX', 'CD_NAME', 'CD_IMG', 'CD_IMG2']);
    }
}
