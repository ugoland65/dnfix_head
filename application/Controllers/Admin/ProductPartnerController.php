<?php

namespace App\Controllers\Admin;

use Throwable;
use Exception;
use App\Core\AuthAdmin;
use App\Core\BaseClass;
use App\Utils\HttpClient; 
use App\Classes\Request;
use App\Models\ProductPartnerModel;

use App\Services\ProductPartnerService;
use App\Services\ProductPartnerApiService;
use App\Services\ProductSupplierPyApiService;
use App\Services\PartnersService;
use App\Services\BrandService;
use App\Utils\Pagination;

class ProductPartnerController extends BaseClass 
{

    /**
     * 공급사 상품 관리
     * 
     * @param Request $request
     * @return view
     */
    public function getProductPartnerList( Request $request ) 
    {

        try{

            $requestData = $request->all();

            $page = $requestData['page'] ?? 1;
            $s_partner = $requestData['s_partner'] ?? null; // 공급사
            $s_godo_match = $requestData['s_godo_match'] ?? null; // 고도몰 매칭
            $s_supplier_match = $requestData['s_supplier_match'] ?? null; // 공급사 매칭
            $s_keyword = $requestData['s_keyword'] ?? null; // 상품명 검색
            $s_brand = $requestData['s_brand'] ?? null; // 브랜드
            $sort_mode = $requestData['sort_mode'] ?? 'idx'; // 정렬 모드
            $s_godo_sale_status = $requestData['s_godo_sale_status'] ?? null; // 고도몰 판매상태

            $payload = [
                'paging' => true,
                'page' => $page,
                'per_page' => 100,
                's_partner' => $s_partner,
                's_godo_match' => $s_godo_match,
                's_supplier_match' => $s_supplier_match,
                's_keyword' => $s_keyword,
                's_brand' => $s_brand,
                'sort_mode' => $sort_mode,
                's_godo_sale_status' => $s_godo_sale_status,
            ];

            $productPartnerService = new ProductPartnerService();
            $productPartnerList = $productPartnerService->getProductPartnerList($payload);

            $pagination = new Pagination(
                $productPartnerList['total'],
                $productPartnerList['per_page'],
                $productPartnerList['current_page'],
                10
            );

            $paginationHtml = $pagination->renderLinks();
            $paginationArray = $pagination->toArray();

            $partnersService = new PartnersService();
            $partnerForSelect = $partnersService->getPartnersForSelect(['showMode' => 'WHOLE_SUPPLIER']);

            // 브랜드 셀렉트바를 위한 조회
            $brandService = new BrandService();
            $brandForSelect = $brandService->getBrandForSelect(['listActive' => true]);

            //공급매칭 pks 수집
            $supplier_prd_idxs = [];
            foreach( $productPartnerList['data'] as $item ){
                $supplier_prd_idx = $item['supplier_prd_idx'] ?? 0;
                if( $supplier_prd_idx > 0 ){
                    $supplier_prd_idxs[] = $supplier_prd_idx;
                }
            }

            $supplier_prd_idxs = array_unique($supplier_prd_idxs);

            /*
            $url = 'https://dnetc01.mycafe24.com/api/SupplierProductPksList';

            $payload = [
                'pks' => $supplier_prd_idxs,
            ];

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'X-API-KEY: DNP_2024_SUPPLIER_API_KEY_v1_8f9e2c7b4a1d6e3f',
                ],
                CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                CURLOPT_TIMEOUT => 15,
            ]);

            $raw = curl_exec($ch);
            if ($raw === false) {
                $err = curl_error($ch);
                curl_close($ch);
                throw new Exception("B API 호출 실패: {$err}");
            }

            $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http < 200 || $http >= 300) {
                throw new Exception("B API HTTP 오류: {$http}, 응답: {$raw}");
            }

            $apiResponse = json_decode($raw, true);

            //dd($apiResponse);

            $supplierProductMap =[];

            foreach( $apiResponse['data'] ?? [] as $item ){
                $supplierProductMap[$item['idx']] = $item;
            }
            */

            $productPartnerApiService = new ProductPartnerApiService();
            $supplierProductMap = $productPartnerApiService->getSupplierProductIdxMap($supplier_prd_idxs);   
            

            $data = [
                's_partner' => $s_partner,
                's_godo_match' => $s_godo_match,
                's_supplier_match' => $s_supplier_match,
                's_keyword' => $s_keyword,
                's_brand' => $s_brand,
                's_status' => $s_godo_sale_status,
                'sort_mode' => $sort_mode,
                'pagination' => $paginationArray,
                'paginationHtml' => $paginationHtml,
                'productPartnerList' => $productPartnerList['data'],
                'partnerForSelect' => $partnerForSelect,
                'brandForSelect' => $brandForSelect,
                'supplierProductMap' => $supplierProductMap,
            ];

            return view('admin.provider.provider_product', $data)
                ->extends('admin.layout.layout',[
                    'pageGroup2' => 'prd',
                    'pageNameCode' => 'prd_provider'
                ]);

        } catch (Throwable $e) {
            dump($e->getMessage());
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }

    }


    /**
     * 공급사 사이트 상품DB 목록
     * 
     * @param Request $request
     */
    public function getSupplierProductDb( Request $request ) 
    {

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        try{

            $requestData = $request->all();

            $s_match_status = $requestData['s_match_status'] ?? 'unmatched';
            $site = $requestData['s_site'] ?? null;
            $s_keyword = $requestData['s_keyword'] ?? '';
            $page = $requestData['page'] ?? 1;
            $s_status = $requestData['s_status'] ?? '';

            if( $site ){

                $url = "https://dnetc01.mycafe24.com/api/SupplierProduct";
                $url .= "?site=".$site;
                $url .= "&status=".$s_status;
                $url .= "&match_status=".$s_match_status;
                $url .= "&keyword=".urlencode($s_keyword);
                $url .= "&page=".$page;
                $url .= "&limit=500";
            
                // 보낼 API Key
                $headers = [
                    "Content-Type: application/json",
                    "X-API-KEY: DNP_2024_SUPPLIER_API_KEY_v1_8f9e2c7b4a1d6e3f"
                ];
            
                // GET 요청
                $response = HttpClient::getData($url, $headers);
                $SupplierProductApiData = json_decode($response, true);

                //dd($SupplierProductApiData);
            
                $supplierProductsMatchIdxs = [];
                foreach ( $SupplierProductApiData['data']['supplierProducts'] ?? [] as $row ){
                    if( !empty($row['provider_prd_idx']) ){
                        $supplierProductsMatchIdxs[] = $row['provider_prd_idx'];
                    }
                }
            
                $supplierProductsMatchIdxs = array_unique($supplierProductsMatchIdxs);
            
                $productPartnerMatchData = [];
                if (!empty($supplierProductsMatchIdxs)) {
                    $productPartnerService = new ProductPartnerService();
                    $productPartnerMatchData = $productPartnerService->getProductPartnerWhereInIdx($supplierProductsMatchIdxs);
                }
            

                $pagination_total = $SupplierProductApiData['data']['pagination']['total'];
                $pagination_per_page = $SupplierProductApiData['data']['pagination']['per_page'];
                $pagination_current_page = $SupplierProductApiData['data']['pagination']['current_page'];
            
                $pagination = new Pagination($pagination_total, $pagination_per_page, $pagination_current_page, 10);
                $paginationHtml = $pagination->renderLinks();
            
            }else{
            
                $SupplierProductApiData = [];
                $pagination_total = 0;
                $pagination_per_page = 0;
                $pagination_current_page = 0;
                $paginationHtml = '';
            
            }

            //dd($SupplierProductApiData['data']['supplierProducts']);

            $provider_data = config('admin.provider');
            $supplier_code_data = $provider_data['supplier_code_data'];

            $data = [
                'supplier_code_data' => $supplier_code_data,
                's_match_status' => $s_match_status,
                'site' => $site,
                's_keyword' => $s_keyword,
                'page' => $page,
                's_status' => $s_status,
                'SupplierProductApiData' => $SupplierProductApiData,
                'pagination_total' => $pagination_total,
                'pagination_per_page' => $pagination_per_page,
                'pagination_current_page' => $pagination_current_page,
                'paginationHtml' => $paginationHtml,
                'productPartnerMatchData' => $productPartnerMatchData,
            ];

            return view('admin.provider.supplier_product_db', $data)
                ->extends('admin.layout.layout',[
                    'pageGroup2' => 'provider',
                    'pageNameCode' => 'prd_provider_db'
                ]);

        }
        catch(Throwable $e){
            dump($e->getMessage());
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }


    /**
     * 공급사 상품 상세 
     * 
     * @param Request $request
     */
    public function getProductPartnerDetail( Request $request ) 
    {

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        try{

            $requestData = $request->all();

            $prd_idx = $requestData['prd_idx'] ?? null;

            if(empty($prd_idx)){
                throw new \Exception('prd_idx가 비어있습니다.');
            }


            $productPartnerService = new ProductPartnerService();
            $productPartner = $productPartnerService->getProductPartnerInfo($prd_idx);

            /*
            $supplier_prd_pk = $productPartner['supplier_prd_pk'] ?? null;

            if( $supplier_prd_pk ){
                $crawlerUrl = 'https://showdang-crawler-git-465761242376.asia-northeast3.run.app/mobe_detail';
                $crawlerHeaders = [
                    'Content-Type: application/json',
                    'X-API-KEY: A9X7QW3ZLMN4T8V2R5CY24802480',
                ];
                $crawlerPayload = [
                    'id' => (int)$supplier_prd_pk,
                ];
                $crawlerResponse = HttpClient::postData($crawlerUrl, $crawlerPayload, $crawlerHeaders);
                dump($crawlerResponse);
            }
            */

            $config_product = config('admin.product');
            $prd_kind_name = $config_product['prd_kind_name'] ?? [];

            // 브랜드 셀렉트바
            $extraData = ['listActive' => true];
            $brandService = new BrandService();
            $brandForSelect = $brandService->getBrandForSelect($extraData);

            // 공급사 셀렉트바
            $extraData = ['showMode' => 'WHOLE_SUPPLIER'];
            $partnersService = new PartnersService();
            $partnerForSelect = $partnersService->getPartnersForSelect($extraData);

            $data = [
                'prd_kind_name' => $prd_kind_name,
                'brandForSelect' => $brandForSelect,
                'partnerForSelect' => $partnerForSelect,
                'prd_data' => $productPartner ?? [],
            ];

            return view('admin.provider.provider_prd_form', $data);

        } catch(Throwable $e){
            dump($e->getMessage());
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    
    }


    /**
     * 공급사 상품 상세 저장
     * 
     * @param Request $request
     */
    public function saveProductPartnerDetail( Request $request ) 
    {
        try{

            $requestData = $request->all();
            $prd_idx = $requestData['prd_idx'] ?? null;

            if(empty($prd_idx)){
                throw new \Exception('prd_idx가 비어있습니다.');
            }

            $productPartnerService = new ProductPartnerService();
            $result = $productPartnerService->saveProductPartner($requestData);

            if($result['status'] == 'success'){
                return response()->json([
                    'status' => 'success', 
                    'message' => '저장되었습니다.',
                ]);
            }else{
                throw new \Exception($result['message']);
            }

        } catch(Throwable $e){
            return response()->json([
                'status' => 'error', 
                'message' => $e->getMessage()
            ]);
        }
    }


    /**
     * 공급사 상품 액션
     * 
     * @param Request $request
     * @return array
     */
    public function providerProductAction( Request $request ) 
    {

        try{

            $requestData = $request->all();
            $actionMode = $requestData['action_mode'] ?? null;

            if(empty($actionMode)){
                throw new \Exception('action_mode가 비어있습니다.');
            }

            $productPartnerService = new ProductPartnerService();
            $productPartnerApiService = new ProductPartnerApiService();
            $productSupplierPyApiService = new ProductSupplierPyApiService();

            $errorMessage = '처리에 실패했습니다.';

            // 공급사 DB 상품 등록대기로 등록
            if( $actionMode == 'product_standby_register' ){

                $payload = [
                    'pks' => $requestData['pks'] ?? [],
                ];
                $result = $productPartnerApiService->productStandbyRegister($payload);
                $message = '등록대기 처리되었습니다.';
                $errorMessage = '등록대기 처리에 실패했습니다.';

            // 공급사 상품 매칭제외로 처리
            }elseif( $actionMode == 'product_match_excluded' ){
                
                $db1_idx = $requestData['db1_idx'] ?? null;
                $db2_idx = $requestData['db2_idx'] ?? null;
                
                $payload = [
                    'db1_idx' => $db1_idx,
                    'db2_idx' => $db2_idx,
                    'process_reason' => $requestData['process_reason'] ?? null,
                ];
                $result = $productPartnerApiService->productMatchExcluded($payload);
                $message = '매칭제외 처리되었습니다.';
                $errorMessage = '매칭제외 처리에 실패했습니다.';

            // 공급사 사이트 디테일 크롤링후 업데이트
            }elseif( $actionMode == 'update_supplier_product_detail' ){
                
                $payload = [
                    'prd_idx' => $requestData['prd_idx'] ?? null,
                    'supplier_prd_pk' => $requestData['supplier_prd_pk'] ?? null,
                ];
                $result = $productSupplierPyApiService->updateSupplierProductDetail($payload);

                if( $result['status'] == 'success' ){
                    $message = $result['message'];
                }else{
                    $errorMessage = $result['message'];
                    //throw new \Exception($result['message']);
                }

                /*
                $message = '업데이트되었습니다.';
                $errorMessage = '업데이트에 실패했습니다.';
                */

            // 공급사 상품 -> 상품DB로 등록후 매칭
            }elseif( $actionMode == 'product_register_to_supplier_product' ){
                
                $payload = [
                    'prd_idx' => $requestData['prd_idx'] ?? null
                ];
                $result = $productPartnerService->productRegisterToSupplierProduct($payload);
                $message = '상품DB로 등록되었습니다.';
                $errorMessage = '상품DB로 등록에 실패했습니다.';

            }else{
                throw new \Exception('지원하지 않는 action_mode 입니다.');
            }
            

            if( $result ){
                return response()->json([
                    'status' => 'success', 
                    'message' => $message,
                ]);
            }else{
                throw new \Exception($errorMessage);
            }

        } catch(Throwable $e){
            
            return response()->json([
                'status' => 'error', 
                'message' => $e->getMessage()
            ]);
        }   
    }
        
    /**
     * 공급사 상품 매칭
     * @return array
     */
    public function matchProviderProduct( Request $request ) 
    {

        try{
            
            $requestData = $request->all();

            $mode = $requestData['mode'] ?? 'direct';
            $db1_idx = $requestData['db1_idx'] ?? null;
            $db2_idx = $requestData['db2_idx'] ?? null;
            $option_match = $requestData['option_match'] ?? null;

            if(empty($db1_idx) ){
                throw new \Exception('db1_idx가 비어있습니다.');
            }

            if(empty($db2_idx) ){
                throw new \Exception('db2_idx가 비어있습니다.');
            }
            
            $productPartner = ProductPartnerModel::find($db1_idx);

            if(empty($productPartner)){
                throw new \Exception("{$db1_idx}에 해당하는 상품이 없습니다.");
            }

            $matching_data = json_decode($productPartner['matching_data'], true) ?? [];

            $matching_data[] = [
                'supplier' => [
                    'mb_idx' => AuthAdmin::getSession('sess_idx'),
                    'reg' => AuthAdmin::getConnectionInfo(), JSON_UNESCAPED_UNICODE ?? [],
                ]
            ];

            $matching_data = json_encode($matching_data, JSON_UNESCAPED_UNICODE);

            //$matching_data['supplier_prd_idx'] = $db2_idx;
            $cost_price = $requestData['price'] ?? 0;
            $delivery_fee = $requestData['delivery_fee'] ?? null;
            $supplier_site = $requestData['supplier_site'] ?? null;
            $supplier_2nd_name = $requestData['supplier_2nd_name'] ?? null;
            $supplier_prd_pk = $requestData['supplier_prd_pk'] ?? null;
            $supplier_img_src = $requestData['supplier_img_src'] ?? null;
            // 따옴표 포함 이름도 그대로 저장될 수 있도록 HTML 엔티티 해제
            $prd_name = $requestData['prd_name'] ?? null;
            if ($prd_name !== null) {
                $prd_name = html_entity_decode($prd_name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
            $is_vat = $requestData['is_vat'] ?? null;

            if($is_vat == 'N'){
                $order_price = ($cost_price * 1.1 ) + $delivery_fee;
                $vat = $cost_price * 0.1;
                $cost_price_save = $cost_price;
            }else{
                $order_price = $cost_price + $delivery_fee;
                $vat = $cost_price / 11;
                $cost_price_save = ($cost_price / 1.1);
            }

            $price_data = [
                'is_vat' => $is_vat, // 부가세
                'cost_price' => $cost_price_save,
                'delivery_fee' => $delivery_fee, // 배송비
                'vat' => $vat, // 부가세
            ];

            $price_data = json_encode($price_data, JSON_UNESCAPED_UNICODE);

            $updateData = [
                'name_p' => $prd_name,
                'order_price' => $order_price,
                'cost_price' => $cost_price,
                'price_data' => $price_data,
                'supplier_prd_idx' => $db2_idx,
                'supplier_site' => $supplier_site,
                'supplier_prd_pk' => $supplier_prd_pk,
                'supplier_2nd_name' => $supplier_2nd_name,
                'supplier_img_mode' => 'out',
                'supplier_img_src' => $supplier_img_src,
                'matching_option' => $option_match,
                'matching_data' => $matching_data,
            ];

            $result = ProductPartnerModel::where('idx', $db1_idx)->update($updateData);

            $domain = "https://dnetc01.mycafe24.com";
            $url = $domain."/api/SupplierProductMatch?mode=match&d1_idx=".$db1_idx."&d2_idx=".$db2_idx."&match_mode=".$mode."&option_match=".urlencode($option_match);

            // 보낼 API Key
            $headers = [
                "Content-Type: application/json",
                "X-API-KEY: DNP_2024_SUPPLIER_API_KEY_v1_8f9e2c7b4a1d6e3f"
            ];
            
            // GET 요청
            $response = HttpClient::getData($url, $headers);
            $data = json_decode($response, true);

            if($result){

                return response()->json([
                    'status' => 'success', 
                    'message' => '저장되었습니다.',
                    'data' => $data
                ]);

                 //return true;

             }else{
                 throw new \Exception('상품 공급사 매칭에 실패했습니다.');
             }


        } catch(\Exception $e){
            return response()->json([
                'status' => 'error', 
                'message' => $e->getMessage()
            ]);
        }

    }


    /**
     * 공급사 상품 매칭 취소
     * 
     * @param Request $request
     * @return array
     */
    public function cancelMatchProviderProduct( Request $request )
    {

        try{

            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            $requestData = $request->all();

            $db1_idx = $requestData['db1_idx'] ?? null;
            $db2_idx = $requestData['db2_idx'] ?? null;
            
            if(empty($db1_idx) ){
                throw new \Exception('db1_idx가 비어있습니다.');
            }

            if(empty($db2_idx) ){
                throw new \Exception('db2_idx가 비어있습니다.');
            }

            $productPartner = ProductPartnerModel::find($db1_idx);

            //옵션값이 있으면 option, 없으면 direct
            $match_mode = $productPartner['matching_option'] == null ? 'direct' : 'option';
            $option_match = $productPartner['matching_option'] ?? null;
            
            if(empty($productPartner)){
                throw new \Exception("{$db1_idx}에 해당하는 상품이 없습니다.");
            }

            $matching_data = json_decode($productPartner['matching_data'], true) ?? [];

            $matching_data[] = [
                'cancel' => [
                    'mb_idx' => AuthAdmin::getSession('sess_idx'),
                    'reg' => AuthAdmin::getConnectionInfo(), JSON_UNESCAPED_UNICODE ?? [],
                ]
            ];

            $matching_data = json_encode($matching_data, JSON_UNESCAPED_UNICODE);

            $updateData = [
                'name_p' => null,
                'order_price' => 0,
                'cost_price' => 0,
                'price_data' => '',
                'supplier_prd_idx' => 0,
                'supplier_site' => null,
                'supplier_prd_pk' => 0,
                'supplier_2nd_name' => null,
                'supplier_img_mode' => 'out',
                'supplier_img_src' => null,
                'matching_option' => null,
                'matching_data' => $matching_data,
            ];

            $result = ProductPartnerModel::where('idx', $db1_idx)->update($updateData);

            $domain = "https://dnetc01.mycafe24.com";
            $url = $domain."/api/SupplierProductMatch?mode=cancel&d1_idx=".$db1_idx."&d2_idx=".$db2_idx."&match_mode=".$match_mode."&option_match=".urlencode($option_match);

            // 보낼 API Key
            $headers = [
                "Content-Type: application/json",
                "X-API-KEY: DNP_2024_SUPPLIER_API_KEY_v1_8f9e2c7b4a1d6e3f"
            ];

            $response = HttpClient::getData($url, $headers);
            $data = json_decode($response, true);

            

            if($result){

                return response()->json([
                    'status' => 'success', 
                    'message' => '취소되었습니다.',
                    'data' => $data
                ]);

            }

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error', 
                'message' => $e->getMessage()
            ]);
        }
    }
    

    /**
     * 고도몰 매칭 상품 정보 갱신
     * 
     * @param Request $request
     * @return array
     */
    public function loadGodoGoodsInfo( Request $request ) 
    {
        try{
            
            $requestData = $request->all();
            $prd_idx = $requestData['prd_idx'] ?? null;
            $godo_goodsNo = $requestData['godo_goodsNo'] ?? null;

            if(empty($prd_idx)){
                throw new \Exception('prd_idx가 비어있습니다.');
            }

            if(empty($godo_goodsNo)){
                throw new \Exception('godo_goodsNo가 비어있습니다.');
            }

            $productPartnerService = new ProductPartnerService();
            $result = $productPartnerService->updateProductPartnerByGodoGoodsInfo([
                'prd_idx' => $prd_idx,
                'godo_goodsNo' => $godo_goodsNo,
            ]);

            if($result){
                return response()->json([
                    'status' => 'success', 
                    'message' => '고도몰 상품 정보 갱신 완료',
                ]);
            }

            /*
            @deprecated

            $apiUrl = 'https://showdang.co.kr/dnfix/api/goods_api.php?mode=detail&goodsNo='.$godo_goodsNo;
            $response = HttpClient::getData($apiUrl);
            
            $responseData = json_decode($response, true);
            if(!is_array($responseData)){
                throw new \Exception('고도몰 API 응답 파싱 실패');
            }
            $godoGoods = $responseData;

            
            //$productPartnerService = new ProductPartnerService();
            //$productPartner = $productPartnerService->getProductPartnerInfo($prd_idx);
            
            $productPartner = ProductPartnerModel::find($prd_idx);
            if(empty($productPartner)){
                throw new \Exception("{$prd_idx}에 해당하는 상품이 없습니다.");
            }

            $optionData = [];

            $optionFl = strtolower($godoGoods['optionFl'] ?? 'n');
            
            if( $optionFl === 'y' ){
                $optionName = explode('^|^', $godoGoods['optionName'] ?? '');
                $optionData = [
                    'displayFl' => $godoGoods['optionDisplayFl'] ?? 'n',
                    'name' =>$optionName,
                    'items' => $godoGoods['options'] ?? [],
                ];
            }

            $is_modify = false;
            $modify_message = [];

            //고도몰은 옵션이 있는데 공급사는 옵션이 없으면 옵션 추가
            if( $optionFl === 'y' && ($productPartner['godo_is_option'] ?? 'N') == 'N' ){
                
                $productPartner->godo_is_option = 'Y';
                $productPartner->godo_option = json_encode($optionData, JSON_UNESCAPED_UNICODE);
                $productPartner->update();

                $is_modify = true;
                $modify_message[] = '옵션 추가';

            }

            $returnData = [
                'optionData' => $optionData,
                'productPartner' => $productPartner,
                'godoGoods' => $responseData
            ];

            return response()->json([
                'status' => 'success', 
                'data' => $returnData,
                'is_modify' => $is_modify,
                'modify_message' => $modify_message
            ]);
            */
            
        } catch(\Exception $e){

            return response()->json([
                'status' => 'error', 
                'message' => $e->getMessage()
            ]);
            
        }

    }


    
}