<?
$pageGroup = "product";
$pageName = "product_list";

include "../lib/inc_common.php";

	$pd_where = "where PD_IDX > 0 ";

	$total_count = wepix_counter(_DB_PRODUCT_TRAVEL, $pd_where);

	$list_num = 50;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	$pd_query = "select * from "._DB_PRODUCT_TRAVEL." ".$pd_where." order by PD_IDX desc limit ".$from_record.", ".$list_num;
	$pd_result = wepix_query_error($pd_query);

	$page_link_text = _A_PATH_PRODUCT_LIST."?pn=";
	$view_paging = paging($pn, $total_page, $list_num, $page_num, $page_link_text);

include "../layout/header.php";

?>
<STYLE TYPE="text/css">
.table-style{ width:100% !important; }
.pd-name{ text-align:left; cursor:pointer; }
.pd-checkbox{ height:30px; }
.product-search{ display:table; }
.product-search ul{ display:table-row; }
.product-search ul li { display:table-cell; box-sizing:border-box; padding-top:3px; padding-bottom:3px; }
.product-search ul li.clause-name{ width:80px; text-align:right; padding-right:7px; }
.search-text{ width:300px !important; }

.bl_tr{ background:#ffffff; cursor:pointer; }
.bl_tr2{ background:#f5f3f4; cursor:pointer; }
.bl_tr:hover, .bl_tr2:hover{ background:#dee7f9; }
</STYLE>
<div id="contents_head">
	<h1>상품 목록</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="search-wrap">
			<ul class="td search-img"></ul>
			<ul class="td search-body">

				<div class="product-search">
					<ul>
						<li class="clause-name">검색어</li>
						<li>
							<select name="">
								<option value="" >상품명</option>
								<option value="" >영문상품명</option>
							</select>
							<input type='text' id='search_text' name='search_text' class="search-text" value='<?=$search_text?>' placeholder="검색어를 입력하세요">
						</li>
					</ul>
					<ul>
						<li class="clause-name">상품분류</li>
						<li>
							<select  name='pdc_depth_1' id='pdc_depth_1' class="cate-select" onchange="showDepthChange('', this.value, '1', 'pdc_depth_2');">
								<option value="">=== 1차 분류 ===</option>
								<?
								$pdc_depth_1_query = "select PDC_NAME, PDC_ID, PDC_IDX from "._DB_PRODUCT_CATAGORY_TRAVEL." where PDC_DEPTH = '0' and PDC_NEW_KIND ='G' order by PDC_ID asc ";
								$pdc_depth_1_result = wepix_query_error($pdc_depth_1_query);
								while($pdc_depth_1_list = wepix_fetch_array($pdc_depth_1_result)){
								?>
								<option value="<?=$pdc_depth_1_list[PDC_ID]?>" ><?=$pdc_depth_1_list[PDC_NAME]?></option>
								<? } ?>
							</select>
							<select name='pdc_depth_2' id="pdc_depth_2" class="cate-select" onchange="showDepthChange('', this.value, '2', 'pdc_depth_3');" >
								<option value="">=== 2차 분류 ===</option>
							</select>
							<select name='pdc_depth_3' id="pdc_depth_3" class="cate-select" onchange="showDepthChange('', this.value, '3', 'pdc_depth_4');" >
								<option value="">=== 3차 분류 ===</option>
							</select>
							<select name='pdc_depth_4' id="pdc_depth_4" class="cate-select">
								<option value="">=== 4차 분류 ===</option>
							</select>
						</li>
					</ul>
				</div>

			</ul>
            <ul class="td search-button">
				<input type="submit" value="Searching">
			</ul>
		</div>

		<div class="list-top-btn-wrap">
			<ul class="list-top-total">
				<span class="count">등록된 상품수 : <b><?=$total_count?></b></span>
			</ul>
			<ul class="list-top-btn"></ul>
		</div>

		<div id="list-box">
			<div class="table-wrap">
				<table cellspacing="1px" cellpadding="0" border="0" class="table-style">	
					<tr>
						<th width="25px"><input type="checkbox" name="" onclick="select_all()"></th>
						<th width="80px">고유번호</th>
						<th width="170px">카테고리</th>
						<th>상품명</th>
						<th width="100px">판매가</th>
						<th width="100px">원가</th>
						<th width="60px">노출</th>
<!-- 
						<th width="30px">관리 </th>
 -->
					</tr>
<?
$_row_num = 0;
while($pd_list = wepix_fetch_array($pd_result)){
	$_row_num++;
	if( $_row_num % 2 == 1){ $trclass = "bl_tr"; }elseif( $_row_num % 2 == 0){ $trclass = "bl_tr2"; }
?>
					<tr align="center" id="trid_<?=$pd_list[PD_IDX]?>" class="<?=$trclass?>">
						<td class="pd-checkbox" ><input type="checkbox" name="key_check[]" value="<?=$pd_list[PD_IDX]?>" ></td>
						<td><?= $pd_list[PD_IDX]?></td>
						<td><?= $pd_list[PD_CATAGORY]?></td>
						<td class="pd-name"><a onclick="productModify('<?= $pd_list[PD_IDX]?>');"><b><?= $pd_list[PD_NAME]?></b></a></td>
						<td align="center"><?= number_format($pd_list[PD_SALE_PRICE])?> </td>
						<td align="right"><?= number_format($pd_list[PD_COST_PRICE])?> </td>
						<td><?= $viewYn?></td>
<!-- 
						<td>
							<input type='button' value='수정' class="basicSBtn" onclick="location.href='product_form.php?mode=modify&mokey=<?= $pd_list[PD_IDX]?>'">

							<input type='button' onclick="javascript:goModify('<?= $pd_list[PD_IDX]?>');"; value='수정'>
							<input type='button' onclick="javascript:goDel('<?= $pd_list[PD_IDX]?>')"; value='삭제'>

						</td>
 -->
					</tr>
<? } ?>
				</table>
			</div>
		</div>
		<div class="paging-wrap"><?=$view_paging?></div>	
	</div>
</div>
<script type="text/javascript"> 
<!-- 
function productModify(key){
	window.open("<?=_A_PATH_PRODUCT_MOD_POPUP?>?key="+key, "overlap_"+key, "width=1070,height=660,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no");
}

function showDepthChange(ct_id, depth_id, depth, target_id){ 
		$.ajax({
			type : "POST",
			url : "<?=_A_PATH_PRODUCT_CATE_SELECT_SHOW?>",
			data : { ct_id : ct_id, depth_id : depth_id, depth : depth },
			error : function(){
			},
			success : function(data){
				$("#"+ target_id).html(data) ;
			}
        });
}
//--> 
</script> 
<?
include "../layout/footer.php";
exit;
?>