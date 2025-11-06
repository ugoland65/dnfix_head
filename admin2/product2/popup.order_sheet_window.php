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
				"ps_stock_sum" => ( $prd_data['ps_stock'] + $_qty )
			);

		} //for END z
	} //for END i

$popup_browser_title = "[주문상품 - ".$data['oo_name']."] 출력시간 : ".$action_time." ";

include "../layout/header_popup.php";
?>

<div class="print-wrap">
<table class="table-list" id="" >

<?
for ($i=0; $i<count($_prd); $i++){

	$_bar_code_normal = substr($_prd[$i]['bar_code'],0, -4 );
	$_bar_code_point = substr($_prd[$i]['bar_code'], -4 );
?>

<?
 if( ($i%12) ==0 ){
?>
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
<? } ?>

	<tr>
		<td style="height:82px;"><?=$_prd[$i]['ps_idx']?></td>
		<td><img src="<?=$_prd[$i]['img_path']?>" style="width:70px; "></td>
		<td><?=$_prd[$i]['brand_name']?></td>
		<td class="text-left">

			<? if( $_prd[$i]['bar_code'] ){ ?> <p>( <span ><?=$_bar_code_normal?> <b style="color:#ff0000; font-size:16px;"><?=$_bar_code_point?></b></span> )</p><? } ?>
			<!-- <p>( <b style="font-size:14px"><?=$_json_data[$i]['bar_code']?></b> )</p> -->

			<p class="m-t-5" style="cursor:pointer;" onclick="onlyAD.prdView('<?=$_prd[$i]['ps_prd_idx']?>','info');"><b style="font-size:14px"><?=$_prd[$i]['prd_name']?></b></p>
		</td>
		<td style="width:80px;"><b style="font-size:16px"><?=$_prd[$i]['ps_rack_code']?></b></td>
		<td style="width:70px; background-color:#f5f5f5;"><? if( $_prd[$i]['in_qty'] > 0 ){ ?><b style="font-size:14px; color:#ff0000;"><?=$_prd[$i]['in_qty']?></b><? } ?></td>
		<td style="width:70px;"><?=$_prd[$i]['ps_stock']?></td>
		<td style="width:70px; background-color:#f5f5f5;"><b style="font-size:14px;"><?=$_prd[$i]['ps_stock_sum']?></b></td>
	</tr>

<? } ?>
</table>
</div>

<script src="/admin2/js/common.js?ver=<?=$wepix_now_time?>"></script>