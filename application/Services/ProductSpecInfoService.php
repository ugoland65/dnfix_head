<?php

namespace App\Services;

use Exception;
use App\Classes\ImageStorage;
use App\Models\ProductModel;
use App\Models\ProductSpecInfoModel;

class ProductSpecInfoService
{
    private const IMAGE_WEB_PREFIX = '/data/prd_spec_info';
    private const ALLOWED_ITEM_TYPES = ['total', 'inner', 'inner_1', 'inner_2'];

    /**
     * 상품 스펙 측정 페이지 데이터
     */
    public function getPageData(int $cdIdx): array
    {
        $product = $this->requireProduct($cdIdx);
        $row = ProductSpecInfoModel::where('psi_cd_idx', $cdIdx)->first();

        return [
            'prd_idx' => $cdIdx,
            'product_name' => (string)($product['CD_NAME'] ?? ''),
            'spec' => $this->normalizeRow($row, $cdIdx),
        ];
    }

    /**
     * 단면도 업로드. 이미지 교체 시 기존 측정값은 초기화한다.
     */
    public function uploadImage(int $cdIdx, array $file, array $admin = []): array
    {
        $this->requireProduct($cdIdx);

        $existing = ProductSpecInfoModel::where('psi_cd_idx', $cdIdx)->first();
        $existingData = $this->rowToArray($existing);
        $imagePath = $this->storeLocalImage($cdIdx, $file, (string)($existingData['psi_image_path'] ?? ''));

        $now = date('Y-m-d H:i:s');
        $saved = ProductSpecInfoModel::updateOrCreate(
            ['psi_cd_idx' => $cdIdx],
            [
                'psi_image_path' => $imagePath,
                'psi_total_length_cm' => null,
                'psi_px_per_cm' => null,
                'psi_items' => [],
                'psi_admin_idx' => (int)($admin['idx'] ?? 0) ?: null,
                'psi_admin_name' => (string)($admin['name'] ?? '') ?: null,
                'updated_at' => $now,
            ]
        );

        return $this->normalizeRow($saved, $cdIdx);
    }

    /**
     * 외부 URL 이미지를 서버에 저장하지 않고 미리보기용으로만 가져온다.
     */
    public function previewRemoteImage(string $url): array
    {
        return $this->downloadPublicImage($url);
    }

    /**
     * 측정값 저장
     */
    public function save(int $cdIdx, array $data, array $admin = []): array
    {
        $this->requireProduct($cdIdx);

        $existing = ProductSpecInfoModel::where('psi_cd_idx', $cdIdx)->first();
        $existingData = $this->normalizeRow($existing, $cdIdx);
        $imagePath = (string)($existingData['psi_image_path'] ?? '');
        $sourceUrl = trim((string)($data['image_url'] ?? ''));
        $rotatedFile = is_array($data['rotated_file'] ?? null) ? $data['rotated_file'] : [];
        if (!empty($rotatedFile['tmp_name'])) {
            $imagePath = $this->storeLocalImage($cdIdx, $rotatedFile, $imagePath);
        } elseif ($sourceUrl !== '') {
            $imagePath = $this->storeImageFromUrl($cdIdx, $sourceUrl, $imagePath);
        }
        if ($imagePath === '') {
            throw new Exception('단면도를 파일로 올리거나 URL을 입력해주세요.');
        }

        $items = $this->normalizeItems($data['psi_items'] ?? []);
        $totalItem = $this->findItem($items, 'total');
        $totalLength = $this->toDecimal($data['psi_total_length_cm'] ?? ($totalItem['cm'] ?? null), 2);
        if ($totalLength === null || $totalLength <= 0) {
            throw new Exception('전체길이를 입력해주세요.');
        }
        if ($totalItem === null || (float)($totalItem['px'] ?? 0) <= 0) {
            throw new Exception('전체길이 시작과 끝을 지정해주세요.');
        }

        $pxPerCm = round((float)$totalItem['px'] / $totalLength, 4);
        $items = $this->recalculateItems($items, $totalLength);

        $now = date('Y-m-d H:i:s');
        $saved = ProductSpecInfoModel::updateOrCreate(
            ['psi_cd_idx' => $cdIdx],
            [
                'psi_image_path' => $imagePath,
                'psi_total_length_cm' => $totalLength,
                'psi_px_per_cm' => $pxPerCm,
                'psi_items' => $items,
                'psi_admin_idx' => (int)($admin['idx'] ?? 0) ?: null,
                'psi_admin_name' => (string)($admin['name'] ?? '') ?: null,
                'updated_at' => $now,
            ]
        );

        return $this->normalizeRow($saved, $cdIdx);
    }

    private function requireProduct(int $cdIdx): array
    {
        if ($cdIdx <= 0) {
            throw new Exception('상품 번호가 없습니다.');
        }
        $product = ProductModel::find($cdIdx);
        if (empty($product)) {
            throw new Exception('상품을 찾을 수 없습니다.');
        }
        return is_array($product) ? $product : $product->toArray();
    }

    private function normalizeRow($row, int $cdIdx): array
    {
        $data = $this->rowToArray($row);
        $items = $data['psi_items'] ?? [];
        if (is_string($items)) {
            $decoded = json_decode($items, true);
            $items = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($items)) {
            $items = [];
        }

        return [
            'psi_idx' => (int)($data['psi_idx'] ?? 0),
            'psi_cd_idx' => (int)($data['psi_cd_idx'] ?? $cdIdx),
            'psi_image_path' => (string)($data['psi_image_path'] ?? ''),
            'psi_total_length_cm' => $this->toDecimal($data['psi_total_length_cm'] ?? null, 2),
            'psi_px_per_cm' => $this->toDecimal($data['psi_px_per_cm'] ?? null, 4),
            'psi_items' => $this->normalizeItems($items),
            'psi_admin_name' => (string)($data['psi_admin_name'] ?? ''),
            'updated_at' => (string)($data['updated_at'] ?? ''),
        ];
    }

    private function normalizeItems($raw): array
    {
        if (!is_array($raw)) {
            return [];
        }

        $items = [];
        foreach ($raw as $row) {
            if (!is_array($row)) {
                continue;
            }
            $type = trim((string)($row['type'] ?? ''));
            if (!in_array($type, self::ALLOWED_ITEM_TYPES, true)) {
                continue;
            }
            $x1 = (float)($row['x1'] ?? 0);
            $y1 = (float)($row['y1'] ?? 0);
            $x2 = (float)($row['x2'] ?? 0);
            $items[] = [
                'type' => $type,
                'label' => (string)($row['label'] ?? $this->defaultLabel($type)),
                'x1' => round($x1, 2),
                'y1' => round($y1, 2),
                'x2' => round($x2, 2),
                'y2' => round($y1, 2),
                'px' => round(abs($x2 - $x1), 2),
                'cm' => $this->toDecimal($row['cm'] ?? null, 2),
            ];
        }

        return $items;
    }

    private function recalculateItems(array $items, float $totalLength): array
    {
        $total = $this->findItem($items, 'total');
        $totalPx = (float)($total['px'] ?? 0);
        if ($totalPx <= 0) {
            return $items;
        }

        foreach ($items as $index => $item) {
            $px = (float)($item['px'] ?? 0);
            if (($item['type'] ?? '') === 'total') {
                $items[$index]['cm'] = $totalLength;
                continue;
            }
            $items[$index]['cm'] = $px > 0 ? round($px / $totalPx * $totalLength, 1) : null;
        }

        return $items;
    }

    private function findItem(array $items, string $type): ?array
    {
        foreach ($items as $item) {
            if (($item['type'] ?? '') === $type) {
                return $item;
            }
        }
        return null;
    }

    private function defaultLabel(string $type): string
    {
        $labels = [
            'total' => '전체길이',
            'inner' => '내부길이',
            'inner_1' => '내부길이 1',
            'inner_2' => '내부길이 2',
        ];
        return $labels[$type] ?? $type;
    }

    private function rowToArray($row): array
    {
        if (empty($row)) {
            return [];
        }
        if (is_array($row)) {
            return $row;
        }
        if (is_object($row) && method_exists($row, 'toArray')) {
            return $row->toArray();
        }
        return (array)$row;
    }

    private function toDecimal($value, int $scale): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (!is_numeric($value)) {
            return null;
        }
        return round((float)$value, $scale);
    }

    private function storeLocalImage(int $cdIdx, array $file, string $oldImagePath = ''): string
    {
        $relativeDir = self::IMAGE_WEB_PREFIX . '/' . date('Y') . '/' . date('m');
        $uploadsDir = $this->toAbsolutePath($relativeDir);
        $baseName = 'psi_' . $cdIdx . '_' . time();
        $fileName = (new ImageStorage())->storeUploaded($file, $uploadsDir, $baseName);
        $this->deleteStoredImage($oldImagePath);

        return $relativeDir . '/' . $fileName;
    }

    private function storeImageFromUrl(int $cdIdx, string $url, string $oldImagePath = ''): string
    {
        $this->assertPublicImageUrl($url);

        $relativeDir = self::IMAGE_WEB_PREFIX . '/' . date('Y') . '/' . date('m');
        $uploadsDir = $this->toAbsolutePath($relativeDir);
        $baseName = 'psi_' . $cdIdx . '_' . time();
        $fileName = (new ImageStorage())->storeFromUrl($url, $uploadsDir, $baseName);
        $this->deleteStoredImage($oldImagePath);

        return $relativeDir . '/' . $fileName;
    }

    private function downloadPublicImage(string $url): array
    {
        $url = $this->assertPublicImageUrl($url);
        $raw = $this->fetchUrlBinary($url);
        if ($raw === '') {
            throw new Exception('이미지를 가져오지 못했습니다.');
        }
        if (strlen($raw) > 15 * 1024 * 1024) {
            throw new Exception('이미지 용량이 너무 큽니다.');
        }

        $imageInfo = @getimagesizefromstring($raw);
        if (!is_array($imageInfo) || empty($imageInfo['mime'])) {
            throw new Exception('유효한 이미지 URL이 아닙니다.');
        }
        $mime = strtolower((string)$imageInfo['mime']);
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true)) {
            throw new Exception('jpg, png, gif, webp만 사용할 수 있습니다.');
        }

        return [
            'body' => $raw,
            'content_type' => $mime,
        ];
    }

    private function assertPublicImageUrl(string $url): string
    {
        $url = trim($url);
        $parts = parse_url($url);
        $scheme = strtolower((string)($parts['scheme'] ?? ''));
        $host = strtolower((string)($parts['host'] ?? ''));
        if (!in_array($scheme, ['http', 'https'], true) || $host === '') {
            throw new Exception('http/https 이미지 URL만 사용할 수 있습니다.');
        }
        if (in_array($host, ['localhost', '127.0.0.1', '0.0.0.0', '::1'], true)) {
            throw new Exception('내부 주소는 사용할 수 없습니다.');
        }

        $ips = @gethostbynamel($host);
        if (!is_array($ips) || $ips === []) {
            throw new Exception('이미지 주소를 확인할 수 없습니다.');
        }
        foreach ($ips as $ip) {
            if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                throw new Exception('내부 주소는 사용할 수 없습니다.');
            }
        }

        return $url;
    }

    private function fetchUrlBinary(string $url): string
    {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_CONNECTTIMEOUT => 8,
                CURLOPT_TIMEOUT => 20,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; A1SpecInfo/1.0)',
            ]);
            $raw = curl_exec($ch);
            $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if (!is_string($raw) || $httpCode < 200 || $httpCode >= 300) {
                return '';
            }
            return $raw;
        }

        return (string)@file_get_contents($url);
    }

    private function deleteStoredImage(string $webPath): void
    {
        $webPath = str_replace('\\', '/', trim($webPath));
        if ($webPath === '' || strpos($webPath, self::IMAGE_WEB_PREFIX . '/') !== 0) {
            return;
        }
        $abs = $this->toAbsolutePath($webPath);
        if (is_file($abs)) {
            @unlink($abs);
        }
    }

    private function toAbsolutePath(string $webPath): string
    {
        $webPath = '/' . ltrim(str_replace('\\', '/', $webPath), '/');
        return rtrim((string)($_SERVER['DOCUMENT_ROOT'] ?? ''), '/\\') . str_replace('/', DIRECTORY_SEPARATOR, $webPath);
    }
}
