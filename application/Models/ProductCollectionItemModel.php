<?php

namespace App\Models;

use App\Core\BaseModel;

class ProductCollectionItemModel extends BaseModel
{
    protected $table = 'product_collection_item';
    protected $primaryKey = 'idx';

    protected $fillable = [
        'matched_product_pk',
        'source_type',
        'source_site_code',
        'source_product_pk',
        'source_collected_at',
        'image_storage_path',
        'image_upload_status',
        'source_image_urls_json',
        'hosting_image_urls_json',
        'image_total_count',
        'image_success_count',
        'image_failed_count',
        'error_message',
        'translated_accessories',
        'translated_maker_comment',
        'translation_updated_at',
    ];
}
