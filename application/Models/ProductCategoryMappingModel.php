<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class ProductCategoryMappingModel extends BaseModel
{
    protected $table = 'product_category_mappings';
    protected $primaryKey = 'idx';

    protected $fillable = [
        'product_type',
        'product_idx',
        'category_type',
        'category_code',
        'display_order',
        'created_at',
        'updated_at',
    ];

    /**
     * 유니크 키(uq_product_category_mapping) 기준으로 없으면 insert, 있으면 display_order만 갱신한다.
     */
    public static function upsertMapping(
        string $productType,
        int $productIdx,
        string $categoryType,
        string $categoryCode,
        int $displayOrder
    ): void {
        $productType = trim($productType);
        $categoryType = trim($categoryType);
        $categoryCode = trim($categoryCode);
        if ($productType === '' || $productIdx <= 0 || $categoryType === '' || $categoryCode === '') {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $sql = 'INSERT INTO `product_category_mappings`
            (`product_type`, `product_idx`, `category_type`, `category_code`, `display_order`, `created_at`, `updated_at`)
            VALUES
            (:product_type, :product_idx, :category_type, :category_code, :display_order, :created_at, :updated_at)
            ON DUPLICATE KEY UPDATE
                `display_order` = VALUES(`display_order`),
                `updated_at` = VALUES(`updated_at`)';

        $pdo = static::getInstance()->db;
        if (!$pdo instanceof PDO) {
            throw new \RuntimeException('Database connection is not available.');
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':product_type' => $productType,
            ':product_idx' => $productIdx,
            ':category_type' => $categoryType,
            ':category_code' => $categoryCode,
            ':display_order' => $displayOrder,
            ':created_at' => $now,
            ':updated_at' => $now,
        ]);
    }
}
