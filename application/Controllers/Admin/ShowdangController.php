<?php

namespace App\Controllers\Admin;

use App\Core\BaseClass;
use App\Classes\Request;
use App\Models\ProductModel;
use App\Models\ProductStockModel;
use App\Services\GodoApiService;
use App\Services\ProductStockService;
use App\Services\ProductActionService;
use Throwable;

class ShowdangController extends BaseClass {

    public function __construct() {
        parent::__construct();
    }

    public function godoHbtiStatisticsIndex($mode='hbti') {

        $godoApiService = new GodoApiService();

        $result = $godoApiService->getHbtiStatistics($mode);
        
        //dd($result);
        $data = [
            'hbtiCount' => $result
        ];

        return $data;

    }

    /**
     * 월간 할인 관리
     */
    public function monthlyDiscountManagement(Request $request)
    {
        try {

            $cateCd = '014005'; //월간 할인 카테고리 코드
            $godoApiService = new GodoApiService();
            $godoGoods = $godoApiService->getGodoGoodsInfoByCategory($cateCd);

            if (!is_array($godoGoods)) {
                $godoGoods = [];
            }

            $goodsCdList = [];
            $goodsNoFallbackList = [];
            foreach ($godoGoods as $godoGood) {
                if (!is_array($godoGood)) {
                    continue;
                }
                $goodsCd = trim((string)($godoGood['goodsCd'] ?? ''));
                if ($goodsCd !== '' && ctype_digit($goodsCd)) {
                    $goodsCdList[] = $goodsCd;
                } else {
                    $goodsNo = trim((string)($godoGood['goodsNo'] ?? ''));
                    if ($goodsNo !== '') {
                        $goodsNoFallbackList[] = $goodsNo;
                    }
                }
            }
            $goodsCdList = array_values(array_unique($goodsCdList));
            $goodsNoFallbackList = array_values(array_unique($goodsNoFallbackList));

            $productStockService = new ProductStockService();
            $productDataMap = $productStockService->getProductStockWhereIn($goodsCdList);

            // goodsCd가 비숫자/빈값인 경우 goodsNo -> cd_godo_code 기준으로 보조 매칭
            $productDataByGoodsNo = [];
            if (!empty($goodsNoFallbackList)) {
                $fallbackRows = ProductModel::query()
                    ->select([
                        'COMPARISON_DB.cd_godo_code',
                        'prd_stock.ps_idx',
                        'prd_stock.ps_stock',
                        'prd_stock.is_sale_month',
                        'COMPARISON_DB.CD_IDX',
                        'COMPARISON_DB.CD_CODE',
                        'COMPARISON_DB.CD_NAME',
                        'COMPARISON_DB.cd_cost_price',
                        'COMPARISON_DB.cd_size_fn',
                        'COMPARISON_DB.cd_add_img',
                        'COMPARISON_DB.img_mode',
                        'COMPARISON_DB.CD_IMG',
                    ])
                    ->join('prd_stock as prd_stock', 'prd_stock.ps_prd_idx', '=', 'COMPARISON_DB.CD_IDX', 'LEFT')
                    ->whereIn('COMPARISON_DB.cd_godo_code', $goodsNoFallbackList)
                    ->get();
                $fallbackRows = is_array($fallbackRows) ? $fallbackRows : $fallbackRows->toArray();
                foreach ($fallbackRows as $fallbackRow) {
                    if (!is_array($fallbackRow)) {
                        continue;
                    }
                    $godoCode = trim((string)($fallbackRow['cd_godo_code'] ?? ''));
                    if ($godoCode === '') {
                        continue;
                    }
                    $productDataByGoodsNo[$godoCode] = $fallbackRow;
                }
            }

            $matchedPsIdxList = [];
            foreach ($godoGoods as &$godoGood) {
                if (!is_array($godoGood)) {
                    continue;
                }
                $goodsCd = trim((string)($godoGood['goodsCd'] ?? ''));
                $goodsNo = trim((string)($godoGood['goodsNo'] ?? ''));
                $godoGood['product_data'] = [];
                if ($goodsCd !== '' && !empty($productDataMap[$goodsCd])) {
                    $godoGood['product_data'] = $productDataMap[$goodsCd];
                } elseif ($goodsNo !== '' && !empty($productDataByGoodsNo[$goodsNo])) {
                    $godoGood['product_data'] = $productDataByGoodsNo[$goodsNo];
                }
                if (!empty($godoGood['product_data']['ps_idx'])) {
                    $matchedPsIdxList[] = (int)$godoGood['product_data']['ps_idx'];
                }
            }
            unset($godoGood);
            $matchedPsIdxList = array_values(array_unique(array_filter($matchedPsIdxList)));

            // 월간할인 대상(is_sale_month=1) 중, 고도 카테고리 매칭에 없는 상품 조회
            $missingMonthlyProductsQuery = ProductStockModel::query()
                ->select([
                    'prd_stock.ps_idx',
                    'prd_stock.ps_prd_idx',
                    'prd_stock.ps_stock',
                    'prd_stock.is_sale_month',
                    'cd.CD_IDX',
                    'cd.CD_NAME',
                    'cd.CD_CODE',
                    'cd.CD_KIND_CODE',
                    'cd.CD_BRAND_IDX',
                    'cd.CD_IMG',
                    'cd.img_mode',
                    'cd.cd_sale_price',
                    'cd.cd_cost_price',
                    'cd.cd_godo_code',
                    'brand.BD_NAME as brand_name',
                ])
                ->join('COMPARISON_DB as cd', 'prd_stock.ps_prd_idx', '=', 'cd.CD_IDX', 'LEFT')
                ->join('BRAND_DB as brand', 'brand.BD_IDX', '=', 'cd.CD_BRAND_IDX', 'LEFT')
                ->where('prd_stock.is_sale_month', 1);

            if (!empty($matchedPsIdxList)) {
                $missingMonthlyProductsQuery->whereNotIn('prd_stock.ps_idx', $matchedPsIdxList);
            }
            $missingMonthlyProducts = $missingMonthlyProductsQuery
                ->orderBy('prd_stock.ps_idx', 'ASC')
                ->get();
            $missingMonthlyProducts = is_array($missingMonthlyProducts) ? $missingMonthlyProducts : $missingMonthlyProducts->toArray();

            // 미매칭 월간할인 상품의 cd_godo_code 기준으로 고도몰 상품 정보 매칭
            $missingGodoCodes = [];
            foreach ($missingMonthlyProducts as $missingProduct) {
                if (!is_array($missingProduct)) {
                    continue;
                }
                $godoCode = trim((string)($missingProduct['cd_godo_code'] ?? ''));
                if ($godoCode !== '') {
                    $missingGodoCodes[] = $godoCode;
                }
            }
            $missingGodoCodes = array_values(array_unique($missingGodoCodes));
            $godoGoodsByNo = [];
            if (!empty($missingGodoCodes)) {
                $goodsNos = implode(',', $missingGodoCodes);
                try {
                    $godoGoodsByGoodsNoResponse = $godoApiService->getGodoGoodsInfoByGoodsNo($goodsNos);
                    $godoGoodsByGoodsNoRows = [];
                    if (is_array($godoGoodsByGoodsNoResponse['data'] ?? null)) {
                        $godoGoodsByGoodsNoRows = $godoGoodsByGoodsNoResponse['data'];
                    } elseif (is_array($godoGoodsByGoodsNoResponse)) {
                        $godoGoodsByGoodsNoRows = $godoGoodsByGoodsNoResponse;
                    }
                    foreach ($godoGoodsByGoodsNoRows as $godoRow) {
                        if (!is_array($godoRow)) {
                            continue;
                        }
                        $goodsNoKey = trim((string)($godoRow['goodsNo'] ?? ''));
                        if ($goodsNoKey === '') {
                            continue;
                        }
                        $godoGoodsByNo[$goodsNoKey] = $godoRow;
                    }
                } catch (\Throwable $ignore) {
                    // 고도몰 보조 조회 실패 시에도 기본 목록은 노출한다.
                }
            }

            $configProduct = config('admin.product');
            $prdKindNameMap = $configProduct['prd_kind_name'] ?? [];
            foreach ($missingMonthlyProducts as &$missingProduct) {
                if (!is_array($missingProduct)) {
                    continue;
                }
                $kindCode = trim((string)($missingProduct['CD_KIND_CODE'] ?? ''));
                $missingProduct['prd_kind_name'] = (string)($prdKindNameMap[$kindCode] ?? '미지정');
                $godoCode = trim((string)($missingProduct['cd_godo_code'] ?? ''));
                $missingProduct['godo_goods_data'] = $godoCode !== '' ? ($godoGoodsByNo[$godoCode] ?? []) : [];
            }
            unset($missingProduct);

            // 품절 상품 집계 (뷰의 판정 로직과 동일)
            $soldOutProducts = [];
            foreach ($godoGoods as $godoGood) {
                if (!is_array($godoGood)) {
                    continue;
                }
                $stockFl = strtolower(trim((string)($godoGood['stockFl'] ?? '')));
                $totalStock = (int)($godoGood['totalStock'] ?? 0);
                $isSoldOut = ($stockFl === 'y' && $totalStock === 0);
                if ($isSoldOut) {
                    $soldOutProducts[] = $godoGood;
                }
            }
            $soldOutProductCount = count($soldOutProducts);

            
            $data = [
                'godoGoods' => $godoGoods,
                'missingMonthlyProducts' => $missingMonthlyProducts,
                'soldOutProductCount' => $soldOutProductCount,
            ];

            return view('admin.showdang.monthly_discount_management', $data)
                ->extends('admin.layout.layout', [
                    'pageGroup2' => 'prd',
                    'pageNameCode' => 'monthly_discount_management',
                ]);
                
                
        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }

    /**
     * 월간 할인 관리 액션
     * 
     * 상품 액션에 있음 삭제예정
     */
    /* @deprecated
    public function monthlyDiscountManagementAction(Request $request)
    {
        try {

            $requestData = $request->all();

            $actionMode = trim((string)($requestData['action_mode'] ?? ''));

            $goodsNo = trim((string)($requestData['goods_no'] ?? ''));
            $prdIdx = trim((string)($requestData['prd_idx'] ?? ''));
            $prdStockIdx = trim((string)($requestData['prd_stock_idx'] ?? ''));
            $fixedPrice = trim((string)($requestData['fixed_price'] ?? ''));
            $goodsPrice = trim((string)($requestData['goods_price'] ?? ''));

            if ($actionMode === '') {
                throw new \Exception('action_mode가 비어있습니다.');
            }
            if ($goodsNo === '') {
                throw new \Exception('goods_no가 비어있습니다.');
            }

            $godoApiService = new GodoApiService();

            if ($actionMode === 'release_monthly_discount') {

                $payload = [
                    'goodsNo' => $goodsNo,
                    'prdIdx' => $prdIdx,
                    'prdStockIdx' => $prdStockIdx,
                    'fixedPrice' => $fixedPrice,
                    'goodsPrice' => $goodsPrice
                ];

                $productActionService = new ProductActionService();
                $result = $productActionService->prdReleaseMonthlyDiscount($payload);
                if (!is_array($result)) {
                    throw new \Exception('고도몰 응답 형식이 올바르지 않습니다.');
                }

                $status = strtolower(trim((string)($result['status'] ?? '')));
                if ($status !== '' && $status !== 'success') {
                    $message = trim((string)($result['message'] ?? ''));
                    throw new \Exception($message !== '' ? $message : '할인해제 처리에 실패했습니다.');
                }

                return response()->json([
                    'success' => true,
                    'message' => trim((string)($result['message'] ?? '할인해제가 완료되었습니다.')),
                    'data' => $result['data'] ?? $result,
                ]);
            }

            throw new \Exception('유효하지 않은 action_mode 입니다.');
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
         */

}

