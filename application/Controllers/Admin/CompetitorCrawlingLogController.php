<?php

namespace App\Controllers\Admin;

use App\Classes\Request;
use App\Core\BaseClass;
use App\Services\ProductPartnerApiService;

class CompetitorCrawlingLogController extends BaseClass
{
    /**
     * 크롤러 실행 결과 로그 목록.
     */
    public function index(Request $request)
    {
        try {
            $requestData = $request->all();
            $page = max(1, (int)($requestData['page'] ?? 1));
            $limit = min(500, max(20, (int)($requestData['limit'] ?? 100)));
            $filters = [
                'kind' => trim((string)($requestData['kind'] ?? '')),
                'site_code' => trim((string)($requestData['site_code'] ?? '')),
                'site_name' => trim((string)($requestData['site_name'] ?? '')),
                'run_type' => trim((string)($requestData['run_type'] ?? '')),
                'status' => trim((string)($requestData['status'] ?? '')),
                'started_from' => str_replace('T', ' ', trim((string)($requestData['started_from'] ?? ''))),
                'started_to' => str_replace('T', ' ', trim((string)($requestData['started_to'] ?? ''))),
            ];
            $criteria = array_filter($filters, static function ($value) {
                return $value !== '';
            });
            $criteria += [
                'page' => $page,
                'limit' => $limit,
            ];

            $apiData = (new ProductPartnerApiService())->getCrawlRunLogs($criteria);
            $data = $apiData['data'] ?? [];
            $logs = $data['crawl_run_logs'] ?? [];
            if (!is_array($logs)) {
                $logs = [];
            }

            $pagination = is_array($data['pagination'] ?? null) ? $data['pagination'] : [];
            $total = (int)($pagination['total'] ?? count($logs));

            return view('admin.competitor.crawling_log', [
                'logs' => $logs,
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'filters' => $filters,
                'hasNextPage' => !empty($pagination['has_next_page']),
                'hasPrevPage' => !empty($pagination['has_prev_page']),
            ])->extends('admin.layout.layout', [
                'pageGroup2' => 'provider',
                'pageNameCode' => 'competitor_crawling_log',
            ]);
        } catch (\Throwable $e) {
            return view('admin.competitor.crawling_log', [
                'logs' => [],
                'page' => 1,
                'limit' => 100,
                'total' => 0,
                'filters' => [],
                'hasNextPage' => false,
                'hasPrevPage' => false,
                'apiError' => $e->getMessage(),
            ])->extends('admin.layout.layout', [
                'pageGroup2' => 'provider',
                'pageNameCode' => 'competitor_crawling_log',
            ]);
        }
    }
}
