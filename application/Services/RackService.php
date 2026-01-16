<?php
namespace App\Services;

use Exception;
use App\Models\RackModel;
use App\Models\ProductStockModel;

class RackService
{

    /**
     * 랙 코드에서 그룹명 생성
     * 
     * @param string $code 랙 코드
     * @return string 코드 그룹
     */
    private function generateCodeGroup($code)
    {
        // 마지막 숫자 제거하여 그룹 생성
        // 예: 'XA-A1' → 'XA-A', 'AA-A12' → 'AA-A', 'FD-A0' → 'FD-A'
        $group = preg_replace('/\d+$/', '', $code);
        
        // 만약 숫자가 없었다면 (변화 없음) 앞 두 자리만
        if ($group === $code && strlen($code) > 2) {
            $group = substr($code, 0, 2);
        }

        return $group;
    }

    /**
     * 랙 목록 조회
     * 
     * @param array $criteria 검색 조건
     * @return array
     */
    public function getRackList($criteria=null)
    {
        $showMode = $criteria['showMode'] ?? null;

        $query = RackModel::query()
            ->orderBy('code', 'asc')
            ->get();
            
        $rackList = $query->toArray();

        // ps_rack_code별 상품 수 미리 집계 (DB에서 GROUP BY로 효율적 처리)
        $rackCodeCounts = [];
        if( $showMode == 'withPrdCount' ){
            $prdStockList = ProductStockModel::query()
                ->selectRaw('ps_rack_code, COUNT(*) AS cnt')
                ->whereNotNull('ps_rack_code')
                ->where('ps_rack_code', '!=', '')
                ->groupBy('ps_rack_code')
                ->get()
                ->toArray();

            // 결과를 연관 배열로 변환
            foreach ($prdStockList as $row) {
                $rackCode = $row['ps_rack_code'] ?? '';
                if (!empty($rackCode)) {
                    $rackCodeCounts[$rackCode] = (int)($row['cnt'] ?? 0);
                }
            }
        }

        foreach ($rackList as &$rack) {

            $code = $rack['code'] ?? '';
            
            // 코드 그룹 생성
            $rack['code_group'] = $this->generateCodeGroup($code);

            // prd_count 추가
            if( $showMode == 'withPrdCount' ){
                $rack['prd_count'] = $rackCodeCounts[$code] ?? 0;
            }
        }
        unset($rack);

        return $rackList;

    }


    /**
     * 랙 상세 조회
     * 
     * @param array $criteria 검색 조건
     * @return array
     */
    public function getRackInfo($idx)
    {
        if( empty($idx) ){
            throw new Exception('랙 고유번호가 없습니다.');
        }

        $rackInfo = RackModel::query()->where('idx', $idx)->first()->toArray();

        // 코드 그룹 생성
        $code = $rackInfo['code'] ?? '';
        $rackInfo['code_group'] = $this->generateCodeGroup($code);

        return $rackInfo;
    }


    /**
     * 랙 저장
     * 
     * @param array $data 랙 데이터
     * @return array
     */
    public function saveRack($data)
    {

        $idx = $data['idx'] ?? null;

        $isModify = !empty($idx) ? true : false;

        if( $isModify ){
            $checkCode = RackModel::where('code', $data['code'])->where('idx', '!=', $idx)->first();
            if( $checkCode ){
                throw new Exception('랙 코드가 중복됩니다.');
            }
        }else{
            $checkCode = RackModel::where('code', $data['code'])->first();
            if( $checkCode ){
                throw new Exception('랙 코드가 중복됩니다.');
            }
        }

        $input_data = [
            'name' => $data['name'] ?? '',
            'code' => $data['code'] ?? '',
            'memo' => $data['memo'] ?? '',
        ];

        $result = RackModel::updateOrCreate(
            ['idx' => $idx],
            $input_data
        );

        return $result;

    }


    /**
     * 랙 삭제
     * 
     * @param array $data 랙 데이터
     * @return array
     */
    public function deleteRack($data)
    {

        $idx = $data['idx'] ?? null;

        if( empty($idx) ){
            throw new Exception('랙 고유번호가 없습니다.');
        }

        $rackInfo = $this->getRackInfo($idx);

        if( empty($rackInfo) ){
            throw new Exception('랙 정보를 찾을 수 없습니다.');
        }

        $code = $rackInfo['code'] ?? '';

        if( empty($code) ){
            throw new Exception('랙 코드를 찾을 수 없습니다.');
        }

        $prdRackCount = ProductStockModel::where('ps_rack_code', $code)->count();

        if( $prdRackCount > 0 ){
            throw new Exception('랙에 상품이 있어 삭제가 불가능 합니다.');
        }

        $result = RackModel::where('idx', $idx)->delete();
        return $result;
        
    }


    /**
     * 랙 그룹명 변경
     * 
     * @param array $data 랙 데이터
     * @return array
     */
    public function changeRackGroup($data)
    {

        $codeGroup = $data['code_group'] ?? '';    // 현재 그룹 (변경 대상)
        $changeCode = $data['change_code'] ?? '';  // 새로운 그룹명

        if( empty($codeGroup) ){
            throw new Exception('현재 랙 그룹 코드가 없습니다.');
        }

        if( empty($changeCode) ){
            throw new Exception('변경할 랙 그룹 코드가 없습니다.');
        }

        // $changeCode로 시작하는 랙이 이미 있는지 확인 (중복 방지)
        // 예: $changeCode = 'BB-B' → code LIKE 'BB-B%' 검색
        $checkCodeGroup = RackModel::where('code', 'LIKE', $changeCode . '%')->first();
        if( $checkCodeGroup ){
            throw new Exception('해당 랙 그룹이 이미 존재합니다.');
        }

        // 변경 대상 그룹($codeGroup)의 모든 랙 코드를 조회
        // 예: $codeGroup = 'AA-A' → code LIKE 'AA-A%' (AA-A0, AA-A1 등)
        $rackList = RackModel::where('code', 'LIKE', $codeGroup . '%')->get()->toArray();

        if( empty($rackList) ){
            throw new Exception('변경할 랙을 찾을 수 없습니다.');
        }

        // 모든 해당 랙의 code를 업데이트
        foreach ($rackList as $rack) {
            $oldCode = $rack['code'];
            // $codeGroup 부분을 $changeCode로 변경
            // 예: 'AA-A0' → 'BB-B0' (AA-A를 BB-B로 변경)
            $newCode = str_replace($codeGroup, $changeCode, $oldCode);
            
            RackModel::where('idx', $rack['idx'])->update([
                'code' => $newCode
            ]);

            ProductStockModel::where('ps_rack_code', $oldCode)->update([
                'ps_rack_code' => $newCode
            ]);
        }

        return ['success' => true, 'updated_count' => count($rackList)];

    }

    /**
     * 랙 상품 이동
     * 
     * @param array $data 랙 데이터
     * @return boolean
     */
    public function moveRackProduct($data)
    {
        $code = $data['code'] ?? '';
        $changeCode = $data['change_code'] ?? '';

        if( empty($code) ){
            throw new Exception('이동할 랙 코드가 없습니다.');
        }

        if( empty($changeCode) ){
            throw new Exception('변경할 랙 코드가 없습니다.');
        }

        ProductStockModel::where('ps_rack_code', $code)->update([
            'ps_rack_code' => $changeCode
        ]);

        return true;

    }

}
