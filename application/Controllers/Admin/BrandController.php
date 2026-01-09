<?php

namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Utils\Pagination;

use App\Services\BrandService;


class BrandController extends BaseClass 
{

    /**
     * 브랜드 목록
     * 
     * @param Request $request
     * @return view
     */
    public function brandList( Request $request )
    {

        try{

            $requestData = $request->all();
            $page = $requestData['page'] ?? 1;

            $brandService = new BrandService();

            $payload = [
                'paging' => true,
                'page' => $page,
                'per_page' => 500,
                'product_count' => true,
            ];

            $brandList = $brandService->getBrandList($payload);

            $pagination = new Pagination(
                $brandList['total'],
                $brandList['per_page'],
                $brandList['current_page'],
                10
            );
            $paginationHtml = $pagination->renderLinks();

            $data = [
                'brandList' => $brandList['data'] ?? [],
                'paginationHtml' => $paginationHtml,
                'pagination' => $pagination->toArray(),
            ];

            return view('admin.product.brand_list', $data)
                ->extends('admin.layout.layout',[
                    'pageGroup2' => 'prd',
                    'pageNameCode' => 'brand'
                ]);

        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }


    /**
     * 브랜드 상세 화면
     * 
     * @param Request $request
     * @param string $idx 브랜드IDX
     * @return view
     */
    public function brandDetail( Request $request, $idx )
    {
        try{

            $requestData = $request->all();

            $brandService = new BrandService();
            $brandInfo = $brandService->getBrandInfo($idx);

            $data = [
                'brandInfo' => $brandInfo ?? [],
            ];

            return view('admin.product.brand_detail', $data)
                ->extends('admin.layout.popup_layout');

        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }


    /**
     * 브랜드 수정
     */
    public function saveBrand( Request $request )
    {
        try{

            $payload = $request->all();
            $files = $request->allFiles();

            $brandService = new BrandService();
            $brandInfo = $brandService->saveBrandInfo($payload, $files);

            return redirect()->back()->with('success', '브랜드 수정 완료');

        } catch (Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    /**
     * 브랜드 신규생성 페이지
     */
    public function brandReg( Request $request )
    {
        try{

            return view('admin.product.brand_create');

        } catch (Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    /**
     * 브랜드 신규생성
     */
    public function createBrand( Request $request )
    {
        try{

            $payload = $request->all();

            $brandService = new BrandService();
            $brandInfo = $brandService->createBrand($payload);

            return response()->json([
                'success' => true,
                'message' => '브랜드 생성 완료',
            ]);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }


}