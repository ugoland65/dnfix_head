<?php

namespace App\Services;

use Exception;
use App\Models\ProductStockHistoryModel;
use App\Models\ProductModel;
use App\Models\BrandModel;
use App\Models\ProductStockModel;
use App\Services\ProductService;

class ProductStockHistoryService
{

    /**
     * 피킹리스트 조회
     * @param int $idx
     * @return array
     */
    public function getPickingList($idx)
    {

        if (!$idx) {
            throw new Exception('idx is required');
        }

        $productStockHistoryData = ProductStockHistoryModel::find($idx);
        
        if (!$productStockHistoryData) {
            throw new Exception('ProductStockHistory not found');
        }

        $productStockHistoryArray = $productStockHistoryData->toArray();
        $pickingData = json_decode($productStockHistoryArray['data'] ?? '[]', true);

        if (!is_array($pickingData) || empty($pickingData)) {
            return [];
        }

        // 1단계: 모든 외래키 수집 (중복 제거)
        $psIdxList = [];
        $cdIdxList = [];
        $brandIdxList = [];

        foreach ($pickingData as $row) {
            if (!empty($row['ps_idx'])) {
                $psIdxList[] = (int)$row['ps_idx'];
            }
            if (!empty($row['cd_idx'])) {
                $cdIdxList[] = (int)$row['cd_idx'];
            }
            if (!empty($row['brand_idx'])) {
                $brandIdxList[] = (int)$row['brand_idx'];
            }
        }

        $psIdxList = array_unique($psIdxList);
        $cdIdxList = array_unique($cdIdxList);
        $brandIdxList = array_unique($brandIdxList);

        // 2단계: 각 모델에서 whereIn으로 한 번에 조회
        $productStocks = [];
        if (!empty($psIdxList)) {
            $productStocksData = ProductStockModel::query()
                ->select(['ps_idx', 'ps_prd_idx', 'ps_rack_code', 'ps_stock'])
                ->whereIn('ps_idx', $psIdxList)
                ->get()
                ->toArray();
            
            foreach ($productStocksData as $stock) {
                $productStocks[$stock['ps_idx']] = $stock;
            }
        }

        $products = [];
        if (!empty($cdIdxList)) {
            $productsData = ProductModel::query()
                ->select(['CD_IDX', 'CD_NAME', 'CD_IMG', 'CD_CODE', 'CD_BRAND_IDX', 'cd_size_fn'])
                ->whereIn('CD_IDX', $cdIdxList)
                ->get()
                ->toArray();
            
            $productService = new ProductService();

            foreach ($productsData as $product) {
                $product['cd_size_fn'] = json_decode($product['cd_size_fn'] ?? '{}', true);
                if (!is_array($product['cd_size_fn'])) {
                    $product['cd_size_fn'] = [];
                }

                $_cd_size_w = (float)($product['cd_size_fn']['package']['W'] ?? 0);
                $_cd_size_h = (float)($product['cd_size_fn']['package']['H'] ?? 0);
                $_cd_size_d = (float)($product['cd_size_fn']['package']['D'] ?? 0);

                if( !empty($_cd_size_w) || !empty($_cd_size_h) || !empty($_cd_size_d) ){
                    $_cd_size_volume = $_cd_size_w * $_cd_size_h * $_cd_size_d;
                    $product['package_volume'] = $_cd_size_volume;
                    $product['package_volume_m3'] = $_cd_size_volume / 1000000;
                    $product['package_volume_level'] = $productService->getVolumeLevel($_cd_size_volume);
                }else{
                    $product['package_volume'] = 0;
                    $product['package_volume_m3'] = 0;
                    $product['package_volume_level'] = 0;    
                }

                $products[$product['CD_IDX']] = $product;
            }
        }

        $brands = [];
        if (!empty($brandIdxList)) {
            $brandsData = BrandModel::query()
                ->select(['BD_IDX', 'BD_NAME'])
                ->whereIn('BD_IDX', $brandIdxList)
                ->get()
                ->toArray();
            
            foreach ($brandsData as $brand) {
                $brands[$brand['BD_IDX']] = $brand;
            }
        }

        // 3단계: 데이터 맵핑 및 결과 배열 구성
        $pickingDataResult = [];

        foreach ($pickingData as $row) {
            $psIdx = (int)($row['ps_idx'] ?? 0);
            $cdIdx = (int)($row['cd_idx'] ?? 0);
            $brandIdx = (int)($row['brand_idx'] ?? 0);

            // ProductStock 데이터
            $stockData = $productStocks[$psIdx] ?? null;
            
            // Product 데이터
            $productData = $products[$cdIdx] ?? null;
            
            // Brand 데이터 (product에서 CD_BRAND_IDX를 사용하거나 row의 brand_idx 사용)
            $brandIdxToUse = $brandIdx;
            if (!$brandIdxToUse && $productData) {
                $brandIdxToUse = (int)($productData['CD_BRAND_IDX'] ?? 0);
            }
            $brandData = $brands[$brandIdxToUse] ?? null;

            // 원본 $pickingData의 모든 데이터 + 조회된 JOIN 데이터 병합
            // $row의 모든 키-값이 유지되며, 아래 키들이 추가/덮어쓰기됨
            $currentStock = (int)($stockData['ps_stock'] ?? 0);
            $currentQty = (int)($row['qty'] ?? 0);
            
            $resultRow = array_merge($row, [
                // ProductStock 데이터
                'ps_prd_idx' => $stockData['ps_prd_idx'] ?? null,
                'ps_rack_code' => $stockData['ps_rack_code'] ?? '',
                'ps_stock' => $currentStock,
                'ps_stock_sum' => $currentStock - $currentQty,
                
                // Product 데이터
                'CD_NAME' => $productData['CD_NAME'] ?? '',
                'CD_IMG' => $productData['CD_IMG'] ?? '',
                'CD_CODE' => $productData['CD_CODE'] ?? '',
                'package_volume' => $productData['package_volume'] ?? 0,
                'package_volume_m3' => $productData['package_volume_m3'] ?? 0,
                'package_volume_level' => $productData['package_volume_level'] ?? 0,
                
                // Brand 데이터
                'BD_NAME' => $brandData['BD_NAME'] ?? '',
                'brand_name' => $brandData['BD_NAME'] ?? '', // 뷰 호환성
                
                // 계산 필드
                'ps_stock_sum' => $currentStock - $currentQty, // 남는 재고
            ]);

            $pickingDataResult[] = $resultRow;
        }

        // ps_rack_code 기준 오름차순 정렬
        usort($pickingDataResult, function($a, $b) {
            $aCode = $a['ps_rack_code'] ?? '';
            $bCode = $b['ps_rack_code'] ?? '';
            return strcmp($aCode, $bCode); // 오름차순 (ASC)
        });

        // error 필드 처리 (JSON 디코드 및 배열로 변환)
        $errorData = $productStockHistoryArray['error'] ?? [];
        
        // error가 JSON 문자열인 경우 디코드
        if (is_string($errorData)) {
            $decodedError = json_decode($errorData, true);
            
            // JSON 디코드 성공 시
            if (is_array($decodedError)) {
                // 'result' 키가 있으면 그것을 사용
                if (isset($decodedError['result']) && is_array($decodedError['result'])) {
                    $errorData = $decodedError['result'];
                } else {
                    // 'result' 키가 없으면 전체 배열 사용
                    $errorData = $decodedError;
                }
            } else {
                // JSON 디코드 실패 시 빈 문자열이면 빈 배열, 아니면 해당 문자열을 배열로
                $errorData = !empty($errorData) ? [$errorData] : [];
            }
        }
        
        // error가 배열이 아닌 경우 빈 배열로 처리
        if (!is_array($errorData)) {
            $errorData = [];
        }

        $result = [
            'pickingList' => $pickingDataResult,
            'error' => $errorData,
        ];

        return $result;
    }

}