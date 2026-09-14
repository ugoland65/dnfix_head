<?php

namespace App\Models;

use App\Core\BaseModel;

class ProductDetailContentDeployLogModel extends BaseModel
{
    protected $table = 'prd_detail_content_deploy_log';
    protected $primaryKey = 'idx';

    protected $fillable = [
        'prd_pk',
        'godo_code',
        'content_idx',
        'deploy_version',
        'deploy_version_code',
        'original_name',
        'korean_name',
        'title',
        'maker_comment',
        'md_comment',
        'summary_points',
        'specs',
        'html',
        'snapshot',
        'deploy_status',
        'godo_http_code',
        'godo_response',
        'error_message',
        'admin_idx',
        'admin_name',
        'created_at',
    ];

    protected $casts = [
        'idx' => 'int',
        'prd_pk' => 'int',
        'content_idx' => 'int',
        'deploy_version' => 'int',
        'summary_points' => 'array',
        'specs' => 'array',
        'snapshot' => 'array',
        'godo_http_code' => 'int',
        'godo_response' => 'array',
        'admin_idx' => 'int',
    ];
}
