<?php

namespace App\Controllers\Admin;

use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Services\SaleHistoryService;
use App\Utils\Pagination;

class SaleHistoryController extends BaseClass
{

    /**
     * 할인 이력 목록
     *
     * @param Request $request
     * @return mixed
     */
    public function saleHistoryList(Request $request)
    {
        try {
            $requestData = $request->all();

            $normalizeAll = static function ($value): string {
                $normalized = trim((string)$value);
                $normalizedLower = strtolower($normalized);
                if ($normalized === '' || $normalizedLower === 'all' || $normalized === '전체') {
                    return '';
                }
                return $normalized;
            };

            $saleStatus = $normalizeAll($requestData['s_sale_status'] ?? '');
            $saleMode = $normalizeAll($requestData['s_sale_mode'] ?? '');
            $page = (int)($requestData['page'] ?? 1);

            $saleHistoryService = new SaleHistoryService();
            $saleHistoryList = $saleHistoryService->getSaleHistoryList([
                'sale_status' => $saleStatus,
                'sale_mode' => $saleMode,
                'paging' => true,
                'page' => $page,
                'per_page' => 100,
            ]);

            $pagination = new Pagination(
                $saleHistoryList['total'] ?? 0,
                $saleHistoryList['per_page'] ?? 100,
                $saleHistoryList['current_page'] ?? 1,
                10
            );

            $data = [
                's_sale_status' => $saleStatus,
                's_sale_mode' => $saleMode,
                'saleHistoryList' => $saleHistoryList['data'] ?? [],
                'paginationHtml' => $pagination->renderLinks(),
                'pagination' => $pagination->toArray(),
            ];

            return view('admin.product.sale_history', $data)
                ->extends('admin.layout.layout', [
                    'pageGroup2' => 'prd',
                    'pageNameCode' => 'sale_history_list',
                ]);
        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }

    /**
     * 할인 이력 생성 페이지
     *
     * @param Request $request
     * @return mixed
     */
    public function saleHistoryCreate(Request $request)
    {

        $config_product = config('admin.product');
        $prd_kind_name = $config_product['prd_kind_name'] ?? [];

        $data = [
            'prd_kind_name' => $prd_kind_name,
        ];

        return view('admin.product.sale_history_create', $data)
            ->extends('admin.layout.layout', [
                'pageGroup2' => 'prd',
                'pageNameCode' => 'sale_history_create',
            ]);
    }


    /**
     * 할인 이력 상세
     *
     * @param Request $request
     * @param int|string $idx
     * @return mixed
     */
    public function saleHistoryDetail(Request $request, $idx)
    {
        try {

            $saleHistoryService = new SaleHistoryService();
            $saleHistory = $saleHistoryService->getSaleHistoryDetail((int)$idx);

            $data = [
                'saleHistory' => $saleHistory,
            ];
    
            return view('admin.product.sale_history_detail', $data)
                ->extends('admin.layout.layout', [
                    'pageGroup2' => 'prd',
                    'pageNameCode' => 'sale_history_list',
                ]);

        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * 할인 여러가지 액션
     *
     * @param Request $request
     * @return mixed
     */
    public function saleHistoryAction(Request $request)
    {
        try {
            $requestData = $request->all();
            
            $actionMode = $requestData['action_mode'] ?? null;

            $saleHistoryService = new SaleHistoryService();
            switch ($actionMode) {

                case 'load_random_product':
                    $result = $saleHistoryService->loadRandomProduct($requestData);
                    $message = '할인 이력 랜덤 상품 불러오기 완료';
                    break;

                case 'exclude_discount_product':
                    $result = $saleHistoryService->excludeDiscountProducts($requestData);
                    $message = '선택한 상품을 할인대상에서 제외했습니다.';
                    break;

                case 'load_godo_goods_info_by_stock_codes':
                    $result = $saleHistoryService->getGodoGoodsInfoByStockCodes($requestData);
                    $message = '고도몰 상품 검수';
                    break;

                case 'refresh_current_product_list':
                    $result = $saleHistoryService->refreshCurrentProductList($requestData);
                    $message = '현재 목록 새로고침 완료';
                    break;

                case 'insert_product_by_code':
                    $result = $saleHistoryService->insertProductByCode($requestData);
                    $message = '상품 삽입 완료';
                    break;

                case 'update_godo_goods_cost_price':
                    $result = $saleHistoryService->updateGodoGoodsCostPriceFromInspection($requestData);
                    $message = '판매가/원가 반영 완료';
                    break;

                case 'update_godo_goods_cost_price_bulk':
                    $result = $saleHistoryService->updateGodoGoodsCostPriceBulkFromInspection($requestData);
                    $message = '판매가/원가 일괄 반영 완료';
                    break;

                case 'create_godo_time_sale_from_history':
                    $result = $saleHistoryService->createGodoTimeSaleFromHistory($requestData);
                    $message = '고도몰 타임세일 등록 완료';
                    break;

                case 'create_godo_time_sale_group_from_history':
                    $result = $saleHistoryService->createGodoTimeSaleGroupFromHistory($requestData);
                    $message = '고도몰 타임세일 그룹 등록 완료';
                    break;

                case 'move_sale_history_discount_group_item':
                    $result = $saleHistoryService->moveSaleHistoryDiscountGroupItem($requestData);
                    $message = '할인율 그룹 이동 저장 완료';
                    break;
                    
                case 'restore_sale_date_from_history_upload':
                    $result = $saleHistoryService->restoreSaleDateFromHistoryUpload($requestData);
                    $message = '할인일 원상복구 완료';
                    break;

                default:
                    throw new \InvalidArgumentException('유효하지 않은 action_mode 입니다.');
            }

            return response()->json([
                'status' => 'success',
                'message' => $message ?? '완료',
                'data' => $result,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }


    /**
     * 할인 이력 저장
     *
     * @param Request $request
     * @return mixed
     */
    public function saveSaleHistory(Request $request)
    {
        try {
            $requestData = $request->all();
            $saleHistoryService = new SaleHistoryService();
            $result = $saleHistoryService->saveSaleHistory($requestData);

            return response()->json([
                'status' => 'success',
                'message' => '할인 이력이 저장되었습니다.',
                'data' => $result,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * 할인 이력 삭제
     *
     * @param Request $request
     * @return mixed
     */
    public function deleteSaleHistory(Request $request)
    {
        try {
            $requestData = $request->all();
            $saleHistoryService = new SaleHistoryService();
            $result = $saleHistoryService->deleteSaleHistory($requestData);

            return response()->json([
                'status' => 'success',
                'message' => '할인 이력이 삭제되었습니다.',
                'data' => $result,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
