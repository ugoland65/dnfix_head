<?php

namespace App\Controllers\Api;

use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Services\ProductDetailContentService;

class ProductDetailContentApi extends BaseClass
{
    /**
     * 고도몰이 상품 컨텐츠를 받아 DB에 저장하기 위한 조회 API
     *
     * GET/POST /api2/product/detail-content
     * - godo_code (또는 goodsNo)
     * - deploy_version
     * - deploy_version_code
     * - target=bottom 이거나 배포코드가 PDB- 이면 하단 컨텐츠
     * - 하단 data: bottom_html, bottom_items, bottom_position, bottom_deploy_version, bottom_deploy_version_code
     *   (html / goodsBottomDescription 은 같은 goods2-info-block HTML)
     *   bottom_position: top=본문상단, bottom=본문하단
     * - X-Api-Key 또는 api_key
     */
    public function detailContentApi(Request $request, ProductDetailContentService $service)
    {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');

        try {
            if (!$this->isValidApiKey($request)) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'UNAUTHORIZED',
                    'message' => '유효한 API 키가 필요합니다.',
                ], 401);
            }

            $criteria = [
                'godo_code' => $this->readParam($request, ['godo_code', 'goodsNo', 'goods_no']),
                'deploy_version' => $this->readParam($request, ['deploy_version', 'version']),
                'deploy_version_code' => $this->readParam($request, ['deploy_version_code', 'deploy_code', 'version_code']),
                'target' => $this->readParam($request, ['target', 'scope', 'content_type']),
            ];
            $result = $this->isBottomTarget($criteria)
                ? $service->getGodoBottomSyncPayload($criteria)
                : $service->getGodoSyncPayload($criteria);

            $status = (int)($result['status'] ?? 500);
            $payload = [
                'success' => !empty($result['ok']),
                'message' => (string)($result['message'] ?? ''),
            ];
            if (!empty($result['error_code'])) {
                $payload['error_code'] = (string)$result['error_code'];
            }
            if (!empty($result['data']) && is_array($result['data'])) {
                $payload['data'] = $result['data'];
            }
            if (!empty($result['current']) && is_array($result['current'])) {
                $payload['current'] = $result['current'];
            }
            $payload['server_time'] = date('Y-m-d\TH:i:sP');

            return response()->json($payload, $status > 0 ? $status : 500);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'error_code' => 'SERVER_ERROR',
                'message' => '서버 요청 처리 중 오류가 발생했습니다.',
            ], 500);
        }
    }

    private function isBottomTarget(array $criteria): bool
    {
        $target = strtolower(trim((string)($criteria['target'] ?? '')));
        if (in_array($target, ['bottom', 'pdb', 'goods2', 'goods2-info'], true)) {
            return true;
        }
        $versionCode = strtoupper(trim((string)($criteria['deploy_version_code'] ?? '')));
        return strpos($versionCode, 'PDB-') === 0;
    }

    private function isValidApiKey(Request $request): bool
    {
        $config = config('admin.godo_api');
        $expected = is_array($config) ? trim((string)($config['inbound_api_key'] ?? '')) : '';
        if ($expected === '') {
            return false;
        }

        $provided = $this->readApiKey($request);
        return $provided !== '' && hash_equals($expected, $provided);
    }

    private function readApiKey(Request $request): string
    {
        $header = $this->readHeader('X-Api-Key');
        if ($header !== '') {
            return $header;
        }

        $authorization = $this->readHeader('Authorization');
        if (stripos($authorization, 'Bearer ') === 0) {
            return trim(substr($authorization, 7));
        }

        return trim((string)$request->input('api_key', '', FILTER_DEFAULT));
    }

    private function readParam(Request $request, array $keys): string
    {
        foreach ($keys as $key) {
            $value = trim((string)$request->input($key, '', FILTER_DEFAULT));
            if ($value !== '') {
                return $value;
            }
        }
        return '';
    }

    private function readHeader(string $name): string
    {
        $headers = [];
        if (function_exists('getallheaders')) {
            $raw = getallheaders();
            if (is_array($raw)) {
                $headers = $raw;
            }
        }

        foreach ($headers as $key => $value) {
            if (strcasecmp((string)$key, $name) === 0) {
                return trim((string)$value);
            }
        }

        $serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return trim((string)($_SERVER[$serverKey] ?? ''));
    }
}
