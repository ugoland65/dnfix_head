<?
$pageGroup = "product";
$pageName = "category_form";

include "../lib/inc_common.php";

	$_prd_mode = securityVal($prd_mode);
	$_prd_brand = securityVal($prd_brand);
	$_prd_keyword = securityVal($prd_keyword);

	if( !$_prd_mode ) $_prd_mode = "all";

	if( $_prd_mode == "all" ){
		$_serch_query = " where CD_IDX > 0 ";
	}else{
		$_serch_query = " where CD_KIND_CODE = '".$_prd_mode."' ";
	}

	if( $_prd_brand ){
		$_serch_query .= " and CD_BRAND_IDX = '".$_prd_brand."' ";
	}


	//검색이 있을경우
	if( $_prd_keyword != "" ){
		if (preg_match("/[a-zA-Z]/", $_prd_keyword)){
			$_serch_query .= " AND INSTR(LOWER(CD_NAME), LOWER('".$_prd_keyword."')) ";
		}else{
			$_serch_query .= " AND INSTR(LOWER(CD_NAME), '".$_prd_keyword."') ";
		}
	}


	$total_count = wepix_counter(_DB_COMPARISON, $_serch_query);
	
	$list_num = 100;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	if( $_sort_kind == "hit" ) {
		$_sort_text = "CD_HIT desc";
	}else{
		$_sort_text = "CD_IDX desc";
	}

	$query = "select * from "._DB_COMPARISON." ".$_serch_query." order by CD_IDX desc";
	$result = wepix_query_error($query);

	$page_link_text = _A_PATH_COMPARISON_LIST."?s_active=".$_s_active."&s_text=".$_s_text."&sort_kind=".$_sort_kind."&pn=";
	$_view_paging = publicPaging($pn, $total_page, $list_num, $page_num, $page_link_text);

?>
	<table class="table-list">
	<?
	while($list = wepix_fetch_array($result)){
		$brand_data = wepix_fetch_array(wepix_query_error("select BD_NAME from "._DB_BRAND." where BD_IDX = '".$list[CD_BRAND_IDX]."' "));
		$_view_brand_name = $brand_data[BD_NAME];

							$img_path = '../../data/comparion/'.$list[CD_IMG];

							if($list[CD_COMPARISON] == "N" ){
								$_trcolor = "#eee";
							}else{
								$_trcolor = "#fff";
							}
	?>
		<tr>
			<td><?=$list[CD_IDX]?></td>
			<td>
				<span id="ps_brand_name_<?=$list[CD_IDX]?>"><?=$_view_brand_name?></span>
				<br><b><?=$koedge_prd_kind_name[$list[CD_KIND_CODE]]?></b>
			</td>
			<td>
				<? if($list[CD_COMPARISON] == "N" ){?>비노출<br><?}?>
				<span id="ps_prd_img_<?=$list[CD_IDX]?>"><? if( $list[CD_IMG]){?><img src="<?=$img_path?>" style="height:70px;"><? } ?></span>
			</td>
			<td style="text-align:left">
				<span id="ps_prd_name_<?=$list[CD_IDX]?>" style="color:#000; font-size:13px"><?=$list[CD_NAME]?></span>
				<?if( $list[CD_NAME_OG]){ ?><br><span style="color:#999; font-size:11px">(<?=$list[CD_NAME_OG]?>)</span><? }?>
			</td>
			<td><button type="button" id="show_type_all" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="prdRankingCart('<?=$list[CD_IDX]?>');"><i class="fa fa-plus-circle" ></i></button></td>
		</tr>
	<? } ?>
	</table>
<?
exit;
?>