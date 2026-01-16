<?php

namespace App\Services;

use Exception;  
use Throwable;
use App\Models\BrandModel;
use App\Models\ProductModel;
use App\Models\ProductPartnerModel;
use App\Classes\UploadedFile;

class BrandService
{


    /**
     * 브랜드 목록 조회
     * 
     * @param array $criteria 검색 조건
     * @return array
     */
    public function getBrandList($criteria)
    {

        $paging = $criteria['paging'] ?? true;
        $perPage = $criteria['per_page'] ?? 100;
        $page = $criteria['page'] ?? 1;
        $productCount = $criteria['product_count'] ?? false;
        $sort_kind = $criteria['sort_kind'] ?? 'idx';
        $search_value = $criteria['search_value'] ?? '';

        $countSortKinds = ['have_stock_count', 'product_count', 'partner_count'];

        // 기본 테이블에 별칭 부여 (서브쿼리 조인 시 사용)
        $query = BrandModel::query()
            ->from('BRAND_DB as B')
            ->when($search_value, function($query) use ($search_value) {
                $query->where('B.BD_NAME', 'like', '%' . $search_value . '%');
                $query->orWhere('B.BD_NAME_EN', 'like', '%' . $search_value . '%');
            })
            ->when($sort_kind, function($query) use ($sort_kind) {
                // 카운트 정렬은 조인 후에 처리
                if(in_array($sort_kind, ['have_stock_count', 'product_count', 'partner_count'])){
                    return;
                }
                if($sort_kind == 'name'){
                    $query->orderBy('B.BD_NAME', 'asc');
                }elseif($sort_kind == 'name_en'){
                    $query->orderBy('B.BD_NAME_EN', 'asc');
                }elseif($sort_kind == 'updated_at'){
                    $query->orderBy('B.updated_at', 'desc');
                }else{
                    $query->orderBy('B.BD_IDX', 'desc');
                }
            });

        if ($productCount) {

            // COMPARISON_DB(= ProductModel) + prd_stock 를 한번에 집계 (중복 방지 위해 DISTINCT)
            $productAggSub = ProductModel::query()
                ->from('COMPARISON_DB as P')
                ->leftJoin('prd_stock as D', 'D.ps_prd_idx', '=', 'P.CD_IDX')
                ->selectRaw('P.CD_BRAND_IDX as brand_idx')
                ->selectRaw('COUNT(DISTINCT P.CD_IDX) as product_count')
                ->selectRaw('COUNT(D.ps_idx) as stock_count')
                ->selectRaw('SUM(CASE WHEN D.ps_stock > 0 THEN 1 ELSE 0 END) as have_stock_count')
                ->whereNotNull('P.CD_BRAND_IDX')
                ->groupBy('P.CD_BRAND_IDX');
    
            // prd_partner(= ProductPartnerModel)에서 브랜드별 파트너 상품 수 집계
            $partnerCountSub = ProductPartnerModel::query()
                ->from('prd_partner as PP')
                ->selectRaw('PP.brand_idx as brand_idx, COUNT(*) as partner_count')
                ->whereNotNull('PP.brand_idx')
                ->groupBy('PP.brand_idx');

            // QueryBuilder는 leftJoinSub/addSelect 미지원 → joinSub + select로 구성
            $query
                ->joinSub($productAggSub, 'PC', function ($join) {
                    $join->on('PC.brand_idx', '=', 'B.BD_IDX');
                }, 'LEFT')
                ->joinSub($partnerCountSub, 'PPC', function ($join) {
                    $join->on('PPC.brand_idx', '=', 'B.BD_IDX');
                }, 'LEFT')
                ->select([
                    'B.*',
                    'COALESCE(PC.product_count, 0) as product_count',
                    'COALESCE(PC.stock_count, 0) as stock_count',
                    'COALESCE(PC.have_stock_count, 0) as have_stock_count',
                    'COALESCE(PPC.partner_count, 0) as partner_count',
                    '(COALESCE(PC.product_count, 0) + COALESCE(PPC.partner_count, 0)) as total_product_count',
                ]);

            // 카운트 기반 정렬 처리
            if ($sort_kind === 'have_stock_count') {
                $query->orderByRaw('COALESCE(PC.have_stock_count, 0) DESC');
            } elseif ($sort_kind === 'product_count') {
                $query->orderByRaw('COALESCE(PC.product_count, 0) DESC');
            } elseif ($sort_kind === 'partner_count') {
                $query->orderByRaw('COALESCE(PPC.partner_count, 0) DESC');
            }
        } else {
            // productCount가 false인데 카운트 기반 정렬을 요청한 경우 기본 정렬로 처리
            if (in_array($sort_kind, $countSortKinds)) {
                $query->orderBy('B.BD_IDX', 'desc');
            }
        }

        $result = $paging ? $query->paginate($perPage, $page)
            : $query->get()->toArray();

        foreach ($result['data'] as $key => $value) {
            $result['data'][$key]['bd_api_info'] = json_decode($value['bd_api_info'], true);
            $result['data'][$key]['bd_kind'] = json_decode($value['bd_kind'], true);
        }

        return $result;

    }


    /**
     * 브랜드 셀렉트바를 위한 조회
     * 
     * @param array|null $extraData 추가 파라미터
     * └ $extraData {bool} $extraData['listActive'] : 목록 화면에서 사용 여부
     * @return array
     */
    public function getBrandForSelect($extraData=null) 
    {
        
        $query = BrandModel::select('BD_IDX', 'BD_NAME')
            ->orderBy('BD_NAME', 'asc');

        // null-safe 처리: $extraData가 배열이고 'listActive' 키가 존재하며 true일 경우에만 조건 추가
        if( is_array($extraData) && !empty($extraData['listActive']) ){
            $query->where('BD_LIST_ACTIVE', 'Y');
        }

        $brandList = $query->get()
            ->toArray();

        return $brandList;

    }


    /**
     * 브랜드 목록 조회
     * 
     * @param array $criteria 검색 조건
     * @return array
     */
    public function getOnadbBrandList($criteria)
    {

        $brandList = BrandModel::query()
            ->where('bd_onadb_active', 'Y')
            ->orderBy('bd_onadb_sort_num', 'asc')
            ->get()
            ->toArray();

        return $brandList;

    }


    /**
     * 브랜드 정보 조회
     * 
     * @param array $criteria 검색 조건
     * @return array
     */
    public function getBrandInfo($idx)
    {
        $brandInfo = BrandModel::query()
            ->where('BD_IDX', $idx)
            ->first()
            ->toArray();

        $brandInfo['bd_api_info'] = json_decode($brandInfo['bd_api_info'], true);
        if (!is_array($brandInfo['bd_api_info'])) {
            $brandInfo['bd_api_info'] = [];
        }

        $brandInfo['bd_kind'] = json_decode($brandInfo['bd_kind'], true);
        if (!is_array($brandInfo['bd_kind'])) {
            $brandInfo['bd_kind'] = [];
        }

        return $brandInfo;

    }

    /**
     * 브랜드 정보 수정
     * 
     * @param array $data 수정 데이터
     * @param array $files 업로드된 파일
     * @return array
     */
    public function saveBrandInfo($data, $files)
    {

        try{

            //dd($data);

            $idx = $data['idx'] ?? 0;
            $modifyBdLogo = $data['modify_bd_logo'] ?? '';

            $bdName = $data['bd_name'] ?? ''; //이름(국문)
            $bdNameEn = $data['bd_name_en'] ?? ''; //이름(영문)
            $bdNameGroup = $data['bd_name_group'] ?? ''; //한글초성
            $bdNameEnGroup = $data['bd_name_en_group'] ?? ''; //알파벳 초성
            $bdActive = $data['bd_active'] ?? '';
            $bdListActive = $data['bd_list_active'] ?? '';
            $bdDomain = $data['bd_domain'] ?? ''; //홈페이지
            $bdIntroduce = $data['bd_introduce'] ?? ''; //간략소개
            $bdCode = $data['bd_code'] ?? ''; //브랜드 코드
            $bdKindCode = $data['bd_kind_code'] ?? ''; //구분코드   
            $bdCateNo = $data['bd_cate_no'] ?? 0; //카페24 카테고리 넘버
            $bdMatchingCate = $data['bd_matching_cate'] ?? ''; //고도몰 카테고리 코드
            $bdMatchingBrand = $data['bd_matching_brand'] ?? ''; //고도몰 브랜드 코드
            $bdApiIntroduce = $data['bd_api_introduce'] ?? '';
            //$bdShowdangActive = $data['bd_showdang_active'] ?? '';
            $bdOnadbActive = $data['bd_onadb_active'] ?? '';
            $bdOnadbSortNum = $data['bd_onadb_sort_num'] ?? 0;
            $bdMemo = $data['bd_memo'] ?? '';

            // 로고 업로드 처리 (새 파일이 없으면 기존 값 유지)
            $bdLogo = $modifyBdLogo;
            if (!empty($files['bd_logo'])) {
                $uploaded = new UploadedFile($files['bd_logo']);
                if ($uploaded->isValid()) {
                    // 기본 업로드 경로: /data/brand_logo (웹 루트 기준)
                    $uploadDir = ($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__, 2)) . '/data/brand_logo';
                    $savedPath = $uploaded->moveImage($uploadDir);
                    $bdLogo = basename($savedPath); // 파일명만 저장
                }
            }
            
            //브랜드 카테고리
            $bdKindAry = [
                'ona' => $data['bd_kind_ona'] ?? 'N',
                'breast' => $data['bd_kind_breast'] ?? 'N',
                'gel' => $data['bd_kind_gel'] ?? 'N',
                'condom' => $data['bd_kind_condom'] ?? 'N',
                'annal' => $data['bd_kind_annal'] ?? 'N',
                'prostate' => $data['bd_kind_prostate'] ?? 'N',
                'care' => $data['bd_kind_care'] ?? 'N',
                'dildo' => $data['bd_kind_dildo'] ?? 'N',
                'vibe' => $data['bd_kind_vibe'] ?? 'N',
                'suction' => $data['bd_kind_suction'] ?? 'N',
                'man' => $data['bd_kind_man'] ?? 'N',
                'nipple' => $data['bd_kind_nipple'] ?? 'N',
                'cos' => $data['bd_kind_cos'] ?? 'N',
                'perfume' => $data['bd_kind_perfume'] ?? 'N',
                'bdsm' => $data['bd_kind_bdsm'] ?? 'N'
            ];

            $bdKind = json_encode($bdKindAry, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);


            //쑈당몰 디스플레이
            $bdApiActive = $data['bd_api_active'] ?? ''; //사용여부
            $bdCateNo = $data['bd_cate_no'] ?? ''; //카페24 카테고리 넘버
            $bdMatchingCate = $data['bd_matching_cate'] ?? ''; //고도몰 카테고리 코드
            $bdMatchingBrand = $data['bd_matching_brand'] ?? ''; //고도몰 브랜드 코드
            $bdApiName = $data['bd_api_name'] ?? ''; //쑈당몰 디스플레이 이름
            $bdApiNameEn = $data['bd_api_name_en'] ?? ''; //쑈당몰 디스플레이 이름 영문
            $bdApiLogo = $data['bd_api_logo'] ?? ''; //logo 파일위치
            $bdApiLogoMobile = $data['bd_api_logo_mobile'] ?? ''; //쑈당몰 디스플레이 로고 모바일
            $bdApiBg = $data['bd_api_bg'] ?? ''; //쑈당몰 디스플레이 배경
            $bdApiBgRgb = $data['bd_api_bg_rgb'] ?? ''; //쑈당몰 디스플레이 배경 색상
            $bdApiInfoClass = $data['bd_api_info_class'] ?? ''; //쑈당몰 디스플레이 정보 클래스
            $bdApiBgMobile = $data['bd_api_bg_mobile'] ?? ''; //쑈당몰 디스플레이 배경 모바일

            $api_info_ary = [
                'active' => $bdApiActive ?? '', //사용여부
                'name' => $bdApiName ?? '', //쑈당몰 디스플레이 이름
                'name_en' => $bdApiNameEn ?? '', //쑈당몰 디스플레이 이름 영문
                'logo' => $bdApiLogo ?? '', //logo 파일위치
                'logo_mobile' => $bdApiLogoMobile ?? '', //쑈당몰 디스플레이 로고 모바일
                'bg' => $bdApiBg ?? '', //쑈당몰 디스플레이 배경
                'bg_rgb' => $bdApiBgRgb ?? '', //쑈당몰 디스플레이 배경 색상
                'info_class' => $bdApiInfoClass ?? '', //쑈당몰 디스플레이 정보 클래스
                'bg_mobile' => $bdApiBgMobile ?? '' //쑈당몰 디스플레이 배경 모바일
            ];

            $bdApiInfo = json_encode($api_info_ary, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);

            //dd($bdApiInfo);

            $brandInfo = BrandModel::updateOrCreate(
                ['BD_IDX' => $idx],
                [
                    'BD_NAME' => $bdName,
                    'BD_NAME_EN' => $bdNameEn,
                    'BD_NAME_GROUP' => $bdNameGroup,
                    'BD_NAME_EN_GROUP' => $bdNameEnGroup,
                    'BD_ACTIVE' => $bdActive,
                    'BD_LIST_ACTIVE' => $bdListActive,
                    'BD_LOGO' => $bdLogo,
                    'BD_DOMAIN' => $bdDomain,
                    'BD_INTRODUCE' => $bdIntroduce,
                    'BD_CODE' => $bdCode,
                    'BD_KIND_CODE' => $bdKindCode,
                    'bd_kind' => $bdKind,
                    'bd_showdang_active' => $bdApiActive,
                    'bd_cate_no' => $bdCateNo,
                    'bd_matching_cate' => $bdMatchingCate,
                    'bd_matching_brand' => $bdMatchingBrand,
                    'bd_api_info' => $bdApiInfo,
                    'bd_api_introduce' => $bdApiIntroduce,
                    'bd_onadb_active' => $bdOnadbActive,
                    'bd_onadb_sort_num' => $bdOnadbSortNum,
                    'bd_memo' => $bdMemo,
                ]
            );

            //dd($brandInfo);

            return $brandInfo;

        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }

        
    }

    /**
     * 브랜드 신규생성
     * 
     * @param array $data 생성 데이터
     * @return array
     */
    public function createBrand($data)
    {

        $input_data = [
            'BD_NAME' => $data['bd_name'] ?? '',
            'BD_NAME_EN' => $data['bd_name_en'] ?? '',
            'bd_memo' => $data['bd_memo'] ?? '',
            'BD_NAME_GROUP' => $data['bd_name_group'] ?? '',
            'BD_NAME_EN_GROUP' => $data['bd_name_en_group'] ?? '',
            'BD_ACTIVE' => $data['bd_active'] ?? 'N',
            'BD_LIST_ACTIVE' => $data['bd_list_active'] ?? 'N',
            'bd_showdang_active' => $data['bd_showdang_active'] ?? 'N',
            'bd_onadb_active' => $data['bd_onadb_active'] ?? 'N',
        ];

        $result = BrandModel::create($input_data);

        return $result;
    }

}

