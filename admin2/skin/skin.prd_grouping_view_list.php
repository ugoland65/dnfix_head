<?

$_idx = $_get1;
if( $_idx ){

	$data = sql_fetch_array(sql_query_error("select * from prd_grouping WHERE idx = '".$_idx."' "));
	
	$_prd_jsondata = json_decode($data['data'], true);

	$_mode_text['sale'] = "할인";
	$_mode_text['qty'] = "수량체크";
	$_mode_text['event'] = "기획전";
}

include($docRoot."/admin2/layout/header_popup.php");
?>

<table class="table-list" id="" >
	<tr class="list">
		<th style="width:100px">고유번호</th>
		<th style="width:80px">이미지</th>
		<th style="width:90px">분류</th>
		<th class="">이름</th>

		<? if( $data['pg_mode'] == "qty" ){ ?>
		<th style="width:50px">수량</th>
		<? }elseif( $data['pg_mode'] == "sale" ){ ?>
		<th>판매가<br>원가</th>
		<th>할인률</th>
		<? } ?>

		<th style="width:200px">메모</th>
	</tr>
	<?
	for ($z=0; $z<count($_prd_jsondata); $z++){

		$_prd_idx = $_prd_jsondata[$z]['idx'];
		$_ps_idx = $_prd_jsondata[$z]['stockidx'];
		$_pname = $_prd_jsondata[$z]['pname'];

		if( $_prd_idx == "Instant" ){
		}else{

			$_colum = "A.CD_IDX, A.CD_IMG, A.CD_CODE2, A.CD_CODE3, A.CD_NAME, A.CD_KIND_CODE, A.cd_code_fn, A.cd_sale_price, A.cd_cost_price";
			$_colum .= ", B.ps_idx";
			$_colum .= ", C.BD_NAME";

			$_query = "select ".$_colum." from "._DB_COMPARISON." A
				left join prd_stock B ON (B.ps_prd_idx = A.CD_IDX ) 
				left join "._DB_BRAND." C ON (C.BD_IDX = A.CD_BRAND_IDX  ) 
				where CD_IDX = '".$_prd_idx."' ";

			$prd_data = sql_fetch_array(sql_query_error($_query));

			if( $prd_data['CD_IMG'] ){
				$img_path = '/data/comparion/'.$prd_data['CD_IMG'];
			}

			$_name = $prd_data['CD_NAME'];

			$_cd_code_data = json_decode($prd_data['cd_code_fn'], true);

			$_jancode = $_cd_code_data['jan'];
		}

	?>
	<tr align="center" id="trid_<?=$_list['idx']?>" bgcolor="<?=$trcolor?>">
		<td>
			<div>
				<ul><span style="font-size:12px;"><?=$_prd_idx?></span></ul>
				
				<? if( $_prd_idx != "Instant" ){ ?>
				<ul>( <b style='color:#0093e9; font-size:14px;'><?=$_ps_idx?></b> )</ul>
				<? } ?>
			</div>
		</td>
		<td >
			<? if( $prd_data['CD_IMG'] ){ ?>
				<img src="<?=$img_path?>" style="height:70px; border:1px solid #eee !important;">
			<? }else{ ?>
				<div class="no-image">No image</div>
			<? } ?>
		</td>
		<td class="">
			<?=$koedge_prd_kind_name[$prd_data['CD_KIND_CODE']]?>
		</td>
		<td class="text-left" style="max-width:300px;">
			
			<? if( $_prd_idx == "Instant" ){ ?>
				<b><?=$_pname?></b>
			<? }else{ ?>
			<div>
				<ul><?=$_jancode?></ul>
				<ul class="m-t-3"><?=$prd_data['BD_NAME']?></ul>
				<ul class="m-t-3"><b onclick="onlyAD.prdView('<?=$prd_data['CD_IDX']?>','info');" style="cursor:pointer;" ><?=$_name?></b></ul>
			</div>
			<? } ?>
		</td>

		<? 
		// 수량체크 일경우
		if( $data['pg_mode'] == "qty" ){
		?>
		<td>
			<!-- <input type='text' name="pg_prd_qty[]" id='' size='20' value="<?=$_prd_jsondata[$z]['mode_data']['qty']?>" placeholder="" class="pg-qty"> -->
			<?=$_prd_jsondata[$z]['mode_data']['qty']?>
		</td>
		<? 
		// 할인일경우
		}elseif( $data['pg_mode'] == "sale" ){ 
			$_margin = "";
			$_margin_pre = "";
			if( $prd_data['cd_sale_price'] > 0 && $prd_data['cd_cost_price'] > 0 ){
				$_margin = $prd_data['cd_sale_price'] - $prd_data['cd_cost_price'];
				$_margin_pre = round(($prd_data['cd_sale_price'] - $prd_data['cd_cost_price']) / $prd_data['cd_sale_price'] * 100, 2);
			}
		?>
		<td class="text-right">
			<div>
				<ul>판매 : <b><?=number_format($prd_data['cd_sale_price'])?></b></ul>
				<ul>원가 : <b><?=number_format($prd_data['cd_cost_price'])?></b></ul>
				<? if( $prd_data['cd_sale_price'] > 0 && $prd_data['cd_cost_price'] > 0 ){ ?>
				<ul>마진 : <b><?=number_format($_margin)?></b><br>( <b><?=$_margin_pre?></b> %) </ul>
				<? } ?>
			</div>
		</td>
		<td>
			<input type='text' name="pg_prd_qty[]" id='' size='20' value="<?=$_prd_jsondata[$z]['mode_data']['qty']?>" placeholder="" class="pg-qty">
			<?=$_prd_jsondata[$z]['mode_data']['qty']?>
		</td>
		<? }?>

		<td>
			<!-- <textarea name="pg_prd_memo[]" class="pg-memo"><?=$_prd_jsondata[$z]['memo']?></textarea> -->
			<?=$_prd_jsondata[$z]['memo']?>
		</td>

	<tr>
	<? }?>
</table>