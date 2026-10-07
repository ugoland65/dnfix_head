<?php

namespace App\Controllers\Admobile;

use App\Auth\AdmobileSession;
use App\Classes\Request;
use App\Core\BaseClass;
use App\Services\ProductGroupingService;
use App\Utils\Pagination;
use Throwable;

class ProductGroupingController extends BaseClass
{
    /**
     * 모바일 상품 그룹핑 목록.
     */
    public function list(Request $request)
    {
        if (!AdmobileSession::isAuthenticated()) {
            return redirect('/admobile/login');
        }

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

            $prdMode = $normalizeAll($requestData['s_prd_mode'] ?? '');
            $pgMode = $normalizeAll($requestData['s_pg_mode'] ?? '');
            $pgState = (string)($requestData['s_pg_state'] ?? '진행');
            $page = max(1, (int)($requestData['page'] ?? 1));

            $productGroupingList = (new ProductGroupingService())->getProductGroupingList([
                'prd_mode' => $prdMode,
                'pg_mode' => $pgMode,
                'pg_state' => $pgState,
                'paging' => true,
                'page' => $page,
                'per_page' => 20,
            ]);

            $pagination = new Pagination(
                (int)($productGroupingList['total'] ?? 0),
                (int)($productGroupingList['per_page'] ?? 20),
                (int)($productGroupingList['current_page'] ?? $page),
                5
            );

            return view('admobile.product.grouping', [
                's_prd_mode' => $prdMode,
                's_pg_mode' => $pgMode,
                's_pg_state' => $pgState,
                'paginationHtml' => $pagination->renderLinks(),
                'pagination' => $pagination->toArray(),
                'productGroupingList' => $productGroupingList['data'] ?? [],
                'flashSuccess' => $_SESSION['_flash']['success'] ?? '',
                'flashError' => $_SESSION['_flash']['error'] ?? '',
            ])->extends('admobile.layout.layout', [
                'pageTitle' => '상품 그룹핑',
            ]);
        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }

    /**
     * 모바일 상품 그룹핑 상세.
     */
    public function view(Request $request, $idx)
    {
        if (!AdmobileSession::isAuthenticated()) {
            return redirect('/admobile/login');
        }

        try {
            $productGrouping = (new ProductGroupingService())->getProductGrouping((int)$idx);
            $productConfig = config('admin.product');

            return view('admobile.product.grouping_view', [
                'prd_kind_name' => $productConfig['prd_kind_name'] ?? [],
                'productGrouping' => $productGrouping,
                'flashSuccess' => $_SESSION['_flash']['success'] ?? '',
                'flashError' => $_SESSION['_flash']['error'] ?? '',
            ])->extends('admobile.layout.layout', [
                'pageTitle' => '상품 그룹핑 상세',
            ]);
        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }

    /**
     * 모바일 상품 그룹핑 저장.
     */
    public function save(Request $request)
    {
        if (!AdmobileSession::isAuthenticated()) {
            return redirect('/admobile/login');
        }

        $requestData = $request->all();
        $idx = (int)($requestData['idx'] ?? 0);

        try {
            (new ProductGroupingService())->productGroupingUpdate($requestData);

            if ($idx > 0) {
                return redirect()->to('/admobile/product/grouping/view/' . $idx)->with('success', '그룹핑 저장 완료');
            }

            return redirect()->to('/admobile/product/grouping')->with('success', '그룹핑 저장 완료');
        } catch (Throwable $e) {
            if ($idx > 0) {
                return redirect()->to('/admobile/product/grouping/view/' . $idx)->with('error', $e->getMessage());
            }

            return redirect()->to('/admobile/product/grouping')->with('error', $e->getMessage());
        }
    }
}
