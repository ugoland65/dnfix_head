<?php
namespace App\Services;

use Throwable;
use App\Utils\HttpClient;
use App\Models\ProductPartnerModel;
use App\Services\AdminActionLogService;
use App\Services\ProductPartnerApiService;

class ProductSupplierPyApiService
{

    private $domain = "https://showdang-crawler-git-465761242376.asia-northeast3.run.app";
    private $apiKey = "A9X7QW3ZLMN4T8V2R5CY24802480";

    /**
     * 공급사 사이트 디테일 크롤링후 업데이트
     * 
     * @param array $data
     * @return boolean
     */
    public function updateSupplierProductDetail($data)
    {

        $prd_idx = $data['prd_idx'] ?? null;
        if(empty($prd_idx)){
            throw new \Exception('prd_idx가 비어있습니다.');
        }

        $productPartner = ProductPartnerModel::find($prd_idx)->toArray();
        if(empty($productPartner)){
            throw new \Exception('prd_idx에 해당하는 상품이 없습니다.');
        }

        $supplier_prd_idx = $productPartner['supplier_prd_idx'] ?? null;

        $supplier_prd_pk = $data['supplier_prd_pk'] ?? null;
        if(empty($supplier_prd_pk)){
            throw new \Exception('supplier_prd_pk가 비어있습니다.');
        }

        $config_provider = config('admin.provider');
        $supplier_code_data = $config_provider['supplier_code_data'];

        $site_code = '';
        foreach ($supplier_code_data as $supplierCode => $supplierInfo) {
            if ((int)($supplierInfo['idx'] ?? 0) === (int)($productPartner['partner_idx'] ?? 0)) {
                $site_code = (string)($supplierInfo['code'] ?? $supplierCode);
                break;
            }
        }

        $url = $this->domain."/detail_crawling";
 
        $headers = [
            'Content-Type: application/json',
            'X-API-KEY: '.$this->apiKey,
        ];


        $payload = [
            'id' => (int)$supplier_prd_pk,
            'site_code' => $site_code,
        ];
        $response = HttpClient::postData($url, $payload, $headers);
        $responseData = json_decode($response, true);
        
        if($responseData['success'] === true){
            //return $responseData['data'];

            $beforeData = [
                'name_p' => $productPartner['name_p'] ?? null,
                'cost_price' => $productPartner['cost_price'] ?? null,
                'min_sale_price' => $productPartner['min_sale_price'] ?? null,
                'supplier_2nd_name' => $productPartner['supplier_2nd_name'] ?? null,
                'price_data' => $productPartner['price_data'] ?? null,
                'supplier_is_option' => $productPartner['supplier_is_option'] ?? null,
                'supplier_option_data' => $productPartner['supplier_option_data'] ?? null,
                'supplier_detail_img' => $productPartner['supplier_detail_img'] ?? null,
            ];

            $name_p = $responseData['name'] ?? null;
            $cost_price = $responseData['price'] ?? null;
            $min_sale_price = $responseData['min_sale_price'] ?? null;
            $supplier_2nd_name = $responseData['supplier_2nd_name'] ?? null;

            $delivery_com = $responseData['delivery_com'] ?? null;
            $delivery_fee = $responseData['delivery_fee'] ?? null;
            $delivery_time = $responseData['delivery_time'] ?? null;

            $supplier_is_option = $responseData['is_option'] ?? 'N';
            $option_data = $responseData['option_data'] ?? null;
            $supplier_detail_img = $responseData['detail_img'] ?? null;

            /* 모브,도라도라는 부가세 미포함 */
            if( $productPartner['partner_idx'] == 3 || $productPartner['partner_idx'] == 6 ){
                $is_vat = "N";
            }else{
                $is_vat = "Y";
            }

            if( $is_vat == 'N' ){
                $vat = $cost_price * 0.1;
                $cost_price_save = $cost_price;
                $order_price = $cost_price + $vat + $delivery_fee; 
            }else{
                $vat = $cost_price / 11;
                $cost_price_save = ($cost_price / 1.1);
                $order_price = $cost_price + $delivery_fee; 
            }

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
            $supplier_option_data = json_encode($option_data, JSON_UNESCAPED_UNICODE);
            $supplier_detail_img = json_encode($supplier_detail_img, JSON_UNESCAPED_UNICODE);

            $updateData = [
                'name_p' => $name_p,
                'order_price' => $order_price,
                'cost_price' => $cost_price,
                'min_sale_price' => $min_sale_price,
                'supplier_2nd_name' => $supplier_2nd_name,
                'price_data' => $price_data,
                'supplier_is_option' => $supplier_is_option,
                'supplier_option_data' => $supplier_option_data,
                'supplier_detail_img' => $supplier_detail_img,
            ];

            $result = ProductPartnerModel::where('idx', $prd_idx)->update($updateData);

            // 공급사 DB 상품 수정
            $productPartnerApiService = new ProductPartnerApiService();
            $productPartnerApiResult = $productPartnerApiService->productUpdate([
                'idx' => $supplier_prd_idx,
                'is_detail_crawler' => 'Y',
                'is_option' => $supplier_is_option,
                'option_data' => $supplier_option_data,
            ]);

            $adminActionLogService = new AdminActionLogService();

            $diff = $adminActionLogService->buildDiff($beforeData, $updateData);

            $adminActionLogService = new AdminActionLogService();

            $adminActionLogService->log([
                'target_type' => 'prd_partner',
                'target_table' => 'prd_partner',
                'target_pk' => (string)($prd_idx ?? ''),
                'action_mode' => 'update',
                'action_summary' => '공급사 사이트 디테일 크롤링후 업데이트',
                'before_json' => $beforeData,
                'after_json' => $updateData,
                'diff_json' => $diff,
            ]);

            //공급사 DB에 업데이트

            return ['status' => 'success', 'message' => '업데이트되었습니다.'];

        }else{
            throw new \Exception($responseData['message']);
        }
    }

}