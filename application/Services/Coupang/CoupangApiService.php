<?php
namespace App\Services\Coupang;

use Exception;
use App\Auth\AdminAuth;
use App\Classes\DB;
use App\Models\CoupangProductModel;
use App\Utils\HttpClient;

class CoupangApiService
{

    /**
     * 쿠팡 상품 동기화
     * 
     * @param array $payload
     * @return array
     */
    public function productSync($payload)
    {
        $maxPerSize = (int)($payload['maxPerSize'] ?? 100);
        if ($maxPerSize < 1 || $maxPerSize > 100) {
            $maxPerSize = 100;
        }

        $nextToken = $payload['nextToken'] ?? 1;
        if ($nextToken === '' || $nextToken === null) {
            $nextToken = 1;
        }

        $basePayload = [
            'maxPerSize' => $maxPerSize,
            'sellerProductId' => $payload['sellerProductId'] ?? null,
            'sellerProductName' => $payload['sellerProductName'] ?? null,
            'status' => $payload['status'] ?? null,
            'manufacture' => $payload['manufacture'] ?? null,
            'createdAt' => $payload['createdAt'] ?? null,
        ];

        $pageCount = 0;
        $fetchedCount = 0;
        $createdCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $visitedTokens = [];

        while (true) {
            $pageCount++;

            if ($pageCount > 1000) {
                throw new Exception('쿠팡 동기화 안전 제한(1000페이지)을 초과했습니다.');
            }

            $requestPayload = $basePayload;
            $requestPayload['nextToken'] = $nextToken;

            $apiData = $this->getCoupangPrdList($requestPayload);
            if (!is_array($apiData)) {
                throw new Exception('쿠팡 API 응답 형식이 올바르지 않습니다.');
            }

            $apiCode = $apiData['code'] ?? '';
            if ($apiCode !== 'SUCCESS') {
                $apiMessage = $apiData['message'] ?? '쿠팡 API 호출 실패';
                throw new Exception($apiMessage);
            }

            $items = $apiData['data'] ?? [];
            if (!is_array($items)) {
                $items = [];
            }

            foreach ($items as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $fetchedCount++;
                $sellerProductId = $item['sellerProductId'] ?? null;
                $productId = $item['productId'] ?? null;
                if (empty($sellerProductId) || empty($productId)) {
                    $skippedCount++;
                    continue;
                }

                $exists = CoupangProductModel::where('seller_product_id', $sellerProductId)->first();
                if (!$exists && !empty($productId)) {
                    $exists = CoupangProductModel::where('product_id', $productId)->first();
                }

                $coupangCreatedAt = $this->normalizeDateTime($item['createdAt'] ?? null);
                if (empty($coupangCreatedAt)) {
                    $coupangCreatedAt = $this->normalizeDateTime($exists->coupang_created_at ?? null);
                }
                if (empty($coupangCreatedAt)) {
                    $coupangCreatedAt = date('Y-m-d H:i:s');
                }
                $thumbnail = $this->extractRepresentationCdnPath($item['images'] ?? null);
                if (empty($thumbnail)) {
                    $thumbnail = $exists->thumbnail ?? null;
                }

                $syncData = [
                    'product_id' => $productId,
                    'seller_product_id' => $sellerProductId,
                    'name' => $item['sellerProductName'] ?? ($exists->name ?? null),
                    'brand' => $item['brand'] ?? ($exists->brand ?? null),
                    'category_id' => $item['categoryId'] ?? ($exists->category_id ?? null),
                    'display_category_code' => $item['displayCategoryCode'] ?? ($exists->display_category_code ?? null),
                    'status' => $item['statusName'] ?? ($item['status'] ?? ($exists->status ?? null)),
                    'sale_started_at' => $this->normalizeDateTime($item['saleStartedAt'] ?? null) ?? ($exists->sale_started_at ?? null),
                    'sale_ended_at' => $this->normalizeDateTime($item['saleEndedAt'] ?? null) ?? ($exists->sale_ended_at ?? null),
                    'coupang_created_at' => $coupangCreatedAt,
                    'vendor_id' => $item['vendorId'] ?? ($exists->vendor_id ?? null),
                    'md_id' => $exists->md_id ?? null,
                    'md_name' => $exists->md_name ?? null,
                    'thumbnail' => $thumbnail,
                    'raw_json' => json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'synced_at' => date('Y-m-d H:i:s'),
                ];
                $syncData = $this->filterExistingCoupangProductColumns($syncData);

                try {
                    if ($exists) {
                        CoupangProductModel::where('idx', $exists->idx)->update($syncData);
                        $updatedCount++;
                    } else {
                        CoupangProductModel::create($syncData);
                        $createdCount++;
                    }
                } catch (Exception $e) {
                    // 동시성 등으로 간헐적인 unique 충돌이 발생하면 스킵 처리
                    if (strpos($e->getMessage(), '1062') !== false) {
                        $skippedCount++;
                        continue;
                    }
                    throw $e;
                }
            }

            $responseNextToken = trim((string)($apiData['nextToken'] ?? ''));

            if ($responseNextToken === '') {
                break;
            }

            if (isset($visitedTokens[$responseNextToken])) {
                break;
            }

            if ((string)$responseNextToken === (string)$nextToken) {
                break;
            }

            $visitedTokens[$responseNextToken] = true;
            $nextToken = $responseNextToken;
        }

        return [
            'fetched_count' => $fetchedCount,
            'created_count' => $createdCount,
            'updated_count' => $updatedCount,
            'skipped_count' => $skippedCount,
            'page_count' => $pageCount,
            'message' => '쿠팡 상품 동기화 완료 (신규 저장: ' . $createdCount . '건, 기존 수정: ' . $updatedCount . '건, 스킵: ' . $skippedCount . '건)',
        ];
    }


    /**
     * 쿠팡 상품 상세 동기화
     * 
     * @param array $payload
     * @return array
     */
    public function productDetailSync($payload)
    {

        $sellerProductId = $payload['sellerProductId'] ?? ($payload['seller_product_id'] ?? null);
        $payloadProductId = $payload['productId'] ?? ($payload['product_id'] ?? null);
        $syncStock = $this->toBool($payload['syncStock'] ?? ($payload['sync_stock'] ?? 'Y'));
        if (empty($sellerProductId)) {
            throw new Exception('sellerProductId 값이 필요합니다.');
        }

        $apiData = $this->getCoupangProductDetail($sellerProductId);
        if (!is_array($apiData)) {
            throw new Exception('쿠팡 상세 API 응답 형식이 올바르지 않습니다.');
        }

        $apiCode = $apiData['code'] ?? '';
        if ($apiCode !== 'SUCCESS') {
            $apiMessage = $apiData['message'] ?? '쿠팡 상세 API 호출 실패';
            throw new Exception($apiMessage);
        }

        $detail = $apiData['data'] ?? [];
        if (!is_array($detail) || empty($detail)) {
            throw new Exception('쿠팡 상세 데이터가 없습니다.');
        }

        $resolvedSellerProductId = $detail['sellerProductId'] ?? $sellerProductId;

        if (empty($resolvedSellerProductId)) {
            throw new Exception('sellerProductId가 응답에 없습니다.');
        }

        // 상세 응답에 productId가 없는 케이스 대응:
        // 1) 응답 productId
        // 2) 요청 payload productId
        // 3) 기존 DB product_id 재사용
        $exists = CoupangProductModel::where('seller_product_id', $resolvedSellerProductId)->first();
        $productId = $detail['productId'] ?? $payloadProductId ?? ($exists->product_id ?? null);
        if (!$exists && !empty($productId)) {
            $exists = CoupangProductModel::where('product_id', $productId)->first();
        }

        if (empty($productId)) {
            throw new Exception('productId가 응답/요청/기존데이터 어디에도 없어 상세 동기화를 진행할 수 없습니다.');
        }

        $items = isset($detail['items']) && is_array($detail['items']) ? $detail['items'] : [];
        $contents = isset($detail['contents']) && is_array($detail['contents']) ? $detail['contents'] : [];
        $images = isset($detail['images']) && is_array($detail['images']) ? $detail['images'] : [];
        $notices = isset($detail['notices']) && is_array($detail['notices']) ? $detail['notices'] : [];
        $attributes = isset($detail['attributes']) && is_array($detail['attributes']) ? $detail['attributes'] : [];
        $searchTags = isset($detail['searchTags']) ? $detail['searchTags'] : [];
        $extraProperties = $detail['extraProperties'] ?? null;
        $certifications = isset($detail['certifications']) && is_array($detail['certifications']) ? $detail['certifications'] : [];
        $bundleInfo = $detail['bundleInfo'] ?? null;

        $syncAdminInfo = $this->buildSyncAdminInfo();
        $now = date('Y-m-d H:i:s');
        $firstItem = $items[0] ?? [];
        $coupangCreatedAt = $this->normalizeDateTime($detail['createdAt'] ?? null);
        if (empty($coupangCreatedAt)) {
            $coupangCreatedAt = $this->normalizeDateTime($exists->coupang_created_at ?? null);
        }
        if (empty($coupangCreatedAt)) {
            $coupangCreatedAt = $now;
        }
        $resolvedCategoryId = $detail['categoryId'] ?? ($exists->category_id ?? null);
        $resolvedDisplayCategoryCode = $detail['displayCategoryCode'] ?? ($exists->display_category_code ?? null);
        $resolvedThumbnail = $this->extractRepresentationCdnPath($images);
        if (empty($resolvedThumbnail)) {
            $resolvedThumbnail = $this->extractRepresentationCdnPathFromItems($items);
        }
        if (empty($resolvedThumbnail)) {
            $resolvedThumbnail = $exists->thumbnail ?? null;
        }

        //-----------------------------------------------------------------------------------------------------------------------

        // 로켓상품 여부
        if( !empty($firstItem['rocketGrowthItemData']) ){
            $isRocket = true;
            $is_rocket = 'Y';
            $vendor_item_id = $firstItem['marketplaceItemData']['vendorItemId'] ?? null;
            $rocket_vendor_item_id = $firstItem['rocketGrowthItemData']['vendorItemId'] ?? null;

            if ($syncStock) {
                $stock_json = $this->getCoupangProductItemDetail($vendor_item_id);
                $rocket_stock_json = $this->getCoupangProductItemDetail($rocket_vendor_item_id);
            } else {
                $stock_json = $exists->stock_json ?? null;
                $rocket_stock_json = $exists->rocket_stock_json ?? null;
            }

        }else{
            $isRocket = false;
            $is_rocket = 'N';
            $vendor_item_id = $firstItem['vendorItemId'] ?? null;
            $rocket_vendor_item_id = null;

            if ($syncStock) {
                $stock_json = $this->getCoupangProductItemDetail($vendor_item_id);
            } else {
                $stock_json = $exists->stock_json ?? null;
            }
            $rocket_stock_json = null;

        }

        //-----------------------------------------------------------------------------------------------------------------------

        $saveData = [
            'product_id' => $productId,
            'seller_product_id' => $resolvedSellerProductId,

            'name' => $detail['sellerProductName'] ?? null,
            'brand' => $detail['brand'] ?? null,
            'category_id' => $resolvedCategoryId,
            'display_category_code' => $resolvedDisplayCategoryCode,

            'status' => $detail['statusName'] ?? null,
            'requested' => $this->toYn($detail['requested'] ?? null),
            'status_updated_at' => $now,

            'sale_started_at' => $this->normalizeDateTime($detail['saleStartedAt'] ?? null),
            'sale_ended_at' => $this->normalizeDateTime($detail['saleEndedAt'] ?? null),
            'coupang_created_at' => $coupangCreatedAt,

            'vendor_id' => $detail['vendorId'] ?? null,
            'md_id' => null,
            'md_name' => null,
            'thumbnail' => $resolvedThumbnail,

            'display_product_name' => $detail['displayProductName'] ?? null,
            'general_product_name' => $detail['generalProductName'] ?? null,
            'manufacturer' => $detail['manufacturer'] ?? null,
            'product_group' => $detail['productGroup'] ?? null,

            'delivery_method' => $detail['deliveryMethod'] ?? null,
            'delivery_company_code' => $detail['deliveryCompanyCode'] ?? null,
            'delivery_charge_type' => $detail['deliveryChargeType'] ?? null,
            'delivery_charge' => $detail['deliveryCharge'] ?? null,
            'free_ship_over_amount' => $detail['freeShipOverAmount'] ?? null,
            'delivery_charge_on_return' => $detail['deliveryChargeOnReturn'] ?? null,
            'return_charge' => $detail['returnCharge'] ?? null,
            'remote_area_deliverable' => $detail['remoteAreaDeliverable'] ?? null,
            'outbound_shipping_place_code' => $detail['outboundShippingPlaceCode'] ?? null,

            'return_center_code' => $detail['returnCenterCode'] ?? null,
            'return_charge_name' => $detail['returnChargeName'] ?? null,
            'company_contact_number' => $detail['companyContactNumber'] ?? null,
            'return_zip_code' => $detail['returnZipCode'] ?? null,

            'adult_only' => $detail['adultOnly'] ?? null,
            'tax_type' => $detail['taxType'] ?? null,
            'parallel_imported' => $detail['parallelImported'] ?? null,
            'overseas_purchased' => $detail['overseasPurchased'] ?? null,
            'pcc_needed' => $detail['pccNeeded'] ?? null,

            'offer_condition' => $detail['offerCondition'] ?? null,
            'offer_description' => $detail['offerDescription'] ?? null,

            'search_tags' => $this->encodeJson($searchTags),
            'extra_properties' => $this->encodeJson($extraProperties),
            'certifications_json' => $this->encodeJson($certifications),
            'bundle_info' => $this->encodeJson($bundleInfo),

            'content_json' => $this->encodeJson($contents),
            'contents_type' => $contents[0]['contentsType'] ?? null,
            'images_json' => $this->encodeJson($images),
            'notices_json' => $this->encodeJson($notices),
            'attributes_json' => $this->encodeJson($attributes),
            'items_json' => $this->encodeJson($items),
            'main_vendor_item_id' => $firstItem['vendorItemId'] ?? null,

            'raw_json' => $this->encodeJson($detail),
            'detail_loaded_at' => $now,
            'last_synced_at' => $now,
            'last_synced_by' => (int)($syncAdminInfo['admin_pk'] ?? 0),
            'last_synced_by_info' => $this->encodeJson($syncAdminInfo),

            'vendor_item_id' => $vendor_item_id,
            'rocket_vendor_item_id' => $rocket_vendor_item_id,
            'is_rocket' => $is_rocket,
            'stock_json' => is_string($stock_json) ? $stock_json : $this->encodeJson($stock_json),
            'rocket_stock_json' => is_string($rocket_stock_json) ? $rocket_stock_json : $this->encodeJson($rocket_stock_json),
            'stock_synced_at' => $syncStock ? $now : ($exists->stock_synced_at ?? null),
        ];
        $saveData = $this->filterExistingCoupangProductColumns($saveData);

        if ($exists) {
            if (empty($exists->api_created_at)) {
                $saveData['api_created_at'] = $now;
            }
            $saveData = $this->filterExistingCoupangProductColumns($saveData);

            CoupangProductModel::where('idx', $exists->idx)->update($saveData);
        } else {
            $createData = $saveData;
            $createData['synced_at'] = $now;
            $createData['api_created_at'] = $now;
            $createData = $this->filterExistingCoupangProductColumns($createData);

            CoupangProductModel::create($createData);
        }

        return [
            'seller_product_id' => $resolvedSellerProductId,
            'product_id' => $productId,
            'message' => '상품 상세 데이터 수집이 완료되었습니다. (sellerProductId: ' . $resolvedSellerProductId . ')',
        ];
    }


    /**
     * 상품 아이템별 수량/가격/상태 조회
     * 
     * @param string $vendorItemId
     * @return array
     */
    public function getCoupangProductItemDetail($vendorItemId)
    {

        $coupangConfig = config('admin.coupang');
        $vendorId = $coupangConfig['company_code'];

        $apiUrl = "https://api-gateway.coupang.com/v2/providers/seller_api/apis/api/v1/marketplace/vendor-items/" . rawurlencode((string)$vendorItemId)."/inventories";
        $headers = $this->buildCoupangHeaders('GET', $apiUrl, $coupangConfig, $vendorId);

        $response = HttpClient::getData($apiUrl, $headers);
        $apiData = json_decode($response, true);

        return is_array($apiData) ? $apiData : [];

    }


    private function getCoupangProductDetail($sellerProductId)
    {
        $coupangConfig = config('admin.coupang');
        $vendorId = $coupangConfig['company_code'];

        $apiUrl = "https://api-gateway.coupang.com/v2/providers/seller_api/apis/api/v1/marketplace/seller-products/" . rawurlencode((string)$sellerProductId);
        $headers = $this->buildCoupangHeaders('GET', $apiUrl, $coupangConfig, $vendorId);

        $response = HttpClient::getData($apiUrl, $headers);
        $apiData = json_decode($response, true);

        return is_array($apiData) ? $apiData : [];
    }

    private function buildSyncAdminInfo()
    {
        $auth = AdminAuth::user() ?? [];
        $adminPk = (int)($auth['sess_idx'] ?? 0);
        $loginId = (string)($auth['sess_id'] ?? '');
        $name = (string)($auth['sess_name'] ?? '');
        $adminLevel = (int)($auth['ad_level'] ?? 0);
        $role = $adminLevel >= 100 ? 'super_admin' : 'admin';

        return [
            'admin_pk' => $adminPk,
            'login_id' => $loginId,
            'name' => $name,
            'role' => $role,
        ];
    }

    private function encodeJson($value)
    {
        if ($value === null) {
            return null;
        }

        $encoded = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($encoded === false) {
            return null;
        }

        return $encoded;
    }

    private function filterExistingCoupangProductColumns(array $data)
    {
        $columns = $this->getCoupangProductTableColumns();
        if (empty($columns)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($columns));
    }

    private function getCoupangProductTableColumns()
    {
        static $columns = null;
        if ($columns !== null) {
            return $columns;
        }

        try {
            $connection = DB::connection();
            $statement = $connection->query("SHOW COLUMNS FROM `coupang_products`");
            $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

            $columns = [];
            foreach ($rows as $row) {
                if (isset($row['Field'])) {
                    $columns[] = $row['Field'];
                }
            }
        } catch (\Exception $e) {
            $columns = [];
        }

        return $columns;
    }

    private function toYn($value)
    {
        if (is_bool($value)) {
            return $value ? 'Y' : 'N';
        }

        if ($value === null || $value === '') {
            return null;
        }

        $normalized = strtolower(trim((string)$value));
        if (in_array($normalized, ['y', 'yes', 'true', '1'], true)) {
            return 'Y';
        }
        if (in_array($normalized, ['n', 'no', 'false', '0'], true)) {
            return 'N';
        }

        return (string)$value;
    }

    private function toBool($value)
    {
        if (is_bool($value)) {
            return $value;
        }
        if ($value === null) {
            return false;
        }
        $normalized = strtolower(trim((string)$value));
        return in_array($normalized, ['y', 'yes', 'true', '1'], true);
    }

    public function getCoupangPrdList($payload)
    {

        $coupangConfig = config('admin.coupang');

        $vendorId = $coupangConfig['company_code'];
        $nextToken = $payload['nextToken'] ?? 1;
        $maxPerSize = $payload['maxPerSize'] ?? 100;
        $sellerProductId = $payload['sellerProductId'] ?? null;
        $sellerProductName = $payload['sellerProductName'] ?? null;
        $status = $payload['status'] ?? null;
        $manufacture = $payload['manufacture'] ?? null;
        $createdAt = $payload['createdAt'] ?? null; 


        $baseUrl = "https://api-gateway.coupang.com/v2/providers/seller_api/apis/api/v1/marketplace/seller-products";
        $queryParts = [
            ['vendorId', $vendorId],
            ['nextToken', $nextToken],
            ['maxPerPage', $maxPerSize],
        ];

        //등록상품ID
        if($sellerProductId) {
            $queryParts[] = ['sellerProductId', $sellerProductId];
        }

        //등록상품명
        if($sellerProductName) {
            $queryParts[] = ['sellerProductName', $sellerProductName];
        }

        //업체상품상태
        /*
            IN_REVIEW	심사중
            SAVED	임시저장
            APPROVING	승인대기중
            APPROVED	승인완료
            PARTIAL_APPROVED	부분승인완료
            DENIED	승인반려
            DELETED	상품삭제
        */
        if($status) {
            $queryParts[] = ['status', $status];
        }

        //제조사
        if($manufacture) {
            $queryParts[] = ['manufacture', $manufacture];
        }

        //상품등록일시
        if($createdAt) {
            $queryParts[] = ['createdAt', $createdAt];
        }

        $queryParts[] = ['violationTypes', 'ATTR'];
        $queryParts[] = ['violationTypes', 'MOTA_V2'];
        $queryParts[] = ['violationTypeAndOr', 'OR'];

        $queryString = $this->buildQueryString($queryParts);
        $apiUrl = $baseUrl . '?' . $queryString;
        $headers = $this->buildCoupangHeaders('GET', $apiUrl, $coupangConfig, $vendorId);

        $response = HttpClient::getData($apiUrl, $headers);
        
        $apiData = json_decode($response, true);
        return is_array($apiData) ? $apiData : [];
    }

    private function buildCoupangHeaders($method, $url, $coupangConfig, $vendorId)
    {
        $signedDate = gmdate('ymd\THis\Z');
        $path = parse_url($url, PHP_URL_PATH);
        $queryString = parse_url($url, PHP_URL_QUERY);
        $message = $signedDate . strtoupper($method) . $path . (string)$queryString;
        $signature = hash_hmac('sha256', $message, $coupangConfig['secret_key']);
        $authorization = 'CEA algorithm=HmacSHA256, access-key=' . $coupangConfig['access_key'] . ', signed-date=' . $signedDate . ', signature=' . $signature;

        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $authorization,
            'X-Requested-By: ' . $vendorId,
        ];

        if (!empty($coupangConfig['market'])) {
            $headers[] = 'X-Market: ' . $coupangConfig['market'];
        }

        return $headers;
    }

    private function buildQueryString($queryParts)
    {
        $encodedQuery = [];

        foreach ($queryParts as $part) {
            $key = $part[0];
            $value = $part[1];
            $encodedQuery[] = rawurlencode((string)$key) . '=' . rawurlencode((string)$value);
        }

        return implode('&', $encodedQuery);
    }

    private function normalizeDateTime($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $timestamp = strtotime((string)$value);
        if ($timestamp === false) {
            return null;
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    private function extractRepresentationCdnPath($images)
    {
        if (!is_array($images)) {
            return null;
        }

        foreach ($images as $image) {
            if (!is_array($image)) {
                continue;
            }

            $imageType = strtoupper((string)($image['imageType'] ?? ''));
            if ($imageType !== 'REPRESENTATION') {
                continue;
            }

            $cdnPath = trim((string)($image['cdnPath'] ?? ''));
            if ($cdnPath !== '') {
                return $cdnPath;
            }
        }

        return null;
    }

    private function extractRepresentationCdnPathFromItems($items)
    {
        if (!is_array($items)) {
            return null;
        }

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $cdnPath = $this->extractRepresentationCdnPath($item['images'] ?? null);
            if (!empty($cdnPath)) {
                return $cdnPath;
            }
        }

        return null;
    }

}