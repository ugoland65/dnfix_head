<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Classes\Database;
use App\Utils\HttpClient; 

$apiUrl = 'https://showdang.co.kr/dnfix/api/brand_api.php';
$response = HttpClient::getData($apiUrl);

$responseData = json_decode($response, true);

$db = Database::getInstance()->getConnection();

// cateNm 값 추출
$arrCateNm = array_column($responseData, 'cateNm');

// IN 조건을 위한 플레이스홀더 생성
$placeholders = implode(',', array_fill(0, count($arrCateNm), '?'));

// SQL 생성 - bd_matching_cate와 bd_matching_brand를 함께 조회
$sql = "SELECT BD_IDX, BD_NAME, bd_matching_cate, bd_matching_brand 
	FROM BRAND_DB 
	WHERE BD_NAME IN ($placeholders) 
	ORDER BY BD_NAME ASC";

$stmt = $db->prepare($sql);
$stmt->execute($arrCateNm);
$matchedData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// SQL 생성 - 포함되지 않는 데이터 조회
$sqlNotIn = "SELECT BD_IDX, BD_NAME, bd_matching_cate, bd_matching_brand 
	FROM BRAND_DB 
	WHERE BD_NAME NOT IN ($placeholders) 
	ORDER BY BD_NAME ASC";

$stmtNotIn = $db->prepare($sqlNotIn);
$stmtNotIn->execute($arrCateNm);
$unmatchedData = $stmtNotIn->fetchAll(PDO::FETCH_ASSOC);


// 일치 여부 확인 및 결과 생성
$results = [];
foreach ($responseData as $item) {
	
    // 매칭된 데이터를 검색
    $matchedRow = array_filter($matchedData, function ($row) use ($item) {
        return $row['BD_NAME'] === $item['cateNm'];
    });

    // 매칭된 데이터가 있으면 추가, 없으면 기본값으로 처리
    if (!empty($matchedRow)) {
        $matchedRow = array_shift($matchedRow); // 첫 번째 매칭된 데이터 사용
        $results[] = [
            'cateNm' => $item['cateNm'],
            'cateCd' => $item['cateCd'],
            'matched' => true,
            'bd_matching_cate' => $matchedRow['bd_matching_cate'],
            'bd_matching_brand' => $matchedRow['bd_matching_brand'],
            'idx' => $matchedRow['BD_IDX'],
        ];
    } else {
        $results[] = [
            'cateNm' => $item['cateNm'],
            'cateCd' => $item['cateCd'],
            'matched' => false,
            'bd_matching_cate' => null,
            'bd_matching_brand' => null,
        ];
    }
}

?>
<style type="text/css">

</style>

<div id="contents_head">
	<h1>고도몰 브랜드 매칭</h1>
    <div id="head_write_btn">
		<!-- 
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='/ad/onadb/onadb_board_reg/<?=$_get1?>'" > 
			<i class="fas fa-plus-circle"></i>
			신규 업무게시판 등록
		</button>
		-->
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
		<div id="list_new_wrap">

<div id="" class="table-wrap5">
	<div class=" scroll-wrap">

		<table class="table-st1">
			<thead>
			<tr>
				<th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
				<th class="list-idx">index</th>
				<th class="">고도몰 등록 이름</th>
				<th class="">고도몰 카테 코드</th>
				<th class="">등록된 카테 코드</th>
				<th>등록된 브랜드 코드</th>
				<th>idx</th>
				<th>메모</th>
			</tr>
			</thead>
			<tbody>
			<?
				$updateCount = 0; 
				foreach ( $results as $key => $row ){

					$_tr_class = "";
					
					if($row['cateCd'] != $row['bd_matching_cate'] ){
						$_tr_class = "status_bl";
						

						if( !empty($row['idx']) ){
							// 데이터 준비
							$cateCd = $row['cateCd'];
							$idx = $row['idx']; // 필요한 식별자 값 (예: PK 또는 고유 키)

							// SQL 업데이트 쿼리
							$query = "UPDATE BRAND_DB SET bd_matching_cate = :cateCd WHERE BD_IDX = :idx";
							$stmt = $db->prepare($query);

							// 바인딩
							$stmt->bindParam(':cateCd', $cateCd, PDO::PARAM_STR);
							$stmt->bindParam(':idx', $idx, PDO::PARAM_INT);

							// 실행
							if ($stmt->execute()) {
								// 업데이트가 성공적으로 완료되면 카운트 증가
								$updateCount++;
							}
						}

					}

			?>
			<tr align="center" id="trid_<?=$key?>" class="<?=$_tr_class?>">
				<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$key?>" ></td>	
				<td class="list-idx"><?=$key?></td>
				<td class=""><?=$row['cateNm']?></td>
				<td class=""><?=$row['cateCd']?></td>
				<td class=""><?=$row['bd_matching_cate']?></td>
				<td class=""><?=$row['bd_matching_brand']?></td>
				<td class="">
					<? if( $row['idx'] ?? '' ){ ?>
						<button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="koegAd.brandModify('<?=$row['idx']?>')" ><?=$row['idx']?></button>
					<? } ?>
				</td>
				<td class="text-left">
				</td>
			</tr>
			<? } ?>
					
			<tr>
				<td colspan="8">업데이트 : <b><?=$updateCount?></b></td>
			</tr>
					
			<tr>
				<td colspan="8">고도몰에 등록되지 않는 브랜드</td>
			</tr>	
			<?
				foreach ( $unmatchedData as $key => $row ){
			?>
			<tr align="center" id="trid_<?=$row['BD_IDX']?>" class="<?=$_tr_class?>">
				<td class="list-checkbox"><input type="checkbox" name="key_check[]" value="<?=$row['BD_IDX']?>" ></td>	
				<td class="list-idx"><?=$key?></td>
				<td class=""><?=$row['BD_NAME']?></td>
				<td class=""></td>
				<td class=""><?=$row['bd_matching_cate']?></td>
				<td class=""><?=$row['bd_matching_brand']?></td>
				<td class=""><button type="button" id="" class="btnstyle1 btnstyle1-sm" onclick="koegAd.brandModify('<?=$row['BD_IDX']?>')" ><?=$row['BD_IDX']?></button></td>
				<td class="text-left">
				</td>
			</tr>
			<? } ?>
			</tbody>
		</table>
		
	</div>
</div>
<?
/*
	echo "<pre>";
	print_r($notMatched);
	echo "</pre>";

	echo "<pre>";
	print_r($results);
	echo "</pre>";
*/
?>
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