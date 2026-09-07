<?php

namespace App\Services;

use Exception;
use App\Models\OrderGroupModel;
use App\Models\OrderGroupProductModel;
use App\Models\ProductModel;
use App\Models\ProductStockModel;
use App\Models\ProductStockUnitModel;
use App\Services\ProductActionService;

class OrderGroupService
{

    /**
     * 주문서 그룹 셀렉트바를 위한 조회
     * @param array|null $criteria 검색 조건
     * @return array
     */
    public function getOnaOrderGroupForSelect($criteria=null) 
    {
        $query = OrderGroupModel::select('oog_idx', 'oog_name')
            ->orderBy('oog_name', 'asc');

        $result = $query->get()
            ->toArray();

        return $result;
    }

    /**
     * 상품 발주서 주문코드 셀렉트용 주문서 폼 목록.
     */
    public function getOrderGroupCodeOptions(): array
    {
        $hiddenCodes = ['jan', 'pcode', 'code3'];
        $rows = OrderGroupModel::query()
            ->select(['oog_idx', 'oog_name', 'oog_code'])
            ->orderBy('oog_name', 'ASC')
            ->get()
            ->toArray();

        $options = [];
        $seenCodes = [];
        foreach ($rows as $row) {
            $code = trim((string)($row['oog_code'] ?? ''));
            if ($code === '' || in_array($code, $hiddenCodes, true) || isset($seenCodes[$code])) {
                continue;
            }
            $seenCodes[$code] = true;
            $options[] = [
                'oog_idx' => (int)($row['oog_idx'] ?? 0),
                'oog_name' => trim((string)($row['oog_name'] ?? '')),
                'oog_code' => $code,
            ];
        }

        return $options;
    }

    /**
     * 주문서 폼 상세 조회
     * @param int $idx
     * @return array
     */
    public function getOrderGroupInfo($idx)
    {
        $query = OrderGroupModel::find($idx);

        if (!$query) {
            throw new Exception("주문서 폼 정보를 찾을 수 없습니다.");
        }

        $result = $query->toArray();

        $result['bank'] = json_decode($result['bank'] ?? '[]', true);

        return $result; 
    }


    /**
     * 주문서 폼 수정
     * @param array $requestData
     * @return array
     */
    public function updateOrderGroup($requestData)
    {

        $mode = $requestData['mode'] ?? '';
        $idx = $requestData['idx'] ?? null;

        if ( empty($idx) && $mode === 'modify' ) {
            throw new Exception("주문서 폼 번호가 없습니다.");
        }

        $oog_name =  $requestData['oog_name'] ?? "";
        $oog_import = $requestData['oog_import'] ?? "";
        $oog_code = $requestData['oog_code'] ?? "";
        $oog_group = $requestData['oog_group'] ?? "";
        $memo = $requestData['memo'] ?? "";

        $oog_bank_name = $requestData['oog_bank_name'] ?? "";
        $oog_bank_account = $requestData['oog_bank_account'] ?? "";
        $oog_bank_depositor = $requestData['oog_bank_depositor'] ?? "";
        $oog_import_account = $requestData['oog_import_account'] ?? "";
    
        $oog_data_json = [
            'domestic' => [
                'bank' => $oog_bank_name,
                'account' => $oog_bank_account,
                'depositor' => $oog_bank_depositor,
            ],
            'import_account' => $oog_import_account,
        ];
        $bank = json_encode($oog_data_json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $saveData = [
            'oog_name' => $oog_name,
            'oog_import' => $oog_import,
            'oog_code' => $oog_code,
            'oog_group' => $oog_group,
            'bank' => $bank,
            'memo' => $memo,
        ];

        if ($mode === 'create') {
            $newIdx = OrderGroupModel::query()->insertGetId($saveData);
            return [
                'mode' => 'create',
                'idx' => $newIdx,
            ];
        }

        if ($mode === 'modify') {
            $updated = OrderGroupModel::where('oog_idx', $idx)->update($saveData);
            return [
                'mode' => 'modify',
                'idx' => $idx,
                'updated' => $updated,
            ];
        }

        throw new Exception("유효하지 않은 mode 값입니다.");
    }


    /**
     * 주문서 폼 그룹 수정
     * @param array $requestData
     * @return array
     */
    public function updateOrderGroupGroup($requestData)
    {
        $idx = trim((string)($requestData['idx'] ?? ''));
        $nameList = $requestData['name'] ?? [];
        $oopIdxList = $requestData['oop_idx'] ?? [];
        $activeList = $requestData['active'] ?? [];
        $oopCode = trim((string)($requestData['oop_code'] ?? ''));

        if ($idx === '') {
            throw new Exception("주문서 폼 번호가 없습니다.");
        }
        if ($oopCode === '') {
            throw new Exception("가격코드값이 비어있습니다.");
        }

        if (!is_array($nameList)) {
            $nameList = [];
        }
        if (!is_array($oopIdxList)) {
            $oopIdxList = [];
        }
        if (!is_array($activeList)) {
            $activeList = [];
        }

        $dataAry = [];
        $nameCount = count($nameList);
        for ($i = 0; $i < $nameCount; $i++) {
            $thisName = trim((string)($nameList[$i] ?? ''));
            $thisOopIdx = trim((string)($oopIdxList[$i] ?? ''));
            if ($thisName === '') {
                continue;
            }

            if ($thisOopIdx === '') {
                $thisOopIdx = (string)OrderGroupProductModel::query()->insertGetId([
                    'oop_name' => $thisName,
                    'oop_code' => $oopCode,
                    'oop_data' => '[]',
                ]);
            }

            $dataAry[] = [
                'name' => $thisName,
                'active' => (string)($activeList[$i] ?? ''),
                'oop_idx' => $thisOopIdx,
            ];
        }

        $oogBrand = json_encode($dataAry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($oogBrand === false) {
            $oogBrand = '[]';
        }

        $updated = OrderGroupModel::query()
            ->where('oog_idx', '=', $idx)
            ->update([
                'oog_brand' => $oogBrand,
            ]);

        return [
            'updated' => $updated,
            'oog_brand' => $dataAry,
        ];
    }

    /**
     * 폼그룹 상품의 고도몰 재입고 알림 요청 수를 일괄 수집한다.
     */
    public function syncOrderGroupRestockAlertCounts(array $requestData): array
    {
        $oopIdx = trim((string)($requestData['oop_idx'] ?? ''));
        $actionUrl = trim((string)($requestData['action_url'] ?? ($_SERVER['HTTP_REFERER'] ?? $_SERVER['REQUEST_URI'] ?? '')));
        if ($oopIdx === '' || !ctype_digit($oopIdx) || (int)$oopIdx <= 0) {
            throw new Exception('유효한 폼그룹 번호가 없습니다.');
        }

        $orderGroupProduct = OrderGroupProductModel::query()
            ->select(['oop_idx', 'oop_data'])
            ->where('oop_idx', '=', (int)$oopIdx)
            ->first();
        if (!$orderGroupProduct) {
            throw new Exception('폼그룹 상품을 찾을 수 없습니다.');
        }

        $orderGroupProductData = $orderGroupProduct->toArray();
        $oopData = json_decode($orderGroupProductData['oop_data'] ?? '[]', true);
        if (!is_array($oopData)) {
            throw new Exception('폼그룹 상품 데이터 형식이 올바르지 않습니다.');
        }

        $prdIdxs = [];
        foreach ($oopData as $item) {
            if (!is_array($item)) {
                continue;
            }
            $prdIdx = (int)($item['idx'] ?? 0);
            if ($prdIdx > 0) {
                $prdIdxs[] = $prdIdx;
            }
        }

        $productActionService = new ProductActionService();
        return $productActionService->syncGodoRestockAlertCounts($prdIdxs, $actionUrl);
    }

    /**
     * 주문서폼 그룹 상품관리 화면 데이터.
     */
    public function getFormGroupProductPageData($oopIdx): array
    {
        $oopIdx = (int)$oopIdx;
        $brandForSelect = (new BrandService())->getBrandForSelect(['listActive' => true]);
        if ($oopIdx <= 0) {
            return [
                'oopIdx' => 0,
                'products' => [],
                'brandForSelect' => $brandForSelect,
            ];
        }

        $orderGroupProduct = OrderGroupProductModel::query()
            ->where('oop_idx', '=', $oopIdx)
            ->first();
        if (!$orderGroupProduct) {
            throw new Exception('폼그룹 상품을 찾을 수 없습니다.');
        }

        $orderGroupProductData = $orderGroupProduct->toArray();
        $oopRows = $this->decodeFormGroupProductJson($orderGroupProductData['oop_data'] ?? '');
        $prdIdxs = [];
        foreach ($oopRows as $row) {
            $prdIdx = (int)($row['idx'] ?? 0);
            if ($prdIdx > 0) {
                $prdIdxs[] = $prdIdx;
            }
        }
        $prdIdxs = array_values(array_unique($prdIdxs));

        $productByIdx = [];
        if (!empty($prdIdxs)) {
            $productRows = ProductModel::query()
                ->from('COMPARISON_DB as A')
                ->leftJoin('prd_stock as B', 'B.ps_prd_idx', '=', 'A.CD_IDX')
                ->whereIn('A.CD_IDX', $prdIdxs)
                ->select([
                    'A.CD_IDX',
                    'A.CD_IMG',
                    'A.CD_CODE2',
                    'A.CD_CODE3',
                    'A.CD_NAME',
                    'B.ps_idx',
                ])
                ->get()
                ->toArray();

            foreach ($productRows as $productRow) {
                $prdIdx = (string)($productRow['CD_IDX'] ?? '');
                if ($prdIdx === '' || isset($productByIdx[$prdIdx])) {
                    continue;
                }
                $productByIdx[$prdIdx] = $productRow;
            }
        }

        $products = [];
        foreach ($oopRows as $row) {
            $prdIdx = (string)($row['idx'] ?? '');
            $productRow = $productByIdx[$prdIdx] ?? [];
            $imgName = trim((string)($productRow['CD_IMG'] ?? ''));
            $productName = trim((string)($row['pname'] ?? ''));
            if ($productName === '') {
                $productName = trim((string)($productRow['CD_NAME'] ?? ''));
            }

            $products[] = [
                'prd_idx' => $prdIdx,
                'ps_idx' => (string)($productRow['ps_idx'] ?? ($row['stockidx'] ?? '')),
                'pname' => $productName,
                'om' => (string)($row['om'] ?? ''),
                'state' => (string)($row['state'] ?? 'on'),
                'code2' => (string)($productRow['CD_CODE2'] ?? ''),
                'code3' => (string)($productRow['CD_CODE3'] ?? ''),
                'img_path' => $imgName !== '' ? '/data/comparion/' . $imgName : '',
            ];
        }

        return [
            'oopIdx' => $oopIdx,
            'products' => $products,
            'brandForSelect' => $brandForSelect,
        ];
    }

    /**
     * 주문서폼 그룹 상품 목록 저장.
     * 빈 주문 메모도 삭제된 값으로 저장한다.
     */
    public function saveFormGroupProducts(array $requestData): array
    {
        $oopIdx = trim((string)($requestData['idx'] ?? ($requestData['oop_idx'] ?? '')));
        if ($oopIdx === '' || !ctype_digit($oopIdx) || (int)$oopIdx <= 0) {
            throw new Exception('유효한 폼그룹 번호가 없습니다.');
        }

        $exists = OrderGroupProductModel::query()
            ->where('oop_idx', '=', (int)$oopIdx)
            ->exists();
        if (!$exists) {
            throw new Exception('폼그룹 상품을 찾을 수 없습니다.');
        }

        $prdIdxList = $requestData['prd_idx'] ?? [];
        $psIdxList = $requestData['ps_idx'] ?? [];
        $orderMemoList = $requestData['ordermemo'] ?? [];
        $stateList = $requestData['state'] ?? [];

        if (!is_array($prdIdxList)) {
            $prdIdxList = [];
        }
        if (!is_array($psIdxList)) {
            $psIdxList = [];
        }
        if (!is_array($orderMemoList)) {
            $orderMemoList = [];
        }
        if (!is_array($stateList)) {
            $stateList = [];
        }

        $psIdxs = [];
        foreach ($psIdxList as $psIdx) {
            $psIdx = (int)$psIdx;
            if ($psIdx > 0) {
                $psIdxs[] = $psIdx;
            }
        }
        $lastInByPsIdx = $this->getLatestInboundByStockIdx($psIdxs);

        $dataArray = [];
        $prdCount = count($prdIdxList);
        for ($i = 0; $i < $prdCount; $i++) {
            $thisIdx = trim((string)($prdIdxList[$i] ?? ''));
            if ($thisIdx === '') {
                continue;
            }

            $thisPsIdx = trim((string)($psIdxList[$i] ?? ''));
            $thisOm = array_key_exists($i, $orderMemoList) ? (string)$orderMemoList[$i] : '';
            $thisState = trim((string)($stateList[$i] ?? 'on'));
            if ($thisState === '') {
                $thisState = 'on';
            }

            $last = '';
            $lastData = '';
            if ($thisPsIdx !== '' && isset($lastInByPsIdx[$thisPsIdx])) {
                $lastIn = $lastInByPsIdx[$thisPsIdx];
                $last = '( ' . ($lastIn['psu_qry'] ?? 0) . ' ) ' . ($lastIn['psu_memo'] ?? '');
                $lastData = [
                    'idx' => $lastIn['psu_idx'] ?? '',
                    'qty' => $lastIn['psu_qry'] ?? 0,
                    'memo' => $lastIn['psu_memo'] ?? '',
                ];
            }

            $dataArray[] = [
                'idx' => $thisIdx,
                'stockidx' => $thisPsIdx,
                'om' => $thisOm,
                'last' => $last,
                'last_data' => $lastData,
                'state' => $thisState,
            ];
        }

        $oopData = json_encode($dataArray, JSON_UNESCAPED_UNICODE);
        if ($oopData === false) {
            throw new Exception('상품 데이터를 저장할 수 없습니다.');
        }

        OrderGroupProductModel::query()
            ->where('oop_idx', '=', (int)$oopIdx)
            ->update([
                'oop_data' => $oopData,
            ]);

        return [
            'oop_idx' => (int)$oopIdx,
            'count' => count($dataArray),
        ];
    }

    /**
     * 주문서폼 그룹에 추가할 상품 검색.
     */
    public function searchFormGroupProducts(array $requestData): array
    {
        $keyword = trim((string)($requestData['keyword'] ?? ''));
        $brandIdx = trim((string)($requestData['s_brand'] ?? ''));

        if ($keyword === '' && $brandIdx === '') {
            return [
                'count' => 0,
                'prd_data' => [],
            ];
        }

        $query = ProductModel::query()
            ->select(['CD_IDX', 'CD_NAME', 'CD_IMG', 'cd_code_fn'])
            ->orderBy('CD_IDX', 'desc');

        if ($keyword !== '') {
            $query->where(function ($inner) use ($keyword) {
                if (preg_match('/[a-zA-Z]/', $keyword)) {
                    $inner->whereRaw('INSTR(LOWER(CD_NAME), LOWER(?))', [$keyword])
                        ->orWhereRaw("INSTR(REPLACE(CD_NAME, ' ', ''), LOWER(?))", [$keyword])
                        ->orWhereRaw('INSTR(LOWER(CD_SEARCH_TERM), LOWER(?))', [$keyword])
                        ->orWhereRaw('INSTR(LOWER(CD_NAME_OG), LOWER(?))', [$keyword])
                        ->orWhereRaw('INSTR(cd_code_fn, ?)', [$keyword]);
                } else {
                    $inner->whereRaw('INSTR(CD_NAME, ?)', [$keyword])
                        ->orWhereRaw("INSTR(REPLACE(CD_NAME, ' ', ''), ?)", [$keyword])
                        ->orWhereRaw('INSTR(CD_SEARCH_TERM, ?)', [$keyword])
                        ->orWhereRaw('INSTR(CD_NAME_OG, ?)', [$keyword])
                        ->orWhereRaw('INSTR(cd_code_fn, ?)', [$keyword]);
                }
                $inner->orWhere('CD_IDX', '=', $keyword);
            });
        }

        if ($brandIdx !== '') {
            $query->where(function ($inner) use ($brandIdx) {
                $inner->where('CD_BRAND_IDX', '=', $brandIdx)
                    ->orWhere('CD_BRAND2_IDX', '=', $brandIdx);
            });
        }

        $productRows = $query->get()->toArray();
        $prdIdxs = [];
        foreach ($productRows as $row) {
            $prdIdx = (int)($row['CD_IDX'] ?? 0);
            if ($prdIdx > 0) {
                $prdIdxs[] = $prdIdx;
            }
        }

        $stockByPrdIdx = [];
        if (!empty($prdIdxs)) {
            $stockRows = ProductStockModel::query()
                ->select(['ps_idx', 'ps_prd_idx', 'ps_rack_code'])
                ->whereIn('ps_prd_idx', array_values(array_unique($prdIdxs)))
                ->get()
                ->toArray();
            foreach ($stockRows as $stockRow) {
                $prdIdx = (string)($stockRow['ps_prd_idx'] ?? '');
                if ($prdIdx === '' || isset($stockByPrdIdx[$prdIdx])) {
                    continue;
                }
                $stockByPrdIdx[$prdIdx] = $stockRow;
            }
        }

        $prdData = [];
        foreach ($productRows as $row) {
            $prdIdx = (string)($row['CD_IDX'] ?? '');
            $codeData = json_decode((string)($row['cd_code_fn'] ?? '{}'), true);
            if (!is_array($codeData)) {
                $codeData = [];
            }
            $stockRow = $stockByPrdIdx[$prdIdx] ?? [];

            $prdData[] = [
                'idx' => $prdIdx,
                'ps_idx' => (string)($stockRow['ps_idx'] ?? ''),
                'jancode' => (string)($codeData['jan'] ?? ''),
                'ps_rack_code' => (string)($stockRow['ps_rack_code'] ?? ''),
                'name' => (string)($row['CD_NAME'] ?? ''),
                'img' => (string)($row['CD_IMG'] ?? ''),
            ];
        }

        return [
            'count' => count($prdData),
            'prd_data' => $prdData,
        ];
    }

    /**
     * 레거시 oop_data JSON과 대괄호 없는 목록을 배열로 변환한다.
     */
    private function decodeFormGroupProductJson($raw): array
    {
        $text = trim((string)$raw);
        if ($text === '') {
            return [];
        }

        $decoded = json_decode($text, true);
        if (is_array($decoded)) {
            return array_values(array_filter($decoded, static function ($row) {
                return is_array($row);
            }));
        }

        $decoded = json_decode('[' . $text . ']', true);
        if (!is_array($decoded)) {
            return [];
        }

        return array_values(array_filter($decoded, static function ($row) {
            return is_array($row);
        }));
    }

    /**
     * 재고별 최근 신규입고 이력을 조회한다.
     */
    private function getLatestInboundByStockIdx(array $psIdxs): array
    {
        $psIdxs = array_values(array_unique(array_filter(array_map('intval', $psIdxs), static function ($psIdx) {
            return $psIdx > 0;
        })));
        if (empty($psIdxs)) {
            return [];
        }

        $rows = ProductStockUnitModel::query()
            ->select(['psu_idx', 'psu_stock_idx', 'psu_qry', 'psu_memo', 'psu_date'])
            ->whereIn('psu_stock_idx', $psIdxs)
            ->where('psu_mode', '=', 'plus')
            ->where('psu_kind', '=', '신규입고')
            ->orderBy('psu_date', 'desc')
            ->get()
            ->toArray();

        $lastInByPsIdx = [];
        foreach ($rows as $row) {
            $psIdx = (string)($row['psu_stock_idx'] ?? '');
            if ($psIdx === '' || isset($lastInByPsIdx[$psIdx])) {
                continue;
            }
            $lastInByPsIdx[$psIdx] = $row;
        }

        return $lastInByPsIdx;
    }

}