<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Classes\Database;
use App\Utils\HttpClient; 


if( empty($_pagegodo) ) $_pagegodo = 1;

$apiUrl = 'https://showdang.co.kr/dnfix/api/goods_api.php?page='.$_pagegodo;
$response = HttpClient::getData($apiUrl);

$responseData = json_decode($response, true);

$db = Database::getInstance()->getConnection();

$goodsCdList = array_column($responseData['data'] ?? [], 'goodsCd');

    $placeholders = implode(',', array_fill(0, count($goodsCdList), '?'));

    $sql = "
        SELECT ps.ps_idx, ps.ps_prd_idx, cd.CD_IDX, cd.CD_NAME, cd.cd_godo_code
        FROM prd_stock ps
        JOIN COMPARISON_DB cd ON ps.ps_prd_idx = cd.CD_IDX
        WHERE ps.ps_idx IN ($placeholders)
    ";
	
    $stmt = $db->prepare($sql);
    $stmt->execute($goodsCdList);
    $matchedData = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// ✅ STEP 3: API 데이터 매칭을 위한 맵 생성 (goodsCd 기준)
    $apiDataMap = [];
    foreach ( $responseData['data'] as $product ) {
        $apiDataMap[$product['goodsCd']] = [
            'goodsNo' => $product['goodsNo'],
            'goodsNm' => $product['goodsNm'],
            'scmNo' => $product['scmNo']			
        ];
    }

    // ✅ STEP 4: 매칭된 ps_idx 수집
    $matchedPsIdx = array_column($matchedData, 'ps_idx');

$scmMapping = [
    0  => '오류',
    1  => '주식회사 디엔픽스',
    2  => '모브X',
    3  => '모브',
    4  => '공급사사입',
    5  => '바니컴퍼니',
    6  => '바이담',
    7  => '해외직구',
    8  => '그린쉘프',
    9  => '울컨코리아',
    10 => '모노프로',
    11 => '핑크에그',
    12 => '리퍼브',
    13 => 'MSHb2b',
    14 => 'JPDOLL',
    15 => '도라토이',
    16 => '대형',	
    17 => '리퍼브',	
    18 => '랜덤박스',	
    19 => '예비1',	
    20 => '예비2',		
];

?>

<div id="contents_head">
	<h1>고도몰 상품 매칭<?=$_pagegodo?></h1>
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
    <?php
    //$total = 9500; // 총 개수
	$total = $responseData['total'];
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
    ?>
</div>

		<div id="list_new_wrap" class="m-t-5">

<div id="" class="table-wrap5">
	<div class=" scroll-wrap">

		<table class="table-st1">
			<thead>
			<tr>
				<th class="list-idx">매칭 재고코드</th>
				<th class="">고도몰 상품번호</th>
				<th class="">고도몰 상품명</th>
				<th class="">고도몰 자체코드</th>		
				<th class="">고도몰 공급사</th>			
				<th class="">코엣지 IDX</th>
				<th>코엣지 상품명</th>
				<th>매칭 상태</th>
				<th>메모</th>
			</tr>
			</thead>
			<tbody>
			<?
				
				$updateCount = 0; 
				
				//매칭된 데이터
				foreach ($matchedData as $row) {
					
					$ps_idx = $row['ps_idx'];
					$goodsNo = $apiDataMap[$ps_idx]['goodsNo'] ?? 'N/A';
					$goodsNm = $apiDataMap[$ps_idx]['goodsNm'] ?? 'N/A';
					$scmNo = $apiDataMap[$ps_idx]['scmNo'] ?? '0';
						
					$_tr_class = "";
					if( $apiDataMap[$ps_idx]['goodsNo'] != $row['cd_godo_code'] ){
						
						$_tr_class = "status_bl";
						
						$idx = $row['CD_IDX'];

						// SQL 업데이트 쿼리
						$query = "UPDATE COMPARISON_DB SET cd_godo_code = :goodsNo WHERE CD_IDX = :idx";
						$stmt = $db->prepare($query);

						// 바인딩
						$stmt->bindParam(':goodsNo', $goodsNo, PDO::PARAM_STR);
						$stmt->bindParam(':idx', $idx, PDO::PARAM_INT);

						// 실행
						if ($stmt->execute()) {
							// 업데이트가 성공적으로 완료되면 카운트 증가
							$updateCount++;
						}
						
					}
			
			?>
			<tr align="center" id="trid_<?=$ps_idx?>" class="<?=$_tr_class?>">
				<td class="list-idx"><?=$ps_idx?></td>
				<td class=""><?=$goodsNo?></td>
				<td class=""><?=$goodsNm?></td>
				<td class=""><?=$ps_idx?></td>
				<td class=""><?=$scmNo?> | <?=$scmMapping[$scmNo]?></td>
				<td class="">
					<? if( $row['CD_IDX'] ?? '' ){ ?>
						<button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="onlyAD.prdView('<?=$row['CD_IDX']?>','info');"><?=$row['CD_IDX']?></button>
					<? } ?>
				</td>
				<td class=""><?=$row['CD_NAME']?></td>
				<td class="">
					<?
						if( $apiDataMap[$ps_idx]['goodsNo'] != $row['cd_godo_code'] ){
					?>
						매칭 X
					<? }else{ ?>
						매칭완료
					<? } ?>
				</td>
				<td class="text-left">
				</td>
			</tr>
			<? } ?>
						
			<tr>
				<td colspan="10">업데이트 : <b><?=$updateCount?></b></td>
			</tr>
			<tr>
				<td colspan="10">코엣지에 검색되지 않는 고도몰 상품</td>
			</tr>	
			
			<?
				foreach ( $responseData['data'] as $product ) {
					if (!in_array($product['goodsCd'], $matchedPsIdx)) {
						$scmNo = $product['scmNo'] ?? '0';
						
						/*
							17 = 리퍼브
						*/
						$_tr_class = "status_bl";
						
						if( $scmNo == 17 ){
							$_tr_class = "status_clx";
						}
						
			?>
			<tr align="center" id="trid_<?=$ps_idx?>" class="<?=$_tr_class?>">
				<td class="list-idx"></td>
				<td class=""><?=$product['goodsNo']?></td>
				<td class=""><?=$product['goodsNm']?></td>
				<td class=""><?=$product['goodsCd']?></td>
				<td class=""><?=$product['scmNo']?> | <?=$scmMapping[$scmNo]?></td>
				<td class="">
				</td>
				<td class="">
				</td>
				<td class="">
				</td>
				<td class="text-left">
				</td>
			</tr>
			<? } } ?>
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