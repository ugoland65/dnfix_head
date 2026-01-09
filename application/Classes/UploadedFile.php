<?php

namespace App\Classes;

use RuntimeException;

/**
 * 간단한 업로드 파일 헬퍼 (이미지 저장 지원)
 * - $_FILES 항목을 받아 원본명/확장자/사이즈/타입 확인
 * - 이미지 여부 확인 후 지정 경로로 move 처리
 */
class UploadedFile
{
    private string $originalName;
    private string $tmpPath;
    private ?string $mimeType;
    private int $size;
    private int $error;

    public function __construct(array $file)
    {
        $this->originalName = $file['name'] ?? '';
        $this->tmpPath      = $file['tmp_name'] ?? '';
        $this->mimeType     = $file['type'] ?? null;
        $this->size         = (int)($file['size'] ?? 0);
        $this->error        = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
    }

    public function isValid(): bool
    {
        return $this->error === UPLOAD_ERR_OK && is_uploaded_file($this->tmpPath);
    }

    public function getClientOriginalName(): string
    {
        return $this->originalName;
    }

    public function getClientOriginalExtension(): string
    {
        return pathinfo($this->originalName, PATHINFO_EXTENSION) ?: '';
    }

    public function getMimeType(): ?string
    {
        if ($this->mimeType) {
            return $this->mimeType;
        }

        if (!is_file($this->tmpPath)) {
            return null;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = $finfo ? finfo_file($finfo, $this->tmpPath) : null;
        if ($finfo) {
            finfo_close($finfo);
        }
        return $mime ?: null;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getPath(): string
    {
        return $this->tmpPath;
    }

    /**
     * 이미지 파일만 허용하여 저장
     * @param string $directory 저장 디렉토리
     * @param string|null $filename 저장 파일명 (없으면 uniqid 기반)
     * @return string 저장된 풀 패스
     */
    public function moveImage(string $directory, string $filename = null): string
    {
        $mime = $this->getMimeType();
        if (!$mime || strpos($mime, 'image/') !== 0) {
            throw new RuntimeException('올바른 이미지 파일이 아닙니다.');
        }

        return $this->move($directory, $filename);
    }

    /**
     * 지정 경로로 파일 이동
     * @param string $directory 저장 디렉토리
     * @param string|null $filename 저장 파일명 (없으면 uniqid 기반)
     * @return string 저장된 풀 패스
     */
    public function move(string $directory, string $filename = null): string
    {
        if (!$this->isValid()) {
            throw new RuntimeException('업로드 파일이 유효하지 않습니다.');
        }

        if (!is_dir($directory)) {
            if (!mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new RuntimeException('저장 경로를 생성할 수 없습니다: ' . $directory);
            }
        }

        $safeDir = rtrim($directory, DIRECTORY_SEPARATOR);
        $extension = $this->getClientOriginalExtension();
        $name = $filename ?: (uniqid('upload_', true) . ($extension ? '.' . $extension : ''));
        $target = $safeDir . DIRECTORY_SEPARATOR . $name;

        if (!move_uploaded_file($this->tmpPath, $target)) {
            throw new RuntimeException('파일 저장에 실패했습니다.');
        }

        return $target;
    }
}
