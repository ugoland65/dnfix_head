<?php

namespace App\Classes;

use Exception;

class ImageStorage
{
    /**
     * 업로드 파일 저장
     *
     * @param array $file $_FILES 항목
     * @param string $directory 저장 경로(절대 경로)
     * @param string $baseName 파일명(확장자 제외)
     * @param int|null $width 리사이즈 가로
     * @param int|null $height 리사이즈 세로
     * @return string 저장된 파일명
     * @throws Exception
     */
    public function storeUploaded(array $file, string $directory, string $baseName, ?int $width = null, ?int $height = null): string
    {
        $tmpFile = (string)($file['tmp_name'] ?? '');
        $originName = (string)($file['name'] ?? '');
        if ($tmpFile === '' || !is_uploaded_file($tmpFile)) {
            throw new Exception('유효한 업로드 파일이 아닙니다.');
        }

        $extension = $this->resolveExtension($originName, $tmpFile);
        $saveFileName = $baseName . '.' . $extension;
        $destination = $this->buildDestination($directory, $saveFileName);

        if (!move_uploaded_file($tmpFile, $destination)) {
            throw new Exception('이미지 저장에 실패했습니다.');
        }

        $this->resizeIfNeeded($destination, $width, $height);
        return $saveFileName;
    }

    /**
     * 외부 URL 이미지 저장
     *
     * @param string $url 이미지 URL
     * @param string $directory 저장 경로(절대 경로)
     * @param string $baseName 파일명(확장자 제외)
     * @param int|null $width 리사이즈 가로
     * @param int|null $height 리사이즈 세로
     * @return string 저장된 파일명
     * @throws Exception
     */
    public function storeFromUrl(string $url, string $directory, string $baseName, ?int $width = null, ?int $height = null): string
    {
        $url = trim($url);
        if ($url === '') {
            throw new Exception('이미지 URL이 비어 있습니다.');
        }

        $raw = $this->downloadBinary($url);
        if ($raw === '') {
            throw new Exception('외부 이미지를 가져오지 못했습니다.');
        }

        $imageInfo = @getimagesizefromstring($raw);
        if (!is_array($imageInfo)) {
            throw new Exception('유효한 이미지 파일이 아닙니다.');
        }

        $extension = $this->extensionByImageType((int)($imageInfo[2] ?? 0)) ?: $this->extensionByUrl($url);
        if ($extension === '') {
            $extension = 'jpg';
        }

        $saveFileName = $baseName . '.' . $extension;
        $destination = $this->buildDestination($directory, $saveFileName);
        if (@file_put_contents($destination, $raw) === false) {
            throw new Exception('외부 이미지 저장에 실패했습니다.');
        }

        $this->resizeIfNeeded($destination, $width, $height);
        return $saveFileName;
    }

    private function buildDestination(string $directory, string $fileName): string
    {
        $directory = rtrim($directory, '/\\');
        if (!is_dir($directory) && !@mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new Exception('이미지 저장 경로 생성에 실패했습니다.');
        }
        return $directory . DIRECTORY_SEPARATOR . $fileName;
    }

    private function resolveExtension(string $originName, string $tmpFile): string
    {
        $ext = strtolower(pathinfo($originName, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
            return $ext;
        }

        $imageInfo = @getimagesize($tmpFile);
        $byType = $this->extensionByImageType((int)($imageInfo[2] ?? 0));
        return $byType ?: 'jpg';
    }

    private function extensionByUrl(string $url): string
    {
        $path = (string)parse_url($url, PHP_URL_PATH);
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true) ? $ext : '';
    }

    private function extensionByImageType(int $imageType): string
    {
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                return 'jpg';
            case IMAGETYPE_PNG:
                return 'png';
            case IMAGETYPE_GIF:
                return 'gif';
            case IMAGETYPE_WEBP:
                return 'webp';
            default:
                return '';
        }
    }

    private function downloadBinary(string $url): string
    {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            $raw = (string)curl_exec($ch);
            curl_close($ch);
            return $raw;
        }

        return (string)@file_get_contents($url);
    }

    private function resizeIfNeeded(string $filePath, ?int $width, ?int $height): void
    {
        $width = (int)($width ?? 0);
        $height = (int)($height ?? 0);
        if ($width <= 0 || $height <= 0) {
            return;
        }

        if (!extension_loaded('gd')) {
            return;
        }

        $info = @getimagesize($filePath);
        if (!is_array($info) || empty($info[0]) || empty($info[1])) {
            return;
        }

        $srcW = (int)$info[0];
        $srcH = (int)$info[1];
        if ($srcW <= 0 || $srcH <= 0) {
            return;
        }

        $type = (int)($info[2] ?? 0);
        $src = $this->createImageResource($filePath, $type);
        if (!$src) {
            return;
        }

        $srcRatio = $srcW / $srcH;
        $targetRatio = $width / $height;
        $isRatioDifferent = abs($srcRatio - $targetRatio) > 0.0001;

        // 비율이 같고 원본이 더 작으면 기존처럼 원본 유지
        if (!$isRatioDifferent && $srcW <= $width && $srcH <= $height) {
            imagedestroy($src);
            return;
        }

        $dst = imagecreatetruecolor($width, $height);
        if (in_array($type, [IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP], true)) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefilledrectangle($dst, 0, 0, $width, $height, $transparent);
        } else {
            $white = imagecolorallocate($dst, 255, 255, 255);
            imagefilledrectangle($dst, 0, 0, $width, $height, $white);
        }

        // 긴 변 기준으로 축소(업스케일 금지) 후, 남는 공간은 여백으로 채운다.
        $scale = min($width / $srcW, $height / $srcH, 1);
        $dstW = max(1, (int)round($srcW * $scale));
        $dstH = max(1, (int)round($srcH * $scale));
        $dstX = (int)floor(($width - $dstW) / 2);
        $dstY = (int)floor(($height - $dstH) / 2);

        imagecopyresampled($dst, $src, $dstX, $dstY, 0, 0, $dstW, $dstH, $srcW, $srcH);
        $this->saveImageResource($dst, $filePath, $type);

        imagedestroy($src);
        imagedestroy($dst);
    }

    private function createImageResource(string $filePath, int $type)
    {
        switch ($type) {
            case IMAGETYPE_JPEG:
                return @imagecreatefromjpeg($filePath);
            case IMAGETYPE_PNG:
                return @imagecreatefrompng($filePath);
            case IMAGETYPE_GIF:
                return @imagecreatefromgif($filePath);
            case IMAGETYPE_WEBP:
                return function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($filePath) : null;
            default:
                return null;
        }
    }

    private function saveImageResource($resource, string $filePath, int $type): void
    {
        switch ($type) {
            case IMAGETYPE_JPEG:
                @imagejpeg($resource, $filePath, 85);
                break;
            case IMAGETYPE_PNG:
                @imagepng($resource, $filePath);
                break;
            case IMAGETYPE_GIF:
                @imagegif($resource, $filePath);
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagewebp')) {
                    @imagewebp($resource, $filePath, 85);
                } else {
                    @imagepng($resource, $filePath);
                }
                break;
            default:
                @imagejpeg($resource, $filePath, 85);
                break;
        }
    }
}

