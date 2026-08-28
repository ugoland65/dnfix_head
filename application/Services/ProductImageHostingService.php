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
        foreach (array_values($sourceImageUrls) as $index => $sourceImageUrl) {
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
            $filename = sprintf('%s_%02d.%s', $siteCode, $index + 1, $extension);
            $uploads[] = [
                'sort_no' => $index + 1,
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

    private function downloadSourceImage(string $sourceUrl): array
    {
        $urlParts = parse_url($sourceUrl);
        $host = preg_replace('/^www\./', '', strtolower((string)($urlParts['host'] ?? '')));
        $imageSourceSites = [
            'nipporigift.net' => 'http://www.nipporigift.net/',
            'tamatoys.tma.co.jp' => 'https://tamatoys.tma.co.jp/',
            'prod-tamatoys.s3.amazonaws.com' => 'https://tamatoys.tma.co.jp/',
            'mzakka.com' => 'https://mzakka.com/',
            'i.mzakka.com' => 'https://mzakka.com/',
            'img07.shop-pro.jp' => 'https://www.nobunaga-toys.com/',
            'e-nls.com' => 'https://www.e-nls.com/',
            'image.e-nls.com' => 'https://www.e-nls.com/',
        ];
        if (!isset($imageSourceSites[$host])) {
            throw new \InvalidArgumentException('허용되지 않은 원본 이미지 도메인입니다.');
        }

        $curl = curl_init($sourceUrl);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; A1ProductCollector/1.0)',
            CURLOPT_REFERER => $imageSourceSites[$host],
        ]);
        $body = curl_exec($curl);
        $httpCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $contentType = strtolower(trim(explode(';', (string)curl_getinfo($curl, CURLINFO_CONTENT_TYPE))[0]));
        curl_close($curl);

        if (!is_string($body) || $httpCode < 200 || $httpCode >= 300) {
            throw new \RuntimeException('원본 이미지를 다운로드하지 못했습니다.');
        }
        if (!in_array($contentType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true) || strlen($body) > 10 * 1024 * 1024) {
            throw new \RuntimeException('허용되지 않은 이미지 형식 또는 크기입니다.');
        }

        return ['body' => $body, 'content_type' => $contentType];
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
