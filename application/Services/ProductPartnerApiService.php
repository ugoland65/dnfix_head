<?php
namespace App\Services;

use Throwable;
use App\Utils\HttpClient;
use App\Services\ProductPartnerService;

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

            $delivery_fee = $row['delivery_fee'] ?? 0;
            $order_price = $row['price'] + $delivery_fee;
            $cost_price = $row['price'] ?? 0;
            $vat = $cost_price * 0.1;
            $supplier_2nd_name = $row['supplier'] ?? '';

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

}