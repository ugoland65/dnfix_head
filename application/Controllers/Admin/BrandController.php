<?php

namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Utils\Pagination;

use App\Services\BrandService;
use App\Models\BrandModel;


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

            /*
                $brandRows = BrandModel::query()
                    ->select([
                        'bd_matching_brand',
                        'bd_matching_cate',
                        'bd_api_info',
                        'bd_api_introduce',
                        'bd_kind',
                        'BD_NAME_EN',
                        'BD_NAME_GROUP',
                        'BD_NAME_EN_GROUP',
                    ])
                    ->whereNotNull('bd_matching_brand')
                    ->where('bd_matching_brand', '!=', '')
                    ->get()
                    ->toArray();

                $lines = [];
                foreach ($brandRows as $row) {
                    $apiInfo = json_decode((string)($row['bd_api_info'] ?? ''), true);
                    if (!is_array($apiInfo)) {
                        $apiInfo = [];
                    }
                    $bdKind = json_decode((string)($row['bd_kind'] ?? ''), true);
                    if (!is_array($bdKind)) {
                        $bdKind = [];
                    }

                    $logo = (string)($apiInfo['logo'] ?? '');
                    $goodsCateCd = str_replace("'", "''", (string)($row['bd_matching_cate'] ?? ''));
                    $thumbUrl = str_replace("'", "''", $logo);
                    $brandNmEnRaw = html_entity_decode((string)($row['BD_NAME_EN'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $brandNmEn = str_replace("'", "''", $brandNmEnRaw);
                    $initialKr = str_replace("'", "''", (string)($row['BD_NAME_GROUP'] ?? ''));
                    $initialEn = str_replace("'", "''", (string)($row['BD_NAME_EN_GROUP'] ?? ''));
                    $brandDescRaw = html_entity_decode((string)($row['bd_api_introduce'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $brandDesc = str_replace("'", "''", $brandDescRaw);
                    $cateCd = str_replace("'", "''", (string)($row['bd_matching_brand'] ?? ''));    

                    $categoryKeys = [
                        'ona',
                        'breast',
                        'gel',
                        'condom',
                        'annal',
                        'prostate',
                        'care',
                        'dildo',
                        'vibe',
                        'suction',
                        'man',
                        'nipple',
                        'cos',
                        'perfume',
                        'bdsm',
                    ];
                    $categoryTypesArray = [];
                    foreach ($categoryKeys as $key) {
                        if (($bdKind[$key] ?? '') === 'Y') {
                            $categoryTypesArray[] = $key;
                        }
                    }
                    $categoryTypes = json_encode($categoryTypesArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    if ($categoryTypes === false) {
                        $categoryTypes = '[]';
                    }
                    $categoryTypes = str_replace("'", "''", $categoryTypes);

                    $displayJsonData = [
                        'logo' => (string)($apiInfo['logo'] ?? ''),
                        'bg_img' => (string)($apiInfo['bg'] ?? ''),
                        'info_class' => (string)($apiInfo['info_class'] ?? ''),
                        'bg_rgb' => (string)($apiInfo['bg_rgb'] ?? ''),
                        'logo_mobile' => (string)($apiInfo['logo_mobile'] ?? ''),
                        'bg_img_mobile' => (string)($apiInfo['bg_mobile'] ?? ''),
                    ];
                    $displayJson = json_encode($displayJsonData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    if ($displayJson === false) {
                        $displayJson = '{}';
                    }
                    $displayJson = str_replace("'", "''", $displayJson);


                    
                    $lines[] = "UPDATE dnfix_brand SET\n"
                        . "goodsCateCd = '" . $goodsCateCd . "',\n"
                        . "thumbUrl = '" . $thumbUrl . "',\n"
                        . "brandNmEn = '" . $brandNmEn . "',\n"
                        . "initialKr = '" . $initialKr . "',\n"
                        . "initialEn = '" . $initialEn . "',\n"
                        . "categoryTypes = '" . $categoryTypes . "',\n"
                        . "displayJson = '" . $displayJson . "',\n"
                        . "brandDesc = '" . $brandDesc . "'\n"
                        . "WHERE cateCd = '" . $cateCd . "';";
                }

                header('Content-Type: text/plain; charset=UTF-8');
                echo implode("\n\n", $lines);
                exit;

            */

            $page = $requestData['page'] ?? 1;
            $sort_kind = $requestData['sort_kind'] ?? "";
            $search_value = $requestData['search_value'] ?? "";

            $brandService = new BrandService();

            $payload = [
                'paging' => true,
                'page' => $page,
                'per_page' => 500,
                'product_count' => true,
                'sort_kind' => $sort_kind,
                'search_value' => $search_value,
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
                'sort_kind' => $sort_kind,
                'search_value' => $search_value,
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

            // AJAX(X-Requested-With) 요청일 때만 JSON 응답
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => '브랜드 수정 완료',
                    'data' => $brandInfo ?? []
                ]);
            }

            return redirect()->back()->with('success', '브랜드 수정 완료');

        } catch (Throwable $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }

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