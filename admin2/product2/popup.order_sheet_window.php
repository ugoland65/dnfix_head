<?
include "../lib/inc_common.php";

	$_idx = securityVal($idx);

	$data = sql_fetch_array(sql_query_error("select * from ona_order where oo_idx = '".$_idx."' "));

	$_order_sec_json = json_decode($data['oo_json'], true);

	$num = 0;

	for ($i=0; $i<count($_order_sec_json); $i++){

		$_os_data_idx = $_order_sec_json[$i]['bidx'];
		$_order_sec_data[$_os_data_idx]['item'] = $_order_sec_json[$i]['item'];
		$_order_sec_data[$_os_data_idx]['qty'] = $_order_sec_json[$i]['qty'];
		$_order_sec_data[$_os_data_idx]['price'] = $_order_sec_json[$i]['price'];

		$_select_json = $_order_sec_json[$i]['selpd'];



		for ($z=0; $z<count($_select_json); $z++){ 

			$num++;

			$_pidx = $_select_json[$z]['pidx'];
			$_qty = $_select_json[$z]['qty'];

			$prd_data = wepix_fetch_array(wepix_query_error("select 
				A.CD_NAME, A.CD_IMG, A.CD_CODE, A.CD_CODE2, A.cd_code_fn, A.CD_INV_NAME1, A.CD_INV_NAME2, A.CD_INV_MATERIAL, A.CD_NAME_OG, A.CD_COO,
				B.ps_idx, B.ps_rack_code, B.ps_stock,
				C.BD_NAME 
				from "._DB_COMPARISON." A 
				left join prd_stock B ON (A.CD_IDX = B.ps_prd_idx ) 
				left join "._DB_BRAND." C  ON (A.CD_BRAND_IDX = C.BD_IDX ) 
				where CD_IDX = '".$_pidx."' "));

			$img_path = '../../data/comparion/'.$prd_data['CD_IMG'];

			$_prd[] = array(
				"ps_idx" => $prd_data['ps_idx'],
				"ps_prd_idx" => $_pidx,
				"img_path" => $img_path,
				"brand_name" => $prd_data['BD_NAME'],
				"bar_code" => $prd_data['CD_CODE'],
				"prd_name" => $prd_data['CD_NAME'],
				"ps_rack_code" => $prd_data['ps_rack_code'],
				"in_qty" => $_qty,
				"ps_stock" => $prd_data['ps_stock'],
				"ps_stock_sum" => ( $prd_data['ps_stock'] + $_qty ),
				"order_code" => $prd_data['CD_CODE2'],
			);

		} //for END z
	} //for END i

// sortmode에 따라 정렬
$_sortmode = securityVal($sortmode);
if(is_array($_prd)){
	if($_sortmode == 'barcode_desc'){
		// 바코드 마지막 4자리 기준 내림차순 정렬
		usort($_prd, function($a, $b) {
			$point_a = intval(substr($a['bar_code'], -4));
			$point_b = intval(substr($b['bar_code'], -4));
			return $point_b - $point_a;
		});
	} elseif($_sortmode == 'barcode_asc'){
		// 바코드 마지막 4자리 기준 오름차순 정렬
		usort($_prd, function($a, $b) {
			$point_a = intval(substr($a['bar_code'], -4));
			$point_b = intval(substr($b['bar_code'], -4));
			return $point_a - $point_b;
		});
	} elseif($_sortmode == 'rack_asc'){
		// 렉코드 오름차순 정렬
		usort($_prd, function($a, $b) {
			return strcmp($a['ps_rack_code'], $b['ps_rack_code']);
		});
	}
}

$popup_browser_title = "[주문상품 - ".$data['oo_name']."] 출력시간 : ".$action_time." ";

include "../layout/header_popup.php";
?>

<div class="print-wrap">

<div style="margin-bottom:10px; text-align:right;" class="no-print">
	<button type="button" onclick="location.href='?idx=<?=$_idx?>';" class="btn btn-sm <?=($_sortmode==''?'btn-primary':'btn-default')?>">기본 정렬</button>
	<button type="button" onclick="location.href='?idx=<?=$_idx?>&sortmode=barcode_desc';" class="btn btn-sm <?=($_sortmode=='barcode_desc'?'btn-primary':'btn-default')?>">바코드↓</button>
	<button type="button" onclick="location.href='?idx=<?=$_idx?>&sortmode=barcode_asc';" class="btn btn-sm <?=($_sortmode=='barcode_asc'?'btn-primary':'btn-default')?>">바코드↑</button>
	<button type="button" onclick="location.href='?idx=<?=$_idx?>&sortmode=rack_asc';" class="btn btn-sm <?=($_sortmode=='rack_asc'?'btn-primary':'btn-default')?>">렉코드↑</button>
	<button type="button" onclick="window.print();" class="btn btn-sm btn-success" style="margin-left:10px;">인쇄</button>
</div>

<div class="print-header">
	<div class="print-date">출력일: <?=$action_time?></div>
	<div class="print-page-number">페이지 <span class="current-page"></span> / <span class="total-pages"></span></div>
</div>

<table class="table-list" id="" >
<thead>
	<tr>
		<th>재고<br>코드</th>
		<th>이미지</th>
		<th>브랜드</th>
		<th>상품명</th>
		<th>렉코드</th>
		<th>입고 수량</th>
		<th>현재 재고</th>
		<th>합계 재고</th>
	</tr>
</thead>
<tbody>
<?
for ($i=0; $i<count($_prd); $i++){

	$_bar_code_normal = substr($_prd[$i]['bar_code'],0, -4 );
	$_bar_code_point = substr($_prd[$i]['bar_code'], -4 );
?>
	<tr>
		<td style="height:100px;"><?=$_prd[$i]['ps_idx']?></td>
		<td><img src="<?=$_prd[$i]['img_path']?>" style="width:100px; "></td>
		<td><?=$_prd[$i]['brand_name']?></td>
		<td class="text-left">

			<? if( $_prd[$i]['bar_code'] ){ ?> <p>( <span ><?=$_bar_code_normal?> <b style="color:#ff0000; font-size:16px;"><?=$_bar_code_point?></b></span> )</p><? } ?>
			<!-- <p>( <b style="font-size:14px"><?=$_json_data[$i]['bar_code']?></b> )</p> -->
			<p class="m-t-5" style="cursor:pointer;" onclick="onlyAD.prdView('<?=$_prd[$i]['ps_prd_idx']?>','info');"><?=$_prd[$i]['prd_name']?></p>
			<p class="m-t-10"><b style="font-size:15px"><?=$_prd[$i]['order_code']?></b></p>

		</td>
		<td style="width:80px;"><b style="font-size:16px"><?=$_prd[$i]['ps_rack_code']?></b></td>
		<td style="width:70px; background-color:#f5f5f5;"><? if( $_prd[$i]['in_qty'] > 0 ){ ?><b style="font-size:14px; color:#ff0000;"><?=$_prd[$i]['in_qty']?></b><? } ?></td>
		<td style="width:70px;"><?=$_prd[$i]['ps_stock']?></td>
		<td style="width:70px; background-color:#f5f5f5;"><b style="font-size:14px;"><?=$_prd[$i]['ps_stock_sum']?></b></td>
	</tr>

<? } ?>
</tbody>
</table>
</div>

<style>
@media screen {
	.print-header {
		display: none;
	}
	.print-date {
		text-align: right;
		padding: 10px 0;
		font-size: 14px;
		font-weight: bold;
	}
}

@media print {
	.no-print {
		display: none !important;
	}
	
	@page {
		margin-top: 70px;
		margin-bottom: 30px;
		margin-left: 10px;
		margin-right: 10px;
	}
	
	.print-header {
		position: fixed;
		top: -70px;
		left: 0;
		right: 0;
		height: 50px;
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 10px 15px;
		font-size: 13px;
		font-weight: bold;
		border-bottom: 2px solid #333;
		background: white;
		z-index: 9999;
	}
	
	.print-date {
		text-align: left;
	}
	
	.print-page-number {
		text-align: right;
	}
	
	.current-page::after {
		content: counter(page);
	}
	
	body {
		margin: 0;
		padding: 0;
		counter-reset: page;
	}
	
	.print-wrap {
		position: relative;
		margin: 0;
		padding: 0;
	}
	
	.table-list {
		width: 100%;
		border-collapse: collapse;
	}
	
	.table-list thead {
		display: table-header-group;
	}
	
	.table-list tbody {
		display: table-row-group;
	}
	
	.table-list td img {
		width: 100px !important;
		max-width: 100px !important;
		height: auto !important;
	}
}
</style>

<script src="/admin2/js/common.js?ver=<?=$wepix_now_time?>"></script>
<script>
// 총 페이지 수 계산
function calculateTotalPages() {
	var totalRows = <?=count($_prd)?>;
	var rowHeight = 100; // td 높이
	var headerHeight = 40; // thead 높이
	var pageContentHeight = 1050; // A4 기준 페이지 콘텐츠 영역 (대략)
	
	var rowsPerPage = Math.floor((pageContentHeight - headerHeight) / rowHeight);
	var totalPages = Math.ceil(totalRows / rowsPerPage);
	
	return totalPages > 0 ? totalPages : 1;
}

// 페이지 번호 업데이트
function updatePageNumbers() {
	var totalPages = calculateTotalPages();
	var pageNumberElements = document.querySelectorAll('.print-page-number');
	
	pageNumberElements.forEach(function(element) {
		var totalPagesSpan = element.querySelector('.total-pages');
		if (totalPagesSpan) {
			totalPagesSpan.textContent = totalPages;
		}
	});
}

// 인쇄 전 페이지 번호 설정
window.onbeforeprint = function() {
	updatePageNumbers();
	
	// CSS counter를 사용하여 현재 페이지 표시
	var style = document.createElement('style');
	style.innerHTML = '@media print { .current-page::after { content: counter(page); } }';
	document.head.appendChild(style);
};

// 페이지 로드 시 실행
document.addEventListener('DOMContentLoaded', function() {
	updatePageNumbers();
});
</script>