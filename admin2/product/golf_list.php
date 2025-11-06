<?
$pageGroup = "product";
$pageName = "golf_list";

include "../lib/inc_common.php";

	$golf_where = "where GF_IDX > 0 ";

	$total_count = wepix_counter(_DB_GOLF, $golf_where);

	$list_num = 50;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	$golf_query = "select * from "._DB_GOLF." ".$golf_where." order by GF_IDX desc limit ".$from_record.", ".$list_num;
	$golf_result = wepix_query_error($golf_query);

	$page_link_text = _A_PATH_PRODUCT_GOLF_LIST."?pn=";
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
</STYLE>
<div id="contents_head">
	<h1>골프장 목록</h1>
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='<?=_A_PATH_PRODUCT_GOLF_REG?>'" > 
			<i class="fas fa-plus-circle"></i>
			신규등록
		</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="search-wrap">
			<ul class="td search-img"></ul>
			<ul class="td search-body">

				<div class="product-search" style="display:none;">
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
				<span class="count">등록된 골프장 : <b><?=$total_count?></b></span>
			</ul>
			<ul class="list-top-btn"></ul>
		</div>

		<div id="list-box">
			<div class="table-wrap">
				<table cellspacing="1px" cellpadding="0" border="0" class="table-style">	
					<tr>
						<th width="25px"><input type="checkbox" name="" onclick="select_all()"></th>
						<th width="80px">고유번호</th>
						<th width="170px">지역</th>
						<th>골프장명</th>
						<th width="60px">노출</th>
					</tr>
<?
				while($golf_list = wepix_fetch_array($golf_result)){
					$_view2_hotel_active  = ( $golf_list[HOT_VIEW]=="Y" ) ? "노출" : "비노출";
?>
					<tr align="center" id="trid_<?=$golf_list[GF_IDX]?>" bgcolor="<?=$trcolor?>">
						<td class="pd-checkbox" ><input type="checkbox" name="key_check[]" value="<?=$golf_list[GF_IDX]?>" ></td>
						<td><?=$golf_list[GF_IDX]?></td>
						<td><?=$bva_tr_area_name_ko[$golf_list[GF_AREA]]?></td>
						<td class="pd-name">
							<a onclick="productModify('<?= $golf_list[GF_IDX]?>');">
								<b><?=$golf_list[GF_NAME]?></b>
							</a>
						</td>
						<td><?=$_view2_hotel_active?></td>
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
	location.href = "<?=_A_PATH_PRODUCT_GOLF_REG?>?key="+key+"&mode=modify";
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