<?php

namespace App\Services;

use Exception;
use App\Models\OrderGroupModel;
use App\Models\OrderGroupProductModel;

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

}