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

//dd($responseData);

$db = Database::getInstance()->getConnection();

$goodsCdList = array_column($responseData['data'] ?? [], 'goodsCd');

    $placeholders = implode(',', array_fill(0, count($goodsCdList), '?'));

    $sql = "
        SELECT 
		ps.ps_idx, ps.ps_prd_idx, ps.ps_stock,
		cd.CD_IDX, cd.CD_NAME, cd.cd_godo_code, cd.cd_sale_price, cd.cd_cost_price
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
            'scmNo' => $product['scmNo'],
			'goodsPrice' => $product['goodsPrice'],
			'goodsDiscountFl' => $product['goodsDiscountFl'],

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

	.text-danger{
		font-weight: bold;
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
				<th class="" width="200">고도몰 상품명</th>
				<th class="">고도몰 자체코드</th>		
				<th class="">고도몰 공급사</th>		
				<th class="">고도몰 판매가</th>
				<th class="">코엣지 IDX</th>
				<th class="" width="200">코엣지 상품명</th>
				<th>코엣지 판매가</th>
				<th>코엣지 원가</th>
				<th>마진금액</th>
				<th>마진율</th>
				<th>등급</th>
				<th>재고</th>
				<th>매칭 상태</th>
				<th>메모</th>
			</tr>
			</thead>
			<tbody>
			<?
				
				$updateCount = 0; 
				$insertSqlList = []; // INSERT SQL 문 저장 배열
				
				//매칭된 데이터
				foreach ($matchedData as $row) {
					
					$ps_idx = $row['ps_idx'];
					$goodsNo = $apiDataMap[$ps_idx]['goodsNo'] ?? 'N/A';
					$goodsNm = $apiDataMap[$ps_idx]['goodsNm'] ?? 'N/A';
					$scmNo = $apiDataMap[$ps_idx]['scmNo'] ?? '0';
					$goodsPrice = $apiDataMap[$ps_idx]['goodsPrice'] ?? '0';
					$goodsDiscountFl = $apiDataMap[$ps_idx]['goodsDiscountFl'] ?? 'n';

					// 마진 계산
					$salePrice = (float)($row['cd_sale_price'] ?? 0);
					$costPrice = (float)($row['cd_cost_price'] ?? 0);
					$marginAmount = $salePrice - $costPrice;
					$marginRate = $salePrice > 0 ? ($marginAmount / $salePrice) * 100 : 0;
					
					// 등급 계산 (40% 기준, 5단위)
					if ($marginRate > 39) {
						$grade = 'A';
						$gradeColor = '#28a745'; // 초록색
						$cateCd = '046001001';
					} elseif ($marginRate >= 35) {
						$grade = 'B';
						$gradeColor = '#20c997'; // 연두색
						$cateCd = '046001002';
					} elseif ($marginRate >= 30) {
						$grade = 'C';
						$gradeColor = '#17a2b8'; // 청록색
						$cateCd = '046001003';
					} elseif ($marginRate >= 25) {
						$grade = 'D';
						$gradeColor = '#0dcaf0'; // 하늘색
						$cateCd = '046001004';
					} elseif ($marginRate >= 20) {
						$grade = 'E';
						$gradeColor = '#ffc107'; // 노란색
						$cateCd = '046001005';
					} elseif ($marginRate >= 15) {
						$grade = 'F';
						$gradeColor = '#fd7e14'; // 오렌지색
						$cateCd = '046001006';
					} elseif ($marginRate >= 10) {
						$grade = 'G';
						$gradeColor = '#dc3545'; // 빨간색
						$cateCd = '046001007';
					} elseif ($marginRate >= 5) {
						$grade = 'H';
						$gradeColor = '#d63384'; // 진한 빨강
						$cateCd = '046001008';
					} else {
						$grade = 'I';
						$gradeColor = '#6c757d'; // 회색
						$cateCd = '046001009';
					}

					// INSERT SQL 생성 (판매가와 원가가 0이 아닌 경우에만)
					if ($salePrice > 0 && $costPrice > 0 && !empty($grade)) {
						$currentTime = date('Y-m-d H:i:s');
						$insertSql = "INSERT INTO es_goodsLinkCategory (goodsNo, cateCd, cateLinkFl, goodsSort, fixSort, regDt, modDt) VALUES ('{$goodsNo}', '{$cateCd}', 'y', 0, 0, '{$currentTime}', '{$currentTime}');";
						$insertSqlList[] = $insertSql;
					}

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
			<trid="trid_<?=$ps_idx?>" class="<?=$_tr_class?>">
				<td class="list-idx"><?=$ps_idx?></td>
				<td class=""><?=$goodsNo?></td>
				<td class="text-right">
					<? if( $goodsDiscountFl == 'y' ){ ?>
						<span class="text-danger">할인</span>
					<? } ?>
					<?=$goodsNm?>
				</td>
				<td class=""><?=$ps_idx?></td>
				<td class=""><?=$scmNo?> | <?=$scmMapping[$scmNo]?></td>
				<td class="text-right"><?=number_format($goodsPrice)?></td>
				<td class="">
					<? if( $row['CD_IDX'] ?? '' ){ ?>
						<button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="onlyAD.prdView('<?=$row['CD_IDX']?>','info');"><?=$row['CD_IDX']?></button>
					<? } ?>
				</td>
				<td class="text-left"><?=$row['CD_NAME']?></td>
				<td class="text-right" >
					<span class="<? if( $row['cd_sale_price'] != $goodsPrice ){ ?>text-danger<? } else{ ?>text-success<? } ?>">
					<?=number_format($row['cd_sale_price'])?>
					</span>
				</td>
				<td class="text-right"><?=number_format($row['cd_cost_price'])?></td>
				<td class="text-right"><?=number_format($marginAmount)?>원</td>
				<td class="text-right"><b><?=number_format($marginRate, 1)?>%</b></td>
				<td class="text-center">
					<span style="display:inline-block; padding:2px 8px; background-color:<?=$gradeColor?>; color:white; font-weight:bold; border-radius:3px;">
						<?=$grade?>
					</span>
				</td>
				<td class="text-right"><?=$row['ps_stock']?></td>
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
				<td colspan="14">업데이트 : <b><?=$updateCount?></b></td>
			</tr>
			<tr>
				<td colspan="14">코엣지에 검색되지 않는 고도몰 상품</td>
			</tr>
			<tr>
				<td colspan="16" style="padding:20px; background-color:#f8f9fa;">
					<h3 style="margin-bottom:10px;">📋 INSERT SQL 문 (복사용)</h3>
					<textarea id="insertSqlArea" style="width:100%; height:300px; font-family:monospace; font-size:12px; padding:10px; border:1px solid #ddd; background-color:#fff;" readonly><?php 
						if (!empty($insertSqlList)) {
							echo implode("\n", $insertSqlList);
						} else {
							echo "-- 생성된 SQL 문이 없습니다.";
						}
					?></textarea>
					<button type="button" onclick="copyInsertSql()" style="margin-top:10px; padding:8px 20px; background-color:#007bff; color:white; border:none; border-radius:4px; cursor:pointer;">
						📋 SQL 복사하기
					</button>
					<span id="copyMessage" style="margin-left:10px; color:green; font-weight:bold; display:none;">✓ 복사완료!</span>
				</td>
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

function copyInsertSql() {
	var textarea = document.getElementById('insertSqlArea');
	textarea.select();
	textarea.setSelectionRange(0, 99999); // 모바일 대응
	
	try {
		document.execCommand('copy');
		var message = document.getElementById('copyMessage');
		message.style.display = 'inline';
		setTimeout(function() {
			message.style.display = 'none';
		}, 2000);
	} catch (err) {
		alert('복사 실패. 수동으로 복사해주세요.');
	}
}

//--> 
</script>	