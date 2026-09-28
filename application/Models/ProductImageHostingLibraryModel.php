<?php

namespace App\Models;

use App\Core\BaseModel;

class ProductImageHostingLibraryModel extends BaseModel
{
    protected $table = 'prd_image_hosting_library';
    protected $primaryKey = 'idx';

    protected $fillable = [
        'prd_pk',
        'filename',
        'hosting_url',
        'remote_path',
        'storage_path',
        'sort_no',
        'file_size',
        'width',
        'height',
        'imported_at',
        'created_at',
    ];

    protected $casts = [
        'idx' => 'int',
        'prd_pk' => 'int',
        'sort_no' => 'int',
        'file_size' => 'int',
        'width' => 'int',
        'height' => 'int',
    ];
}
