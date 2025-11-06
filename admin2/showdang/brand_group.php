<?
$pageGroup = "showdang";
$pageName = "brand_group";

include "../lib/inc_common.php";

	$_search_query = " WHERE BD_NAME_GROUP != '' AND bd_showdang_active = 'Y' ";
	$query = "select * from "._DB_BRAND." ".$_search_query." order by BD_NAME desc";
	$result = wepix_query_error($query);
	while($list = wepix_fetch_array($result)){
	
		$_bd_kind = json_decode($list['bd_kind'], true);
		
		$arycate = [];
		
		if( $_bd_kind['ona'] == "Y" ) $arycate[] = "오나홀";
		if( $_bd_kind['breast'] == "Y" ) $arycate[] = "가슴형";
		if( $_bd_kind['gel'] == "Y" ) $arycate[] = "윤활제";
		if( $_bd_kind['condom'] == "Y" ) $arycate[] = "콘돔";
		if( $_bd_kind['annal'] == "Y" ) $arycate[] = "애널용품";
		if( $_bd_kind['prostate'] == "Y" ) $arycate[] = "전립선자극";
		if( $_bd_kind['care'] == "Y" ) $arycate[] = "관리/보조";
		if( $_bd_kind['dildo'] == "Y" ) $arycate[] = "딜도";
		if( $_bd_kind['vibe'] == "Y" ) $arycate[] = "바이브";
		if( $_bd_kind['suction'] == "Y" ) $arycate[] = "흡입토이";
		if( $_bd_kind['man'] == "Y" ) $arycate[] = "남성보조";
		if( $_bd_kind['nipple'] == "Y" ) $arycate[] = "니플/유두";
		if( $_bd_kind['cos'] == "Y" ) $arycate[] = "코스튬/속옷";
		if( $_bd_kind['perfume'] == "Y" ) $arycate[] = "향수/목욕";
		if( $_bd_kind['bdsm'] == "Y" ) $arycate[] = "BDSM";

		$_show_cate = implode("|", $arycate);


		$arr_gp_ko[$list['BD_NAME_GROUP']][] = array( 
			'idx' => $list['BD_IDX'],
			'name' => $list['BD_NAME'],
			'name_en' => $list['BD_NAME_EN'],
			'cate_no' => $list['bd_cate_no'],
			'show_cate' => $_show_cate
		);

		$arr_gp_en[$list['BD_NAME_EN_GROUP']][] = array( 
			'idx' => $list['BD_IDX'],
			'name' => $list['BD_NAME'],
			'name_en' => $list['BD_NAME_EN'],
			'cate_no' => $list['bd_cate_no'],
			'show_cate' => $_show_cate
		);

		/*
		$query = "update "._DB_BRAND." set
			bd_showdang_active = 'Y'
			where BD_IDX = '".$list['BD_IDX']."' ";
		sql_query_error($query);
		*/

	}


include "../layout/header.php";
?>
<div id="contents_head">
	<h1>브랜드 그룹</h1>
    <div id="head_write_btn">
	</div>
</div>

<style type="text/css">
.name-group-wrap{ 
	display:table; border-bottom:1px solid #aaa; 
	ul{ display:table-cell; padding:6px; 
		&.gp-name{ width:80px; 
			p{ font-size:17px; }
		}

		&:nth-of-type(2) {
			display:flex;
			flex-wrap: wrap;
			gap:5px;

			.b-name{ border:1px solid #777; background:#fff; padding:6px 10px; margin:4px 0; border-radius:6px; cursor:pointer; }
			.b-name:hover{ background:#ffd4f6; }
		}
	}
}



</style>

<div id="contents_body">
	<div id="contents_body_wrap">
<!-- <?=$query?> -->
	<? 
	for ($i=0; $i<count($arr_ko_1st); $i++){ 
		$_chos_code = $arr_ko_1st[$i];
		$_chos_ary = $arr_gp_ko[$_chos_code];

		sort($_chos_ary);

	?>
		<div class="name-group-wrap">
			<ul class="gp-name"><p><?=$arr_ko_1st[$i]?></p></ul>
			<ul>
			<? for ($z=0; $z<count($_chos_ary); $z++){ ?>
				<div class="b-name" onclick="koegAd.brandModify('<?=$_chos_ary[$z]['idx']?>')">
					<b><?=$_chos_ary[$z]['name']?></b><br>
					<?=$_chos_ary[$z]['name_en']?><br>
					<!-- 
					<ul>( <?=$_chos_ary[$z]['cate_no']?> )</ul>
					-->
					<?=$_chos_ary[$z]['show_cate']?>
				</div>
			<? } ?>
			</ul>
		</div>
	<? } ?>

	<div class="m-t-50">알파벳 순</div>
	<? 
	for ($i=0; $i<count($arr_en_1st); $i++){ 
		$_chos_code = $arr_en_1st[$i];
		$_chos_ary = $arr_gp_en[$_chos_code];

		sort($_chos_ary);

	?>
		<div class="name-group-wrap">
			<ul class="gp-name"><p><?=$arr_en_1st[$i]?></p></ul>
			<ul>
			<? for ($z=0; $z<count($_chos_ary); $z++){ ?>
				<span class="b-name" onclick="koegAd.brandModify('<?=$_chos_ary[$z]['idx']?>')">
					<b><?=$_chos_ary[$z]['name_en']?></b><br>
					<?=$_chos_ary[$z]['name']?><br>
					( <?=$_chos_ary[$z]['cate_no']?> )<br>
					<?=$_chos_ary[$z]['show_cate']?>
				</span>
			<? } ?>
			</ul>
		</div>
	<? } ?>

	</div>
</div>
<?
include "../layout/footer.php";
exit;
?>
