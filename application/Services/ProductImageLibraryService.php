<?php

namespace App\Services;

use App\Classes\Database;
use App\Models\ProductImageHostingLibraryModel;
use App\Models\ProductModel;
use Exception;

class ProductImageLibraryService
{
    /**
     * 상품에 목록화해 둔 이미지 호스팅 라이브러리
     */
    public function getLibrary(int $prdPk): array
    {
        $this->ensureTable();
        if ($prdPk <= 0) {
            return [];
        }

        $rows = ProductImageHostingLibraryModel::query()
            ->where('prd_pk', '=', $prdPk)
            ->orderBy('sort_no', 'asc')
            ->orderBy('idx', 'asc')
            ->get()
            ->toArray();

        $items = [];
        foreach ($rows as $row) {
            if (!is_array($row) || $row === []) {
                continue;
            }
            $items[] = $this->normalizeItem($row);
        }

        return $items;
    }

    /**
     * 이미지 저장소 폴더를 읽어 DB에 없는 파일만 추가한다.
     * 이미 목록에 있는 파일은 삭제·변경하지 않는다.
     */
    public function importFromHosting(int $prdPk): array
    {
        $this->ensureTable();
        if ($prdPk <= 0) {
            throw new Exception('상품 번호가 없습니다.');
        }

        $product = ProductModel::query()
            ->select(['CD_IDX', 'CD_IMAGE_STORAGE_PATH'])
            ->where('CD_IDX', '=', $prdPk)
            ->first();
        $product = $product ? (is_array($product) ? $product : $product->toArray()) : [];
        if ($product === []) {
            throw new Exception('상품을 찾을 수 없습니다.');
        }

        $storagePath = trim((string)($product['CD_IMAGE_STORAGE_PATH'] ?? ''));
        if ($storagePath === '') {
            throw new Exception('이미지 저장소 경로가 없습니다. 상품 정보수집에서 먼저 설정해 주세요.');
        }

        $existingRows = $this->getLibrary($prdPk);
        $existingByFilename = [];
        $maxSortNo = 0;
        foreach ($existingRows as $existing) {
            $filename = (string)($existing['filename'] ?? '');
            if ($filename !== '') {
                $existingByFilename[$filename] = $existing;
            }
            $maxSortNo = max($maxSortNo, (int)($existing['sort_no'] ?? 0));
        }

        $listed = (new ProductImageHostingService())->listStorageImages(
            $storagePath,
            static function (string $filename) use ($existingByFilename): bool {
                if (!isset($existingByFilename[$filename])) {
                    return true;
                }
                $existing = $existingByFilename[$filename];

                return (int)($existing['file_size'] ?? 0) <= 0
                    || (int)($existing['width'] ?? 0) <= 0
                    || (int)($existing['height'] ?? 0) <= 0;
            }
        );
        $added = [];
        $updatedMeta = 0;
        $now = date('Y-m-d H:i:s');
        foreach ($listed as $file) {
            $filename = (string)($file['filename'] ?? '');
            if ($filename === '') {
                continue;
            }
            $fileSize = (int)($file['file_size'] ?? 0);
            $width = (int)($file['width'] ?? 0);
            $height = (int)($file['height'] ?? 0);

            if (isset($existingByFilename[$filename])) {
                $existing = $existingByFilename[$filename];
                $existingIdx = (int)($existing['idx'] ?? 0);
                $needsMeta = $existingIdx > 0
                    && ((int)($existing['file_size'] ?? 0) <= 0 || (int)($existing['width'] ?? 0) <= 0 || (int)($existing['height'] ?? 0) <= 0)
                    && ($fileSize > 0 || $width > 0 || $height > 0);
                if ($needsMeta) {
                    ProductImageHostingLibraryModel::query()->update(
                        [
                            'file_size' => $fileSize > 0 ? $fileSize : (int)($existing['file_size'] ?? 0),
                            'width' => $width > 0 ? $width : (int)($existing['width'] ?? 0),
                            'height' => $height > 0 ? $height : (int)($existing['height'] ?? 0),
                        ],
                        ['idx' => $existingIdx]
                    );
                    $updatedMeta++;
                }
                continue;
            }

            $maxSortNo++;
            $insertId = (int)ProductImageHostingLibraryModel::query()->insertGetId([
                'prd_pk' => $prdPk,
                'filename' => $filename,
                'hosting_url' => (string)($file['hosting_url'] ?? ''),
                'remote_path' => (string)($file['remote_path'] ?? ''),
                'storage_path' => $storagePath,
                'sort_no' => $maxSortNo,
                'file_size' => $fileSize,
                'width' => $width,
                'height' => $height,
                'imported_at' => $now,
                'created_at' => $now,
            ]);
            $added[] = $this->normalizeItem([
                'idx' => $insertId,
                'prd_pk' => $prdPk,
                'filename' => $filename,
                'hosting_url' => (string)($file['hosting_url'] ?? ''),
                'remote_path' => (string)($file['remote_path'] ?? ''),
                'storage_path' => $storagePath,
                'sort_no' => $maxSortNo,
                'file_size' => $fileSize,
                'width' => $width,
                'height' => $height,
                'imported_at' => $now,
            ]);
            $existingByFilename[$filename] = true;
        }

        $items = $this->getLibrary($prdPk);
        $addedCount = count($added);
        $listedCount = count($listed);

        if ($listedCount === 0) {
            $message = '이미지 저장소 폴더에서 이미지를 찾지 못했습니다.';
        } elseif ($addedCount === 0 && $updatedMeta > 0) {
            $message = '이미 목록화된 이미지입니다. 크기·해상도 정보를 채워 넣었습니다.';
        } elseif ($addedCount === 0) {
            $message = '이미 목록화된 이미지입니다. 새로 추가된 파일이 없습니다.';
        } elseif ($updatedMeta > 0) {
            $message = $addedCount . '장을 추가하고, 기존 이미지 크기·해상도를 채워 넣었습니다.';
        } else {
            $message = $addedCount . '장을 라이브러리에 추가했습니다.';
        }

        return [
            'storage_path' => $storagePath,
            'listed_count' => $listedCount,
            'added_count' => $addedCount,
            'already_count' => max(0, $listedCount - $addedCount),
            'updated_meta_count' => $updatedMeta,
            'items' => $items,
            'message' => $message,
        ];
    }

    /**
     * 로컬 이미지를 저장소에 올리고 라이브러리에 추가한다.
     *
     * @param array<int,array{name?:string,tmp_name?:string,body?:string,size?:int,error?:int}> $files
     */
    public function uploadToLibrary(int $prdPk, array $files): array
    {
        $this->ensureTable();
        if ($prdPk <= 0) {
            throw new Exception('상품 번호가 없습니다.');
        }
        if ($files === []) {
            throw new Exception('업로드할 이미지가 없습니다.');
        }

        $product = ProductModel::query()
            ->select(['CD_IDX', 'CD_IMAGE_STORAGE_PATH'])
            ->where('CD_IDX', '=', $prdPk)
            ->first();
        $product = $product ? (is_array($product) ? $product : $product->toArray()) : [];
        if ($product === []) {
            throw new Exception('상품을 찾을 수 없습니다.');
        }

        $storagePath = trim((string)($product['CD_IMAGE_STORAGE_PATH'] ?? ''));
        if ($storagePath === '') {
            throw new Exception('이미지 저장소 경로가 없습니다. 상품 정보수집에서 먼저 설정해 주세요.');
        }

        $uploaded = (new ProductImageHostingService())->uploadLocalImages($storagePath, $files);
        $existingRows = $this->getLibrary($prdPk);
        $existingByFilename = [];
        $maxSortNo = 0;
        foreach ($existingRows as $existing) {
            $filename = (string)($existing['filename'] ?? '');
            if ($filename !== '') {
                $existingByFilename[$filename] = $existing;
            }
            $maxSortNo = max($maxSortNo, (int)($existing['sort_no'] ?? 0));
        }

        $added = [];
        $now = date('Y-m-d H:i:s');
        foreach ($uploaded as $file) {
            $filename = (string)($file['filename'] ?? '');
            if ($filename === '') {
                continue;
            }
            if (isset($existingByFilename[$filename])) {
                $added[] = $existingByFilename[$filename];
                continue;
            }
            $maxSortNo++;
            $insertId = (int)ProductImageHostingLibraryModel::query()->insertGetId([
                'prd_pk' => $prdPk,
                'filename' => $filename,
                'hosting_url' => (string)($file['hosting_url'] ?? ''),
                'remote_path' => (string)($file['remote_path'] ?? ''),
                'storage_path' => $storagePath,
                'sort_no' => $maxSortNo,
                'file_size' => (int)($file['file_size'] ?? 0),
                'width' => (int)($file['width'] ?? 0),
                'height' => (int)($file['height'] ?? 0),
                'imported_at' => $now,
                'created_at' => $now,
            ]);
            $added[] = $this->normalizeItem([
                'idx' => $insertId,
                'prd_pk' => $prdPk,
                'filename' => $filename,
                'hosting_url' => (string)($file['hosting_url'] ?? ''),
                'remote_path' => (string)($file['remote_path'] ?? ''),
                'storage_path' => $storagePath,
                'sort_no' => $maxSortNo,
                'file_size' => (int)($file['file_size'] ?? 0),
                'width' => (int)($file['width'] ?? 0),
                'height' => (int)($file['height'] ?? 0),
                'imported_at' => $now,
            ]);
        }

        return [
            'storage_path' => $storagePath,
            'added_count' => count($added),
            'added' => $added,
            'items' => $this->getLibrary($prdPk),
            'message' => count($added) . '장을 이미지 저장소와 라이브러리에 추가했습니다.',
        ];
    }

    private function normalizeItem(array $item): array
    {
        $fileSize = (int)($item['file_size'] ?? 0);
        $width = (int)($item['width'] ?? 0);
        $height = (int)($item['height'] ?? 0);
        $dimensionLabel = ($width > 0 && $height > 0) ? ($width . '×' . $height) : '';
        $fileSizeLabel = $this->formatFileSize($fileSize);
        $metaParts = array_values(array_filter([$dimensionLabel, $fileSizeLabel], static function ($value) {
            return $value !== '';
        }));

        return [
            'idx' => (int)($item['idx'] ?? 0),
            'prd_pk' => (int)($item['prd_pk'] ?? 0),
            'filename' => (string)($item['filename'] ?? ''),
            'hosting_url' => (string)($item['hosting_url'] ?? ''),
            'remote_path' => (string)($item['remote_path'] ?? ''),
            'storage_path' => (string)($item['storage_path'] ?? ''),
            'sort_no' => (int)($item['sort_no'] ?? 0),
            'file_size' => $fileSize,
            'width' => $width,
            'height' => $height,
            'file_size_label' => $fileSizeLabel,
            'dimension_label' => $dimensionLabel,
            'meta_label' => implode(' · ', $metaParts),
            'imported_at' => (string)($item['imported_at'] ?? ''),
        ];
    }

    private function formatFileSize(int $bytes): string
    {
        if ($bytes <= 0) {
            return '';
        }
        if ($bytes < 1024) {
            return $bytes . ' B';
        }
        if ($bytes < 1024 * 1024) {
            return rtrim(rtrim(number_format($bytes / 1024, 1, '.', ''), '0'), '.') . ' KB';
        }

        return rtrim(rtrim(number_format($bytes / (1024 * 1024), 1, '.', ''), '0'), '.') . ' MB';
    }

    private function ensureTable(): void
    {
        Database::getInstance()->execute("
            CREATE TABLE IF NOT EXISTS `prd_image_hosting_library` (
                `idx` int unsigned NOT NULL AUTO_INCREMENT,
                `prd_pk` int unsigned NOT NULL,
                `filename` varchar(255) NOT NULL,
                `hosting_url` varchar(500) NOT NULL,
                `remote_path` varchar(500) NOT NULL DEFAULT '',
                `storage_path` varchar(255) NOT NULL DEFAULT '',
                `sort_no` int NOT NULL DEFAULT 0,
                `file_size` int unsigned NOT NULL DEFAULT 0,
                `width` int unsigned NOT NULL DEFAULT 0,
                `height` int unsigned NOT NULL DEFAULT 0,
                `imported_at` datetime DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                PRIMARY KEY (`idx`),
                UNIQUE KEY `uniq_prd_filename` (`prd_pk`, `filename`),
                KEY `idx_prd_pk` (`prd_pk`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $this->ensureColumns();
    }

    private function ensureColumns(): void
    {
        $columns = Database::getInstance()->fetchAll('SHOW COLUMNS FROM `prd_image_hosting_library`');
        $names = [];
        foreach ($columns as $column) {
            $name = (string)($column['Field'] ?? '');
            if ($name !== '') {
                $names[$name] = true;
            }
        }
        if (empty($names['file_size'])) {
            Database::getInstance()->execute('ALTER TABLE `prd_image_hosting_library` ADD COLUMN `file_size` int unsigned NOT NULL DEFAULT 0 AFTER `sort_no`');
        }
        if (empty($names['width'])) {
            Database::getInstance()->execute('ALTER TABLE `prd_image_hosting_library` ADD COLUMN `width` int unsigned NOT NULL DEFAULT 0 AFTER `file_size`');
        }
        if (empty($names['height'])) {
            Database::getInstance()->execute('ALTER TABLE `prd_image_hosting_library` ADD COLUMN `height` int unsigned NOT NULL DEFAULT 0 AFTER `width`');
        }
    }
}
