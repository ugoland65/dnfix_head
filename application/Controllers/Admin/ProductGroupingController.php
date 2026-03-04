<?php

namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Classes\Request;
use App\Core\BaseClass;
use App\Services\ProductGroupingService;
use App\Utils\Pagination;


class ProductGroupingController extends BaseClass
{

    /**
     * 그룹핑 목록
     * 
     * @param Request $request
     * @return View
     */
    public function productGroupingList(Request $request)
    {
        try{

            $requestData = $request->all();

            $normalizeAll = static function ($value): string {
                $normalized = trim((string)$value);
                $normalizedLower = strtolower($normalized);
                if ($normalized === '' || $normalizedLower === 'all' || $normalized === '전체') {
                    return '';
                }
                return $normalized;
            };

            $prd_mode = $normalizeAll($requestData['s_prd_mode'] ?? '');
            $pg_mode = $normalizeAll($requestData['s_pg_mode'] ?? '');
            $pg_state = $requestData['s_pg_state'] ?? '진행';

            $page = $requestData['page'] ?? 1;

            $payload = [
                'prd_mode' => $prd_mode,
                'pg_mode' => $pg_mode,
                'pg_state' => $pg_state,
                'paging' => true,
                'page' => $page,
                'per_page' => 100,
            ];

            $productGroupingService = new ProductGroupingService();
            $productGroupingList = $productGroupingService->getProductGroupingList($payload);

            $pagination = new Pagination(
                $productGroupingList['total'],
                $productGroupingList['per_page'],
                $productGroupingList['current_page'],
                10
            );
            $paginationHtml = $pagination->renderLinks();

            $data = [
                's_prd_mode' => $prd_mode,
                's_pg_mode' => $pg_mode,
                's_pg_state' => $pg_state,
                'paginationHtml' => $paginationHtml,
                'pagination' => $pagination->toArray(),
                'productGroupingList' => $productGroupingList['data'] ?? [],
            ];

            return view('admin.product.grouping', $data)
                ->extends('admin.layout.layout', [
                    'pageGroup2' => 'prd',
                    'pageNameCode' => 'product_grouping_list',
                ]); 

        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }


    /**
     * 그룹핑 지정 페이지
     * 
     * @param Request $request
     * @return View
     */
    public function productGroupingAdd(Request $request)
    {
        try{
            
            $requestData = $request->all();

            $mode = $requestData['mode'] ?? 'prdDB';

            if( $mode == 'product_db' ){
                $prd_mode = 'prdDB';
            }else if( $mode == 'product_stock' ){
                $prd_mode = 'prdDB';
            }else{
                $prd_mode = $mode;
            }
        
            $idxsRaw = $requestData['idxs'] ?? ($requestData['idxs[]'] ?? []);
            if (is_array($idxsRaw)) {
                $idxs = $idxsRaw;
            } elseif (is_string($idxsRaw) && trim($idxsRaw) !== '') {
                $idxs = explode(',', $idxsRaw);
            } else {
                $idxs = [];
            }
            $idxs = array_values(array_filter($idxs, static fn($v) => $v !== null && $v !== ''));

            if( $idxs && count($idxs) > 0 ){
                $prd_count = count($idxs);
            }else{
                $prd_count = 0;
            }

            $productGroupingService = new ProductGroupingService();
            $payload = [
                'pg_state' => '진행',
                'prd_mode' => $prd_mode,
            ];
            $productGroupingForSelect = $productGroupingService->getProductGroupingForSelect($payload) ?? [];

            if( $mode == 'product_db' ){
                $prd_mode_text = '상품 DB';
            }else if( $mode == 'product_stock' ){
                $prd_mode_text = '보유 상품';
            }else if( $mode == 'provider' ){
                $prd_mode_text = '공급사 상품';
            }

            $data = [
                'mode' => $mode,
                'prd_mode' => $prd_mode,
                'prd_mode_text' => $prd_mode_text,
                'idxs' => $idxs,
                'data' => [
                    'prd_count' => $prd_count,
                    'pg_sday' => $requestData['pg_sday'] ?? '',
                    'pg_day' => $requestData['pg_day'] ?? '',
                ],
                'productGroupingForSelect' => $productGroupingForSelect ?? [],
            ];

            return view('admin.product.grouping_add', $data);

        }
        catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * 그룹핑 상품 지정
     * 
     * @param Request $request
     * @return array
     */
    public function productGroupingAddSave(Request $request)
    {
        try{

            $requestData = $request->all();

            $mode = $requestData['mode'] ?? 'prdDB'; //prdDB: 상품DB, provider: 공급사 상품

            $pg_subject = $requestData['pg_subject'] ?? null;
            $public = $requestData['public'] ?? '공개';
            $pg_mode = $requestData['pg_mode'] ?? 'op'; //운영
            $prd_mode = $mode; 
            $pg_state = $requestData['pg_state'] ?? '진행';
            $pg_sday = $requestData['pg_sday'] ?? null;
            $pg_day = $requestData['pg_day'] ?? null;
            $pg_memo = $requestData['pg_memo'] ?? null;
            $idx = $requestData['pg_select'] ?? null;
            $prd_idxs = explode(',', $requestData['prd_idxs'] ?? '');

            $productGroupingService = new ProductGroupingService();
            $payload = [
                'mode' => $mode,
                'idx' => $idx,
                'prd_idxs' => $prd_idxs,
                'pg_subject' => $pg_subject,
                'public' => $public,
                'pg_mode' => $pg_mode,
                'prd_mode' => $prd_mode,
                'pg_state' => $pg_state,
                'pg_sday' => $pg_sday,
                'pg_day' => $pg_day,
                'pg_memo' => $pg_memo,
            ];
            $productGroupingAddSave = $productGroupingService->productGroupingAddSave($payload);

            return response()->json([
                'status' => 'success',
                'message' => '등록되었습니다.',
                'data' => $productGroupingAddSave,
            ]);

        }
        catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * 그룹핑 상세
     * 
     * @param Request $request
     * @return View
     */
    public function productGroupingView(Request $request, $idx)
    {
        try{

            $productGroupingService = new ProductGroupingService();
            $productGrouping = $productGroupingService->getProductGrouping($idx);

            $config_product = config('admin.product');
            $prd_kind_name = $config_product['prd_kind_name'] ?? [];

            $data = [
                'prd_kind_name' => $prd_kind_name,
                'productGrouping' => $productGrouping,
            ];

            return view('admin.product.grouping_view', $data)
                ->extends('admin.layout.layout', [
                    'pageGroup2' => 'prd',
                    'pageNameCode' => 'product_grouping_view',
                ]);

        }
        catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }


    /**
     * 그룹핑 저장
     * 
     * @param Request $request
     * @return array
     */
    public function productGroupingSave(Request $request)
    {
        try{
     
            $requestData = $request->all();
            $idx = $requestData['idx'] ?? null;

            $productGroupingService = new ProductGroupingService();
            $productGroupingSave = $productGroupingService->productGroupingUpdate($requestData);

            return redirect()->to('/admin/product/grouping_view/'.$idx)->with('success', '그룹핑 저장 완료');

        }
        catch (Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    /**
     * 그룹핑 상품 순서 변경
     */
    public function productGroupingProductOrderChange(Request $request)
    {
        try{

            $requestData = $request->all();
            $idx = $requestData['idx'] ?? null;

            $productGroupingService = new ProductGroupingService();
            $productGrouping = $productGroupingService->getProductGrouping($idx);

            $config_product = config('admin.product');
            $prd_kind_name = $config_product['prd_kind_name'] ?? [];

            $data = [
                'idx' => $idx,
                'prd_kind_name' => $prd_kind_name,
                'productGrouping' => $productGrouping,
            ];

            return view('admin.product.grouping_order', $data);

        }
        catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }


    /**
     * 그룹핑 상품 순서 변경 저장
     * 
     * @param Request $request
     * @return array
     */
    public function productGroupingProductOrderChangeSave(Request $request)
    {
        try{
            
        }
        catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}