<?php

namespace App\Services;

class ProductImageHostingService
{
    /**
     * 이미지 호스팅 설정과 업로드 대상 경로를 검증하고,
     * 원본 이미지별 원격 경로·공개 URL 계획을 생성한다.
     *
     * 실제 SFTP/FTPS 업로드는 호스팅 접속정보가 설정된 뒤 이 계획을 사용해 추가한다.
     */
    public function prepareUpload(array $data): array
    {
        $config = $this->getConfig();
        $this->validateConfig($config);

        $storagePath = $this->normalizeStoragePath((string)($data['image_storage_path'] ?? ''));
        $siteCode = strtoupper(trim((string)($data['site_code'] ?? '')));
        if ($siteCode === '' || !preg_match('/^[A-Z0-9_-]+$/', $siteCode)) {
            throw new \InvalidArgumentException('이미지 파일명에 사용할 사이트 코드가 올바르지 않습니다.');
        }
        $sourceImageUrls = is_array($data['source_image_urls'] ?? null) ? $data['source_image_urls'] : [];
        if (empty($sourceImageUrls)) {
            throw new \InvalidArgumentException('업로드할 수집 이미지가 없습니다.');
        }

        $remoteBasePath = rtrim((string)$config['remote_base_path'], '/');
        $publicBaseUrl = rtrim((string)$config['public_base_url'], '/');
        $uploads = [];
        foreach (array_values($sourceImageUrls) as $index => $sourceImage) {
            $sortNo = $index + 1;
            $sourceImageUrl = $sourceImage;
            if (is_array($sourceImage)) {
                $sourceImageUrl = trim((string)($sourceImage['url'] ?? $sourceImage['source_url'] ?? ''));
                $sortNo = max(1, (int)($sourceImage['sort_no'] ?? $sortNo));
            }
            $sourceImageUrl = trim((string)$sourceImageUrl);
            $urlParts = parse_url($sourceImageUrl);
            $scheme = strtolower((string)($urlParts['scheme'] ?? ''));
            if (!in_array($scheme, ['http', 'https'], true) || empty($urlParts['host'])) {
                throw new \InvalidArgumentException('올바르지 않은 원본 이미지 URL입니다.');
            }

            $extension = strtolower(pathinfo((string)($urlParts['path'] ?? ''), PATHINFO_EXTENSION));
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                $extension = 'jpg';
            }
            $filename = sprintf('%s_%02d.%s', $siteCode, $sortNo, $extension);
            $uploads[] = [
                'sort_no' => $sortNo,
                'source_url' => $sourceImageUrl,
                'remote_path' => $remoteBasePath . $storagePath . $filename,
                'hosting_url' => $publicBaseUrl . $storagePath . $filename,
                'filename' => $filename,
            ];
        }

        return [
            'protocol' => strtolower((string)$config['protocol']),
            'host' => (string)$config['host'],
            'port' => (int)$config['port'],
            'storage_path' => $storagePath,
            'uploads' => $uploads,
        ];
    }

    /**
     * 원본 이미지를 다운로드해 설정된 이미지 호스팅으로 업로드한다.
     *
     * @param array $data image_storage_path, source_image_urls
     * @return array 업로드된 공개 URL 배열 및 이미지별 결과
     */
    public function uploadCollectionImages(array $data): array
    {
        $plan = $this->prepareUpload($data);
        $config = $this->getConfig();
        $connection = $this->connect($config);
        $results = [];

        try {
            foreach ($plan['uploads'] as $upload) {
                try {
                    $image = $this->downloadSourceImage($upload['source_url']);
                    $this->uploadBinary($connection, $upload['remote_path'], $image['body']);
                    $results[] = [
                        'sort_no' => $upload['sort_no'],
                        'source_url' => $upload['source_url'],
                        'hosting_url' => $upload['hosting_url'],
                        'status' => 'success',
                    ];
                } catch (\Throwable $e) {
                    $results[] = [
                        'sort_no' => $upload['sort_no'],
                        'source_url' => $upload['source_url'],
                        'hosting_url' => null,
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ];
                }
            }
        } finally {
            $this->disconnect($connection);
        }

        $successUrls = array_values(array_filter(array_map(static function (array $result) {
            return $result['status'] === 'success' ? $result['hosting_url'] : null;
        }, $results)));
        $failedCount = count($results) - count($successUrls);

        return [
            'status' => $failedCount === 0 ? 'success' : (empty($successUrls) ? 'failed' : 'partial'),
            'uploads' => $results,
            'hosting_urls' => $successUrls,
            'success_count' => count($successUrls),
            'failed_count' => $failedCount,
        ];
    }

    /**
     * 상품 이미지 저장소 폴더의 이미지 파일 목록을 읽는다.
     *
     * @param callable|null $needsMeta filename => bool. null이면 전부 크기/해상도를 읽는다.
     * @return array<int,array{filename:string,hosting_url:string,remote_path:string,file_size:int,width:int,height:int}>
     */
    public function listStorageImages(string $storagePath, ?callable $needsMeta = null): array
    {
        $config = $this->getConfig();
        $this->validateConfig($config);
        $storagePath = $this->normalizeStoragePath($storagePath);
        $remoteBasePath = rtrim((string)$config['remote_base_path'], '/');
        $publicBaseUrl = rtrim((string)$config['public_base_url'], '/');
        $remoteDir = $remoteBasePath . $storagePath;

        $connection = $this->connect($config);
        try {
            $names = $this->listRemoteFiles($connection, $remoteDir);
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $images = [];
            foreach ($names as $filename) {
                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                if (!in_array($extension, $allowed, true)) {
                    continue;
                }
                $remotePath = $remoteDir . $filename;
                $hostingUrl = $publicBaseUrl . $storagePath . $filename;
                $item = [
                    'filename' => $filename,
                    'hosting_url' => $hostingUrl,
                    'remote_path' => $remotePath,
                    'file_size' => 0,
                    'width' => 0,
                    'height' => 0,
                ];
                if ($needsMeta === null || $needsMeta($filename)) {
                    $meta = $this->inspectRemoteImage($connection, $remotePath, $hostingUrl);
                    $item['file_size'] = (int)($meta['file_size'] ?? 0);
                    $item['width'] = (int)($meta['width'] ?? 0);
                    $item['height'] = (int)($meta['height'] ?? 0);
                }
                $images[] = $item;
            }
        } finally {
            $this->disconnect($connection);
        }

        usort($images, static function (array $a, array $b): int {
            return strnatcasecmp((string)$a['filename'], (string)$b['filename']);
        });

        return $images;
    }

    /**
     * 로컬 파일을 상품 이미지 저장소에 업로드한다.
     *
     * @param array<int,array{name?:string,tmp_name?:string,body?:string,size?:int,error?:int}> $files
     * @return array<int,array{filename:string,hosting_url:string,remote_path:string,file_size:int,width:int,height:int}>
     */
    public function uploadLocalImages(string $storagePath, array $files): array
    {
        $config = $this->getConfig();
        $this->validateConfig($config);
        $storagePath = $this->normalizeStoragePath($storagePath);
        $remoteBasePath = rtrim((string)$config['remote_base_path'], '/');
        $publicBaseUrl = rtrim((string)$config['public_base_url'], '/');

        $connection = $this->connect($config);
        $uploaded = [];
        try {
            $usedNames = [];
            foreach ($this->listRemoteFiles($connection, $remoteBasePath . $storagePath) as $existingName) {
                $usedNames[strtolower((string)$existingName)] = true;
            }
            foreach ($files as $file) {
                $error = (int)($file['error'] ?? 0);
                if ($error !== UPLOAD_ERR_OK && $error !== 0) {
                    throw new \RuntimeException('이미지 업로드에 실패했습니다.');
                }
                $binary = (string)($file['body'] ?? '');
                if ($binary === '') {
                    $tmpName = (string)($file['tmp_name'] ?? '');
                    if ($tmpName === '' || !is_file($tmpName)) {
                        throw new \RuntimeException('업로드 파일을 읽지 못했습니다.');
                    }
                    $binary = (string)file_get_contents($tmpName);
                }
                if ($binary === '') {
                    throw new \RuntimeException('빈 이미지 파일입니다.');
                }
                $info = @getimagesizefromstring($binary);
                if (!is_array($info)) {
                    throw new \RuntimeException('이미지 파일이 아닙니다.');
                }
                $mime = strtolower((string)($info['mime'] ?? ''));
                $extensionMap = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                    'image/webp' => 'webp',
                ];
                if (!isset($extensionMap[$mime])) {
                    throw new \RuntimeException('jpg, png, gif, webp만 업로드할 수 있습니다.');
                }
                $filename = $this->makeUniqueUploadFilename((string)($file['name'] ?? ''), $extensionMap[$mime], $usedNames);
                $usedNames[strtolower($filename)] = true;
                $remotePath = $remoteBasePath . $storagePath . $filename;
                $this->uploadBinary($connection, $remotePath, $binary);
                $uploaded[] = [
                    'filename' => $filename,
                    'hosting_url' => $publicBaseUrl . $storagePath . $filename,
                    'remote_path' => $remotePath,
                    'file_size' => strlen($binary),
                    'width' => (int)($info[0] ?? 0),
                    'height' => (int)($info[1] ?? 0),
                ];
            }
        } finally {
            $this->disconnect($connection);
        }

        return $uploaded;
    }

    private function makeUniqueUploadFilename(string $originalName, string $extension, array $usedNames): string
    {
        $base = strtolower(pathinfo($originalName, PATHINFO_FILENAME));
        $base = preg_replace('/[^a-z0-9._-]+/', '_', $base) ?? '';
        $base = trim($base, '._-');
        if ($base === '' || $base === 'image') {
            $base = 'upload';
        }
        $candidate = $base . '.' . $extension;
        if (empty($usedNames[strtolower($candidate)])) {
            return $candidate;
        }
        for ($i = 0; $i < 20; $i++) {
            $candidate = $base . '_' . date('YmdHis') . '_' . strtolower(bin2hex(random_bytes(2))) . '.' . $extension;
            if (empty($usedNames[strtolower($candidate)])) {
                return $candidate;
            }
            usleep(1000);
        }

        return $base . '_' . uniqid('', true) . '.' . $extension;
    }

    public static function getCollectedImageSourceSites(): array
    {
        return [
            'nipporigift.net' => 'http://www.nipporigift.net/',
            'tamatoys.tma.co.jp' => 'https://tamatoys.tma.co.jp/',
            'prod-tamatoys.s3.amazonaws.com' => 'https://tamatoys.tma.co.jp/',
            'mzakka.com' => 'https://mzakka.com/',
            'i.mzakka.com' => 'https://mzakka.com/',
            'img07.shop-pro.jp' => 'https://www.nobunaga-toys.com/',
            'e-nls.com' => 'https://www.e-nls.com/',
            'image.e-nls.com' => 'https://www.e-nls.com/',
            'msonline-g.com' => 'https://www.ms-online.co.jp/',
            'ms-online.co.jp' => 'https://www.ms-online.co.jp/',
            'go744sfa.user.webaccel.jp' => 'https://www.ms-online.co.jp/',
            'bb-order.com' => 'https://bb-order.com/',
            'ridejapan.net' => 'http://ridejapan.net/',
            'yelolab.jp' => 'https://yelolab.jp/',
        ];
    }

    public static function resolveCollectedImageUrl(string $sourceUrl, string $pageUrl = ''): string
    {
        $sourceUrl = trim($sourceUrl);
        if ($sourceUrl === '') {
            return '';
        }

        if (!preg_match('#^https?://#i', $sourceUrl)) {
            $pageHost = preg_replace('/^www\./', '', strtolower((string)(parse_url($pageUrl, PHP_URL_HOST) ?? '')));
            $bases = self::getCollectedImageSourceSites();
            if ($pageHost !== '' && isset($bases[$pageHost])) {
                $base = rtrim((string)$bases[$pageHost], '/');
            } elseif ($pageHost !== '') {
                $scheme = strtolower((string)(parse_url($pageUrl, PHP_URL_SCHEME) ?? 'http'));
                if (!in_array($scheme, ['http', 'https'], true)) {
                    $scheme = 'http';
                }
                $base = $scheme . '://' . (string)(parse_url($pageUrl, PHP_URL_HOST) ?? '');
            } else {
                $base = 'http://ridejapan.net';
            }
            $path = $sourceUrl[0] === '/' ? $sourceUrl : '/' . ltrim($sourceUrl, '/');
            return $base . $path;
        }

        $urlParts = parse_url($sourceUrl);
        $host = preg_replace('/^www\./', '', strtolower((string)($urlParts['host'] ?? '')));
        if ($host !== 'msonline-g.com') {
            return $sourceUrl;
        }

        $path = (string)($urlParts['path'] ?? '/');
        if ($path === '') {
            $path = '/';
        }
        $query = isset($urlParts['query']) && $urlParts['query'] !== '' ? '?' . $urlParts['query'] : '';

        return 'https://go744sfa.user.webaccel.jp' . $path . $query;
    }

    public static function collectedImageTranslationKey(string $sourceUrl): string
    {
        $resolvedUrl = self::resolveCollectedImageUrl($sourceUrl);
        $path = trim((string)(parse_url($resolvedUrl, PHP_URL_PATH) ?? ''));
        if ($path === '' || $path === '/') {
            return '';
        }
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }
        return $path;
    }

    public static function collectedImageAltTranslation(array $translationMap, string $sourceUrl): string
    {
        $imageKey = self::collectedImageTranslationKey($sourceUrl);
        if ($imageKey === '' || !isset($translationMap[$imageKey])) {
            return '';
        }
        $entry = $translationMap[$imageKey];
        if (is_array($entry)) {
            return trim((string)($entry['translated_alt'] ?? ''));
        }
        return trim((string)$entry);
    }

    public static function downloadCollectedSourceImage(string $sourceUrl, int $timeout = 20): array
    {
        $imageSourceSites = self::getCollectedImageSourceSites();
        $currentUrl = self::resolveCollectedImageUrl($sourceUrl);
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        for ($hop = 0; $hop < 5; $hop++) {
            $urlParts = parse_url($currentUrl);
            $scheme = strtolower((string)($urlParts['scheme'] ?? ''));
            $host = preg_replace('/^www\./', '', strtolower((string)($urlParts['host'] ?? '')));
            if (!in_array($scheme, ['http', 'https'], true) || !isset($imageSourceSites[$host])) {
                throw new \InvalidArgumentException('허용되지 않은 원본 이미지 도메인입니다.');
            }

            $curl = curl_init($currentUrl);
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_HEADER => true,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; A1ProductCollector/1.0)',
                CURLOPT_REFERER => $imageSourceSites[$host],
            ]);
            $response = curl_exec($curl);
            $httpCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $headerSize = (int)curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $contentType = strtolower(trim(explode(';', (string)curl_getinfo($curl, CURLINFO_CONTENT_TYPE))[0]));
            curl_close($curl);

            if (!is_string($response)) {
                throw new \RuntimeException('원본 이미지를 다운로드하지 못했습니다.');
            }

            $headers = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);

            if ($httpCode >= 300 && $httpCode < 400) {
                if (!preg_match('/^location:\s*(.+)$/im', $headers, $locationMatch)) {
                    throw new \RuntimeException('원본 이미지를 다운로드하지 못했습니다.');
                }
                $currentUrl = self::resolveCollectedImageRedirectUrl($currentUrl, trim($locationMatch[1]));
                continue;
            }

            if ($httpCode < 200 || $httpCode >= 300) {
                throw new \RuntimeException('원본 이미지를 다운로드하지 못했습니다.');
            }
            if (!in_array($contentType, $allowedMimeTypes, true) || strlen($body) > 10 * 1024 * 1024) {
                throw new \RuntimeException('허용되지 않은 이미지 형식 또는 크기입니다.');
            }

            return ['body' => $body, 'content_type' => $contentType];
        }

        throw new \RuntimeException('원본 이미지를 다운로드하지 못했습니다.');
    }

    private static function resolveCollectedImageRedirectUrl(string $currentUrl, string $location): string
    {
        $location = trim($location);
        if (preg_match('#^https?://#i', $location)) {
            return $location;
        }

        $parts = parse_url($currentUrl);
        $base = strtolower((string)($parts['scheme'] ?? 'https')) . '://' . (string)($parts['host'] ?? '');
        if (!empty($parts['port'])) {
            $base .= ':' . $parts['port'];
        }
        if ($location === '') {
            return $currentUrl;
        }
        if ($location[0] === '/') {
            return $base . $location;
        }

        $path = (string)($parts['path'] ?? '/');
        $dir = preg_replace('#/[^/]*$#', '/', $path) ?: '/';
        return $base . $dir . $location;
    }

    private function downloadSourceImage(string $sourceUrl): array
    {
        return self::downloadCollectedSourceImage($sourceUrl, 30);
    }

    private function connect(array $config): array
    {
        $protocol = strtolower((string)$config['protocol']);
        if ($protocol === 'sftp') {
            if (!function_exists('ssh2_connect')) {
                throw new \RuntimeException('SFTP 업로드에는 PHP ssh2 확장이 필요합니다.');
            }
            $connection = @call_user_func('ssh2_connect', $config['host'], (int)$config['port']);
            if (!$connection || !@call_user_func('ssh2_auth_password', $connection, $config['username'], $config['password'])) {
                throw new \RuntimeException('이미지 호스팅 SFTP 인증에 실패했습니다.');
            }
            $sftp = call_user_func('ssh2_sftp', $connection);
            if (!$sftp) {
                throw new \RuntimeException('SFTP 세션을 만들 수 없습니다.');
            }
            return ['protocol' => 'sftp', 'connection' => $connection, 'sftp' => $sftp];
        }

        $isFtps = $protocol === 'ftps';
        $connection = $isFtps
            ? (function_exists('ftp_ssl_connect') ? @ftp_ssl_connect($config['host'], (int)$config['port'], 20) : false)
            : @ftp_connect($config['host'], (int)$config['port'], 20);
        if (!$connection || !@ftp_login($connection, $config['username'], $config['password'])) {
            throw new \RuntimeException('이미지 호스팅 FTP 인증에 실패했습니다.');
        }
        ftp_pasv($connection, true);
        return ['protocol' => $protocol, 'connection' => $connection];
    }

    private function uploadBinary(array $connection, string $remotePath, string $binary): void
    {
        $directory = dirname($remotePath);
        if ($connection['protocol'] === 'sftp') {
            $this->createSftpDirectories($connection['sftp'], $directory);
            $remoteStream = @fopen('ssh2.sftp://' . $connection['sftp'] . $remotePath, 'w');
            if (!$remoteStream || fwrite($remoteStream, $binary) === false) {
                throw new \RuntimeException('SFTP 이미지 업로드에 실패했습니다.');
            }
            fclose($remoteStream);
            return;
        }

        $this->createFtpDirectories($connection['connection'], $directory);
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $binary);
        rewind($stream);
        $uploaded = ftp_fput($connection['connection'], $remotePath, $stream, FTP_BINARY);
        fclose($stream);
        if (!$uploaded) {
            throw new \RuntimeException('FTP 이미지 업로드에 실패했습니다.');
        }
    }

    private function listRemoteFiles(array $connection, string $remoteDir): array
    {
        $remoteDir = rtrim($remoteDir, '/') . '/';
        if (($connection['protocol'] ?? '') === 'sftp') {
            $sftp = $connection['sftp'] ?? null;
            if (!$sftp) {
                throw new \RuntimeException('SFTP 세션이 없습니다.');
            }
            $handle = @opendir('ssh2.sftp://' . intval($sftp) . $remoteDir);
            if (!$handle) {
                throw new \RuntimeException('이미지 저장소 폴더를 열 수 없습니다.');
            }
            $names = [];
            while (($entry = readdir($handle)) !== false) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
                $names[] = $entry;
            }
            closedir($handle);
            return $names;
        }

        $ftp = $connection['connection'] ?? null;
        if (!$ftp) {
            throw new \RuntimeException('FTP 연결이 없습니다.');
        }

        $list = @ftp_nlist($ftp, $remoteDir);
        if ($list === false) {
            if (!@ftp_chdir($ftp, rtrim($remoteDir, '/'))) {
                throw new \RuntimeException('이미지 저장소 폴더를 찾을 수 없습니다. 경로를 확인해 주세요.');
            }
            $list = @ftp_nlist($ftp, '.');
            if ($list === false) {
                return [];
            }
        }

        $names = [];
        foreach ($list as $item) {
            $name = basename(str_replace('\\', '/', (string)$item));
            if ($name === '' || $name === '.' || $name === '..') {
                continue;
            }
            $names[] = $name;
        }

        return array_values(array_unique($names));
    }

    /**
     * 원격 이미지의 파일크기와 가로/세로를 읽는다.
     *
     * @return array{file_size:int,width:int,height:int}
     */
    private function inspectRemoteImage(array $connection, string $remotePath, string $publicUrl = ''): array
    {
        $fileSize = $this->getRemoteFileSize($connection, $remotePath);
        $binary = $this->downloadImageHead($publicUrl, $connection, $remotePath, $fileSize);
        $width = 0;
        $height = 0;
        if ($binary !== '') {
            $info = @getimagesizefromstring($binary);
            if (is_array($info)) {
                $width = (int)($info[0] ?? 0);
                $height = (int)($info[1] ?? 0);
            }
            if ($fileSize <= 0) {
                $fileSize = strlen($binary);
            }
        }

        return [
            'file_size' => $fileSize,
            'width' => $width,
            'height' => $height,
        ];
    }

    private function getRemoteFileSize(array $connection, string $remotePath): int
    {
        if (($connection['protocol'] ?? '') === 'sftp') {
            $sftp = $connection['sftp'] ?? null;
            if (!$sftp) {
                return 0;
            }
            $stat = @stat('ssh2.sftp://' . intval($sftp) . $remotePath);
            return (int)($stat['size'] ?? 0);
        }

        $ftp = $connection['connection'] ?? null;
        if (!$ftp) {
            return 0;
        }
        $size = @ftp_size($ftp, $remotePath);

        return (is_int($size) && $size > 0) ? $size : 0;
    }

    private function downloadImageHead(string $publicUrl, array $connection, string $remotePath, int &$fileSize): string
    {
        $headLimit = 512 * 1024;
        if ($publicUrl !== '') {
            $binary = $this->downloadPublicHead($publicUrl, $headLimit, $fileSize);
            if ($binary !== '') {
                return $binary;
            }
        }

        return $this->downloadRemoteBinary($connection, $remotePath, $fileSize, $headLimit);
    }

    private function downloadPublicHead(string $publicUrl, int $maxBytes, int &$fileSize): string
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'Range: bytes=0-' . max(0, $maxBytes - 1) . "\r\n",
                'timeout' => 12,
                'follow_location' => 1,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);
        $binary = @file_get_contents($publicUrl, false, $context);
        if (!is_string($binary) || $binary === '') {
            return '';
        }
        if (isset($http_response_header[0]) && preg_match('/\s[45]\d\d\s/', (string)$http_response_header[0])) {
            return '';
        }
        if ($fileSize <= 0 && isset($http_response_header) && is_array($http_response_header)) {
            foreach ($http_response_header as $header) {
                if (preg_match('/^Content-Range:\s*bytes\s+\d+-\d+\/(\d+)/i', $header, $matches)) {
                    $fileSize = (int)$matches[1];
                    break;
                }
                if (preg_match('/^Content-Length:\s*(\d+)/i', $header, $matches) && $fileSize <= 0) {
                    $fileSize = (int)$matches[1];
                }
            }
        }
        if (strlen($binary) > $maxBytes) {
            return substr($binary, 0, $maxBytes);
        }

        return $binary;
    }

    private function downloadRemoteBinary(array $connection, string $remotePath, int &$fileSize = 0, int $maxBytes = 10485760): string
    {
        if (($connection['protocol'] ?? '') === 'sftp') {
            $sftp = $connection['sftp'] ?? null;
            if (!$sftp) {
                return '';
            }
            $url = 'ssh2.sftp://' . intval($sftp) . $remotePath;
            if ($fileSize <= 0) {
                $stat = @stat($url);
                $fileSize = (int)($stat['size'] ?? 0);
            }
            $handle = @fopen($url, 'rb');
            if (!$handle) {
                return '';
            }
            $binary = stream_get_contents($handle, $maxBytes);
            fclose($handle);
            return is_string($binary) ? $binary : '';
        }

        $ftp = $connection['connection'] ?? null;
        if (!$ftp) {
            return '';
        }
        if ($fileSize <= 0) {
            $size = @ftp_size($ftp, $remotePath);
            if (is_int($size) && $size > 0) {
                $fileSize = $size;
            }
        }
        $stream = fopen('php://temp', 'r+');
        if (!$stream) {
            return '';
        }
        $ok = @ftp_fget($ftp, $stream, $remotePath, FTP_BINARY);
        if (!$ok) {
            fclose($stream);
            return '';
        }
        rewind($stream);
        $binary = stream_get_contents($stream, $maxBytes);
        fclose($stream);
        if (!is_string($binary) || $binary === '') {
            return '';
        }
        if ($fileSize <= 0) {
            $fileSize = strlen($binary);
        }

        return $binary;
    }

    private function createFtpDirectories($connection, string $directory): void
    {
        $path = '';
        foreach (array_filter(explode('/', trim($directory, '/'))) as $part) {
            $path .= '/' . $part;
            @ftp_mkdir($connection, $path);
        }
    }

    private function createSftpDirectories($sftp, string $directory): void
    {
        $path = '';
        foreach (array_filter(explode('/', trim($directory, '/'))) as $part) {
            $path .= '/' . $part;
            @call_user_func('ssh2_sftp_mkdir', $sftp, $path, 0755, true);
        }
    }

    private function disconnect(array $connection): void
    {
        if (in_array($connection['protocol'], ['ftp', 'ftps'], true) && !empty($connection['connection'])) {
            ftp_close($connection['connection']);
        }
    }

    private function validateConfig(array $config): void
    {
        foreach (['protocol', 'host', 'port', 'username', 'password', 'remote_base_path', 'public_base_url'] as $key) {
            if (empty($config[$key]) || $config[$key] === 'CHANGE_ME') {
                throw new \RuntimeException('이미지 호스팅 설정값 ' . $key . '을(를) 확인해 주세요.');
            }
        }
        if (!in_array(strtolower((string)$config['protocol']), ['sftp', 'ftps', 'ftp'], true)) {
            throw new \InvalidArgumentException('이미지 호스팅 protocol은 sftp, ftps, ftp 중 하나여야 합니다.');
        }
    }

    /**
     * 이미지 호스팅 자격증명은 application/config에 별도 보관한다.
     * 공용 config() 헬퍼는 프로젝트 루트 config만 읽으므로 여기서 직접 로드한다.
     */
    private function getConfig(): array
    {
        $configPath = __DIR__ . '/../config/image_hosting.php';
        if (!is_file($configPath)) {
            throw new \RuntimeException('이미지 호스팅 설정 파일을 찾을 수 없습니다.');
        }

        $config = require $configPath;
        if (!is_array($config)) {
            throw new \RuntimeException('이미지 호스팅 설정 형식이 올바르지 않습니다.');
        }

        return $config;
    }

    private function normalizeStoragePath(string $storagePath): string
    {
        $storagePath = preg_replace('#/+#', '/', trim($storagePath));
        if ($storagePath === '' || $storagePath[0] !== '/') {
            throw new \InvalidArgumentException('이미지 저장소 경로는 / 로 시작해야 합니다.');
        }
        if (substr($storagePath, -1) !== '/') {
            $storagePath .= '/';
        }
        if (strpos($storagePath, '..') !== false || !preg_match('#^/[A-Za-z0-9._-]+(?:/[A-Za-z0-9._-]+)*/$#', $storagePath)) {
            throw new \InvalidArgumentException('이미지 저장소 경로 형식이 올바르지 않습니다.');
        }

        return $storagePath;
    }
}
