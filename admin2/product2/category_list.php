<?
$pageGroup = "product2";
$pageName = "category_list";

include "../lib/inc_common.php";

	$_key = securityVal($key);

	//$pd_ct_where = " where PDC_NEW_KIND ='G' ";
	$pd_ct_where = "  ";
	$pd_ct_query = "select * from "._DB_PRODUCT_CATAGORY_TRAVEL." ".$pd_ct_where."order by PDC_ID asc ";
	$pd_ct_result = wepix_query_error($pd_ct_query);

include "../layout/header.php";
?>
<div id="contents_head">
	<h1>상품 분류 관리</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
	
		<table cellspacing="0" cellpadding="0" border="0" class="table-style2 treewrap">	
			<tr>
				<td class="treewrap-menu">
					
					<div class="tree-left-wrap">
						<?
						while($pd_ct_list = wepix_fetch_array($pd_ct_result)){
							$_view2_pdc_name = $pd_ct_list[PDC_NAME];
							if( $pd_ct_list[PDC_DEPTH] == 0 ){
						?>
							<ul id="cate_<?=$pd_ct_list[PDC_IDX]?>" class="tree-big-menu2" onclick="showCategory('view', '<?=$pd_ct_list[PDC_IDX]?>')"><?=$_view2_pdc_name?> (<b><?=$pd_ct_list[PDC_NEW_KIND]?></b>)</ul>
							<? 
							}else{ 

								//중분류
								if( $pd_ct_list[PDC_DEPTH] == 1 ){
									$_view2_pdc_icon = "<i class='fas fa-caret-right'></i> ";
									$_view2_pdc_style = " style='padding-left:35px; !important;' ";
								//소분류
								}elseif( $pd_ct_list[PDC_DEPTH] == 2 ){
									$_view2_pdc_icon =  "<i class='fas fa-genderless'></i> ";
									$_view2_pdc_style = " style='padding-left:55px; !important;' ";
								}elseif( $pd_ct_list[PDC_DEPTH] == 3 ){
									$_view2_pdc_icon =  "<i class='fas fa-caret-right'></i> ";
									$_view2_pdc_style = " style='padding-left:70px; !important;' ";
								}

							?>
							<ul class="tree-mid-wrap" onclick="showCategory('view', '<?=$pd_ct_list[PDC_IDX]?>')">
								<li id="cate_<?=$pd_ct_list[PDC_IDX]?>" class="tree-mid-menu2" <?=$_view2_pdc_style?>>
									<?=$_view2_pdc_icon?><?=$_view2_pdc_name?> (<b><?=$pd_ct_list[PDC_NEW_KIND]?></b>)
								</li>
							</ul>
							<? } ?>
						<? } ?>
					</div>

				</td>
				<td class="treewrap-margin" style="border:none;"></td>
				<td class="treewrap-body">
					<div id="ajax_show">
					</div>
				</td>
			</tr>
		</table>

	</div>
</div>
<script type="text/javascript"> 
<!-- 
	var showCategory = function(wmode, idx){
       
		$(".tree-mid-menu2").each(function(i){
			$(this).css({'font-weight':'','color':'','background-color':'' }); 
		});
		if( wmode == "view" && idx ){
			$("#cate_"+idx).css({'font-weight':'bold', 'color':'#2070db' }); 
		}

		$.ajax({
			type: "post",
			url : "<?=_A_PATH_PRODUCT_CATE_FORM?>",
			data : { wmode : wmode, idx : idx },
			success: function(oHtml) {
				$('#ajax_show').html(oHtml);
			}
		});
	};


<? if( $_key ){ ?>
	showCategory('view', '<?=$_key?>');
<? }else{ ?>
	showCategory('new', '');
<? } ?>

//--> 
</script> 
<?
include "../layout/footer.php";
exit;
?>