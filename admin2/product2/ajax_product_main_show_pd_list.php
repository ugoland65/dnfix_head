<?
include "../lib/inc_common.php";

	$_mps_pd = securityVal($mps_pd);
	$_ary2_mps_pd= array_unique(explode("/", $_mps_pd."/"));
?>

						<div class="btn-wrap">
 							<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="moveUpItem2()" style="width:120px" > <i class="fas fa-chevron-circle-up"></i> 선택 UP</button>
							<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="moveDownItem2()" style="width:120px" > <i class="fas fa-chevron-circle-down"></i> 선택 DOWN</button>
						</div>

<table cellspacing="1px" cellpadding="0" border="0" class="table-style" id='mps_pd_table'>	
<?
for($i=0; $i<count($_ary2_mps_pd); $i++){ 
  if($_ary2_mps_pd[$i] != ''){
	$i2 = $i + 1;
	$_show_sort_no = $i2;
	$_show_sort_id = "mark_".$i2;
	$pd_list = wepix_fetch_array(wepix_query_error("select * from "._DB_PRODUCT_TRAVEL." where PD_IDX = '".$_ary2_mps_pd[$i]."' "));

?>
	<tr align="center" id="<?=$_show_sort_id?>" bgcolor="<?=$trcolor?>">
		<td width="25px">
			<input type='hidden' name='mps_pd_idx[]' value='<?=$pd_list[PD_IDX]?>'>
			<input type="radio" name="chk" id="radio_<?=$_show_sort_id?>" value="<?=$_show_sort_id?>" onclick="chkSelect('<?=$_show_sort_id?>')" />
		</td>
		<td width="50px"><?=$_show_sort_no?></td>
		<td width="80px"><?=$pd_list[PD_IDX]?></td>
		<td width="170px"><?=$pd_list[PD_CATAGORY]?></td>
		<td align="left"><a onclick="productModify('<?=$pd_list[PD_IDX]?>');"><b><?=$pd_list[PD_NAME]?></b></a></td>
		<td width="100px" align="center"><?= number_format($pd_list[PD_SALE_PRICE])?> </td>
		<td width="100px" align="right"><?= number_format($pd_list[PD_COST_PRICE])?> </td>
		<td width="90px">
			<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="moveUpItem('<?=$_show_sort_id?>')" style="width:40px" > 
				<i class="fas fa-chevron-circle-up"></i> 
			</button>
			<button type="button" id="" class="btnstyle1 btnstyle1-info btnstyle1-sm" onclick="moveDownItem('<?=$_show_sort_id?>')" style="width:40px" > 
				<i class="fas fa-chevron-circle-down"></i>
			</button>
		</td>
		<td width="60px">
			<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-sm" onclick="selectDel('<?=$_show_sort_id?>')" > <i class="far fa-trash-alt"></i> 삭제</button>
		</td>
	</tr>
<?} } ?>
</table>

<script type="text/javascript"> 
<!-- 
function chkSelect(id) { 
    $("#mps_pd_table tr td").each(function(){
        $(this).css({'background':'#fff'  });  
    });
	$("#"+id+ " td").css({'background':'#dee7f9'  });  
}

function selectDel(id) { 
	if(confirm('해당상품을 삭제합니다\n삭제 후 완료 버튼을 눌러야 최종 적용됩니다.\n삭제 하시겠습니까?')){
		$("#"+id).remove();
	}
}
//--> 
</script>

<?
exit;
?>