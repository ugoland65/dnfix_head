<?php

namespace App\Services;

use App\Core\AuthAdmin;
use App\Models\AdminModel;
use App\Models\ProductModel;
use App\Models\ProductPartnerModel;
use App\Models\ProductStockModel;
use App\Models\ProductSaleHistoryModel;

class SaleHistoryService
{
    private $saleStatusText = [
        'wait' => '대기',
        'start' => '진행',
        'end' => '종료',
        'upload' => '업로드',
    ];

    private $saleModeText = [
        'day' => '일일할인',
        'period' => '기간할인',
        'week' => '주간할인',
        'month' => '월간할인',
        'event' => '기획전',
    ];

    /**
     * 할인 이력 목록 조회
     *
     * @param array $criteria
     * @return array
     */
    public function getSaleHistoryList(array $criteria): array
    {
        $saleStatus = trim((string)($criteria['sale_status'] ?? ''));
        $saleMode = trim((string)($criteria['sale_mode'] ?? ''));
        $paging = (bool)($criteria['paging'] ?? true);
        $perPage = (int)($criteria['per_page'] ?? 100);
        $page = (int)($criteria['page'] ?? 1);

        $isAllValue = static function ($value): bool {
            $normalized = trim((string)$value);
            $normalizedLower = strtolower($normalized);
            return $normalized === '' || $normalizedLower === 'all' || $normalized === '전체';
        };

        $query = ProductSaleHistoryModel::query()
            ->when(!$isAllValue($saleStatus), function ($query) use ($saleStatus) {
                $query->where('sale_status', $saleStatus);
            })
            ->when(!$isAllValue($saleMode), function ($query) use ($saleMode) {
                $query->where('sale_mode', $saleMode);
            })
            ->orderBy('seq', 'desc');

        $result = $paging
            ? $query->paginate($perPage, $page)
            : ['data' => $query->get()->toArray()];

        $rows = $result['data'] ?? [];
        $adminMap = $this->getAdminMapFromRows($rows);

        foreach ($rows as &$row) {
            $row['sale_status_text'] = $this->saleStatusText[(string)($row['sale_status'] ?? '')] ?? (string)($row['sale_status'] ?? '');
            $row['sale_mode_text'] = $this->saleModeText[(string)($row['sale_mode'] ?? '')] ?? (string)($row['sale_mode'] ?? '');
            $row['product_count'] = $this->countProducts($row['product_json'] ?? '[]');
            $row['created_by_name'] = $adminMap[(int)($row['created_by'] ?? 0)] ?? '-';
            $row['sale_period_text'] = $this->buildSalePeriodText(
                (string)($row['sale_start_date'] ?? ''),
                (string)($row['sale_end_date'] ?? '')
            );
            $row['created_at_text'] = $this->formatDateTime($row['created_at'] ?? '');
        }
        unset($row);

        $result['data'] = $rows;
        if (!$paging) {
            $result['total'] = count($rows);
            $result['per_page'] = count($rows);
            $result['current_page'] = 1;
        }

        return $result;
    }

    /**
     * 할인 이력 상세 조회
     *
     * @param int $seq
     * @return array
     */
    public function getSaleHistoryDetail(int $seq): array
    {
        if ($seq <= 0) {
            throw new \InvalidArgumentException('유효하지 않은 할인 이력 번호입니다.');
        }

        $row = ProductSaleHistoryModel::find($seq);
        $saleHistory = $row ? $row->toArray() : [];
        if (empty($saleHistory)) {
            throw new \RuntimeException('할인 이력을 찾을 수 없습니다.');
        }

        $adminMap = $this->getAdminMapFromRows([$saleHistory]);
        $metaGroupStatusMap = $this->getGodoGroupUploadStatusMapFromMeta($saleHistory['meta_json'] ?? []);
        foreach ($metaGroupStatusMap as $statusRow) {
            $uploadedByIdx = (int)($statusRow['uploaded_by'] ?? 0);
            if ($uploadedByIdx > 0 && !isset($adminMap[$uploadedByIdx])) {
                $adminRow = AdminModel::query()
                    ->select(['idx', 'ad_name', 'ad_id'])
                    ->where('idx', $uploadedByIdx)
                    ->first();
                if ($adminRow) {
                    $adminRow = is_array($adminRow) ? $adminRow : $adminRow->toArray();
                    $adminMap[$uploadedByIdx] = (string)($adminRow['ad_name'] ?? ($adminRow['ad_id'] ?? '-'));
                }
            }
        }
        $saleHistory['sale_status_text'] = $this->saleStatusText[(string)($saleHistory['sale_status'] ?? '')] ?? (string)($saleHistory['sale_status'] ?? '');
        $saleHistory['sale_mode_text'] = $this->saleModeText[(string)($saleHistory['sale_mode'] ?? '')] ?? (string)($saleHistory['sale_mode'] ?? '');
        $decodedProductList = $this->decodeProductJson($saleHistory['product_json'] ?? '[]');
        $saleHistory['product_list'] = $this->normalizeSaleHistoryProductList($decodedProductList);
        $saleHistory['product_list'] = $this->hydrateSaleHistoryProductListFromDb($saleHistory['product_list']);
        $saleHistory['product_count'] = count($saleHistory['product_list']);
        $saleHistory['created_by_name'] = $adminMap[(int)($saleHistory['created_by'] ?? 0)] ?? '-';
        $saleHistory['uploaded_by_name'] = $adminMap[(int)($saleHistory['uploaded_by'] ?? 0)] ?? '-';
        if (!empty($metaGroupStatusMap)) {
            foreach ($metaGroupStatusMap as $rateKey => $statusRow) {
                $uploadedByIdx = (int)($statusRow['uploaded_by'] ?? 0);
                if ($uploadedByIdx > 0) {
                    $metaGroupStatusMap[$rateKey]['uploaded_by_name'] = $adminMap[$uploadedByIdx] ?? (string)($statusRow['uploaded_by_name'] ?? '-');
                }
            }
            $meta = $this->decodeMetaJsonToArray($saleHistory['meta_json'] ?? []);
            $meta['godo_group_upload_status'] = $metaGroupStatusMap;
            $saleHistory['meta_json'] = $meta;
        }

        return $saleHistory;
    }

    
    /**
     * 할인할 상품 랜덤 추출
     * 
     * @param array $requestData
     * @return array
     */
    public function loadRandomProduct(array $requestData): array
    {
        $totalQtyInput = (int)($requestData['total_product_qty'] ?? 24);
        $haveQty = (int)($requestData['have_product_qty'] ?? 0);
        $providerQty = (int)($requestData['provider_product_qty'] ?? 0);

        if ($haveQty < 0) {
            $haveQty = 0;
        }
        if ($providerQty < 0) {
            $providerQty = 0;
        }
        if ($haveQty <= 0 && $providerQty <= 0) {
            $haveQty = $totalQtyInput > 0 ? $totalQtyInput : 1;
        }

        // 과도한 랜덤 추출 요청으로 쿼리 부하가 생기지 않도록 상한을 둔다.
        $maxTotalQty = 500;
        $requestedTotalQty = $haveQty + $providerQty;
        if ($requestedTotalQty > $maxTotalQty) {
            if ($haveQty > $maxTotalQty) {
                $haveQty = $maxTotalQty;
                $providerQty = 0;
            } else {
                $providerQty = max(0, $maxTotalQty - $haveQty);
            }
        }

        $totalQty = $haveQty + $providerQty;
        if ($totalQty <= 0) {
            $totalQty = 1;
            $haveQty = 1;
            $providerQty = 0;
        }

        // 후보군 산정 수량(단건 교체에서도 전체 추출과 동일한 후보 풀을 쓰기 위해 분리)
        $candidateHaveQty = (int)($requestData['candidate_have_product_qty'] ?? $haveQty);
        $candidateProviderQty = (int)($requestData['candidate_provider_product_qty'] ?? $providerQty);
        if ($candidateHaveQty < 0) {
            $candidateHaveQty = 0;
        }
        if ($candidateProviderQty < 0) {
            $candidateProviderQty = 0;
        }
        $candidateRequestedTotal = $candidateHaveQty + $candidateProviderQty;
        if ($candidateRequestedTotal > $maxTotalQty) {
            if ($candidateHaveQty > $maxTotalQty) {
                $candidateHaveQty = $maxTotalQty;
                $candidateProviderQty = 0;
            } else {
                $candidateProviderQty = max(0, $maxTotalQty - $candidateHaveQty);
            }
        }
        if ($candidateHaveQty <= 0 && $haveQty > 0) {
            $candidateHaveQty = $haveQty;
        }
        if ($candidateProviderQty <= 0 && $providerQty > 0) {
            $candidateProviderQty = $providerQty;
        }

        $marginPerMin = (float)($requestData['have_product_margin_per'] ?? ($requestData['margin_per_min'] ?? 20));
        if ($marginPerMin < 0) {
            $marginPerMin = 0;
        }
        $providerMarginPerMin = (float)($requestData['provider_product_margin_per'] ?? $marginPerMin);
        if ($providerMarginPerMin < 0) {
            $providerMarginPerMin = 0;
        }
        $minStock = (int)($requestData['have_product_min_stock'] ?? 3);
        if ($minStock < 1) {
            $minStock = 1;
        }

        $saleDuplicateMode = trim((string)($requestData['sale_duplicate_mode'] ?? '3week'));
        $saleDateDays = 21;
        if ($saleDuplicateMode === '1month') {
            $saleDateDays = 30;
        } elseif ($saleDuplicateMode === '2week') {
            $saleDateDays = 14;
        }
        $saleDateThreshold = date('Y-m-d', strtotime('-' . $saleDateDays . ' days'));
        $excludeBrandIdxs = $requestData['exclude_brand_idxs'] ?? [];
        if (!is_array($excludeBrandIdxs)) {
            $excludeBrandIdxs = [$excludeBrandIdxs];
        }
        $excludeBrandIdxs = array_values(array_unique(array_filter(array_map('intval', $excludeBrandIdxs), static function ($v) {
            return $v > 0;
        })));
        $selectedKindCodes = $requestData['selected_kind_codes'] ?? [];
        if (!is_array($selectedKindCodes)) {
            $selectedKindCodes = [$selectedKindCodes];
        }
        $selectedKindCodes = array_values(array_unique(array_filter(array_map(static function ($v) {
            return trim((string)$v);
        }, $selectedKindCodes), static function ($v) {
            return $v !== '';
        })));

        if (empty($selectedKindCodes)) {
            return [
                'total_count' => 0,
                'selected_count' => 0,
                'items' => [],
                'condition' => [
                    'total_product_qty' => $totalQty,
                    'have_product_qty' => $haveQty,
                    'provider_product_qty' => $providerQty,
                    'min_stock' => $minStock,
                    'sale_duplicate_mode' => $saleDuplicateMode,
                    'exclude_brand_idxs' => $excludeBrandIdxs,
                    'selected_kind_codes' => [],
                    'sale_date_before_or_equal' => $saleDateThreshold,
                    'margin_per_min' => $marginPerMin,
                    'provider_margin_per_min' => $providerMarginPerMin,
                ],
            ];
        }
        $providerKindFilters = $this->buildProviderKindFilterValues($selectedKindCodes);

        $haveCandidateRows = [];
        if ($haveQty > 0) {
            $candidateLimit = max($candidateHaveQty * 10, 200);
            if ($candidateLimit > 5000) {
                $candidateLimit = 5000;
            }

            $haveCandidateRows = ProductStockModel::query()
                ->from('prd_stock AS S')
                ->join('COMPARISON_DB AS P', 'P.CD_IDX', '=', 'S.ps_prd_idx')
                ->leftJoin('BRAND_DB AS B', 'B.BD_IDX', '=', 'P.CD_BRAND_IDX')
                ->select([
                    'S.ps_idx',
                    'S.ps_prd_idx',
                    'S.ps_stock',
                    'S.ps_sale_date',
                    'S.created_at AS ps_created_at',
                    'P.CD_NAME',
                    'P.CD_IMG',
                    'P.CD_KIND_CODE',
                    'P.CD_BRAND_IDX',
                    'B.BD_NAME AS brand_name',
                    'P.cd_godo_code',
                    'P.cd_sale_price',
                    'P.cd_cost_price',
                ])
                ->where('S.ps_stock', '>=', $minStock)
                ->whereRaw("(S.ps_discount_target_yn IS NULL OR S.ps_discount_target_yn = '' OR S.ps_discount_target_yn = 'Y')")
                ->whereRaw('P.cd_sale_price > 0')
                ->whereIn('P.CD_KIND_CODE', $selectedKindCodes)
                ->when(!empty($excludeBrandIdxs), function ($query) use ($excludeBrandIdxs) {
                    $query->whereNotIn('P.CD_BRAND_IDX', $excludeBrandIdxs);
                })
                ->whereRaw("(
                    S.ps_sale_date IS NULL
                    OR S.ps_sale_date = ''
                    OR S.ps_sale_date = '0000-00-00'
                    OR S.ps_sale_date <= '{$saleDateThreshold}'
                )")
                ->whereRaw('((P.cd_sale_price - P.cd_cost_price) / P.cd_sale_price) * 100 >= ' . $marginPerMin)
                // 마지막 할인일이 없으면 가장 먼저, 그 다음 오래된 순
                ->orderByRaw("
                    CASE 
                        WHEN S.ps_sale_date IS NULL OR S.ps_sale_date = '' OR S.ps_sale_date = '0000-00-00' THEN 0
                        ELSE 1
                    END ASC
                ")
                // 재고가 많은 상품 우선
                ->orderBy('S.ps_stock', 'desc')
                ->orderBy('S.ps_sale_date', 'asc')
                // 생성일도 오래된 상품 우선
                ->orderBy('S.created_at', 'asc')
                ->limit($candidateLimit)
                ->get()
                ->toArray();
        }

        $providerCandidateRows = [];
        if ($providerQty > 0) {
            // 위탁상품 표본이 너무 작아지지 않도록 후보군 풀을 넉넉하게 확보
            $providerCandidateLimit = max($candidateProviderQty * 120, 2000);
            if ($providerCandidateLimit > 5000) {
                $providerCandidateLimit = 5000;
            }

            $providerCandidateRows = ProductPartnerModel::query()
                ->from('prd_partner AS PP')
                ->leftJoin('BRAND_DB AS B', 'B.BD_IDX', '=', 'PP.brand_idx')
                ->select([
                    'PP.idx',
                    'PP.name',
                    'PP.img_src',
                    'PP.godo_goodsNo',
                    'PP.supplier_prd_pk',
                    'PP.supplier_site',
                    'PP.supplier_2nd_name',
                    'PP.supplier_status',
                    'PP.kind',
                    'PP.brand_idx',
                    'B.BD_NAME AS brand_name',
                    'PP.sale_price',
                    'PP.order_price',
                    'PP.last_sale_date',
                    'PP.detail_crawler_date',
                    'PP.godo_loaded_at',
                    'PP.discount_target_yn',
                    'PP.created_at',
                ])
                ->whereRaw('PP.sale_price > 0')
                ->whereRaw('PP.order_price > 0')
                ->whereRaw("PP.godo_goodsNo IS NOT NULL AND PP.godo_goodsNo <> ''")
                ->whereRaw("(PP.discount_target_yn IS NULL OR PP.discount_target_yn = '' OR PP.discount_target_yn = 'Y')")
                ->whereIn('PP.kind', $providerKindFilters)
                ->whereRaw("(
                    PP.last_sale_date IS NULL
                    OR PP.last_sale_date = ''
                    OR PP.last_sale_date = '0000-00-00'
                    OR PP.last_sale_date <= '{$saleDateThreshold}'
                )")
                ->whereRaw('((PP.sale_price - PP.order_price) / PP.sale_price) * 100 >= ' . $providerMarginPerMin)
                ->orderByRaw("
                    CASE
                        WHEN PP.last_sale_date IS NULL OR PP.last_sale_date = '' OR PP.last_sale_date = '0000-00-00' THEN 0
                        ELSE 1
                    END ASC
                ")
                ->orderBy('PP.last_sale_date', 'asc')
                ->limit($providerCandidateLimit)
                ->get()
                ->toArray();
        }

        if (empty($haveCandidateRows) && empty($providerCandidateRows)) {
            return [
                'total_count' => 0,
                'have_candidate_count' => 0,
                'provider_candidate_count' => 0,
                'selected_count' => 0,
                'items' => [],
            ];
        }

        // 보유상품 우선순위 후보군에서 랜덤 추출
        shuffle($haveCandidateRows);
        $selectedHaveRows = array_slice($haveCandidateRows, 0, $haveQty);

        // 공급사상품 후보군에서 랜덤 추출
        shuffle($providerCandidateRows);
        $selectedProviderRows = array_slice($providerCandidateRows, 0, $providerQty);

        $items = [];
        foreach ($selectedHaveRows as $row) {
            $salePrice = (int)($row['cd_sale_price'] ?? 0);
            $costPrice = (int)($row['cd_cost_price'] ?? 0);
            $marginPer = 0.0;
            if ($salePrice > 0) {
                $marginPer = round((($salePrice - $costPrice) / $salePrice) * 100, 2);
            }

            $items[] = [
                'ps_idx' => (int)($row['ps_idx'] ?? 0),
                'item_source' => 'have',
                'item_key' => (string)($row['ps_idx'] ?? 0),
                'prd_idx' => (int)($row['ps_prd_idx'] ?? 0),
                'godo_goods_no' => $this->extractGodoGoodsNoFromHaveRow($row),
                'detail_crawler_date' => '',
                'godo_loaded_at' => '',
                'supplier_prd_pk' => '',
                'supplier_site' => '',
                'supplier_2nd_name' => '',
                'supplier_status' => '',
                'prd_name' => (string)($row['CD_NAME'] ?? ''),
                'img_path' => $this->buildProductImagePath((string)($row['CD_IMG'] ?? '')),
                'cd_kind_code' => (string)($row['CD_KIND_CODE'] ?? ''),
                'brand_name' => (string)($row['brand_name'] ?? ''),
                'stock_qty' => (int)($row['ps_stock'] ?? 0),
                'last_sale_date' => (string)($row['ps_sale_date'] ?? ''),
                'created_at' => (string)($row['ps_created_at'] ?? ''),
                'sale_price' => $salePrice,
                'cost_price' => $costPrice,
                'margin_per' => $marginPer,
            ];
        }

        foreach ($selectedProviderRows as $row) {
            $salePrice = (int)($row['sale_price'] ?? 0);
            $costPrice = (int)($row['order_price'] ?? 0);
            $marginPer = 0.0;
            if ($salePrice > 0) {
                $marginPer = round((($salePrice - $costPrice) / $salePrice) * 100, 2);
            }

            $providerIdx = (int)($row['idx'] ?? 0);
            $items[] = [
                'ps_idx' => $providerIdx,
                'item_source' => 'provider',
                'item_key' => 'provider_' . $providerIdx,
                'prd_idx' => 0,
                'godo_goods_no' => (string)($row['godo_goodsNo'] ?? ''),
                'supplier_prd_pk' => (string)($row['supplier_prd_pk'] ?? ''),
                'detail_crawler_date' => (string)($row['detail_crawler_date'] ?? ''),
                'godo_loaded_at' => (string)($row['godo_loaded_at'] ?? ''),
                'supplier_site' => (string)($row['supplier_site'] ?? ''),
                'supplier_2nd_name' => (string)($row['supplier_2nd_name'] ?? ''),
                'supplier_status' => (string)($row['supplier_status'] ?? ''),
                'prd_name' => (string)($row['name'] ?? ''),
                'img_path' => (string)($row['img_src'] ?? ''),
                'cd_kind_code' => (string)($row['kind'] ?? ''),
                'brand_name' => (string)($row['brand_name'] ?? ''),
                'stock_qty' => 0,
                'last_sale_date' => (string)($row['last_sale_date'] ?? ''),
                'created_at' => (string)($row['created_at'] ?? ''),
                'sale_price' => $salePrice,
                'cost_price' => $costPrice,
                'margin_per' => $marginPer,
            ];
        }

        return [
            'total_count' => count($haveCandidateRows) + count($providerCandidateRows),
            'have_candidate_count' => count($haveCandidateRows),
            'provider_candidate_count' => count($providerCandidateRows),
            'selected_count' => count($items),
            'items' => $items,
            'condition' => [
                'total_product_qty' => $totalQty,
                'have_product_qty' => $haveQty,
                'provider_product_qty' => $providerQty,
                'min_stock' => $minStock,
                'sale_duplicate_mode' => $saleDuplicateMode,
                'exclude_brand_idxs' => $excludeBrandIdxs,
                'selected_kind_codes' => $selectedKindCodes,
                'sale_date_before_or_equal' => $saleDateThreshold,
                'margin_per_min' => $marginPerMin,
                'provider_margin_per_min' => $providerMarginPerMin,
            ],
        ];
    }


    /**
     * 할인 대상 제외 처리 (ps_discount_target_yn = 'N')
     *
     * @param array $requestData
     * @return array
     */
    public function excludeDiscountProducts(array $requestData): array
    {
        $itemSource = trim((string)($requestData['item_source'] ?? 'have'));

        if ($itemSource === 'provider') {
            $providerIdxs = $requestData['provider_idxs'] ?? ($requestData['target_idxs'] ?? ($requestData['target_idx'] ?? []));
            if (!is_array($providerIdxs)) {
                $providerIdxs = [$providerIdxs];
            }

            $providerIdxs = array_values(array_unique(array_filter(array_map('intval', $providerIdxs), static function ($v) {
                return $v > 0;
            })));

            if (empty($providerIdxs)) {
                throw new \InvalidArgumentException('제외할 공급사 상품을 선택해주세요.');
            }

            $beforeRows = ProductPartnerModel::query()
                ->select(['idx', 'discount_target_yn'])
                ->whereIn('idx', $providerIdxs)
                ->get()
                ->toArray();

            $updatedCount = ProductPartnerModel::query()
                ->whereIn('idx', $providerIdxs)
                ->update([
                    'discount_target_yn' => 'N',
                ]);

            $adminActionLogService = new AdminActionLogService();
            foreach ($beforeRows as $beforeRow) {
                $before = [
                    'idx' => (int)($beforeRow['idx'] ?? 0),
                    'discount_target_yn' => (string)($beforeRow['discount_target_yn'] ?? 'Y'),
                ];
                $after = $before;
                $after['discount_target_yn'] = 'N';

                $diff = $adminActionLogService->buildDiff($before, $after);
                if (empty($diff)) {
                    continue;
                }

                $adminActionLogService->log([
                    'target_type' => 'prd_partner',
                    'target_table' => 'prd_partner',
                    'target_pk' => (string)$before['idx'],
                    'target_pks_json' => ['idx' => $before['idx']],
                    'action_mode' => 'exclude_discount_target',
                    'action_summary' => '상품 할인 생성 - 공급사 할인대상 제외',
                    'before_json' => $before,
                    'after_json' => $after,
                    'diff_json' => $diff,
                ]);
            }

            return [
                'item_source' => 'provider',
                'requested_count' => count($providerIdxs),
                'updated_count' => (int)$updatedCount,
                'provider_idxs' => $providerIdxs,
            ];
        }

        $psIdxs = $requestData['ps_idxs'] ?? ($requestData['keep_product_ps_idxs'] ?? []);
        if (!is_array($psIdxs)) {
            $psIdxs = [$psIdxs];
        }

        $psIdxs = array_values(array_unique(array_filter(array_map('intval', $psIdxs), static function ($v) {
            return $v > 0;
        })));

        if (empty($psIdxs)) {
            throw new \InvalidArgumentException('제외할 상품을 선택해주세요.');
        }

        $beforeRows = ProductStockModel::query()
            ->select(['ps_idx', 'ps_prd_idx', 'ps_discount_target_yn'])
            ->whereIn('ps_idx', $psIdxs)
            ->get()
            ->toArray();

        $updatedCount = ProductStockModel::query()
            ->whereIn('ps_idx', $psIdxs)
            ->update([
                'ps_discount_target_yn' => 'N',
            ]);

        $adminActionLogService = new AdminActionLogService();
        foreach ($beforeRows as $beforeRow) {
            $before = [
                'ps_idx' => (int)($beforeRow['ps_idx'] ?? 0),
                'ps_prd_idx' => (int)($beforeRow['ps_prd_idx'] ?? 0),
                'ps_discount_target_yn' => (string)($beforeRow['ps_discount_target_yn'] ?? 'Y'),
            ];
            $after = $before;
            $after['ps_discount_target_yn'] = 'N';

            $diff = $adminActionLogService->buildDiff($before, $after);
            if (empty($diff)) {
                continue;
            }

            $adminActionLogService->log([
                'target_type' => 'product',
                'target_table' => 'prd_stock',
                'target_pk' => (string)($before['ps_prd_idx'] ?: $before['ps_idx']),
                'target_pks_json' => ['ps_idx' => $before['ps_idx']],
                'action_mode' => 'exclude_discount_target',
                'action_summary' => '상품 할인 생성 - 할인대상 제외',
                'before_json' => $before,
                'after_json' => $after,
                'diff_json' => $diff,
            ]);
        }

        return [
            'item_source' => 'have',
            'requested_count' => count($psIdxs),
            'updated_count' => (int)$updatedCount,
            'ps_idxs' => $psIdxs,
        ];
    }

    
    /**
     * 재고코드 목록으로 고도몰 상품 정보 조회
     *
     * @param array $requestData
     * @return array
     */
    public function getGodoGoodsInfoByStockCodes(array $requestData): array
    {
        $stockCodes = $requestData['stock_codes'] ?? [];
        if (!is_array($stockCodes)) {
            $stockCodes = [$stockCodes];
        }

        $stockCodes = array_values(array_unique(array_filter(array_map(static function ($v) {
            return trim((string)$v);
        }, $stockCodes), static function ($v) {
            return $v !== '';
        })));

        $goodsNos = $requestData['goods_nos'] ?? [];
        if (!is_array($goodsNos)) {
            $goodsNos = [$goodsNos];
        }
        $goodsNos = array_values(array_unique(array_filter(array_map(static function ($v) {
            return trim((string)$v);
        }, $goodsNos), static function ($v) {
            return $v !== '';
        })));

        if (empty($stockCodes) && empty($goodsNos)) {
            return [
                'stock_codes' => [],
                'goods_nos' => [],
                'count' => 0,
                'items' => [],
                'stock_items' => [],
                'goods_no_items' => [],
            ];
        }

        $godoApiService = new GodoApiService();
        $stockItems = [];
        if (!empty($stockCodes)) {
            $codes = implode(',', $stockCodes);
            $stockItems = $godoApiService->getGodoGoodsInfoByStockCodes($codes);
            if (!is_array($stockItems)) {
                $stockItems = [];
            }

            // 보유상품 검수 시 cd_godo_code가 비어있는 경우 goodsNo로 자동 보정
            $this->syncHaveProductGodoCodeFromInspection($stockCodes, $stockItems);
        }

        $goodsNoItems = [];
        if (!empty($goodsNos)) {
            $goodsNoParam = implode(',', $goodsNos);
            $goodsNoItems = $godoApiService->getGodoGoodsInfoByGoodsNo($goodsNoParam);
            if (!is_array($goodsNoItems)) {
                $goodsNoItems = [];
            }
        }

        $mergedItems = array_merge($stockItems, $goodsNoItems);

        return [
            'stock_codes' => $stockCodes,
            'goods_nos' => $goodsNos,
            'count' => count($mergedItems),
            'items' => $mergedItems,
            'stock_items' => $stockItems,
            'goods_no_items' => $goodsNoItems,
        ];
    }

    /**
     * 현재 스테이지 목록 데이터 새로고침
     *
     * @param array $requestData
     * @return array
     */
    public function refreshCurrentProductList(array $requestData): array
    {
        $havePsIdxs = $requestData['have_ps_idxs'] ?? [];
        if (!is_array($havePsIdxs)) {
            $havePsIdxs = [$havePsIdxs];
        }
        $havePsIdxs = array_values(array_unique(array_filter(array_map('intval', $havePsIdxs), static function ($v) {
            return $v > 0;
        })));

        $providerIdxs = $requestData['provider_idxs'] ?? [];
        if (!is_array($providerIdxs)) {
            $providerIdxs = [$providerIdxs];
        }
        $providerIdxs = array_values(array_unique(array_filter(array_map('intval', $providerIdxs), static function ($v) {
            return $v > 0;
        })));

        if (empty($havePsIdxs) && empty($providerIdxs)) {
            return [
                'total_count' => 0,
                'selected_count' => 0,
                'items' => [],
            ];
        }

        $items = [];

        if (!empty($havePsIdxs)) {
            $haveRows = ProductStockModel::query()
                ->from('prd_stock AS S')
                ->join('COMPARISON_DB AS P', 'P.CD_IDX', '=', 'S.ps_prd_idx')
                ->leftJoin('BRAND_DB AS B', 'B.BD_IDX', '=', 'P.CD_BRAND_IDX')
                ->select([
                    'S.ps_idx',
                    'S.ps_prd_idx',
                    'S.ps_stock',
                    'S.ps_sale_date',
                    'S.created_at AS ps_created_at',
                    'P.CD_NAME',
                    'P.CD_IMG',
                    'P.CD_KIND_CODE',
                    'B.BD_NAME AS brand_name',
                    'P.cd_godo_code',
                    'P.cd_sale_price',
                    'P.cd_cost_price',
                ])
                ->whereIn('S.ps_idx', $havePsIdxs)
                ->get()
                ->toArray();

            foreach ($haveRows as $row) {
                $salePrice = (int)($row['cd_sale_price'] ?? 0);
                $costPrice = (int)($row['cd_cost_price'] ?? 0);
                $marginPer = 0.0;
                if ($salePrice > 0) {
                    $marginPer = round((($salePrice - $costPrice) / $salePrice) * 100, 2);
                }

                $items[] = [
                    'ps_idx' => (int)($row['ps_idx'] ?? 0),
                    'item_source' => 'have',
                    'item_key' => (string)($row['ps_idx'] ?? 0),
                    'prd_idx' => (int)($row['ps_prd_idx'] ?? 0),
                    'godo_goods_no' => $this->extractGodoGoodsNoFromHaveRow($row),
                    'detail_crawler_date' => '',
                    'godo_loaded_at' => '',
                    'supplier_prd_pk' => '',
                    'supplier_site' => '',
                    'supplier_2nd_name' => '',
                    'supplier_status' => '',
                    'prd_name' => (string)($row['CD_NAME'] ?? ''),
                    'img_path' => $this->buildProductImagePath((string)($row['CD_IMG'] ?? '')),
                    'cd_kind_code' => (string)($row['CD_KIND_CODE'] ?? ''),
                    'brand_name' => (string)($row['brand_name'] ?? ''),
                    'stock_qty' => (int)($row['ps_stock'] ?? 0),
                    'last_sale_date' => (string)($row['ps_sale_date'] ?? ''),
                    'created_at' => (string)($row['ps_created_at'] ?? ''),
                    'sale_price' => $salePrice,
                    'cost_price' => $costPrice,
                    'margin_per' => $marginPer,
                ];
            }
        }

        if (!empty($providerIdxs)) {
            $providerRows = ProductPartnerModel::query()
                ->from('prd_partner AS PP')
                ->leftJoin('BRAND_DB AS B', 'B.BD_IDX', '=', 'PP.brand_idx')
                ->select([
                    'PP.idx',
                    'PP.name',
                    'PP.img_src',
                    'PP.godo_goodsNo',
                    'PP.supplier_prd_pk',
                    'PP.supplier_site',
                    'PP.supplier_2nd_name',
                    'PP.supplier_status',
                    'PP.kind',
                    'B.BD_NAME AS brand_name',
                    'PP.sale_price',
                    'PP.order_price',
                    'PP.last_sale_date',
                    'PP.detail_crawler_date',
                    'PP.godo_loaded_at',
                    'PP.created_at',
                ])
                ->whereIn('PP.idx', $providerIdxs)
                ->get()
                ->toArray();

            foreach ($providerRows as $row) {
                $salePrice = (int)($row['sale_price'] ?? 0);
                $costPrice = (int)($row['order_price'] ?? 0);
                $marginPer = 0.0;
                if ($salePrice > 0) {
                    $marginPer = round((($salePrice - $costPrice) / $salePrice) * 100, 2);
                }

                $providerIdx = (int)($row['idx'] ?? 0);
                $items[] = [
                    'ps_idx' => $providerIdx,
                    'item_source' => 'provider',
                    'item_key' => 'provider_' . $providerIdx,
                    'prd_idx' => 0,
                    'godo_goods_no' => (string)($row['godo_goodsNo'] ?? ''),
                    'supplier_prd_pk' => (string)($row['supplier_prd_pk'] ?? ''),
                    'detail_crawler_date' => (string)($row['detail_crawler_date'] ?? ''),
                    'godo_loaded_at' => (string)($row['godo_loaded_at'] ?? ''),
                    'supplier_site' => (string)($row['supplier_site'] ?? ''),
                    'supplier_2nd_name' => (string)($row['supplier_2nd_name'] ?? ''),
                    'supplier_status' => (string)($row['supplier_status'] ?? ''),
                    'prd_name' => (string)($row['name'] ?? ''),
                    'img_path' => (string)($row['img_src'] ?? ''),
                    'cd_kind_code' => (string)($row['kind'] ?? ''),
                    'brand_name' => (string)($row['brand_name'] ?? ''),
                    'stock_qty' => 0,
                    'last_sale_date' => (string)($row['last_sale_date'] ?? ''),
                    'created_at' => (string)($row['created_at'] ?? ''),
                    'sale_price' => $salePrice,
                    'cost_price' => $costPrice,
                    'margin_per' => $marginPer,
                ];
            }
        }

        return [
            'total_count' => count($items),
            'selected_count' => count($items),
            'items' => $items,
        ];
    }

    /**
     * 코드(재고코드/상품코드) 기준으로 스테이지 상품 1건 직접 삽입 조회
     *
     * @param array $requestData
     * @return array
     */
    public function insertProductByCode(array $requestData): array
    {
        $insertMode = trim((string)($requestData['insert_product_mode'] ?? 'have'));
        if ($insertMode !== 'provider') {
            $insertMode = 'have';
        }

        $insertCode = trim((string)($requestData['insert_product_code'] ?? ''));
        if ($insertCode === '') {
            throw new \InvalidArgumentException('삽입할 재고코드/상품코드를 입력해주세요.');
        }

        $existingItemKeys = $requestData['existing_item_keys'] ?? [];
        if (!is_array($existingItemKeys)) {
            $existingItemKeys = [$existingItemKeys];
        }
        $existingItemKeys = array_values(array_unique(array_filter(array_map(static function ($v) {
            return trim((string)$v);
        }, $existingItemKeys), static function ($v) {
            return $v !== '';
        })));
        $existingKeyMap = array_fill_keys($existingItemKeys, true);

        $insertCodeNumeric = (preg_match('/^\d+$/', $insertCode) === 1) ? (int)$insertCode : 0;
        $insertCodeInt = $insertCodeNumeric > 0 ? $insertCodeNumeric : null;

        if ($insertMode === 'provider') {
            $providerRows = ProductPartnerModel::query()
                ->from('prd_partner AS PP')
                ->leftJoin('BRAND_DB AS B', 'B.BD_IDX', '=', 'PP.brand_idx')
                ->select([
                    'PP.idx',
                    'PP.name',
                    'PP.img_src',
                    'PP.godo_goodsNo',
                    'PP.supplier_prd_pk',
                    'PP.supplier_site',
                    'PP.supplier_2nd_name',
                    'PP.supplier_status',
                    'PP.kind',
                    'B.BD_NAME AS brand_name',
                    'PP.sale_price',
                    'PP.order_price',
                    'PP.last_sale_date',
                    'PP.detail_crawler_date',
                    'PP.godo_loaded_at',
                    'PP.created_at',
                ])
                ->whereRaw('PP.sale_price > 0')
                ->whereRaw('PP.order_price > 0')
                ->whereRaw("PP.godo_goodsNo IS NOT NULL AND PP.godo_goodsNo <> ''")
                ->whereRaw("(PP.discount_target_yn IS NULL OR PP.discount_target_yn = '' OR PP.discount_target_yn = 'Y')")
                ->where(function ($query) use ($insertCode, $insertCodeInt) {
                    if ($insertCodeInt !== null) {
                        $query->where('PP.idx', $insertCodeInt)
                            ->orWhere('PP.code', $insertCode)
                            ->orWhere('PP.supplier_prd_pk', $insertCode)
                            ->orWhere('PP.godo_goodsNo', $insertCode);
                        return;
                    }

                    $query->where('PP.code', $insertCode)
                        ->orWhere('PP.supplier_prd_pk', $insertCode)
                        ->orWhere('PP.godo_goodsNo', $insertCode);
                })
                ->orderBy('PP.idx', 'desc')
                ->limit(1)
                ->get()
                ->toArray();

            if (empty($providerRows)) {
                throw new \RuntimeException('입력한 코드와 일치하는 위탁상품을 찾을 수 없습니다.');
            }

            $item = $this->buildProviderStageItem($providerRows[0]);
            if (isset($existingKeyMap[$item['item_key']])) {
                throw new \RuntimeException('이미 리스트에 존재하는 위탁상품입니다.');
            }

            return [
                'insert_mode' => 'provider',
                'insert_code' => $insertCode,
                'item' => $item,
                'items' => [$item],
                'selected_count' => 1,
            ];
        }

        $haveRows = ProductStockModel::query()
            ->from('prd_stock AS S')
            ->join('COMPARISON_DB AS P', 'P.CD_IDX', '=', 'S.ps_prd_idx')
            ->leftJoin('BRAND_DB AS B', 'B.BD_IDX', '=', 'P.CD_BRAND_IDX')
            ->select([
                'S.ps_idx',
                'S.ps_prd_idx',
                'S.ps_stock',
                'S.ps_sale_date',
                'S.created_at AS ps_created_at',
                'P.CD_NAME',
                'P.CD_IMG',
                'P.CD_KIND_CODE',
                'B.BD_NAME AS brand_name',
                'P.cd_godo_code',
                'P.cd_sale_price',
                'P.cd_cost_price',
            ])
            ->whereRaw('P.cd_sale_price > 0')
            ->whereRaw("(S.ps_discount_target_yn IS NULL OR S.ps_discount_target_yn = '' OR S.ps_discount_target_yn = 'Y')")
            ->where(function ($query) use ($insertCode, $insertCodeInt) {
                if ($insertCodeInt !== null) {
                    $query->where('S.ps_idx', $insertCodeInt)
                        ->orWhere('S.ps_prd_idx', $insertCodeInt)
                        ->orWhere('P.CD_CODE', $insertCode);
                    return;
                }

                $query->where('P.CD_CODE', $insertCode);
            })
            ->orderBy('S.ps_idx', 'desc')
            ->limit(1)
            ->get()
            ->toArray();

        if (empty($haveRows)) {
            throw new \RuntimeException('입력한 코드와 일치하는 보유상품을 찾을 수 없습니다.');
        }

        $item = $this->buildHaveStageItem($haveRows[0]);
        if (isset($existingKeyMap[$item['item_key']])) {
            throw new \RuntimeException('이미 리스트에 존재하는 보유상품입니다.');
        }

        return [
            'insert_mode' => 'have',
            'insert_code' => $insertCode,
            'item' => $item,
            'items' => [$item],
            'selected_count' => 1,
        ];
    }

    /**
     * 고도몰 원가 반영 (검수 화면)
     *
     * @param array $requestData
     * @return array
     */
    public function updateGodoGoodsCostPriceFromInspection(array $requestData): array
    {
        $goodsNo = trim((string)($requestData['goods_no'] ?? ''));
        $localGodoCode = trim((string)($requestData['local_godo_code'] ?? ''));
        $costPrice = trim((string)($requestData['cost_price'] ?? ''));
        $psIdx = trim((string)($requestData['ps_idx'] ?? ''));
        $prdIdx = (int)($requestData['prd_idx'] ?? 0);
        $godoSalePrice = trim((string)($requestData['godo_sale_price'] ?? ''));
        $isCostMismatch = strtoupper(trim((string)($requestData['cost_mismatch'] ?? 'N'))) === 'Y';
        $isSaleMismatch = strtoupper(trim((string)($requestData['sale_mismatch'] ?? 'N'))) === 'Y';
        $isGodoCodeMismatch = strtoupper(trim((string)($requestData['godo_code_mismatch'] ?? 'N'))) === 'Y';

        if (!$isCostMismatch && !$isSaleMismatch && !$isGodoCodeMismatch) {
            throw new \InvalidArgumentException('판매가/원가/goodsNo 불일치 항목이 없습니다.');
        }

        $salePriceUpdated = false;
        $updatedSalePrice = null;
        if ($isSaleMismatch) {
            if ($prdIdx <= 0) {
                throw new \InvalidArgumentException('판매가 반영 대상 상품 IDX가 없습니다.');
            }
            if ($godoSalePrice === '') {
                throw new \InvalidArgumentException('고도몰 판매가가 비어있습니다.');
            }

            $salePriceRaw = str_replace(',', '', $godoSalePrice);
            if (!preg_match('/^\d+$/', $salePriceRaw)) {
                throw new \InvalidArgumentException('고도몰 판매가는 숫자만 가능합니다.');
            }
            $updatedSalePrice = (string)((int)$salePriceRaw);

            ProductModel::query()
                ->where('CD_IDX', $prdIdx)
                ->update([
                    'cd_sale_price' => $updatedSalePrice,
                ]);
            $salePriceUpdated = true;
        }

        $costPriceUpdated = false;
        $apiResult = [];
        if ($isCostMismatch) {
            if ($goodsNo === '') {
                throw new \InvalidArgumentException('고도몰 상품번호가 비어있습니다.');
            }
            if ($costPrice === '') {
                throw new \InvalidArgumentException('반영할 원가가 비어있습니다.');
            }

            $godoApiService = new GodoApiService();
            $apiResult = $godoApiService->updateGodoGoodsCostPrice($goodsNo, $costPrice);
            $costPriceUpdated = true;
        }

        $godoCodeUpdated = false;
        if ($isGodoCodeMismatch) {
            if ($prdIdx <= 0) {
                throw new \InvalidArgumentException('cd_godo_code 보정 대상 상품 IDX가 없습니다.');
            }
            if ($goodsNo === '') {
                throw new \InvalidArgumentException('고도몰 상품번호가 비어있어 cd_godo_code를 보정할 수 없습니다.');
            }

            ProductModel::query()
                ->where('CD_IDX', $prdIdx)
                ->update([
                    'cd_godo_code' => $goodsNo,
                ]);
            $godoCodeUpdated = true;
        }

        return [
            'goods_no' => $goodsNo,
            'local_godo_code' => $localGodoCode,
            'cost_price' => $costPrice,
            'ps_idx' => $psIdx,
            'prd_idx' => $prdIdx,
            'godo_sale_price' => $updatedSalePrice,
            'sale_price_updated' => $salePriceUpdated,
            'cost_price_updated' => $costPriceUpdated,
            'godo_code_updated' => $godoCodeUpdated,
            'api_result' => $apiResult,
        ];
    }

    /**
     * 고도몰검수 일괄처리 (검수 화면)
     *
     * 처리 내용:
     * 1) 판매가 불일치(sale_mismatch=Y): 우리 DB 상품 판매가(cd_sale_price)를 고도몰 판매가로 보정
     * 2) 원가 불일치(cost_mismatch=Y): 고도몰 API를 호출해 고도몰 원가를 우리 기준 원가로 반영
     * 3) goodsNo 불일치(godo_code_mismatch=Y): 보유상품 cd_godo_code를 API goodsNo로 보정
     *
     * @param array $requestData
     * @return array
     */
    public function updateGodoGoodsCostPriceBulkFromInspection(array $requestData): array
    {
        $rows = $requestData['rows'] ?? [];
        if (!is_array($rows)) {
            throw new \InvalidArgumentException('고도몰검수 일괄처리 대상 형식이 올바르지 않습니다.');
        }

        // 고도몰 API 과부하 방지용 보호값
        $maxBulkCount = 30;       // 1회 최대 처리 건수
        $throttleMs = 250;        // 호출 간 지연(ms)

        $targets = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $goodsNo = trim((string)($row['goods_no'] ?? ''));
            $localGodoCode = trim((string)($row['local_godo_code'] ?? ''));
            $costPrice = trim((string)($row['cost_price'] ?? ''));
            $psIdx = trim((string)($row['ps_idx'] ?? ''));
            $prdIdx = (int)($row['prd_idx'] ?? 0);
            $godoSalePrice = trim((string)($row['godo_sale_price'] ?? ''));
            $isCostMismatch = strtoupper(trim((string)($row['cost_mismatch'] ?? 'N'))) === 'Y';
            $isSaleMismatch = strtoupper(trim((string)($row['sale_mismatch'] ?? 'N'))) === 'Y';
            $isGodoCodeMismatch = strtoupper(trim((string)($row['godo_code_mismatch'] ?? 'N'))) === 'Y';

            if (!$isCostMismatch && !$isSaleMismatch && !$isGodoCodeMismatch) {
                continue;
            }

            $targets[] = [
                'goods_no' => $goodsNo,
                'local_godo_code' => $localGodoCode,
                'cost_price' => $costPrice,
                'ps_idx' => $psIdx,
                'prd_idx' => $prdIdx,
                'godo_sale_price' => $godoSalePrice,
                'cost_mismatch' => $isCostMismatch,
                'sale_mismatch' => $isSaleMismatch,
                'godo_code_mismatch' => $isGodoCodeMismatch,
            ];
        }

        if (empty($targets)) {
            throw new \InvalidArgumentException('고도몰검수 일괄처리할 판매가/원가/goodsNo 불일치 항목이 없습니다.');
        }

        // 실제 고도몰 API 호출 건수 기준으로 제한
        $apiCount = 0;
        $apiDedupeForCount = [];
        foreach ($targets as $target) {
            if (!($target['cost_mismatch'] ?? false)) {
                continue;
            }
            $goodsNo = (string)($target['goods_no'] ?? '');
            $costPrice = (string)($target['cost_price'] ?? '');
            if ($goodsNo === '' || $costPrice === '') {
                continue;
            }
            $dedupeKey = $goodsNo . '|' . $costPrice;
            if (isset($apiDedupeForCount[$dedupeKey])) {
                continue;
            }
            $apiDedupeForCount[$dedupeKey] = true;
            $apiCount++;
        }

        if ($apiCount > $maxBulkCount) {
            throw new \InvalidArgumentException('고도몰검수 일괄처리는 한 번에 최대 ' . $maxBulkCount . '건까지 가능합니다. 건수를 나눠서 진행해주세요.');
        }

        $godoApiService = new GodoApiService();
        $successItems = [];
        $failedItems = [];
        $saleDedupe = [];
        $costDedupe = [];
        $godoCodeDedupe = [];
        $apiCallIndex = 0;

        foreach ($targets as $target) {
            $resultItem = [
                'goods_no' => (string)($target['goods_no'] ?? ''),
                'local_godo_code' => (string)($target['local_godo_code'] ?? ''),
                'cost_price' => (string)($target['cost_price'] ?? ''),
                'ps_idx' => (string)($target['ps_idx'] ?? ''),
                'prd_idx' => (int)($target['prd_idx'] ?? 0),
                'godo_sale_price' => (string)($target['godo_sale_price'] ?? ''),
                'sale_price_updated' => false,
                'cost_price_updated' => false,
                'godo_code_updated' => false,
                'api_result' => [],
            ];

            try {
                if ($target['sale_mismatch'] ?? false) {
                    $prdIdx = (int)($target['prd_idx'] ?? 0);
                    $godoSalePrice = trim((string)($target['godo_sale_price'] ?? ''));
                    if ($prdIdx <= 0) {
                        throw new \InvalidArgumentException('판매가 반영 대상 상품 IDX가 없습니다.');
                    }
                    if ($godoSalePrice === '') {
                        throw new \InvalidArgumentException('고도몰 판매가가 비어있습니다.');
                    }

                    $salePriceRaw = str_replace(',', '', $godoSalePrice);
                    if (!preg_match('/^\d+$/', $salePriceRaw)) {
                        throw new \InvalidArgumentException('고도몰 판매가는 숫자만 가능합니다.');
                    }

                    $dedupeKey = $prdIdx . '|' . $salePriceRaw;
                    if (!isset($saleDedupe[$dedupeKey])) {
                        ProductModel::query()
                            ->where('CD_IDX', $prdIdx)
                            ->update([
                                'cd_sale_price' => (string)((int)$salePriceRaw),
                            ]);
                        $saleDedupe[$dedupeKey] = true;
                    }

                    $resultItem['godo_sale_price'] = (string)((int)$salePriceRaw);
                    $resultItem['sale_price_updated'] = true;
                }

                if ($target['cost_mismatch'] ?? false) {
                    $goodsNo = trim((string)($target['goods_no'] ?? ''));
                    $costPrice = trim((string)($target['cost_price'] ?? ''));
                    if ($goodsNo === '') {
                        throw new \InvalidArgumentException('고도몰 상품번호가 비어있습니다.');
                    }
                    if ($costPrice === '') {
                        throw new \InvalidArgumentException('반영할 원가가 비어있습니다.');
                    }

                    $dedupeKey = $goodsNo . '|' . $costPrice;
                    if (!isset($costDedupe[$dedupeKey])) {
                        if ($apiCallIndex > 0) {
                            usleep($throttleMs * 1000);
                        }
                        $resultItem['api_result'] = $godoApiService->updateGodoGoodsCostPrice($goodsNo, $costPrice);
                        $costDedupe[$dedupeKey] = true;
                        $apiCallIndex++;
                    }

                    $resultItem['cost_price_updated'] = true;
                }

                if ($target['godo_code_mismatch'] ?? false) {
                    $prdIdx = (int)($target['prd_idx'] ?? 0);
                    $goodsNo = trim((string)($target['goods_no'] ?? ''));
                    if ($prdIdx <= 0) {
                        throw new \InvalidArgumentException('cd_godo_code 보정 대상 상품 IDX가 없습니다.');
                    }
                    if ($goodsNo === '') {
                        throw new \InvalidArgumentException('고도몰 상품번호가 비어있어 cd_godo_code를 보정할 수 없습니다.');
                    }

                    $dedupeKey = $prdIdx . '|' . $goodsNo;
                    if (!isset($godoCodeDedupe[$dedupeKey])) {
                        ProductModel::query()
                            ->where('CD_IDX', $prdIdx)
                            ->update([
                                'cd_godo_code' => $goodsNo,
                            ]);
                        $godoCodeDedupe[$dedupeKey] = true;
                    }

                    $resultItem['godo_code_updated'] = true;
                }

                $successItems[] = $resultItem;
            } catch (\Throwable $e) {
                $failedItems[] = [
                    'goods_no' => (string)($target['goods_no'] ?? ''),
                    'local_godo_code' => (string)($target['local_godo_code'] ?? ''),
                    'cost_price' => (string)($target['cost_price'] ?? ''),
                    'ps_idx' => (string)($target['ps_idx'] ?? ''),
                    'prd_idx' => (int)($target['prd_idx'] ?? 0),
                    'godo_sale_price' => (string)($target['godo_sale_price'] ?? ''),
                    'message' => $e->getMessage(),
                ];
            }
        }

        return [
            'requested_count' => count($targets),
            'success_count' => count($successItems),
            'failed_count' => count($failedItems),
            'throttle_ms' => $throttleMs,
            'api_call_count' => $apiCallIndex,
            'applied_items' => $successItems,
            'failed_items' => $failedItems,
        ];
    }


    /**
     * 할인 이력 기준 고도몰 타임세일 전체 등록
     * - sale_status=wait 상태에서만 허용
     * - 이미 그룹별 개별 등록 완료된 할인율 그룹은 자동 제외
     * - 모든 그룹 성공 시 sale_status=upload 로 전환
     *
     * @param array $requestData
     * @return array
     */
    public function createGodoTimeSaleFromHistory(array $requestData): array
    {
        $seq = (int)($requestData['seq'] ?? ($requestData['sale_history_seq'] ?? 0));
        if ($seq <= 0) {
            throw new \InvalidArgumentException('유효한 할인 이력 번호가 없습니다.');
        }

        $saleHistory = $this->getSaleHistoryDetail($seq);
        $saleStatus = trim((string)($saleHistory['sale_status'] ?? ''));
        if ($saleStatus !== 'wait') {
            throw new \InvalidArgumentException('sale_status가 wait 상태일 때만 고도몰 등록이 가능합니다.');
        }

        $context = $this->buildGodoTimeSaleContextFromHistory($saleHistory);
        $grouped = $context['grouped'];
        if (empty($grouped)) {
            throw new \InvalidArgumentException('할인율/고도몰상품번호 기준 등록 가능한 상품이 없습니다.');
        }

        $groupStatusMap = $this->getGodoGroupUploadStatusMapFromMeta($saleHistory['meta_json'] ?? []);
        $targetGrouped = [];
        $skippedGroups = [];
        foreach ($grouped as $discountRate => $goodsNoMap) {
            $statusRow = $groupStatusMap[$discountRate] ?? [];
            if (($statusRow['status'] ?? '') === 'success') {
                $skippedGroups[] = [
                    'discount_rate' => $discountRate,
                    'goods_count' => count(array_keys($goodsNoMap)),
                    'uploaded_at' => (string)($statusRow['uploaded_at'] ?? ''),
                    'uploaded_by' => (int)($statusRow['uploaded_by'] ?? 0),
                    'uploaded_by_name' => (string)($statusRow['uploaded_by_name'] ?? ''),
                ];
                continue;
            }
            $targetGrouped[$discountRate] = $goodsNoMap;
        }

        $godoApiService = new GodoApiService();
        $successGroups = [];
        $failedGroups = [];
        $uploadedBy = (int)($requestData['uploaded_by'] ?? (AuthAdmin::getSession('sess_idx') ?? 0));
        $uploadedByName = trim((string)(AuthAdmin::getSession('sess_name') ?? ''));

        foreach ($targetGrouped as $discountRate => $goodsNoMap) {
            $goodsNos = array_keys($goodsNoMap);
            $goodsNosCsv = implode(',', $goodsNos);
            try {
                $apiResult = $godoApiService->createGodoTimeSale([
                    'startDt' => $context['start_dt'],
                    'endDt' => $context['end_dt'],
                    'tsKind' => $context['ts_kind'],
                    'discountRate' => $discountRate,
                    'goodsNos' => $goodsNosCsv,
                ]);

                $statusAt = date('Y-m-d H:i:s');
                $groupStatusMap[$discountRate] = [
                    'status' => 'success',
                    'discount_rate' => $discountRate,
                    'goods_count' => count($goodsNos),
                    'goods_nos' => $goodsNos,
                    'uploaded_at' => $statusAt,
                    'uploaded_by' => $uploadedBy > 0 ? $uploadedBy : null,
                    'uploaded_by_name' => $uploadedByName,
                    'source_mode' => 'bulk',
                    'message' => '',
                ];

                $successGroups[] = [
                    'discount_rate' => $discountRate,
                    'goods_count' => count($goodsNos),
                    'goods_nos' => $goodsNos,
                    'api_result' => $apiResult,
                ];
            } catch (\Throwable $e) {
                $statusAt = date('Y-m-d H:i:s');
                $groupStatusMap[$discountRate] = [
                    'status' => 'failed',
                    'discount_rate' => $discountRate,
                    'goods_count' => count($goodsNos),
                    'goods_nos' => $goodsNos,
                    'uploaded_at' => $statusAt,
                    'uploaded_by' => $uploadedBy > 0 ? $uploadedBy : null,
                    'uploaded_by_name' => $uploadedByName,
                    'source_mode' => 'bulk',
                    'message' => $e->getMessage(),
                ];

                $failedGroups[] = [
                    'discount_rate' => $discountRate,
                    'goods_count' => count($goodsNos),
                    'goods_nos' => $goodsNos,
                    'message' => $e->getMessage(),
                ];
            }
        }

        $meta = $this->saveGodoGroupUploadStatusMeta($seq, $saleHistory, $groupStatusMap);
        $saleHistory['meta_json'] = $meta;

        $finalizeResult = $this->finalizeGodoTimeSaleUploadIfCompleted(
            $seq,
            $saleHistory,
            $context,
            $groupStatusMap,
            $requestData
        );

        return [
            'seq' => $seq,
            'sale_status' => $finalizeResult['sale_status'],
            'start_dt' => $context['start_dt'],
            'end_dt' => $context['end_dt'],
            'ts_kind' => $context['ts_kind'],
            'requested_group_count' => count($grouped),
            'target_group_count' => count($targetGrouped),
            'skipped_group_count' => count($skippedGroups),
            'success_group_count' => count($successGroups),
            'failed_group_count' => count($failedGroups),
            'is_upload_completed' => $finalizeResult['is_upload_completed'],
            'uploaded_at' => $finalizeResult['uploaded_at'],
            'uploaded_by' => $finalizeResult['uploaded_by'],
            'success_groups' => $successGroups,
            'failed_groups' => $failedGroups,
            'skipped_groups' => $skippedGroups,
            'sale_record_result' => $finalizeResult['sale_record_result'],
            'rollback_snapshot' => $finalizeResult['rollback_snapshot'],
        ];
    }

    /**
     * 할인 이력 기준 고도몰 타임세일 그룹별 개별 등록
     * - sale_status=wait 상태에서만 허용
     * - 이미 성공 등록된 그룹은 재등록하지 않고 건너뜀
     * - 마지막 남은 그룹까지 성공하면 sale_status=upload 로 전환
     *
     * @param array $requestData
     * @return array
     */
    public function createGodoTimeSaleGroupFromHistory(array $requestData): array
    {
        $seq = (int)($requestData['seq'] ?? ($requestData['sale_history_seq'] ?? 0));
        if ($seq <= 0) {
            throw new \InvalidArgumentException('유효한 할인 이력 번호가 없습니다.');
        }

        $discountRateInput = trim((string)($requestData['discount_rate'] ?? ''));
        $discountRate = $this->normalizeDiscountRateKey($discountRateInput);
        if ($discountRate === '') {
            throw new \InvalidArgumentException('유효한 할인율 그룹 값이 없습니다.');
        }

        $saleHistory = $this->getSaleHistoryDetail($seq);
        $saleStatus = trim((string)($saleHistory['sale_status'] ?? ''));
        if ($saleStatus !== 'wait') {
            throw new \InvalidArgumentException('sale_status가 wait 상태일 때만 고도몰 등록이 가능합니다.');
        }

        $context = $this->buildGodoTimeSaleContextFromHistory($saleHistory);
        $grouped = $context['grouped'];
        if (empty($grouped[$discountRate])) {
            throw new \InvalidArgumentException('요청한 할인율 그룹의 등록 대상 상품이 없습니다.');
        }

        $groupStatusMap = $this->getGodoGroupUploadStatusMapFromMeta($saleHistory['meta_json'] ?? []);
        $existingStatus = $groupStatusMap[$discountRate] ?? [];
        if (($existingStatus['status'] ?? '') === 'success') {
            $finalizeResult = $this->finalizeGodoTimeSaleUploadIfCompleted(
                $seq,
                $saleHistory,
                $context,
                $groupStatusMap,
                $requestData
            );

            return [
                'seq' => $seq,
                'discount_rate' => $discountRate,
                'already_registered' => true,
                'group_status' => $existingStatus,
                'sale_status' => $finalizeResult['sale_status'],
                'is_upload_completed' => $finalizeResult['is_upload_completed'],
                'uploaded_at' => $finalizeResult['uploaded_at'],
                'uploaded_by' => $finalizeResult['uploaded_by'],
                'sale_record_result' => $finalizeResult['sale_record_result'],
            ];
        }

        $goodsNos = array_keys($grouped[$discountRate]);
        $godoApiService = new GodoApiService();
        $uploadedBy = (int)($requestData['uploaded_by'] ?? (AuthAdmin::getSession('sess_idx') ?? 0));
        $uploadedByName = trim((string)(AuthAdmin::getSession('sess_name') ?? ''));
        $statusAt = date('Y-m-d H:i:s');

        $apiResult = [];
        $status = 'success';
        $message = '';
        try {
            $apiResult = $godoApiService->createGodoTimeSale([
                'startDt' => $context['start_dt'],
                'endDt' => $context['end_dt'],
                'tsKind' => $context['ts_kind'],
                'discountRate' => $discountRate,
                'goodsNos' => implode(',', $goodsNos),
            ]);
        } catch (\Throwable $e) {
            $status = 'failed';
            $message = $e->getMessage();
        }

        $groupStatusMap[$discountRate] = [
            'status' => $status,
            'discount_rate' => $discountRate,
            'goods_count' => count($goodsNos),
            'goods_nos' => $goodsNos,
            'uploaded_at' => $statusAt,
            'uploaded_by' => $uploadedBy > 0 ? $uploadedBy : null,
            'uploaded_by_name' => $uploadedByName,
            'source_mode' => 'group',
            'message' => $message,
        ];

        $meta = $this->saveGodoGroupUploadStatusMeta($seq, $saleHistory, $groupStatusMap);
        $saleHistory['meta_json'] = $meta;

        if ($status !== 'success') {
            throw new \RuntimeException($message !== '' ? $message : '할인율 그룹 등록에 실패했습니다.');
        }

        $finalizeResult = $this->finalizeGodoTimeSaleUploadIfCompleted(
            $seq,
            $saleHistory,
            $context,
            $groupStatusMap,
            $requestData
        );

        return [
            'seq' => $seq,
            'discount_rate' => $discountRate,
            'already_registered' => false,
            'group_status' => $groupStatusMap[$discountRate],
            'api_result' => $apiResult,
            'sale_status' => $finalizeResult['sale_status'],
            'is_upload_completed' => $finalizeResult['is_upload_completed'],
            'uploaded_at' => $finalizeResult['uploaded_at'],
            'uploaded_by' => $finalizeResult['uploaded_by'],
            'sale_record_result' => $finalizeResult['sale_record_result'],
        ];
    }

    /**
     * 할인 상세에서 특정 상품의 할인율 그룹을 다른 그룹으로 이동 저장한다.
     * - 대상: sale_status=wait 상태의 할인 이력만 허용
     * - 저장: prd_sale_history.product_json 내부 discount_rate/할인가/할인후마진 재계산 후 반영
     *
     * @param array $requestData
     * @return array
     */
    public function moveSaleHistoryDiscountGroupItem(array $requestData): array
    {
        $seq = (int)($requestData['seq'] ?? ($requestData['sale_history_seq'] ?? 0));
        if ($seq <= 0) {
            throw new \InvalidArgumentException('유효한 할인 이력 번호가 없습니다.');
        }

        $targetRate = $this->normalizeDiscountRateKey($requestData['target_discount_rate'] ?? '');
        if ($targetRate === '') {
            throw new \InvalidArgumentException('이동할 할인율 그룹이 올바르지 않습니다.');
        }

        $itemKey = trim((string)($requestData['item_key'] ?? ''));
        $itemSource = trim((string)($requestData['item_source'] ?? ''));
        $psIdx = (int)($requestData['ps_idx'] ?? 0);
        $prdIdx = (int)($requestData['prd_idx'] ?? 0);

        $row = ProductSaleHistoryModel::find($seq);
        if (!$row) {
            throw new \RuntimeException('할인 이력을 찾을 수 없습니다.');
        }
        $saleHistory = $row->toArray();

        $saleStatus = trim((string)($saleHistory['sale_status'] ?? ''));
        if ($saleStatus !== 'wait') {
            throw new \InvalidArgumentException('sale_status가 wait 상태일 때만 그룹 이동이 가능합니다.');
        }

        $rawProductJson = $this->decodeProductJson($saleHistory['product_json'] ?? '[]');
        $containerKey = null;
        $productList = [];
        if (isset($rawProductJson['products']) && is_array($rawProductJson['products'])) {
            $containerKey = 'products';
            $productList = array_values($rawProductJson['products']);
        } elseif (isset($rawProductJson['data']) && is_array($rawProductJson['data'])) {
            $containerKey = 'data';
            $productList = array_values($rawProductJson['data']);
        } else {
            $productList = $this->normalizeSaleHistoryProductList($rawProductJson);
        }

        if (empty($productList)) {
            throw new \InvalidArgumentException('할인 이력 상품 데이터가 비어있습니다.');
        }

        $targetIndex = null;
        $beforeRate = '';
        foreach ($productList as $idx => $item) {
            if (!is_array($item)) {
                continue;
            }

            $rowItemKey = trim((string)($item['item_key'] ?? ''));
            $rowSource = trim((string)($item['item_source'] ?? ''));
            if ($rowSource === '') {
                $rowSource = ($rowItemKey !== '' && strpos($rowItemKey, 'provider_') === 0) ? 'provider' : 'have';
            }
            $rowPsIdx = (int)($item['ps_idx'] ?? 0);
            $rowPrdIdx = (int)($item['prd_idx'] ?? 0);

            $matched = false;
            if ($itemKey !== '' && $rowItemKey !== '' && $itemKey === $rowItemKey) {
                $matched = true;
            } elseif ($psIdx > 0 && $rowPsIdx > 0 && $psIdx === $rowPsIdx) {
                if ($itemSource === '' || $rowSource === $itemSource) {
                    $matched = true;
                }
            } elseif ($prdIdx > 0 && $rowPrdIdx > 0 && $prdIdx === $rowPrdIdx) {
                $matched = true;
            }

            if (!$matched) {
                continue;
            }

            $targetIndex = $idx;
            $beforeRate = $this->normalizeDiscountRateKey($item['discount_rate'] ?? '');
            break;
        }

        if ($targetIndex === null) {
            throw new \InvalidArgumentException('이동할 상품을 할인 이력에서 찾지 못했습니다.');
        }

        if ($beforeRate === $targetRate) {
            return [
                'seq' => $seq,
                'item_key' => $itemKey,
                'before_discount_rate' => $beforeRate,
                'target_discount_rate' => $targetRate,
                'moved' => false,
            ];
        }

        $targetItem = $productList[$targetIndex];
        $salePrice = (float)($targetItem['sale_price'] ?? 0);
        $costPrice = (float)($targetItem['cost_price'] ?? 0);
        $targetRateNum = (float)$targetRate;

        $discountSalePrice = (int)round($salePrice * (100 - $targetRateNum) / 100);
        if ($discountSalePrice < 0) {
            $discountSalePrice = 0;
        }
        $discountMarginAmount = $discountSalePrice - (int)round($costPrice);
        $discountMarginPer = 0.0;
        if ($discountSalePrice > 0) {
            $discountMarginPer = (($discountSalePrice - $costPrice) / $discountSalePrice) * 100;
        }
        $discountMarginPer = round($discountMarginPer, 2);

        $productList[$targetIndex]['discount_rate'] = (float)$targetRate;
        $productList[$targetIndex]['discount_sale_price'] = $discountSalePrice;
        $productList[$targetIndex]['discount_margin_amount'] = $discountMarginAmount;
        $productList[$targetIndex]['discount_margin_per'] = $discountMarginPer;

        if ($containerKey === 'products') {
            $rawProductJson['products'] = $productList;
            $saveProductJson = $rawProductJson;
        } elseif ($containerKey === 'data') {
            $rawProductJson['data'] = $productList;
            $saveProductJson = $rawProductJson;
        } else {
            $saveProductJson = $productList;
        }

        $meta = $this->decodeMetaJsonToArray($saleHistory['meta_json'] ?? []);
        // 그룹 이동 시 기존 그룹 등록상태(성공/실패)는 기준이 달라지므로 초기화한다.
        $meta['godo_group_upload_status'] = [];
        $meta['group_move_log'][] = [
            'moved_at' => date('Y-m-d H:i:s'),
            'moved_by' => (int)(AuthAdmin::getSession('sess_idx') ?? 0),
            'moved_by_name' => (string)(AuthAdmin::getSession('sess_name') ?? ''),
            'item_key' => (string)($productList[$targetIndex]['item_key'] ?? $itemKey),
            'item_source' => (string)($productList[$targetIndex]['item_source'] ?? $itemSource),
            'ps_idx' => (int)($productList[$targetIndex]['ps_idx'] ?? 0),
            'prd_idx' => (int)($productList[$targetIndex]['prd_idx'] ?? 0),
            'before_discount_rate' => $beforeRate,
            'target_discount_rate' => $targetRate,
        ];

        ProductSaleHistoryModel::query()
            ->where('seq', $seq)
            ->update([
                'product_json' => json_encode($saveProductJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'meta_json' => json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ]);

        return [
            'seq' => $seq,
            'item_key' => (string)($productList[$targetIndex]['item_key'] ?? $itemKey),
            'before_discount_rate' => $beforeRate,
            'target_discount_rate' => $targetRate,
            'discount_sale_price' => $discountSalePrice,
            'discount_margin_amount' => $discountMarginAmount,
            'discount_margin_per' => $discountMarginPer,
            'moved' => true,
        ];
    }

    /**
     * 할인 이력에서 고도몰 등록 컨텍스트를 구성한다.
     *
     * @param array $saleHistory
     * @return array
     */
    private function buildGodoTimeSaleContextFromHistory(array $saleHistory): array
    {
        $startDt = trim((string)($saleHistory['sale_start_date'] ?? ''));
        $endDt = trim((string)($saleHistory['sale_end_date'] ?? ''));
        if ($startDt === '' || $endDt === '') {
            throw new \InvalidArgumentException('할인 시작/종료일 정보가 비어있습니다.');
        }

        $saleMode = trim((string)($saleHistory['sale_mode'] ?? ''));
        $tsKind = 'D';
        if ($saleMode === 'week') {
            $tsKind = 'W';
        } elseif ($saleMode === 'month') {
            $tsKind = 'M';
        }

        $productList = $saleHistory['product_list'] ?? [];
        if (!is_array($productList) || empty($productList)) {
            throw new \InvalidArgumentException('고도몰 등록 대상 상품이 없습니다.');
        }

        $grouped = [];
        foreach ($productList as $item) {
            if (!is_array($item)) {
                continue;
            }

            $discountRate = $this->normalizeDiscountRateKey($item['discount_rate'] ?? '');
            if ($discountRate === '') {
                continue;
            }

            $goodsNo = trim((string)($item['godo_goods_no'] ?? ($item['godo_goodsNo'] ?? ($item['godoNo'] ?? ''))));
            if ($goodsNo === '' || preg_match('/^\d+$/', $goodsNo) !== 1) {
                continue;
            }

            if (!isset($grouped[$discountRate])) {
                $grouped[$discountRate] = [];
            }
            $grouped[$discountRate][$goodsNo] = true;
        }

        return [
            'start_dt' => $startDt,
            'end_dt' => $endDt,
            'sale_mode' => $saleMode,
            'ts_kind' => $tsKind,
            'product_list' => $productList,
            'grouped' => $grouped,
        ];
    }

    /**
     * 할인율 키를 고정 문자열 형태로 정규화한다.
     *
     * @param mixed $value
     * @return string
     */
    private function normalizeDiscountRateKey($value): string
    {
        $raw = trim((string)$value);
        $num = is_numeric($raw) ? (float)$raw : 0.0;
        if ($num <= 0) {
            return '';
        }

        return rtrim(rtrim(number_format($num, 2, '.', ''), '0'), '.');
    }

    /**
     * meta_json에서 할인율 그룹 업로드 상태맵을 읽는다.
     *
     * @param mixed $metaJson
     * @return array
     */
    private function getGodoGroupUploadStatusMapFromMeta($metaJson): array
    {
        $meta = $this->decodeMetaJsonToArray($metaJson);
        $statusMap = [];
        $rawMap = $meta['godo_group_upload_status'] ?? [];
        if (!is_array($rawMap)) {
            return [];
        }

        foreach ($rawMap as $rate => $row) {
            if (!is_array($row)) {
                continue;
            }
            $normalizedRate = $this->normalizeDiscountRateKey($row['discount_rate'] ?? $rate);
            if ($normalizedRate === '') {
                continue;
            }
            $statusMap[$normalizedRate] = [
                'status' => (string)($row['status'] ?? ''),
                'discount_rate' => $normalizedRate,
                'goods_count' => (int)($row['goods_count'] ?? 0),
                'goods_nos' => (isset($row['goods_nos']) && is_array($row['goods_nos'])) ? array_values($row['goods_nos']) : [],
                'uploaded_at' => (string)($row['uploaded_at'] ?? ''),
                'uploaded_by' => (int)($row['uploaded_by'] ?? 0),
                'uploaded_by_name' => (string)($row['uploaded_by_name'] ?? ''),
                'source_mode' => (string)($row['source_mode'] ?? ''),
                'message' => (string)($row['message'] ?? ''),
            ];
        }

        return $statusMap;
    }

    /**
     * 할인율 그룹 업로드 상태맵을 meta_json에 저장한다.
     *
     * @param int $seq
     * @param array $saleHistory
     * @param array $groupStatusMap
     * @return array 저장된 meta 배열
     */
    private function saveGodoGroupUploadStatusMeta(int $seq, array $saleHistory, array $groupStatusMap): array
    {
        $meta = $this->decodeMetaJsonToArray($saleHistory['meta_json'] ?? []);
        $meta['godo_group_upload_status'] = $groupStatusMap;

        ProductSaleHistoryModel::query()
            ->where('seq', $seq)
            ->update([
                'meta_json' => json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ]);

        return $meta;
    }

    /**
     * 할인율 그룹 등록상태가 모두 성공이면 sale_status를 upload로 전환한다.
     *
     * @param int $seq
     * @param array $saleHistory
     * @param array $context
     * @param array $groupStatusMap
     * @param array $requestData
     * @return array
     */
    private function finalizeGodoTimeSaleUploadIfCompleted(
        int $seq,
        array $saleHistory,
        array $context,
        array $groupStatusMap,
        array $requestData
    ): array {
        $grouped = $context['grouped'] ?? [];
        $allSuccess = !empty($grouped);
        foreach (array_keys($grouped) as $rate) {
            $status = (string)(($groupStatusMap[$rate] ?? [])['status'] ?? '');
            if ($status !== 'success') {
                $allSuccess = false;
                break;
            }
        }

        $saleStatus = trim((string)($saleHistory['sale_status'] ?? 'wait'));
        $uploadedAt = null;
        $uploadedBy = null;
        $saleRecordResult = [
            'have_target_count' => 0,
            'have_updated_count' => 0,
            'provider_target_count' => 0,
            'provider_updated_count' => 0,
        ];
        $rollbackSnapshot = [
            'have_count' => 0,
            'provider_count' => 0,
        ];

        if (!$allSuccess) {
            return [
                'is_upload_completed' => false,
                'sale_status' => $saleStatus,
                'uploaded_at' => $uploadedAt,
                'uploaded_by' => $uploadedBy,
                'sale_record_result' => $saleRecordResult,
                'rollback_snapshot' => $rollbackSnapshot,
            ];
        }

        if ($saleStatus === 'upload') {
            return [
                'is_upload_completed' => true,
                'sale_status' => $saleStatus,
                'uploaded_at' => (string)($saleHistory['uploaded_at'] ?? ''),
                'uploaded_by' => (int)($saleHistory['uploaded_by'] ?? 0),
                'sale_record_result' => $saleRecordResult,
                'rollback_snapshot' => $rollbackSnapshot,
            ];
        }

        $rollbackRaw = $this->buildSaleHistoryRollbackSnapshot($context['product_list'] ?? []);
        $rollbackSnapshot = [
            'have_count' => (int)($rollbackRaw['have_count'] ?? 0),
            'provider_count' => (int)($rollbackRaw['provider_count'] ?? 0),
        ];

        $saleRecordResult = $this->recordSaleStartDateToSourceProducts(
            $seq,
            (string)($context['sale_mode'] ?? 'day'),
            (string)($context['start_dt'] ?? ''),
            (string)($context['end_dt'] ?? ''),
            (array)($context['product_list'] ?? [])
        );

        $uploadedAt = date('Y-m-d H:i:s');
        $uploadedBy = (int)($requestData['uploaded_by'] ?? (AuthAdmin::getSession('sess_idx') ?? 0));
        ProductSaleHistoryModel::query()
            ->where('seq', $seq)
            ->update([
                'sale_status' => 'upload',
                'uploaded_at' => $uploadedAt,
                'uploaded_by' => $uploadedBy > 0 ? $uploadedBy : null,
            ]);

        $this->saveUploadRollbackSnapshotMeta(
            $seq,
            $saleHistory,
            $rollbackRaw,
            $uploadedAt,
            $uploadedBy,
            $saleRecordResult
        );

        return [
            'is_upload_completed' => true,
            'sale_status' => 'upload',
            'uploaded_at' => $uploadedAt,
            'uploaded_by' => $uploadedBy,
            'sale_record_result' => $saleRecordResult,
            'rollback_snapshot' => $rollbackSnapshot,
        ];
    }


    /**
     * 할인 이력 업로드 완료 시 보유/위탁 상품의 할인 시작일을 기록한다.
     * - 보유상품: prd_stock.ps_sale_date + ps_sale_log + ps_in_sale_* 갱신
     * - 위탁상품: prd_partner.last_sale_date 갱신
     *
     * @param int $saleHistorySeq
     * @param string $saleMode
     * @param string $startDt
     * @param string $endDt
     * @param array $productList
     * @return array
     */
    private function recordSaleStartDateToSourceProducts(
        int $saleHistorySeq,
        string $saleMode,
        string $startDt,
        string $endDt,
        array $productList
    ): array {
        $startTs = strtotime($startDt);
        if ($startTs === false) {
            $startTs = time();
        }
        $endTs = strtotime($endDt);
        if ($endTs === false) {
            $endTs = $startTs;
        }

        $saleStartDate = date('Y-m-d', $startTs);
        $saleStartDateTime = date('Y-m-d H:i:s', $startTs);
        $saleEndDate = date('Y-m-d', $endTs);
        $saleEndDateTime = date('Y-m-d H:i:s', $endTs);

        $haveItemMap = [];
        $providerIdxMap = [];
        foreach ($productList as $item) {
            if (!is_array($item)) {
                continue;
            }

            $itemSource = trim((string)($item['item_source'] ?? ''));
            $itemKey = trim((string)($item['item_key'] ?? ''));
            if ($itemSource === '') {
                $itemSource = ($itemKey !== '' && strpos($itemKey, 'provider_') === 0) ? 'provider' : 'have';
            }

            if ($itemSource === 'provider') {
                $providerIdx = (int)($item['ps_idx'] ?? 0);
                if ($providerIdx > 0) {
                    $providerIdxMap[$providerIdx] = true;
                }
                continue;
            }

            $psIdx = (int)($item['ps_idx'] ?? 0);
            if ($psIdx > 0) {
                $haveItemMap[$psIdx] = $item;
            }
        }

        $haveUpdatedCount = 0;
        if (!empty($haveItemMap)) {
            $productStockSaleLogService = new ProductStockSaleLogService();
            $haveRows = ProductStockModel::query()
                ->select(['ps_idx', 'ps_sale_date', 'ps_sale_log'])
                ->whereIn('ps_idx', array_keys($haveItemMap))
                ->get()
                ->toArray();

            foreach ($haveRows as $stockRow) {
                $psIdx = (int)($stockRow['ps_idx'] ?? 0);
                if ($psIdx <= 0 || !isset($haveItemMap[$psIdx])) {
                    continue;
                }

                $item = $haveItemMap[$psIdx];
                $beforeSaleDate = trim((string)($stockRow['ps_sale_date'] ?? ''));
                // 마지막 할인일은 할인 종료일 기준으로 기록한다.
                $nextSaleDate = $saleEndDate;
                if ($beforeSaleDate !== '' && $beforeSaleDate !== '0000-00-00') {
                    $beforeTs = strtotime($beforeSaleDate);
                    $nextTs = strtotime($saleEndDate);
                    if ($beforeTs !== false && $nextTs !== false && $beforeTs > $nextTs) {
                        $nextSaleDate = date('Y-m-d', $beforeTs);
                    }
                }

                $psSaleLogData = json_decode((string)($stockRow['ps_sale_log'] ?? '[]'), true);
                if (!is_array($psSaleLogData)) {
                    $psSaleLogData = [];
                }

                $salePerRaw = trim((string)($item['discount_rate'] ?? '0'));
                $salePerNum = is_numeric($salePerRaw) ? (float)$salePerRaw : 0;
                $salePer = rtrim(rtrim(number_format($salePerNum, 2, '.', ''), '0'), '.');
                if ($salePer === '') {
                    $salePer = '0';
                }

                $saleLogUnit = [
                    'sale_mode' => $saleMode,
                    'grouping_idx' => $saleHistorySeq,
                    'sale_history_seq' => $saleHistorySeq,
                    'pg_subject' => '[sale_history]#' . $saleHistorySeq,
                    'pg_sday' => $saleStartDate,
                    'pg_day' => $saleEndDate,
                    'sale_per' => $salePer,
                    'original_price' => (int)($item['sale_price'] ?? 0),
                    'sale_price' => (int)($item['discount_sale_price'] ?? 0),
                    'margin_price' => (int)($item['discount_margin_amount'] ?? 0),
                    'margin_per' => (string)($item['discount_margin_per'] ?? 0),
                    'd' => AuthAdmin::getConnectionInfo(),
                ];

                $exists = false;
                foreach ($psSaleLogData as $logRow) {
                    if (!is_array($logRow)) {
                        continue;
                    }
                    $logSeq = (string)($logRow['sale_history_seq'] ?? ($logRow['grouping_idx'] ?? ''));
                    $logPer = (string)($logRow['sale_per'] ?? '');
                    if ($logSeq === (string)$saleHistorySeq && $logPer === (string)$salePer) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) {
                    array_unshift($psSaleLogData, $saleLogUnit);
                }

                $updated = ProductStockModel::query()
                    ->where('ps_idx', $psIdx)
                    ->update([
                        'ps_sale_date' => $nextSaleDate,
                        'ps_sale_log' => json_encode($psSaleLogData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'ps_in_sale_s' => $saleStartDateTime,
                        'ps_in_sale_e' => $saleEndDateTime,
                        'ps_in_sale_data' => json_encode($saleLogUnit, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    ]);

                if ((int)$updated > 0) {
                    $haveUpdatedCount++;
                }

                // 기존 그룹핑 기반 로그 저장 구조와 동일한 신규 로그 테이블도 함께 적재
                $productStockSaleLogService->createSaleLog([
                    'ps_idx' => $psIdx,
                    'prd_mode' => 'prdDB',
                    'sale_mode' => $saleMode,
                    'grouping_idx' => $saleHistorySeq,
                    'pg_subject' => '[sale_history]#' . $saleHistorySeq,
                    'pg_sday' => $saleStartDate,
                    'pg_day' => $saleEndDate,
                    'sale_per' => $salePer,
                    'original_price' => (int)($item['sale_price'] ?? 0),
                    'sale_price' => (int)($item['discount_sale_price'] ?? 0),
                    'margin_price' => (int)($item['discount_margin_amount'] ?? 0),
                    'margin_per' => (string)($item['discount_margin_per'] ?? 0),
                    'd' => AuthAdmin::getConnectionInfo(),
                ]);
            }
        }

        $providerUpdatedCount = 0;
        if (!empty($providerIdxMap)) {
            $providerRows = ProductPartnerModel::query()
                ->select(['idx', 'last_sale_date'])
                ->whereIn('idx', array_keys($providerIdxMap))
                ->get()
                ->toArray();

            foreach ($providerRows as $providerRow) {
                $providerIdx = (int)($providerRow['idx'] ?? 0);
                if ($providerIdx <= 0) {
                    continue;
                }

                $beforeSaleDate = trim((string)($providerRow['last_sale_date'] ?? ''));
                // 마지막 할인일은 할인 종료일 기준으로 기록한다.
                $nextSaleDate = $saleEndDate;
                if ($beforeSaleDate !== '' && $beforeSaleDate !== '0000-00-00') {
                    $beforeTs = strtotime($beforeSaleDate);
                    $nextTs = strtotime($saleEndDate);
                    if ($beforeTs !== false && $nextTs !== false && $beforeTs > $nextTs) {
                        $nextSaleDate = date('Y-m-d', $beforeTs);
                    }
                }

                $updated = ProductPartnerModel::query()
                    ->where('idx', $providerIdx)
                    ->update([
                        'last_sale_date' => $nextSaleDate,
                    ]);

                if ((int)$updated > 0) {
                    $providerUpdatedCount++;
                }
            }
        }

        return [
            'have_target_count' => count($haveItemMap),
            'have_updated_count' => $haveUpdatedCount,
            'provider_target_count' => count($providerIdxMap),
            'provider_updated_count' => $providerUpdatedCount,
            'sale_start_date' => $saleStartDate,
            'sale_start_datetime' => $saleStartDateTime,
            'sale_end_datetime' => $saleEndDateTime,
        ];
    }

    /**
     * 고도몰 등록 전, 원복용 할인 상태 스냅샷을 구성한다.
     *
     * @param array $productList
     * @return array
     */
    private function buildSaleHistoryRollbackSnapshot(array $productList): array
    {
        $havePsIdxs = [];
        $providerIdxs = [];
        foreach ($productList as $item) {
            if (!is_array($item)) {
                continue;
            }

            $itemSource = trim((string)($item['item_source'] ?? ''));
            $itemKey = trim((string)($item['item_key'] ?? ''));
            if ($itemSource === '') {
                $itemSource = ($itemKey !== '' && strpos($itemKey, 'provider_') === 0) ? 'provider' : 'have';
            }

            $idx = (int)($item['ps_idx'] ?? 0);
            if ($idx <= 0) {
                continue;
            }

            if ($itemSource === 'provider') {
                $providerIdxs[$idx] = true;
            } else {
                $havePsIdxs[$idx] = true;
            }
        }

        $haveRows = [];
        if (!empty($havePsIdxs)) {
            $haveRows = ProductStockModel::query()
                ->select([
                    'ps_idx',
                    'ps_sale_date',
                    'ps_in_sale_s',
                    'ps_in_sale_e',
                    'ps_in_sale_data',
                    'ps_sale_log',
                ])
                ->whereIn('ps_idx', array_keys($havePsIdxs))
                ->get()
                ->toArray();
        }

        $providerRows = [];
        if (!empty($providerIdxs)) {
            $providerRows = ProductPartnerModel::query()
                ->select(['idx', 'last_sale_date'])
                ->whereIn('idx', array_keys($providerIdxs))
                ->get()
                ->toArray();
        }

        return [
            'saved_at' => date('Y-m-d H:i:s'),
            'have_count' => count($haveRows),
            'provider_count' => count($providerRows),
            'have_rows' => $haveRows,
            'provider_rows' => $providerRows,
        ];
    }

    /**
     * 업로드 직전 스냅샷을 sale_history.meta_json에 저장한다.
     *
     * @param int $seq
     * @param array $saleHistory
     * @param array $snapshot
     * @param string $uploadedAt
     * @param int|null $uploadedBy
     * @param array $saleRecordResult
     * @return void
     */
    private function saveUploadRollbackSnapshotMeta(
        int $seq,
        array $saleHistory,
        array $snapshot,
        string $uploadedAt,
        ?int $uploadedBy,
        array $saleRecordResult
    ): void {
        $meta = $this->decodeMetaJsonToArray($saleHistory['meta_json'] ?? []);
        $meta['upload_rollback'] = [
            'is_restored' => false,
            'snapshot' => $snapshot,
            'uploaded_at' => $uploadedAt,
            'uploaded_by' => $uploadedBy,
            'sale_record_result' => $saleRecordResult,
        ];

        ProductSaleHistoryModel::query()
            ->where('seq', $seq)
            ->update([
                'meta_json' => json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ]);
    }

    /**
     * 고도몰 등록 후 할인일 변경분 원상복구
     * - 보유: ps_sale_date/ps_in_sale_s,ps_in_sale_e,ps_in_sale_data,ps_sale_log
     * - 위탁: last_sale_date
     *
     * @param array $requestData
     * @return array
     */
    public function restoreSaleDateFromHistoryUpload(array $requestData): array
    {
        $seq = (int)($requestData['seq'] ?? ($requestData['sale_history_seq'] ?? 0));
        if ($seq <= 0) {
            throw new \InvalidArgumentException('유효한 할인 이력 번호가 없습니다.');
        }

        $saleHistory = $this->getSaleHistoryDetail($seq);
        $meta = $this->decodeMetaJsonToArray($saleHistory['meta_json'] ?? []);
        $rollbackMeta = $meta['upload_rollback'] ?? [];
        if (!is_array($rollbackMeta) || empty($rollbackMeta['snapshot']) || !is_array($rollbackMeta['snapshot'])) {
            throw new \InvalidArgumentException('원상복구 가능한 스냅샷 데이터가 없습니다.');
        }
        if (!empty($rollbackMeta['is_restored'])) {
            throw new \InvalidArgumentException('이미 원상복구가 완료된 이력입니다.');
        }

        $snapshot = $rollbackMeta['snapshot'];
        $haveRows = (isset($snapshot['have_rows']) && is_array($snapshot['have_rows'])) ? $snapshot['have_rows'] : [];
        $providerRows = (isset($snapshot['provider_rows']) && is_array($snapshot['provider_rows'])) ? $snapshot['provider_rows'] : [];

        $haveRestored = 0;
        foreach ($haveRows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $psIdx = (int)($row['ps_idx'] ?? 0);
            if ($psIdx <= 0) {
                continue;
            }

            $updated = ProductStockModel::query()
                ->where('ps_idx', $psIdx)
                ->update([
                    'ps_sale_date' => (string)($row['ps_sale_date'] ?? ''),
                    'ps_in_sale_s' => (string)($row['ps_in_sale_s'] ?? ''),
                    'ps_in_sale_e' => (string)($row['ps_in_sale_e'] ?? ''),
                    'ps_in_sale_data' => (string)($row['ps_in_sale_data'] ?? ''),
                    'ps_sale_log' => (string)($row['ps_sale_log'] ?? '[]'),
                ]);
            if ((int)$updated > 0) {
                $haveRestored++;
            }
        }

        $providerRestored = 0;
        foreach ($providerRows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $providerIdx = (int)($row['idx'] ?? 0);
            if ($providerIdx <= 0) {
                continue;
            }

            $updated = ProductPartnerModel::query()
                ->where('idx', $providerIdx)
                ->update([
                    'last_sale_date' => (string)($row['last_sale_date'] ?? ''),
                ]);
            if ((int)$updated > 0) {
                $providerRestored++;
            }
        }

        $restoredAt = date('Y-m-d H:i:s');
        $restoredBy = (int)($requestData['restored_by'] ?? (AuthAdmin::getSession('sess_idx') ?? 0));
        $rollbackMeta['is_restored'] = true;
        $rollbackMeta['restored_at'] = $restoredAt;
        $rollbackMeta['restored_by'] = $restoredBy > 0 ? $restoredBy : null;
        $rollbackMeta['restored_result'] = [
            'have_target_count' => count($haveRows),
            'have_restored_count' => $haveRestored,
            'provider_target_count' => count($providerRows),
            'provider_restored_count' => $providerRestored,
        ];
        $meta['upload_rollback'] = $rollbackMeta;

        ProductSaleHistoryModel::query()
            ->where('seq', $seq)
            ->update([
                'sale_status' => 'wait',
                'uploaded_at' => null,
                'uploaded_by' => null,
                'meta_json' => json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ]);

        return [
            'seq' => $seq,
            'sale_status' => 'wait',
            'restored_at' => $restoredAt,
            'restored_by' => $restoredBy > 0 ? $restoredBy : null,
            'have_target_count' => count($haveRows),
            'have_restored_count' => $haveRestored,
            'provider_target_count' => count($providerRows),
            'provider_restored_count' => $providerRestored,
        ];
    }

    private function buildProductImagePath(string $cdImg): string
    {
        $cdImg = trim($cdImg);
        if ($cdImg === '') {
            return '';
        }

        if (preg_match('/^https?:\/\//i', $cdImg)) {
            return $cdImg;
        }

        return '/data/comparion/' . ltrim($cdImg, '/');
    }

    /**
     * 보유상품 row를 스테이지 item 구조로 변환
     *
     * @param array $row
     * @return array
     */
    private function buildHaveStageItem(array $row): array
    {
        $salePrice = (int)($row['cd_sale_price'] ?? 0);
        $costPrice = (int)($row['cd_cost_price'] ?? 0);
        $marginPer = 0.0;
        if ($salePrice > 0) {
            $marginPer = round((($salePrice - $costPrice) / $salePrice) * 100, 2);
        }

        return [
            'ps_idx' => (int)($row['ps_idx'] ?? 0),
            'item_source' => 'have',
            'item_key' => (string)($row['ps_idx'] ?? 0),
            'prd_idx' => (int)($row['ps_prd_idx'] ?? 0),
            'godo_goods_no' => $this->extractGodoGoodsNoFromHaveRow($row),
            'detail_crawler_date' => '',
            'godo_loaded_at' => '',
            'supplier_prd_pk' => '',
            'supplier_site' => '',
            'supplier_2nd_name' => '',
            'supplier_status' => '',
            'prd_name' => (string)($row['CD_NAME'] ?? ''),
            'img_path' => $this->buildProductImagePath((string)($row['CD_IMG'] ?? '')),
            'cd_kind_code' => (string)($row['CD_KIND_CODE'] ?? ''),
            'brand_name' => (string)($row['brand_name'] ?? ''),
            'stock_qty' => (int)($row['ps_stock'] ?? 0),
            'last_sale_date' => (string)($row['ps_sale_date'] ?? ''),
            'created_at' => (string)($row['ps_created_at'] ?? ''),
            'sale_price' => $salePrice,
            'cost_price' => $costPrice,
            'margin_per' => $marginPer,
        ];
    }

    /**
     * 위탁상품 row를 스테이지 item 구조로 변환
     *
     * @param array $row
     * @return array
     */
    private function buildProviderStageItem(array $row): array
    {
        $salePrice = (int)($row['sale_price'] ?? 0);
        $costPrice = (int)($row['order_price'] ?? 0);
        $marginPer = 0.0;
        if ($salePrice > 0) {
            $marginPer = round((($salePrice - $costPrice) / $salePrice) * 100, 2);
        }

        $providerIdx = (int)($row['idx'] ?? 0);
        return [
            'ps_idx' => $providerIdx,
            'item_source' => 'provider',
            'item_key' => 'provider_' . $providerIdx,
            'prd_idx' => 0,
            'godo_goods_no' => (string)($row['godo_goodsNo'] ?? ''),
            'supplier_prd_pk' => (string)($row['supplier_prd_pk'] ?? ''),
            'detail_crawler_date' => (string)($row['detail_crawler_date'] ?? ''),
            'godo_loaded_at' => (string)($row['godo_loaded_at'] ?? ''),
            'supplier_site' => (string)($row['supplier_site'] ?? ''),
            'supplier_2nd_name' => (string)($row['supplier_2nd_name'] ?? ''),
            'supplier_status' => (string)($row['supplier_status'] ?? ''),
            'prd_name' => (string)($row['name'] ?? ''),
            'img_path' => (string)($row['img_src'] ?? ''),
            'cd_kind_code' => (string)($row['kind'] ?? ''),
            'brand_name' => (string)($row['brand_name'] ?? ''),
            'stock_qty' => 0,
            'last_sale_date' => (string)($row['last_sale_date'] ?? ''),
            'created_at' => (string)($row['created_at'] ?? ''),
            'sale_price' => $salePrice,
            'cost_price' => $costPrice,
            'margin_per' => $marginPer,
        ];
    }

    /**
     * 보유상품 row에서 고도몰 상품번호를 안전하게 추출
     *
     * @param array $row
     * @return string
     */
    private function extractGodoGoodsNoFromHaveRow(array $row): string
    {
        $candidateKeys = [
            'cd_godo_code',
            'CD_GODO_CODE',
            'godo_goods_no',
            'godo_goodsNo',
            'godoNo',
        ];

        foreach ($candidateKeys as $key) {
            if (!array_key_exists($key, $row)) {
                continue;
            }
            $value = trim((string)($row[$key] ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    /**
     * 보유상품 검수결과(goodsCd/goodsNo)로 cd_godo_code 자동 보정
     * - 대상: cd_godo_code가 비어있는 보유상품만
     * - 기준: goodsCd(재고코드=ps_idx) 매칭 후 goodsNo 반영
     *
     * @param array $stockCodes
     * @param array $stockItems
     * @return void
     */
    private function syncHaveProductGodoCodeFromInspection(array $stockCodes, array $stockItems): void
    {
        if (empty($stockCodes) || empty($stockItems)) {
            return;
        }

        $normalizedStockCodes = array_values(array_unique(array_filter(array_map(static function ($v) {
            return trim((string)$v);
        }, $stockCodes), static function ($v) {
            return $v !== '' && preg_match('/^\d+$/', $v) === 1;
        })));
        if (empty($normalizedStockCodes)) {
            return;
        }

        $requestedCodeMap = array_fill_keys($normalizedStockCodes, true);
        $goodsNoByPsIdx = [];
        foreach ($stockItems as $goods) {
            if (!is_array($goods)) {
                continue;
            }
            $goodsCd = trim((string)($goods['goodsCd'] ?? ''));
            $goodsNo = trim((string)($goods['goodsNo'] ?? ''));
            if ($goodsCd === '' || $goodsNo === '') {
                continue;
            }
            if (!isset($requestedCodeMap[$goodsCd])) {
                continue;
            }
            if (preg_match('/^\d+$/', $goodsNo) !== 1) {
                continue;
            }

            $goodsNoByPsIdx[$goodsCd] = $goodsNo;
        }

        if (empty($goodsNoByPsIdx)) {
            return;
        }

        $haveRows = ProductStockModel::query()
            ->from('prd_stock AS S')
            ->select(['S.ps_idx', 'S.ps_prd_idx'])
            ->whereIn('S.ps_idx', array_keys($goodsNoByPsIdx))
            ->get()
            ->toArray();
        if (empty($haveRows)) {
            return;
        }

        foreach ($haveRows as $haveRow) {
            $psIdx = trim((string)($haveRow['ps_idx'] ?? ''));
            $prdIdx = (int)($haveRow['ps_prd_idx'] ?? 0);
            if ($psIdx === '' || $prdIdx <= 0 || empty($goodsNoByPsIdx[$psIdx])) {
                continue;
            }

            $goodsNo = (string)$goodsNoByPsIdx[$psIdx];
            ProductModel::query()
                ->where('CD_IDX', $prdIdx)
                ->whereRaw("(cd_godo_code IS NULL OR cd_godo_code = '' OR cd_godo_code = 0)")
                ->update([
                    'cd_godo_code' => $goodsNo,
                ]);
        }
    }

    /**
     * 할인 상세용 product_list를 DB 최신값으로 보강(이미지/이름/공급사 정보 등)
     * - 보유상품: prd_stock(ps_idx) + COMPARISON_DB
     * - 위탁상품: prd_partner(idx)
     *
     * @param array $productList
     * @return array
     */
    private function hydrateSaleHistoryProductListFromDb(array $productList): array
    {
        if (empty($productList)) {
            return [];
        }

        $havePsIdxs = [];
        $providerIdxs = [];

        foreach ($productList as $item) {
            if (!is_array($item)) {
                continue;
            }

            $itemSource = trim((string)($item['item_source'] ?? ''));
            $psIdxRaw = trim((string)($item['ps_idx'] ?? ''));
            $itemKey = trim((string)($item['item_key'] ?? ''));

            if ($itemSource === '') {
                if ($itemKey !== '' && strpos($itemKey, 'provider_') === 0) {
                    $itemSource = 'provider';
                } else {
                    $itemSource = 'have';
                }
            }

            if ($itemSource === 'provider') {
                $providerIdx = (int)$psIdxRaw;
                if ($providerIdx <= 0 && preg_match('/^provider_(\d+)$/', $itemKey, $m) === 1) {
                    $providerIdx = (int)$m[1];
                }
                if ($providerIdx > 0) {
                    $providerIdxs[] = $providerIdx;
                }
            } else {
                $havePsIdx = (int)$psIdxRaw;
                if ($havePsIdx > 0) {
                    $havePsIdxs[] = $havePsIdx;
                }
            }
        }

        $havePsIdxs = array_values(array_unique($havePsIdxs));
        $providerIdxs = array_values(array_unique($providerIdxs));

        $haveMap = [];
        if (!empty($havePsIdxs)) {
            $haveRows = ProductStockModel::query()
                ->from('prd_stock AS S')
                ->join('COMPARISON_DB AS P', 'P.CD_IDX', '=', 'S.ps_prd_idx')
                ->leftJoin('BRAND_DB AS B', 'B.BD_IDX', '=', 'P.CD_BRAND_IDX')
                ->select([
                    'S.ps_idx',
                    'S.ps_prd_idx',
                    'S.ps_stock',
                    'S.ps_sale_date',
                    'S.created_at AS ps_created_at',
                    'P.CD_NAME',
                    'P.CD_IMG',
                    'P.CD_KIND_CODE',
                    'B.BD_NAME AS brand_name',
                    'P.cd_godo_code',
                    'P.cd_sale_price',
                    'P.cd_cost_price',
                ])
                ->whereIn('S.ps_idx', $havePsIdxs)
                ->get()
                ->toArray();

            foreach ($haveRows as $row) {
                $key = (int)($row['ps_idx'] ?? 0);
                if ($key <= 0) {
                    continue;
                }
                $haveMap[$key] = $row;
            }
        }

        $providerMap = [];
        if (!empty($providerIdxs)) {
            $providerRows = ProductPartnerModel::query()
                ->from('prd_partner AS PP')
                ->leftJoin('BRAND_DB AS B', 'B.BD_IDX', '=', 'PP.brand_idx')
                ->select([
                    'PP.idx',
                    'PP.name',
                    'PP.img_src',
                    'PP.godo_goodsNo',
                    'PP.supplier_prd_pk',
                    'PP.supplier_site',
                    'PP.supplier_2nd_name',
                    'PP.supplier_status',
                    'PP.kind',
                    'B.BD_NAME AS brand_name',
                    'PP.sale_price',
                    'PP.order_price',
                    'PP.last_sale_date',
                    'PP.detail_crawler_date',
                    'PP.godo_loaded_at',
                    'PP.created_at',
                ])
                ->whereIn('PP.idx', $providerIdxs)
                ->get()
                ->toArray();

            foreach ($providerRows as $row) {
                $key = (int)($row['idx'] ?? 0);
                if ($key <= 0) {
                    continue;
                }
                $providerMap[$key] = $row;
            }
        }

        $hydrated = [];
        foreach ($productList as $item) {
            if (!is_array($item)) {
                continue;
            }

            $current = $item;
            $itemSource = trim((string)($current['item_source'] ?? ''));
            $itemKey = trim((string)($current['item_key'] ?? ''));
            if ($itemSource === '') {
                $itemSource = ($itemKey !== '' && strpos($itemKey, 'provider_') === 0) ? 'provider' : 'have';
                $current['item_source'] = $itemSource;
            }

            if ($itemSource === 'provider') {
                $providerIdx = (int)($current['ps_idx'] ?? 0);
                if ($providerIdx <= 0 && preg_match('/^provider_(\d+)$/', $itemKey, $m) === 1) {
                    $providerIdx = (int)$m[1];
                    $current['ps_idx'] = $providerIdx;
                }

                if ($providerIdx > 0 && isset($providerMap[$providerIdx])) {
                    $db = $providerMap[$providerIdx];
                    $salePrice = (int)($db['sale_price'] ?? ($current['sale_price'] ?? 0));
                    $costPrice = (int)($db['order_price'] ?? ($current['cost_price'] ?? 0));
                    $marginPer = $salePrice > 0 ? round((($salePrice - $costPrice) / $salePrice) * 100, 2) : 0.0;

                    $current['item_key'] = 'provider_' . $providerIdx;
                    $current['prd_name'] = (string)($db['name'] ?? ($current['prd_name'] ?? ''));
                    $current['img_path'] = (string)($db['img_src'] ?? ($current['img_path'] ?? ''));
                    $current['cd_kind_code'] = (string)($db['kind'] ?? ($current['cd_kind_code'] ?? ''));
                    $current['brand_name'] = (string)($db['brand_name'] ?? ($current['brand_name'] ?? ''));
                    $current['godo_goods_no'] = (string)($db['godo_goodsNo'] ?? ($current['godo_goods_no'] ?? ''));
                    $current['supplier_prd_pk'] = (string)($db['supplier_prd_pk'] ?? ($current['supplier_prd_pk'] ?? ''));
                    $current['supplier_site'] = (string)($db['supplier_site'] ?? ($current['supplier_site'] ?? ''));
                    $current['supplier_2nd_name'] = (string)($db['supplier_2nd_name'] ?? ($current['supplier_2nd_name'] ?? ''));
                    $current['supplier_status'] = (string)($db['supplier_status'] ?? ($current['supplier_status'] ?? ''));
                    $current['detail_crawler_date'] = (string)($db['detail_crawler_date'] ?? ($current['detail_crawler_date'] ?? ''));
                    $current['godo_loaded_at'] = (string)($db['godo_loaded_at'] ?? ($current['godo_loaded_at'] ?? ''));
                    $current['last_sale_date'] = (string)($db['last_sale_date'] ?? ($current['last_sale_date'] ?? ''));
                    $current['created_at'] = (string)($db['created_at'] ?? ($current['created_at'] ?? ''));
                    $current['sale_price'] = $salePrice;
                    $current['cost_price'] = $costPrice;
                    $current['margin_per'] = $marginPer;
                }
            } else {
                $havePsIdx = (int)($current['ps_idx'] ?? 0);
                if ($havePsIdx > 0 && isset($haveMap[$havePsIdx])) {
                    $db = $haveMap[$havePsIdx];
                    $salePrice = (int)($db['cd_sale_price'] ?? ($current['sale_price'] ?? 0));
                    $costPrice = (int)($db['cd_cost_price'] ?? ($current['cost_price'] ?? 0));
                    $marginPer = $salePrice > 0 ? round((($salePrice - $costPrice) / $salePrice) * 100, 2) : 0.0;

                    $current['item_key'] = (string)$havePsIdx;
                    $current['prd_idx'] = (int)($db['ps_prd_idx'] ?? ($current['prd_idx'] ?? 0));
                    $current['prd_name'] = (string)($db['CD_NAME'] ?? ($current['prd_name'] ?? ''));
                    $current['img_path'] = $this->buildProductImagePath((string)($db['CD_IMG'] ?? ($current['img_path'] ?? '')));
                    $current['cd_kind_code'] = (string)($db['CD_KIND_CODE'] ?? ($current['cd_kind_code'] ?? ''));
                    $current['brand_name'] = (string)($db['brand_name'] ?? ($current['brand_name'] ?? ''));
                    $current['godo_goods_no'] = $this->extractGodoGoodsNoFromHaveRow($db);
                    $current['stock_qty'] = (int)($db['ps_stock'] ?? ($current['stock_qty'] ?? 0));
                    $current['last_sale_date'] = (string)($db['ps_sale_date'] ?? ($current['last_sale_date'] ?? ''));
                    $current['created_at'] = (string)($db['ps_created_at'] ?? ($current['created_at'] ?? ''));
                    $current['sale_price'] = $salePrice;
                    $current['cost_price'] = $costPrice;
                    $current['margin_per'] = $marginPer;
                }
            }

            $hydrated[] = $current;
        }

        return $hydrated;
    }

    /**
     * 할인 상세 product_json 구조를 화면용 상품 리스트로 정규화
     * - 레거시: {"products":[...]} / {"data":[...]}
     * - 현재: [...]
     *
     * @param array $decodedProductList
     * @return array
     */
    private function normalizeSaleHistoryProductList(array $decodedProductList): array
    {
        if (isset($decodedProductList['products']) && is_array($decodedProductList['products'])) {
            return array_values($decodedProductList['products']);
        }
        if (isset($decodedProductList['data']) && is_array($decodedProductList['data'])) {
            return array_values($decodedProductList['data']);
        }

        // 단일 상품 오브젝트 형태로 들어온 경우도 리스트 형태로 보정
        if (isset($decodedProductList['item_source']) || isset($decodedProductList['ps_idx']) || isset($decodedProductList['prd_idx'])) {
            return [$decodedProductList];
        }

        return array_values($decodedProductList);
    }

    /**
     * 공급사 kind 필터값 생성
     * - 화면 선택값(영문 코드)
     * - config 매핑 한글명
     * - 레거시 데이터 대비 대/소문자 코드
     *
     * @param array $selectedKindCodes
     * @return array
     */
    private function buildProviderKindFilterValues(array $selectedKindCodes): array
    {
        $filters = [];
        $configProduct = config('admin.product');
        $kindMap = $configProduct['prd_kind_name'] ?? [];
        if (!is_array($kindMap)) {
            $kindMap = [];
        }

        foreach ($selectedKindCodes as $codeRaw) {
            $code = trim((string)$codeRaw);
            if ($code === '') {
                continue;
            }

            $filters[$code] = true;
            $filters[strtoupper($code)] = true;
            $filters[strtolower($code)] = true;

            if (isset($kindMap[$code])) {
                $label = trim((string)$kindMap[$code]);
                if ($label !== '') {
                    $filters[$label] = true;
                }
            }
        }

        $values = array_keys($filters);
        return !empty($values) ? $values : $selectedKindCodes;
    }

    /**
     * 위탁상품 할인 로그 화면용 데이터 조회
     * - 할인이력(product_json)에서 해당 위탁상품(idx) 로그를 역추적
     *
     * @param int|string $providerIdx
     * @return array
     */
    public function getProviderDiscountSaleLogPageData($providerIdx): array
    {
        $providerIdx = (int)$providerIdx;
        if ($providerIdx <= 0) {
            throw new \InvalidArgumentException('위탁상품 고유번호가 비어있습니다.');
        }

        $provider = ProductPartnerModel::query()
            ->select(['idx', 'name', 'sale_price', 'order_price', 'last_sale_date'])
            ->where('idx', $providerIdx)
            ->first();
        if (empty($provider)) {
            throw new \RuntimeException('위탁상품 정보를 찾을 수 없습니다.');
        }
        $provider = $provider->toArray();

        $historyRows = ProductSaleHistoryModel::query()
            ->select(['seq', 'sale_mode', 'sale_start_date', 'sale_end_date', 'created_at', 'product_json'])
            ->orderBy('seq', 'desc')
            ->limit(500)
            ->get()
            ->toArray();

        $rows = [];
        foreach ($historyRows as $historyRow) {
            $decoded = $this->decodeProductJson($historyRow['product_json'] ?? '[]');
            $productList = $this->normalizeSaleHistoryProductList($decoded);
            if (empty($productList)) {
                continue;
            }

            foreach ($productList as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $itemSource = trim((string)($item['item_source'] ?? ''));
                $itemKey = trim((string)($item['item_key'] ?? ''));
                if ($itemSource === '') {
                    $itemSource = ($itemKey !== '' && strpos($itemKey, 'provider_') === 0) ? 'provider' : 'have';
                }
                if ($itemSource !== 'provider') {
                    continue;
                }

                $itemProviderIdx = (int)($item['ps_idx'] ?? 0);
                if ($itemProviderIdx <= 0 && preg_match('/^provider_(\d+)$/', $itemKey, $m) === 1) {
                    $itemProviderIdx = (int)$m[1];
                }
                if ($itemProviderIdx !== $providerIdx) {
                    continue;
                }

                $saleMode = trim((string)($historyRow['sale_mode'] ?? ''));
                $saleModeText = $this->saleModeText[$saleMode] ?? $saleMode;
                $startDate = trim((string)($historyRow['sale_start_date'] ?? ''));
                $endDate = trim((string)($historyRow['sale_end_date'] ?? ''));

                $displayDay = $endDate;
                if ($saleMode === 'period' && $startDate !== '' && $endDate !== '') {
                    $displayDay = $startDate . ' ~<br>' . $endDate;
                }

                $originalPrice = (int)($item['sale_price'] ?? ($provider['sale_price'] ?? 0));
                $costPrice = (int)($item['cost_price'] ?? ($provider['order_price'] ?? 0));
                $marginPre = 0;
                if ($originalPrice > 0) {
                    $marginPre = round((($originalPrice - $costPrice) / $originalPrice) * 100, 2);
                }

                $rows[] = [
                    'sale_mode_text' => $saleModeText,
                    'display_day' => $displayDay,
                    'original_price' => $originalPrice,
                    'cost_price' => $costPrice,
                    'margin_pre' => $marginPre,
                    'sale_per' => (string)($item['discount_rate'] ?? 0),
                    'sale_price' => (int)($item['discount_sale_price'] ?? 0),
                    'margin_price' => (int)($item['discount_margin_amount'] ?? 0),
                    'margin_per' => (string)($item['discount_margin_per'] ?? 0),
                    'reg_date' => (string)($historyRow['created_at'] ?? ''),
                    'sale_history_seq' => (int)($historyRow['seq'] ?? 0),
                ];
            }
        }

        return [
            'provider_idx' => $providerIdx,
            'provider_name' => (string)($provider['name'] ?? ''),
            'recent_sale_log' => [
                'last_sale_date' => (string)($provider['last_sale_date'] ?? ''),
            ],
            'rows' => $rows,
        ];
    }

    /**
     * 할인 이력 저장 (신규/수정)
     *
     * @param array $requestData
     * @return array
     */
    public function saveSaleHistory(array $requestData): array
    {
        $mode = trim((string)($requestData['mode'] ?? ''));
        $seq = (int)($requestData['seq'] ?? ($requestData['idx'] ?? 0));

        $saleStatus = trim((string)($requestData['sale_status'] ?? 'wait'));
        $saleMode = trim((string)($requestData['sale_mode'] ?? 'day'));
        $saleStartDate = $this->normalizeDateTime($requestData['sale_start_date'] ?? '');
        $saleEndDate = $this->normalizeDateTime($requestData['sale_end_date'] ?? '');
        $productJson = $this->normalizeProductJson($requestData['product_json'] ?? []);
        $metaJson = $this->normalizeMetaJson($requestData['meta_json'] ?? []);
        $createdBy = (int)($requestData['created_by'] ?? (AuthAdmin::getSession('sess_idx') ?? 0));

        if ($saleStatus === '') {
            throw new \InvalidArgumentException('할인 상태를 선택해주세요.');
        }
        if ($saleMode === '') {
            throw new \InvalidArgumentException('할인 모드를 선택해주세요.');
        }
        if ($saleStartDate === '') {
            throw new \InvalidArgumentException('할인 시작일을 입력해주세요.');
        }

        $saveData = [
            'sale_status' => $saleStatus,
            'sale_mode' => $saleMode,
            'sale_start_date' => $saleStartDate,
            'sale_end_date' => $saleEndDate,
            'product_json' => $productJson,
            'meta_json' => $metaJson,
            'created_by' => $createdBy,
        ];

        $isModify = $mode === 'modify' || $seq > 0;
        if ($isModify) {
            if ($seq <= 0) {
                throw new \InvalidArgumentException('수정할 할인 이력 번호가 없습니다.');
            }

            ProductSaleHistoryModel::where('seq', $seq)->update($saveData);

            return [
                'mode' => 'modify',
                'seq' => $seq,
            ];
        }

        $newSeq = ProductSaleHistoryModel::query()->insertGetId($saveData);

        return [
            'mode' => 'create',
            'seq' => (int)$newSeq,
        ];
    }

    /**
     * 할인 이력 삭제
     *
     * @param array $requestData
     * @return array
     */
    public function deleteSaleHistory(array $requestData): array
    {
        $seq = (int)($requestData['seq'] ?? ($requestData['idx'] ?? 0));
        if ($seq <= 0) {
            throw new \InvalidArgumentException('삭제할 할인 이력 번호가 없습니다.');
        }

        ProductSaleHistoryModel::where('seq', $seq)->delete();

        return [
            'seq' => $seq,
            'deleted' => true,
        ];
    }

    private function normalizeDateTime($value): string
    {
        $value = trim((string)$value);
        if ($value === '') {
            return '';
        }

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return '';
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    private function normalizeProductJson($value): string
    {
        if (is_string($value)) {
            $decoded = json_decode(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'), true);
            if (is_array($decoded)) {
                $encoded = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                return $encoded === false ? '[]' : $encoded;
            }
            return '[]';
        }

        if (is_array($value)) {
            $encoded = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            return $encoded === false ? '[]' : $encoded;
        }

        return '[]';
    }

    private function normalizeMetaJson($value): string
    {
        if (is_string($value)) {
            $decoded = json_decode(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'), true);
            if (is_array($decoded)) {
                $encoded = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                return $encoded === false ? '{}' : $encoded;
            }
            return '{}';
        }

        if (is_array($value)) {
            $encoded = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            return $encoded === false ? '{}' : $encoded;
        }

        return '{}';
    }

    /**
     * meta_json 값을 배열로 안전하게 변환
     *
     * @param mixed $value
     * @return array
     */
    private function decodeMetaJsonToArray($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        $text = trim((string)$value);
        if ($text === '') {
            return [];
        }

        $decoded = json_decode(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'), true);
        return is_array($decoded) ? $decoded : [];
    }

    private function decodeProductJson($value): array
    {
        $decoded = is_array($value) ? $value : json_decode((string)$value, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        return [];
    }

    private function countProducts($productJson): int
    {
        $decoded = $this->decodeProductJson($productJson);

        if (isset($decoded['products']) && is_array($decoded['products'])) {
            return count($decoded['products']);
        }
        if (isset($decoded['data']) && is_array($decoded['data'])) {
            return count($decoded['data']);
        }

        return count($decoded);
    }

    private function buildSalePeriodText(string $startDate, string $endDate): string
    {
        $startDate = trim($startDate);
        $endDate = trim($endDate);

        if ($startDate === '' && $endDate === '') {
            return '-';
        }
        if ($startDate !== '' && $endDate !== '') {
            return $startDate . ' ~ ' . $endDate;
        }
        if ($startDate !== '') {
            return $startDate;
        }

        return $endDate;
    }

    private function formatDateTime($value): string
    {
        $value = trim((string)$value);
        if ($value === '') {
            return '-';
        }

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return '-';
        }

        return date('y.m.d H:i', $timestamp);
    }

    private function getAdminMapFromRows(array $rows): array
    {
        $adminIdxs = [];
        foreach ($rows as $row) {
            $createdBy = (int)($row['created_by'] ?? 0);
            if ($createdBy > 0) {
                $adminIdxs[] = $createdBy;
            }
            $uploadedBy = (int)($row['uploaded_by'] ?? 0);
            if ($uploadedBy > 0) {
                $adminIdxs[] = $uploadedBy;
            }
        }

        $adminIdxs = array_values(array_unique($adminIdxs));
        if (empty($adminIdxs)) {
            return [];
        }

        $adminRows = AdminModel::query()
            ->select(['idx', 'ad_name', 'ad_id'])
            ->whereIn('idx', $adminIdxs)
            ->get()
            ->toArray();

        $adminMap = [];
        foreach ($adminRows as $adminRow) {
            $adminMap[(int)($adminRow['idx'] ?? 0)] = (string)($adminRow['ad_name'] ?? ($adminRow['ad_id'] ?? '-'));
        }

        return $adminMap;
    }
}
