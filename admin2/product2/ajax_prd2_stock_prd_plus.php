<?
$pageGroup = "product";
$pageName = "category_form";

include "../lib/inc_common.php";

	$_mode = securityVal($mode);
	if( !$_mode ) $_mode = "all";

	if( $_mode == "all" ){
		$_serch_query = " where CD_IDX > 0 ";
	}else{
		$_serch_query = " where CD_KIND_CODE = '".$_mode."' ";
	}

	//검색이 있을경우
	if( $_s_active == "on" AND $_s_text != "" ){

		if (preg_match("/[a-zA-Z]/", $_s_text)){
			$_serch_query .= " AND INSTR(LOWER(CD_NAME), LOWER('".$_s_text."')) ";
		}else{
			$_serch_query .= " AND INSTR(LOWER(CD_NAME), '".$_s_text."') ";
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

	//$query = "select * from "._DB_COMPARISON." ".$_serch_query." order by ".$_sort_text." limit ".$from_record.", ".$list_num;
	$query = "select * from "._DB_COMPARISON." ".$_serch_query." order by ".$_sort_text;
	$result = wepix_query_error($query);

	$page_link_text = _A_PATH_COMPARISON_LIST."?s_active=".$_s_active."&s_text=".$_s_text."&sort_kind=".$_sort_kind."&pn=";
	$_view_paging = publicPaging($pn, $total_page, $list_num, $page_num, $page_link_text);

?>
<STYLE TYPE="text/css">
.prd-list{
	height:500px;
	overflow-y:scroll;
	border:1px solid #ddd !important;
}
</STYLE>

<div class="list-button-wrap m-b-5">
	<ul><button type="button" id="bklist_open" state="cloesd" class="btnstyle1 btnstyle1-gary btnstyle1-sm" onclick="prd2_stock_plus_list('all')">전체</button></ul>
	<ul><button type="button" id="bklist_open" state="cloesd" class="btnstyle1 btnstyle1-gary btnstyle1-sm" onclick="prd2_stock_plus_list('ONAHOLE')">오나홀</button></ul>
	<ul><button type="button" id="bklist_open" state="cloesd" class="btnstyle1 btnstyle1-gary btnstyle1-sm" onclick="prd2_stock_plus_list('WOMAN')">여성</button></ul>
	<ul><button type="button" id="bklist_open" state="cloesd" class="btnstyle1 btnstyle1-gary btnstyle1-sm" onclick="prd2_stock_plus_list('GEL')">젤</button></ul>
	<ul><button type="button" id="bklist_open" state="cloesd" class="btnstyle1 btnstyle1-gary btnstyle1-sm" onclick="prd2_stock_plus_list('SIDE')">보조</button></ul>
</div>

<div class="prd-list">
	<table class="table-list">
	<?
	while($list = wepix_fetch_array($result)){
		$brand_data = wepix_fetch_array(wepix_query_error("select BD_NAME from "._DB_BRAND." where BD_IDX = '".$list[CD_BRAND_IDX]."' "));
		$_view_brand_name = $brand_data[BD_NAME];
	?>
		<tr>
			<td class="tl-check"><input type="checkbox" name="key_check[]" value="<?=$list[CD_IDX]?>" ></td>
			<td><span id="ps_brand_name_"><?=$_view_brand_name?></td>
			<td><?=$list[CD_NAME]?></td>
		</tr>
	<? } ?>
	</table>
</div>

<div class="submitBtnWrap">
	<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-lg" onclick="goPlus();" > 
		<i class="far fa-check-circle"></i>
		상품추가
	</button>
</div>
<script type="text/javascript"> 
<!-- 
function goPlus(){

	var checkedCount = $("input[name='key_check[]']:checked").length;
	if( checkedCount < 1 ){
		alert("선택된 상품이 없습니다.");
        return false;
	}

	var passkey = "";
	var passkey_count = 0;
	$("input[name='key_check[]']:checked").each(function(){
		passkey_count++;

		passkey += $(this).val();
		if( passkey_count != checkedCount ){
			passkey += "/";
		}
	});


	$.ajax({
		url: "processing.prd2_stock.php",
		data: {
			"a_mode":"new_stock_prd",
			"ajax_mode":"on",
			"passkey":passkey
		},
		type: "POST",
		dataType: "text",
		success: function(getdata){
			if (getdata){
				redatawa = getdata.split('|');
				ckcode = redatawa[1];
				ckmsg = redatawa[2];
				ckcount = redatawa[3]*1;
				ckreturnkey = redatawa[4];

				if(ckcode == "Processing_Complete"){
					if( ckcount > 0 ){
						prd2_stock_list('all');
						closedPopup();
/*
						var arrString = ckreturnkey.split("/");
						location.reload();

							var html;
						for(var i=0; i<arrString.length; i++) { 

							html += "<tr>";
							html += '<td><span id="ps_brand_name_'+ arrString[i] +'"><?=$_view_brand_name?></span></td>';
							html += '<td><span id="ps_prd_name_'+ arrString[i] +'"><?=$list[CD_NAME]?></span></td>';
							html += '<td><?=$list[ps_stock]?></td>';
							html += '<td><button type="button" id="show_type_all" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="prd2_stock_cart('<?=$list[ps_idx]?>','plus');"><i class="fa fa-plus-circle" ></i></button></td>';
							html += '<td><button type="button" id="show_type_all" class="btnstyle1 btnstyle1-danger btnstyle1-sm" onclick="prd2_stock_cart('<?=$list[ps_idx]?>','minus');"><i class="fa fa-minus-circle" ></i></button></td>';
							html += '</tr>';

      var t = lst[i].split('='); 
      r += (i==0?'':','); 
      r += t[0] + ':"' + t[1] + '"'; 

						} 
	var html;
*/
					}
				}else{
					//return false;
				}
			}
		},
		error: function(){
			$('#modal_alert_msg').html('에러');
			$('#modal-alert').modal({show: true,backdrop:'static'});
		}
	});


/*
	var form = document.form1;
	form.submit();


processing.prd2_stock.php
*/
}



//--> 
</script> 
<?
exit;
?>