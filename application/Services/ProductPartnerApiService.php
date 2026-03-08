<?php
namespace App\Services;

use Throwable;
use App\Utils\HttpClient;
use App\Services\ProductPartnerService;
use App\Core\AuthAdmin;

class ProductPartnerApiService
{

    private $domain = "https://dnetc01.mycafe24.com";
    private $apiKey = "DNP_2024_SUPPLIER_API_KEY_v1_8f9e2c7b4a1d6e3f";

    /**
     * 공급사 DB를 공급사 상품 등록대기로 등록
     * 
     * @param array $data
     * @return boolean
     */
    public function productStandbyRegister($data)
    {

        $dataLoadUrl = $this->domain.'/api/SupplierProductPksList';
        $matchUrl = $this->domain.'/api/SupplierProductMatch';

        $pks = $data['pks'] ?? [];

        /*
        $partner_idx = $data['partner_idx'] ?? null;

        if(empty($partner_idx)){
            throw new \Exception('partner_idx가 비어있습니다.');
        }
        */

        $headers = [
            "Content-Type: application/json",
            "X-API-KEY: {$this->apiKey}",
        ];          

        $payload = [
            'pks' => $pks,
        ];

        $response = HttpClient::postData($dataLoadUrl, $payload, $headers);
        $SupplierProductPksListData = json_decode($response, true);

        $productPartnerService = new ProductPartnerService();

        $provider_data = config('admin.provider');
        $supplier_code_data = $provider_data['supplier_code_data'];

        //dd($SupplierProductPksListData['data']);

        foreach( $SupplierProductPksListData['data'] ?? [] as $row){

            $partner_idx = $supplier_code_data[$row['site']]['idx'] ?? '';

            $is_vat = $row['is_vat'] ?? 'N';

            $delivery_fee = $row['delivery_fee'] ?? 0;
            $delivery_com = $row['delivery_com'] ?? null;
            $delivery_time = $row['delivery_time'] ?? null;

            //$order_price = $row['price'] + $delivery_fee;
            $cost_price = $row['price'] ?? 0;
            //$vat = $cost_price * 0.1;
            $supplier_2nd_name = $row['supplier'] ?? '';

            
            if( $is_vat == 'N' ){
                $vat = $cost_price * 0.1;
                $cost_price_save = $cost_price;
                $order_price = $cost_price + $vat + $delivery_fee; 
            }else{
                $vat = $cost_price / 11;
                $cost_price_save = ($cost_price / 1.1);
                $order_price = $cost_price + $delivery_fee; 
            }

            /*
            $price_data = [
                'is_vat' => $is_vat, // 부가세
                'cost_price' => $cost_price_save,
                'order_price' => $order_price,
                'delivery_com' => $delivery_com,
                'delivery_fee' => $delivery_fee, // 배송비
                'delivery_time' => $delivery_time,
                'vat' => $vat, // 부가세
            ];

            $price_data = json_encode($price_data, JSON_UNESCAPED_UNICODE);
            */


            $inputData = [
                'status' => '등록대기',
                'brand_idx' => 0,
                'partner_idx' => $partner_idx,
                'supplier_prd_idx' => $row['idx'],
                'supplier_prd_pk' => $row['prd_pk'],
                'supplier_site' => $row['site'],
                'supplier_2nd_name' => $supplier_2nd_name,
                'supplier_img_mode' => 'out',
                'supplier_img_src' => $row['image_url'],
                'name' => $row['name'],
                'name_p' => $row['name'],
                'cost_price' =>$cost_price,
                'order_price' => $order_price,
                'is_vat' => $is_vat,
                'delivery_com' => $delivery_com,
                'delivery_fee' => $delivery_fee,
                'delivery_time' => $delivery_time,
                'matching_code' => $partner_idx,
                'img_mode' => 'out',
                'img_src' => $row['image_url'],
                'action_url' => '/admin/provider_product/db',
                'action_summary' => '공급사 DB를 공급사 상품 등록대기로 등록',
            ];

            $result = $productPartnerService->saveProductPartner($inputData);
            //dd($result);

            $matchUrl = $matchUrl."?mode=match&d1_idx=".$result['idx']."&d2_idx=".$row['idx']."&match_mode=direct";
    
            $response = HttpClient::getData($matchUrl, $headers);

        }


        return true;

    }


    /**
     * 공급사 상품 idx Map으로 변환
     * 
     * @param array $pks
     * @return array
     */
    public function getSupplierProductIdxMap($pks)
    {

        $url = $this->domain.'/api/SupplierProductPksList';

        $headers = [
            "Content-Type: application/json",
            "X-API-KEY: {$this->apiKey}",
        ];

        $payload = [
            'pks' => $pks,
        ];
        
        $response = HttpClient::postData($url, $payload, $headers);

        $SupplierProductPksListData = json_decode($response, true);

        $supplierProductMap =[];

        foreach( $SupplierProductPksListData['data'] ?? [] as $item ){
            $supplierProductMap[$item['idx']] = $item;
        }

        return $supplierProductMap;

    }


    /**
     * 공급사 DB 상품 판매중단 처리
     * 
     * @param array $data
     * @return boolean
     */
    public function productDiscontinued($data)
    {
        $idx = $data['idx'] ?? null;
        $url = $this->domain.'/api/SupplierProductAction';

        $headers = [
            "Content-Type: application/json",
            "X-API-KEY: {$this->apiKey}",
        ];          

        $payload = [
            'actionMode' => 'Discontinued',
            'idx' => $idx,
        ];

        $response = HttpClient::postData($url, $payload, $headers);
        $data = json_decode($response, true);

        return $data;

    }


    /**
     * 공급사 DB 상품 판매제외 처리
     * 
     * @param array $data
     * @return boolean
     */
    public function productMatchExcluded($data)
    {

        $db1_idx = $data['db1_idx'] ?? null;
        $db2_idx = $data['db2_idx'] ?? null;

        $match_excluded_date = date('Y-m-d H:i:s');
        $process_reason = $data['process_reason'] ?? null;

        $match_excluded_data = [
            'reg' => AuthAdmin::getConnectionInfo(),
        ];

        $url = $this->domain.'/api/SupplierProductAction';

        $headers = [
            "Content-Type: application/json",
            "X-API-KEY: {$this->apiKey}",
        ];          

        $payload = [
            'actionMode' => 'MatchExcluded',
            'idx' => $db2_idx,
            'match_excluded_date' => $match_excluded_date,
            'match_excluded_memo' => $process_reason,
            'match_excluded_data' => $match_excluded_data,
        ];

        $response = HttpClient::postData($url, $payload, $headers);
        $data = json_decode($response, true);

        //dd($data);

        return $data;

    }

    /**
     * 공급사 DB 상품 수정
     * 
     * @param array $data
     * @return boolean
     */
    public function productUpdate($data)
    {

        $idx = $data['idx'] ?? null;

        $is_detail_crawler = $data['is_detail_crawler'] ?? null;
        $is_option = $data['is_option'] ?? null;
        $option_data = $data['option_data'] ?? null;

        if( empty($idx) ){
            throw new \Exception('idx가 비어있습니다.');
        }

        $url = $this->domain.'/api/SupplierProductUpdate';

        $headers = [
            "Content-Type: application/json",
            "X-API-KEY: {$this->apiKey}",
        ];

        $payload = [
            'idx' => $idx,
            'is_detail_crawler' => $is_detail_crawler,
            'is_option' => $is_option,
            'option_data' => $option_data,
        ];

        $response = HttpClient::postData($url, $payload, $headers);
        $data = json_decode($response, true);

        if( $data['status'] === 'success' ){
            return ['status' => 'success', 'message' => '업데이트되었습니다.'];
        }else{
            return ['status' => 'error', 'message' => $data['message']];
        }

    }


}