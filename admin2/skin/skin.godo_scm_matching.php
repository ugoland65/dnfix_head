<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Classes\Database;
use App\Utils\HttpClient; 

if( empty($_pagegodo) ) $_pagegodo = 1;
if( empty($_scm) ) $_scm = 0;

$scmMapping = [
    0  => ['name' => '오류', 'partner_key' => null, 'display'=>'none' ],
    1  => ['name' => '주식회사 디엔픽스', 'partner_key' => null, 'display'=>'none' ],
    2  => ['name' => '모브X', 'partner_key' => null, 'display'=>'none' ],
    3  => ['name' => '모브', 'partner_key' => 3],
    4  => ['name' => '공급사사입', 'partner_key' => null, 'display'=>'none'],
    5  => ['name' => '바니컴퍼니', 'partner_key' => 8],
    6  => ['name' => '바이담', 'partner_key' => 10],
    7  => ['name' => '해외직구', 'partner_key' => null, 'display'=>'none'],
    8  => ['name' => '그린쉘프', 'partner_key' => 12],
    9  => ['name' => '울컨코리아', 'partner_key' => 7],
    10 => ['name' => '모노프로', 'partner_key' => 11],
    11 => ['name' => '핑크에그', 'partner_key' => 9],
    12 => ['name' => '리퍼브', 'partner_key' => null, 'display'=>'none'],
    13 => ['name' => 'MSHb2b', 'partner_key' => 5],
    14 => ['name' => 'JPDOLL', 'partner_key' => 14],
    15 => ['name' => '도라토이', 'partner_key' => 6],
    16 => ['name' => '대형', 'partner_key' => null, 'display'=>'none'],
    17 => ['name' => '리퍼브', 'partner_key' => null, 'display'=>'none'],
    18 => ['name' => '랜덤박스', 'partner_key' => null, 'display'=>'none'],
    19 => ['name' => '예비1', 'partner_key' => null, 'display'=>'none'],
    20 => ['name' => '예비2', 'partner_key' => null, 'display'=>'none'],
    
];


if( !empty($_scm) ){
        
    $apiUrl = 'https://showdang.co.kr/dnfix/api/goods_api.php?mode=SCM&scm='.$_scm.'&page='.$_pagegodo;
    $response = HttpClient::getData($apiUrl);

    $responseData = json_decode($response, true);

/*
	echo "<pre>";
	print_r($responseData);
	echo "</pre>";
*/

    $db = Database::getInstance()->getConnection();

    $goodsCdList = array_column($responseData['data'] ?? [], 'cateNm');
	
	// 공백 제거 (모든 공백 문자 제거)
	$goodsCdListNoSpaces = array_map(function($v) {
		// 모든 공백 문자(\s)를 제거
		return preg_replace('/\s+/', '', $v);
	}, $goodsCdList);
	
	// 중복 제거
	$goodsCdListNoSpaces = array_unique($goodsCdListNoSpaces);
	
	// ★ 키 재인덱싱 (중요!)
	$goodsCdListNoSpaces = array_values($goodsCdListNoSpaces);
	
	if (empty($goodsCdListNoSpaces)) {
		// 배열이 비어있으면 쿼리 자체를 실행하지 않음
		$brandResults = [];
	} else {
		$placeholders = rtrim(str_repeat('?,', count($goodsCdListNoSpaces)), ',');
		$sql = "
			SELECT BD_NAME, BD_IDX
			FROM BRAND_DB
			WHERE REPLACE(BD_NAME, ' ', '') IN ($placeholders)
		";
		
		$stmt = $db->prepare($sql);
		$stmt->execute($goodsCdListNoSpaces);
		$brandResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}


	$brandMapping = [];
	foreach ($brandResults as $row) {
		// BD_NAME에서 모든 공백(\s) 제거
		$brandNameNoSpace = preg_replace('/\s+/', '', $row['BD_NAME']);

		$brandMapping[$brandNameNoSpace] = [
			'IDX' => $row['BD_IDX']
		];
	}


    $sql = "
    SELECT idx, godo_goodsNo 
    FROM prd_partner  
    WHERE partner_idx = :partner_key
	";

	$stmt = $db->prepare($sql);
	$stmt->bindValue(':partner_key', $scmMapping[$_scm]['partner_key'], PDO::PARAM_INT);
	$stmt->execute();
	$matchedData = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$matchedGoodsNos = [];
	foreach ($matchedData as $row) {
		$matchedGoodsNos[$row['godo_goodsNo']] = $row['idx'];
	}


}
?>

<div id="contents_head">
	<h1>고도몰 공급사상품 매칭<?=$_pagegodo?></h1>
    <div id="head_write_btn">
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
	
	<style>
    /* 버튼 스타일 */
    button.pagebtn {
        padding: 3px 5px;
        /* margin: 5px; */
        border: 1px solid #ccc;
        background-color: #f5f5f5;
        cursor: pointer;
    }

    /* 활성화된 버튼 */
    button.pagebtn.on {
        background-color: #007BFF;
        color: white;
        border-color: #0056b3;
    }
</style>

<div>

	총 갯수 : <b><?=$responseData['total'] ?? 0?></b>
	
    <?php
    /*
    $total = 9500; // 총 개수
    $perPage = 500; // 페이지당 개수
    $totalPages = ceil($total / $perPage); // 총 페이지 수

    // 현재 페이지 번호 가져오기 (GET 파라미터에서)
    $_pagegodo = isset($_GET['pagegodo']) ? (int)$_GET['pagegodo'] : 1;

    for ($i = 1; $i <= $totalPages; $i++) {
        $start = (($i - 1) * $perPage) + 1;
        $end = min($i * $perPage, $total);

        // 현재 페이지와 일치하면 class="on" 추가
        $activeClass = ($_pagegodo === $i) ? 'on' : '';

        echo '<button type="button" class="pagebtn ' . $activeClass . '" data-page="' . $i . '" onclick="location.href=\'/ad/showdang/godo_goods_matching/?pagegodo=' . $i . '\'">' . $start . '~' . $end . '</button>' . PHP_EOL;
    }
        */

	
    foreach( $scmMapping as $key => $value ){
		
		$activeClass = ($_scm == $key) ? 'on' : '';
		$name = isset($value['name']) ? $value['name'] : '이름없음';
		
		if( empty($value['display'] ) ){
			echo '<button type="button" class="pagebtn ' . $activeClass . '" onclick="location.href=\'/ad/provider/godo_scm_matching/?scm=' . $key . '\'">' . $name . '</button>' . PHP_EOL;
		}
	}
    ?>
</div>

		<div id="list_new_wrap" class="m-t-5">

<div id="" class="table-wrap5">
	<div class=" scroll-wrap">

		<table class="table-st1">
			<thead>
			<tr>
				<th class="list-idx">count</th>
				<th class="">고도몰 상품코드</th>
				<th class="">고도몰 이미지</th>
				<th class="">고도몰 상품명</th>
				<th class="">고도몰 자체코드</th>		
				<th class="">고도몰 옵션</th>
				<th class="">고도몰 공급사</th>

				<th class="">고도몰 브랜드코드</th>
				<th class="">고도몰 판매가</th>
				<th class="">코엣지 브랜드</th>
				<th>매칭 상태</th>
				<th>메모</th>
			</tr>
			</thead>
			<tbody>
			<?
				$insertCount = 0;
				$count = 0;
				foreach ( $responseData['data'] ?? [] as $product ) {
					
					$count++;
					$scmNo = $product['scmNo'] ?? '0';
					$cateNmNoSpace = preg_replace('/\s+/', '', $product['cateNm']);
					$goodsNo = $product['goodsNo'];
					
					/*
						17 = 리퍼브
					*/
					$_tr_class = "l";
					
					if( empty( $brandMapping[$cateNmNoSpace] ) ){
						//$_tr_class = "status_clx";
					}
					
					$_errors = [];
					
					if( empty($product['goodsCd']) ){
						$_errors[] = "고도몰 자체코드가 없음";
					}
					
					if (!empty($product['goodsCd']) && ctype_digit($product['goodsCd'])) {
						$_errors[] = "재고코드가 있는것으로 의심됨";
					}
					
					if (!empty($product['goodsCd']) && preg_match('/[^a-zA-Z0-9\-_]/', $product['goodsCd'])) {
						$_errors[] = "코드불량";
					}
					
					if (!empty($product['goodsCd']) && stripos($product['goodsCd'], 'set') !== false) {
						$_errors[] = "세트코드 제외";
					}
					
					if (!empty($product['goodsCd']) && stripos($product['goodsCd'], 'toy') !== false) {
						$_errors[] = "toy 코드 제외";
					}
					
					if( empty( $brandMapping[$cateNmNoSpace] ) ){
						$_errors[] = "브랜드 매칭 안됨 (인트라넷에 브랜드 존재하는지 확인)";
					}
					
					$matchedIdx = null;
					
					$ud = "";

					$options = json_decode($product['options'] ?? '', true);

					$goodsOption = [];
					if (is_array($options)) {
						foreach( $options as $option ){
							if( !empty($option['optionValue1'])){
								$goodsOption[] = $option;
							}
						}
					}

					
					if ( count($_errors) > 0 ) {
						$_tr_class = "status_bl";
					}else{
					
						if (array_key_exists($goodsNo, $matchedGoodsNos)) {
							$matchedIdx = $matchedGoodsNos[$goodsNo];
					
						}else{
							


							$brandIdx = isset($brandMapping[$cateNmNoSpace]['IDX']) ? $brandMapping[$cateNmNoSpace]['IDX'] : null;
							$partnerKey = isset($scmMapping[$scmNo]['partner_key']) ? $scmMapping[$scmNo]['partner_key'] : null;

							$query = "INSERT prd_partner SET 
										name = :name,
										img_mode = :img_mode,
										img_src = :img_src,
										sale_price = :sale_price,
										code = :code,
										partner_idx = :partner_idx,
										brand_idx = :brand_idx,
										godo_goodsNo = :godo_goodsNo";

							$stmt = $db->prepare($query);

							$stmt->bindValue(':name', $product['goodsNm'], PDO::PARAM_STR);
							$stmt->bindValue(':img_mode', 'out', PDO::PARAM_STR);
							$stmt->bindValue(':img_src', $product['thumbImageUrl'], PDO::PARAM_STR);
							$stmt->bindValue(':sale_price', $product['goodsPrice'], PDO::PARAM_INT);
							$stmt->bindValue(':code', $product['goodsCd'], PDO::PARAM_STR);
							$stmt->bindValue(':partner_idx', $partnerKey, PDO::PARAM_INT);
							$stmt->bindValue(':brand_idx', $brandIdx, PDO::PARAM_INT);
							$stmt->bindValue(':godo_goodsNo', $product['goodsNo'], PDO::PARAM_INT);

							try {
								if ($stmt->execute()) {
									$insertCount++;
									$ud = "새롭게 저장됨";
								}
							} catch (PDOException $e) {
								echo "DB Error: " . $e->getMessage();
							}

						
						}
					}
						
			?>
			<tr id="trid_<?=$ps_idx?>" class="<?=$_tr_class?>">
				<td class="list-idx"><?=$count?></td>
				<td class="text-center">
					<button type="button" class="btnstyle1 btnstyle1-success btnstyle1-xs" 
						onclick="goGodoMall(<?=$product['goodsNo']?>);" >#<?=$product['goodsNo']?></button>
				</td>
				<td class=""><img src="<?=$product['thumbImageUrl']?>" style="height:70px; border:1px solid #eee !important;"></td>
				<td class=""><?=$product['goodsNm']?></td>
				<td class=""><?=$product['goodsCd']?></td>
				<td class="">
					<?php
						echo "<pre>";
						print_r($options);
						echo "</pre>";

						echo "<pre>";
						print_r($goodsOption);
						echo "</pre>";
					
					?>
				</td>
				<td class=""><?=$product['scmNo']?> | <?=$scmMapping[$scmNo]['name']?></td>
				<td class=""><?=$product['brandCd']?> | <?=$product['cateNm']?></td>
				<td class="text-right"><?=number_format($product['goodsPrice'])?></td>
				<td class="">
					<? if( empty( $brandMapping[$cateNmNoSpace] ) ){ ?>
						매칭 X
					<? }else{ ?>
						<?=$brandMapping[$cateNmNoSpace]['IDX']?>
					<? } ?>
				</td>
				<td class="">
					<? if( $matchedIdx == null ){ ?>
						매칭X<br>
					<? }else{ ?>
						<button type="button" class="btnstyle1 btnstyle1-success btnstyle1-xs" 
							onclick="prdProviderQuick('<?=$matchedIdx?>');" >#<?=$matchedIdx?></button>	
					<? } ?>
					<?=$ud?>
				</td>
				<td class="text-left">
					<div>
					<? 
					if ( count($_errors) > 0 ) { 
						foreach ( $_errors as $er ) {
					?>
						<ul><?=$er?></ul>
					<? } } ?>
					</div>
				</td>
			</tr>
			<? }  ?>
			</tbody>
		</table>
	</div>
</div>
		
		</div>

		<div id="contents_body_bottom_padding"></div>
	</div>
</div>
<div id="contents_bottom">
	<div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>

<script type="text/javascript"> 
<!-- 

//--> 
</script>	