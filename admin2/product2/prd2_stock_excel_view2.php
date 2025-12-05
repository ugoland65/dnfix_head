<?
include "../lib/inc_common.php";

	$_idx = securityVal($idx);
	$_sort = securityVal($sort);
	$_mode = securityVal($mode);

	$data = sql_fetch_array(sql_query_error("select file_name, data, error from prd_stock_history where uid = '".$_idx."' "));

	$_json_data = json_decode($data['data'],true);
	$_error_data = json_decode($data['error'],true);


for ($i=0; $i<count($_json_data); $i++){

	$_ps_idx = $_json_data[$i]['ps_idx'];
	$prd_data = sql_fetch_array(sql_query_error("select 
		A.ps_prd_idx, A.ps_rack_code, A.ps_stock,
		B.CD_NAME, B.CD_IMG, B.CD_CODE,
		C.BD_NAME 
		from prd_stock A 
		left join "._DB_COMPARISON." B ON (A.ps_prd_idx = B.CD_IDX ) 
		left join "._DB_BRAND." C  ON (B.CD_BRAND_IDX = C.BD_IDX ) 
		where ps_idx = '".$_ps_idx."' "));

	$img_path = '../../data/comparion/'.$prd_data['CD_IMG'];

	$_json_data[$i]['img_path'] = $img_path;
	$_json_data[$i]['brand_name'] = $prd_data['BD_NAME'];
	$_json_data[$i]['bar_code'] = $prd_data['CD_CODE'];
	$_json_data[$i]['prd_name'] = $prd_data['CD_NAME'];
	$_json_data[$i]['ps_rack_code'] = $prd_data['ps_rack_code'];
	$_json_data[$i]['ps_stock'] = $prd_data['ps_stock'];
	$_json_data[$i]['ps_stock_sum'] = ($prd_data['ps_stock'] - $_json_data[$i]['qty']);
	$_json_data[$i]['ps_prd_idx'] = $prd_data['ps_prd_idx'];
}

	if( $_sort == "brand" ){
		$_json_data = arr_sort( $_json_data,'brand_idx', 'desc' );
	}else{
		$_json_data = arr_sort( $_json_data,'qty', 'desc' );
	}
	$_json_data = arr_sort( $_json_data,'ps_rack_code', 'asc' );

include "../layout/header_popup.php";

/*
	echo "<pre>";
	print_r($_json_data);
	echo "</pre>";
*/
?>

<style>
	.table-list tr td{
		padding: 5px !important;
	}
</style>
<div class="print-wrap">
<table class="table-list" id="">
<thead>
	<tr>
		<th>재고<br>코드</th>
		<th>이미지</th>
		<th>상품명</th>
		<th>렉코드</th>
		<th>패킹<br>재거</th>
		<th>단일<br>상품</th>
		<th>세트<br>상품</th>
		<th>금일<br>출고</th>
		<th>현재<br>재고</th>
		<th>남는<br>재고</th>
	</tr>
</thead>
<tbody>
<?
for ($i=0; $i<count($_json_data); $i++){

	$_bar_code_normal = substr($_json_data[$i]['bar_code'],0, -5 );
	$_bar_code_point = substr($_json_data[$i]['bar_code'], -5 );
?>
	<tr>
		<td><?=$_json_data[$i]['ps_idx']?></td>
		<td width="90px"><img src="<?=$_json_data[$i]['img_path']?>" style="width:80px; "></td>
		<td class="text-left">
			<p><?=$_json_data[$i]['brand_name']?></p>
			<? if( $_json_data[$i]['bar_code'] ){ ?> <p>( <span ><?=$_bar_code_normal?> <b style="color:#ff0000; font-size:16px;"><?=$_bar_code_point?></b></span> )</p><? } ?>
			<!-- <p>( <b style="font-size:14px"><?=$_json_data[$i]['bar_code']?></b> )</p> -->

			<p class="m-t-5" style="cursor:pointer;" onclick="onlyAD.prdView('<?=$_json_data[$i]['ps_prd_idx']?>','info');"><b style="font-size:14px"><?=$_json_data[$i]['prd_name']?></b></p>
		</td>
		<td style="width:80px;"><b style="font-size:16px"><?=$_json_data[$i]['ps_rack_code']?></b></td>
		<td style="width:40px; background-color:#f5f5f5;"><? if( $_json_data[$i]['packageOut'] > 0 ){ ?><?=$_json_data[$i]['packageOut']?><? } ?></td>
		<td style="width:40px;"><?=$_json_data[$i]['one']['qty']?></td>
		<td style="width:40px;"><?=$_json_data[$i]['set']['qty']?></td>
		<td style="width:40px; background-color:#f5f5f5;"><b style="font-size:14px; color:#ff0000;"><?=$_json_data[$i]['qty']?></b></td>
		<td style="width:70px;"><?=$_json_data[$i]['ps_stock']?></td>
		<td style="width:70px; background-color:#f5f5f5;"><b style="font-size:14px;"><?=$_json_data[$i]['ps_stock_sum']?></b></td>
	</tr>

<? } ?>
</tbody>
</table>

<?
if( count($_error_data['result']) > 0 ){
?>
에러 항목
<div>
	<? for ($i=0; $i<count($_error_data['result']); $i++){ ?>
	<ul><?=$_error_data['result'][$i]?></ul>
	<? } ?>
</div>
<? } ?>

</div>

<style>
@media print {
	@page {
		margin-top: 30px;
		margin-bottom: 30px;
		margin-left: 10px;
		margin-right: 10px;
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
		width: 80px !important;
		max-width: 80px !important;
		height: auto !important;
	}
	
	/* 프린트 시에도 빨간색 유지 */
	b[style*="color:#ff0000"],
	b[style*="color: #ff0000"] {
		color: #ff0000 !important;
		-webkit-print-color-adjust: exact !important;
		print-color-adjust: exact !important;
	}
}
</style>

<script src="/admin2/js/common.js?ver=<?=$sql_now_time?>"></script>