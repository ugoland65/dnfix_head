<?php
namespace App\Controllers\Api;

use App\Core\BaseClass;


class ShowdangApi extends BaseClass {


    public function brandList() {
		
		$_count = 0;

		// 검색 조건 설정
		$_search_query = [
			['BD_NAME_GROUP', '!=', ''],
			['bd_showdang_active', '=', 'Y']
		];

		// 데이터 조회
		$results = $this->queryBuilder
			->table('BRAND_DB')
			->select('*')
			->where('BD_NAME_GROUP', '!=', '')
			->where('bd_showdang_active', '=', 'Y')
			->orderBy('BD_NAME', 'desc')
			->get()
			->toArray();

		$arr_gp_ko = [];
		$arr_gp_en = [];

		foreach ($results as $list) {
			$_count++;

			$_bd_kind = json_decode($list['bd_kind'], true);
			if (json_last_error() !== JSON_ERROR_NONE) {
				$_bd_kind = [];
			}

			$categories = [
				'ona' => 'ona',
				'breast' => 'breast',
				'gel' => 'gel',
				'condom' => 'condom',
				'annal' => 'annal',
				'prostate' => 'prostate',
				'care' => 'care',
				'dildo' => 'dildo',
				'vibe' => 'vibe',
				'suction' => 'suction',
				'man' => 'man',
				'nipple' => 'nipple',
				'cos' => 'cos',
				'perfume' => 'perfume',
				'bdsm' => 'bdsm'
			];

			// 'Y' 값인 항목만 필터링하여 키를 가져옵니다.
			$arycate = array_keys(array_filter($_bd_kind, function ($value) {
				return $value === 'Y';
			}, ARRAY_FILTER_USE_BOTH));

			// 필요 시 배열 키와 $categories를 매핑해 값을 가져옵니다.
			$_show_cate = implode(" ", array_intersect_key($categories, array_flip($arycate)));

			$arr_gp_ko[$list['BD_NAME_GROUP']][] = [
				'name' => $list['BD_NAME'],
				'name_en' => $list['BD_NAME_EN'],
				'cate_code' => $list['bd_matching_cate'],
				'brand_code' => $list['bd_matching_brand'],
				'show_cate' => $_show_cate
			];

			$arr_gp_en[$list['BD_NAME_EN_GROUP']][] = [
				'name' => $list['BD_NAME'],
				'name_en' => $list['BD_NAME_EN'],
				'cate_code' => $list['bd_matching_cate'],
				'brand_code' => $list['bd_matching_brand'],
				'show_cate' => $_show_cate
			];

		}

		$arr_ko_1st = ['ㄱ', 'ㄴ', 'ㄷ', 'ㄹ', 'ㅁ', 'ㅂ', 'ㅅ', 'ㅇ', 'ㅈ', 'ㅊ', 'ㅋ', 'ㅌ', 'ㅍ', 'ㅎ', '#']; // 초성
		$arr_en_1st = range('A', 'Z');
		array_push($arr_en_1st, '@');

		foreach ($arr_ko_1st as $_chos_code) {
			if (isset($arr_gp_ko[$_chos_code])) {
				usort($arr_gp_ko[$_chos_code], function ($a, $b) {
					return strcmp($a['name'], $b['name']);
				});
			}
		}

		foreach ($arr_en_1st as $_chos_code) {
			if (isset($arr_gp_en[$_chos_code])) {
				usort($arr_gp_en[$_chos_code], function ($a, $b) {
					return strcmp($a['name_en'], $b['name_en']);
				});
			}
		}

		$response = [
			'result' => true,
			'count' => $_count,
			'brand_kr' => $arr_gp_ko,
			'brand_en' => $arr_gp_en,
		];

		return $response;

    }

	/**
	 * 브랜드 정보 조회
	 * @param string $brandCode
	 * @return array
	 */
    public function brandInfo($requestData) {

		$searchMode = $requestData['searchMode'] ?? 'code';
		$brandCode = $requestData['code'] ?? '';

		if( $searchMode == 'code' ){

			// 데이터 조회
			$brand = $this->queryBuilder
				->table('BRAND_DB')
				->find($brandCode,'bd_matching_cate');

		}elseif( $searchMode == 'name' ){

			// 데이터 조회
			$brand = $this->queryBuilder
				->table('BRAND_DB')
				->whereRaw("REPLACE(BD_NAME, ' ', '') = ?", [$brandCode])
				->first();

			if (!$brand) {
				$brand = $this->queryBuilder
					->table('BRAND_DB')
					->whereRaw("REPLACE(BD_NAME, ' ', '') LIKE ?", ['%'.str_replace(' ', '', $brandCode).'%'])
					->first();
			}

		}

		if (!$brand) {
			return [
				'result' => false,
				'msg' => '브랜드 정보를 찾을 수 없습니다. ('.$brandCode.')',
			];
		}

		$_bd_api_info = json_decode($brand['bd_api_info'], true);

		$_active = $_bd_api_info['active'] ?? 'N';

		$response = [
			'result' => true,
			'active' => $_active === 'Y' ? 'Y' : 'N',
			'bd_matching_cate' => $brand['bd_matching_cate'] ?? null,
			'bg_rgb' => $_bd_api_info['bg_rgb'] ?? null,
			'bg' => $_bd_api_info['bg'] ?? null,
			'bg_mobile' => $_bd_api_info['bg_mobile'] ?? null,
			'info_class' => $_bd_api_info['info_class'] ?? null,
			'logo' => $_bd_api_info['logo'] ?? null,
			'logo_mobile' => $_bd_api_info['logo_mobile'] ?? null,
			'name' => $_bd_api_info['name'] ?? null,
			'name_en' => $_bd_api_info['name_en'] ?? null,
			'introduce' => $brand['bd_api_introduce'] ?? null,
			'msg' => '완료',
		];

		return $response;

    }

}