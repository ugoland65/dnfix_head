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
        $is_vat = 'N';
        foreach ($supplier_code_data as $supplierCode => $supplierInfo) {
            if ((int)($supplierInfo['idx'] ?? 0) === (int)($productPartner['partner_idx'] ?? 0)) {
                $site_code = (string)($supplierInfo['code'] ?? $supplierCode);
                $is_vat = (string)($supplierInfo['vat'] ?? 'N');
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
        
        //dd($responseData);

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
                'supplier_status' => $productPartner['supplier_status'] ?? null,
                'supplier_status_date' => $productPartner['supplier_status_date'] ?? null,
            ];

            $in_stock = $responseData['in_stock'] ?? null;
            $sale_status = trim((string)($responseData['sale_status'] ?? ''));
            
            if ($sale_status === '판매중단') {
                $status = '판매중단';
                $supplier_status_date = date('Y-m-d H:i:s');
            } elseif( $in_stock > 0 ){
                $status = '판매중';
                $supplier_status_date = null;
            }else{
                $status = '품절';
                $supplier_status_date = date('Y-m-d H:i:s');
            }


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

            //$message = '성공적으로 업데이트되었습니다.';

            if( $status == '품절' ){
                $message = '공급사 상품이 현재 품절상태입니다.';
            }else{
                $message = '성공적으로 업데이트되었습니다.';
            }

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
                'detail_crawler_date' => date('Y-m-d H:i:s'),
                'supplier_status' => $status,
                'supplier_status_date' => $supplier_status_date,
            ];

            $result = ProductPartnerModel::where('idx', $prd_idx)->update($updateData);

            // 공급사 DB 상품 수정
            /*
                컬럼추가시 $productPartnerApiService->productUpdate에서 $payload값도 추가해야함
            */
            $productPartnerApiService = new ProductPartnerApiService();
            $productPartnerApiResult = $productPartnerApiService->productUpdate([
                'idx' => $supplier_prd_idx,
                'is_detail_crawler' => 'Y',
                'is_option' => $supplier_is_option,
                'option_data' => $supplier_option_data,
                'status' => $status,
                'sold_out_date' => $supplier_status_date,
            ]);

            //dd($productPartnerApiResult);

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

            return ['status' => 'success', 'message' => $message];

        }else{
            throw new \Exception($responseData['message']);
        }
    }

    /**
     * 모브 예치금 조회
     * 
     * @return array
     */
    public function getMobPayBalance($data)
    {
        $url = $this->domain."/mobe/available-deposit";
        $headers = [
            'Accept: application/json',
            'X-API-KEY: '.$this->apiKey,
        ];
        $response = HttpClient::getData($url, $headers);
        $responseData = json_decode($response, true);

        if (!is_array($responseData) || empty($responseData['success'])) {
            throw new \Exception((string)($responseData['message'] ?? '모브 예치금 조회에 실패했습니다.'));
        }

        return [
            'available_deposit' => (int)($responseData['available_deposit'] ?? 0),
        ];
    }

    
    /**
     * 모브 주문을 수집하고 최근 주문 상태를 갱신한다.
     *
     * @param array $data max_pages, refresh_recent_days
     * @return array 수집 페이지 수, 신규/갱신 주문 수, 상세 요청 수를 포함한 API 응답
     */
    public function syncMobeOrders($data = [])
    {
        $maxPages = (int)($data['max_pages'] ?? 2);
        $refreshRecentDays = (int)($data['refresh_recent_days'] ?? 30);

        if ($maxPages < 1) {
            throw new \InvalidArgumentException('max_pages는 1 이상이어야 합니다.');
        }
        if ($refreshRecentDays < 0) {
            throw new \InvalidArgumentException('refresh_recent_days는 0 이상이어야 합니다.');
        }

        $url = $this->domain . '/mobe/orders/sync';
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-API-Key: ' . $this->apiKey,
        ];
        $payload = [
            'max_pages' => $maxPages,
            'refresh_recent_days' => $refreshRecentDays,
        ];

        $response = HttpClient::postData($url, $payload, $headers);
        $responseData = json_decode($response, true);

        if (!is_array($responseData) || empty($responseData['success'])) {
            $message = is_array($responseData)
                ? (string)($responseData['message'] ?? '모브 주문 동기화에 실패했습니다.')
                : '모브 주문 동기화 API 응답 파싱에 실패했습니다.';
            throw new \Exception($message);
        }

        return $responseData;
    }

    /**
     * 도메인별 상품 정보수집을 요청한다.
     *
     * 새 도메인은 $collectorEndpoints에 엔드포인트와 URL 검증 규칙을 추가해 확장한다.
     *
     * @param array $data collection_url, matched_product_pk, requester_user_pk, requester_user_name
     * @return array
     */
    public function requestProductInfoCollection(array $data): array
    {
        $collectionUrl = trim((string)($data['collection_url'] ?? ''));
        $urlParts = parse_url($collectionUrl);
        $scheme = strtolower((string)($urlParts['scheme'] ?? ''));
        $host = strtolower((string)($urlParts['host'] ?? ''));
        $host = preg_replace('/^www\./', '', $host);

        if (!in_array($scheme, ['http', 'https'], true) || $host === '') {
            throw new \InvalidArgumentException('수집 URL 형식이 올바르지 않습니다.');
        }

        $collectorEndpoints = [
            'nipporigift.net' => [
                'endpoint' => '/maker-products/npg/crawl',
                'identifier_type' => 'query',
                'identifier_key' => 'product_id',
                'payload_key' => 'product_pk',
                'payload_value_type' => 'int',
            ],
            'tamatoys.tma.co.jp' => [
                'endpoint' => '/maker-products/tamatoys/crawl',
                'identifier_type' => 'path_code',
                'required_path_prefix' => '/item/detail/',
                'payload_key' => 'product_pk',
                'payload_value_type' => 'string',
            ],
            'mzakka.com' => [
                'endpoint' => '/maker-products/mzakka/crawl',
                'identifier_type' => 'query_code',
                'identifier_key' => 'item_id',
                'required_path' => '/pc/detail/item.php',
                'payload_key' => 'product_pk',
                'payload_value_type' => 'string',
            ],
            'nobunaga-toys.com' => [
                'endpoint' => '/maker-products/nobunaga/crawl',
                'identifier_type' => 'query_numeric',
                'identifier_key' => 'pid',
                'required_path' => '/',
                'payload_key' => 'product_pk',
                'payload_value_type' => 'int',
                'path_error' => '노부나가 URL은 사이트 최상위 상품 페이지여야 합니다.',
                'identifier_error' => '노부나가 URL에는 유효한 pid 값이 필요합니다.',
            ],
            'e-nls.com' => [
                'endpoint' => '/maker-products/nls/crawl',
                'identifier_type' => 'path_pict_numeric',
                'payload_key' => 'product_pk',
                'payload_value_type' => 'int',
            ],
            'ms-online.co.jp' => [
                'endpoint' => '/maker-products/ms/crawl',
                'identifier_type' => 'query_numeric',
                'identifier_key' => 'pclass_id',
                'payload_key' => 'product_pk',
                'payload_value_type' => 'string',
                'identifier_error' => '엠즈 URL에는 유효한 pclass_id 값이 필요합니다.',
            ],
        ];
        $collector = $collectorEndpoints[$host] ?? null;
        if ($collector === null) {
            throw new \InvalidArgumentException('현재 정보수집이 지원되지 않는 도메인입니다: ' . $host);
        }

        $path = (string)($urlParts['path'] ?? '');
        $identifier = '';
        if ($collector['identifier_type'] === 'query') {
            $pageName = basename($path);
            parse_str((string)($urlParts['query'] ?? ''), $queryParams);
            $identifier = trim((string)($queryParams[$collector['identifier_key']] ?? ''));
            if ($pageName !== 'detail.php' || !ctype_digit($identifier) || (int)$identifier < 1) {
                throw new \InvalidArgumentException('닛포리기프트 URL에는 detail.php 페이지와 유효한 product_id 값이 필요합니다.');
            }
        } elseif ($collector['identifier_type'] === 'path_code') {
            $pathPrefix = (string)$collector['required_path_prefix'];
            if (strpos($path, $pathPrefix) !== 0) {
                throw new \InvalidArgumentException('타마토이즈 URL은 /item/detail/상품코드 형식이어야 합니다.');
            }
            $identifier = trim((string)basename($path));
            if ($identifier === '' || !preg_match('/^[A-Za-z0-9_-]+$/', $identifier)) {
                throw new \InvalidArgumentException('타마토이즈 상품 코드가 올바르지 않습니다.');
            }
        } elseif ($collector['identifier_type'] === 'query_code') {
            parse_str((string)($urlParts['query'] ?? ''), $queryParams);
            $identifier = trim((string)($queryParams[$collector['identifier_key']] ?? ''));
            if ($path !== (string)$collector['required_path']) {
                throw new \InvalidArgumentException('엠자카 URL은 /pc/detail/item.php 상품 상세 페이지여야 합니다.');
            }
            if ($identifier === '' || !preg_match('/^[A-Za-z0-9_-]+$/', $identifier)) {
                throw new \InvalidArgumentException('엠자카 URL에는 유효한 item_id 값이 필요합니다.');
            }
        } elseif ($collector['identifier_type'] === 'query_numeric') {
            parse_str((string)($urlParts['query'] ?? ''), $queryParams);
            $identifier = trim((string)($queryParams[$collector['identifier_key']] ?? ''));
            if (isset($collector['required_path']) && $path !== (string)$collector['required_path']) {
                throw new \InvalidArgumentException((string)($collector['path_error'] ?? '상품 상세 페이지 URL이 올바르지 않습니다.'));
            }
            if ($identifier === '' || !ctype_digit($identifier) || (int)$identifier < 1) {
                throw new \InvalidArgumentException((string)($collector['identifier_error'] ?? '상품 식별 값이 올바르지 않습니다.'));
            }
        } elseif ($collector['identifier_type'] === 'path_pict_numeric') {
            if (!preg_match('#^/pict[0-9]+-([1-9][0-9]*)/?$#', $path, $nlsMatches)) {
                throw new \InvalidArgumentException('NLS URL은 /pict1-상품번호 형식이어야 합니다.');
            }
            $identifier = $nlsMatches[1];
        }

        $matchedProductPk = (int)($data['matched_product_pk'] ?? 0);
        $requesterUserPk = (int)($data['requester_user_pk'] ?? 0);
        $requesterUserName = trim((string)($data['requester_user_name'] ?? ''));
        if ($matchedProductPk < 1) {
            throw new \InvalidArgumentException('연결할 내부 상품 번호가 올바르지 않습니다.');
        }
        if ($requesterUserPk < 1 || $requesterUserName === '') {
            throw new \RuntimeException('로그인 사용자 정보를 확인할 수 없습니다.');
        }

        $payload = [
            'matched_product_pk' => $matchedProductPk,
            'requester_user_pk' => $requesterUserPk,
            'requester_user_name' => $requesterUserName,
            'source_url' => $collectionUrl,
            'requested_at' => (new \DateTimeImmutable('now', new \DateTimeZone('Asia/Seoul')))->format(DATE_ATOM),
        ];
        $payload[$collector['payload_key']] = ($collector['payload_value_type'] ?? 'string') === 'int'
            ? (int)$identifier
            : $identifier;
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-API-KEY: ' . $this->apiKey,
        ];
        $httpResult = HttpClient::postDataWithMeta($this->domain . $collector['endpoint'], $payload, $headers);
        $response = (string)($httpResult['response'] ?? '');
        $responseData = json_decode($response, true);
        if (!is_array($responseData)) {
            $httpCode = (int)($httpResult['http_code'] ?? 0);
            $curlError = trim((string)($httpResult['curl_error'] ?? ''));
            $rawResponse = trim($response);
            $detail = $rawResponse !== '' ? $rawResponse : $curlError;
            if (function_exists('mb_substr')) {
                $detail = mb_substr($detail, 0, 3000, 'UTF-8');
            } else {
                $detail = substr($detail, 0, 3000);
            }
            throw new \RuntimeException('정보수집 API 응답을 읽을 수 없습니다. HTTP ' . $httpCode . ($detail !== '' ? ' | 원문: ' . $detail : ''));
        }
        if (isset($responseData['success']) && !$responseData['success']) {
            $rawResponse = json_encode($responseData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            throw new \RuntimeException((string)($responseData['message'] ?? '정보수집 요청에 실패했습니다.') . ($rawResponse ? ' | 원문: ' . $rawResponse : ''));
        }

        return $responseData;
    }

}