<?
include "../lib/inc_common.php";

	$_depth_id = securityVal($depth_id);
	$cateCut =  category_status($_depth_id);

	$pd_where = "where PD_PDC_ID like '".$cateCut."%' ";
	$pd_query = "select * from "._DB_PRODUCT_TRAVEL." ".$pd_where." order by PD_IDX desc";
	$pd_result = wepix_query_error($pd_query);
?>
<div class="table-wrap">
	<table cellspacing="1px" cellpadding="0" border="0" class="table-style product-choice">	
<?
while($pd_list = wepix_fetch_array($pd_result)){
?>
		<tr id="pdtr_<?=$pd_list[PD_IDX]?>">
			<td class="pdtd-checkbox"><input type="checkbox" name="key_check[]" id="key_check_<?=$pd_list[PD_IDX]?>" value="<?=$pd_list[PD_IDX]?>" onclick="chkSelect('<?=$pd_list[PD_IDX]?>');" />
			<td class="pdtd-name"><span id="pass_pd_name_<?=$pd_list[PD_IDX]?>"><?=$pd_list[PD_CATAGORY] ?><br><b><?= $pd_list[PD_NAME]?></b></span></td>
			<td class="pdtd-price"><span id="pass_pd_price_<?=$pd_list[PD_IDX]?>"><?=number_format($pd_list[PD_SALE_PRICE])?> ฿</span></td>
		</tr>
<? } ?>
	</table>
</div>

<script type="text/javascript">
<!-- 
function chkSelect(id) { 

	if($("#key_check_"+id).is(":checked")==true){
		$("#pdtr_"+id+ " td").css({'background':'#dee7f9' }); 
	}else{
		$("#pdtr_"+id+ " td").css({'background':'#ffffff' }); 
	}
/*
    $("#mps_pd_table tr td").each(function(){
        $(this).css({'background':'#fff' }); 
    });

	$("#"+id+ " td").css({'background':'#dee7f9'  }); 
*/
}

//--> 
</script> 

<?
exit;
?>