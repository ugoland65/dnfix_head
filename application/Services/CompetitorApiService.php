<?php
namespace App\Services;

use App\Utils\HttpClient;

class CompetitorApiService
{
    private $domain = 'https://dnetc01.mycafe24.com';
    private $apiKey = 'DNP_2024_SUPPLIER_API_KEY_v1_8f9e2c7b4a1d6e3f';

    /**
     * 경쟁사 상품 목록 조회
     *
     * @param array $params
     * @return array
     */
    public function getCompetitorProducts(array $params): array
    {
        $query = [
            'sort_mode' => (string)($params['sort_mode'] ?? 'code'),
            'page' => (int)($params['page'] ?? 1),
            'limit' => (int)($params['limit'] ?? 100),
        ];
        $site = trim((string)($params['site'] ?? ''));
        $status = trim((string)($params['status'] ?? ''));
        $matchStatus = trim((string)($params['match_status'] ?? ''));
        $keyword = trim((string)($params['keyword'] ?? ''));
        $matchIdx = (int)($params['match_idx'] ?? 0);

        if ($site !== '') {
            $query['site'] = $site;
        }
        if ($status !== '') {
            $query['status'] = $status;
        }
        if ($matchStatus !== '') {
            $query['match_status'] = $matchStatus;
        }
        if ($keyword !== '') {
            $query['keyword'] = $keyword;
        }
        if ($matchIdx > 0) {
            $query['match_idx'] = $matchIdx;
        }

        $url = $this->domain . '/api/CompetitorProduct?' . http_build_query($query);
        $headers = [
            'Content-Type: application/json',
            'X-API-KEY: ' . $this->apiKey,
        ];

        $response = HttpClient::getData($url, $headers);
        $decoded = json_decode($response, true);

        return is_array($decoded) ? $decoded : [];
    }
}
