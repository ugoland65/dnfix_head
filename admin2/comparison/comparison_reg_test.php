<?
$pageGroup = "comparison";
$pageName = "comparison_reg";
include "../lib/inc_common.php";

	

	$_mode = securityVal($mode);
	$_idx = securityVal($key);

	if( $_mode == "modify" ){
		$comparison_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$_idx."' "));

		$page_title_text = "가격비교 수정";
		$submit_btn_text = "가격비교 수정";
		$com_tag_result = wepix_query_error("select * from "._DB_COMPARISON_TAG." where CT_CD_IDX = '".$_idx."' ");
	
		while($com_tag_list = wepix_fetch_array($com_tag_result)){
			$_ary_com_tag[] = $com_tag_list[CT_TG_IDX];
		}

		$query = "select * from "._DB_COMPARISON." com inner join "._DB_COMPARISON_TAG." com_tag ".$_where_sql." group by com.CD_IDX";
		$result = wepix_query_error($query);

	
	}else{
		$page_title_text = "가격비교 등록";
		$submit_btn_text = "가격비교 등록";
	}

	$st_query = "select SD_IDX,SD_NAME from "._DB_SITE." where SD_ACTIVE = 'Y'";

	$tag_query = "select * from "._DB_TAG." where TG_ACTIVE = 'Y' and TG_CODE = 'ONAHOLE' order by TG_HEADER asc";
	$tag_result = wepix_query_error($tag_query); 
	
	while($tag_list = wepix_fetch_array($tag_result)){
		if($tag_list[TG_HEADER] == '2'){	
			$_ary_two_depth_idx[] = $tag_list[TG_IDX];
			$_ary_two_depth_name[] = $tag_list[TG_NAME];
		}elseif($tag_list[TG_HEADER] == '3'){	
			${"_ary_three_depth_".$tag_list[TG_PARENT_IDX]."_idx"}[] = $tag_list[TG_IDX];
			${"_ary_three_depth_".$tag_list[TG_PARENT_IDX]."_name"}[] = $tag_list[TG_NAME];
		}
	}





include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.table-wrap{ width:800px; }
.table-style{ width:100%; }
</STYLE>
<script type='text/javascript'>
var siteCount = 0;
var site_Data = "";
<?
$st_result = wepix_query_error($st_query); 
while($st_list = wepix_fetch_array($st_result)) {
?>
	site_Data += "<option value='<?=$st_list[SD_IDX]?>'><?=$st_list[SD_NAME]?></option>";
<? } ?>

var siteAdd=function(){
	
	siteCount++;

	var showHtml2 =""
	+"<tr id='option_tr_"+ siteCount +"'>"
	+"<td class='tds2'>"
	+"<select name='cl_site[]'>"
	+"<option value=''>Select Site</option>"
	+site_Data
	+"</select>"
	+"</td>"
	+"<input type='hidden' name='cl_idx[]' value='0'>"
	+"<td class='tds2'><input type='text' name='cl_sort[]'></td>"
	+"<td class='tds2'>"
	+"<input type='text' name='cl_price[]'  style='width:80px !important;'> "
	+"<input type='text' name='cl_delivery[]'   style='width:80px !important;'>"
	+"<input type='text' name='cl_memo[]' style='width:200px !important;'><br>"
	+"<textarea name='cl_path[]' style='margin-top:5px; height:60px; background-color:#f7f7f7; border:1px solid #999;'></textarea>"
	+"</td>"
	+"<td class='tds2'><button type=\"button\" class=\"btnstyle1 btnstyle1-danger btnstyle1-xs m-t-3 \" onclick=\"siteDel('"+siteCount+"');\"><i class=\"far fa-trash-alt\"></i></button></td>"
	+"</tr>";


	
	$("#site_table").append(showHtml2);
};

var siteDel = function(key){
    $('#option_tr_'+key).remove();
};

function sitedataDel(idx){
	alert(idx);
	$.ajax({
			type: "post",
			url : "<?=_A_PATH_COMPARISON_OK?>",
			data : { 
				a_mode : "comparisonLinkDel",
				idx : idx ,
			},
			success: function(getdata) {
				alert(getdata);
				makedata = getdata.split('|');
				ckcode = makedata[1];
				if(ckcode=="Processing_Complete"){
					location.reload();
				}else if(ckcode=="Value_null"){

				}
			}
		});
}

function goSubmit(){
	var form = document.form1;
	form.submit();
}

function changeTag(num,idx,type){
		var id = "tg_"+type+"_"+num;
		var checked_yn = $("input:checkbox[id='"+id+"']").is(":checked");
	if(checked_yn == false){

		var checked_value = $("input:checkbox[id='"+id+"']").val();

		$.ajax({
			type: "post",
			url : "<?=_A_PATH_COMPARISON_OK?>",
			data : { 
				a_mode : "TagDel",
				cd_idx : idx ,
				tg_idx : checked_value
			},
			success: function(getdata) {
				makedata = getdata.split('|');
				ckcode = makedata[1];
				if(ckcode=="Processing_Complete"){
					alert('삭제완료');

				}else if(ckcode=="Value_null"){

				}
			}
		});

	}else{

	}


}

</script>
<div id="contents_head">
	<h1><?=$page_title_text?></h1>
    <div id="head_write_btn">

	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">
		
		<div class="display-table">
			<ul class="display-table-cell">


			<div class="table-wrap">
			<form name='form1' action='/admin2/comparison/comparison_ok_test.php' method='post' enctype="multipart/form-data" autocomplete="off">
			
			<? if( $_mode == "modify" ){ ?>
				<input type="hidden" name="a_mode" value="comparisonModify">
				<input type="hidden" name="idx" value="<?=$comparison_data[CD_IDX]?>">
				<input type="hidden" name="cd_maching_code" value="<?=$comparison_data[CD_MACHING_CODE]?>">
				<input type='hidden' name="img_name" id="img_name" value="<?=$comparison_data[CD_IMG]?>" >
			<? }else{ ?>
				<input type="hidden" name="a_mode" value="comparisonNew">
			<? } ?>

				<table cellspacing="1" cellpadding="0" class="table-style">
				 
				 <tr>
					 <th class="tds1">브랜드</th>
					 <td class="tds2">
						<select name='cl_brand'>
							<option value=''>Select Brand</option>
							<?
							$brand_query = "select BD_IDX,BD_NAME from "._DB_BRAND."";
							$brand_result = wepix_query_error($brand_query); 
							while($brand_list = wepix_fetch_array($brand_result)){?>
							<option value='<?=$brand_list[BD_IDX]?>'<? if( $brand_list[BD_IDX] == $comparison_data[CD_BRAND_IDX] ) echo "selected"; ?>><?=$brand_list[BD_NAME]?></option>
							<?}?>
						</select>
					</td>
				 </tr>

			     <tr>
					 <th class="tds1">이름</th>
					 <td class="tds2"><input type='text' name='cd_name'  size='40' value="<?=$comparison_data[CD_NAME]?>" ></td>
				 </tr>
			     <tr>
					 <th class="tds1">해외 이름</th>
					 <td class="tds2"><input type='text' name='cd_name_og'  size='40' value="<?=$comparison_data[CD_NAME_OG]?>" ></td>
				 </tr>
			     <tr>
					 <th class="tds1">태그</th>
					 <td class="tds2">
						<table class="table-style">
						 <?
							for($a=0;$a<count($_ary_two_depth_idx);$a++){
						 ?>
						 <tr>
							 <th class="tds1"><?=$_ary_two_depth_name[$a]?></th>
							 <td class="tds2">
							<?
								for($i=0;$i<count(${"_ary_three_depth_".$_ary_two_depth_idx[$a]."_idx"});$i++){

								$_view_tag_idx = ${"_ary_three_depth_".$_ary_two_depth_idx[$a]."_idx"}[$i];
								$_view_tag_name = ${"_ary_three_depth_".$_ary_two_depth_idx[$a]."_name"}[$i];
							?>
								<label><input type='checkbox'  name='tg_structure[]' id='tg_structure_<?=$_view_tag_idx?>' 
								<?if(in_array($_view_tag_idx, $_ary_com_tag)){ echo "checked";}?> value='<?=$_view_tag_idx?>'
								onclick="changeTag('<?=$_view_tag_idx?>','<?=$_idx?>','structure')"><?=$_view_tag_name?> </label> 
								<?}?>
							 </td>
						 </tr>
						 <?}?>
						</table>
					 </td>
				 </tr>


			    <tr>
					 <th class="tds1">간략 내용</th>
					 <td class="tds2"><input type='text' name='cd_cont'  value="<?=$comparison_data[CD_CONT]?>"></td>
			     </tr>
				 <tr>
					 <th class="tds1">코드</th>
					 <td class="tds2"><input type='text' name='cd_code' value="<?=$comparison_data[CD_CODE]?>"></td>
			     </tr>
				 <tr>
					 <th class="tds1">카테고리</th>
					 <td class="tds2"><input type='text' name='cd_category' value="<?=$comparison_data[CD_CATEGORY]?>"></td>
			     </tr>

				 <tr>
					 <th class="tds1">이미지</th>
					 <td class="tds2">
					 <?
						if($comparison_data[CD_IMG] == ''){
							$img_path = '/test_pd_img.jpg';
						}else{
							$img_path = '../../data/comparion/'.$comparison_data[CD_IMG];
						}
					?>
					 <input type='file' name='cd_img'>
						<img src="<?=$img_path?>" style="height:50px; margin-left:20px;">
						(<?=$img_path?>)
			
					 </td>
			     </tr>
				 <tr>
					 <th class="tds1">가격</th>
					 <td class="tds2"><input type='text' name='cd_price' value="<?=$comparison_data[CD_PRICE]?>"></td>
			     </tr>

				 <tr>
					 <th class="tds1">해쉬태그</th>
					 <td class="tds2"><input type='text' name='cd_hashtag' value="<?=$comparison_data[CD_HASH_TAG]?>"></td>
			     </tr>

				 <tr>
					<th class="tds1">기본 설정</th>
					<td class="tds2">
						평점 <input type='text' style='width:50px;' name='cd_score' value="<?=$comparison_data[CD_SCORE]?>">
						리뷰수 <input type='text' style='width:50px;' name='cd_review' value="<?=$comparison_data[CD_REVIEW]?>">
						찜하기 <input type='text' style='width:50px;' name='cd_keep' value="<?=$comparison_data[CD_KEEP]?>">
						갱신일 <input type='text' style='width:120px;' name='cd_update_date' id="search_st" readonly value="<?=date("Y-m-d",$comparison_data[CD_UPDATE_DATE])?>">
					</td>
				 </tr>

				 <tr>
					 <th class="tds1">바로가기 링크</th>
					 <td class="tds2"><input type='text' name='cd_link' value="<?=$comparison_data[CD_LINK]?>"></td>
			     </tr>
				 <tr>
					 <th class="tds1">연관 상품</th>
					 <td class="tds2"><input type='text' name='cd_related_goods' value="<?=$comparison_data[CD_RELATED_GOODS]?>"></td>
			     </tr>
				 <tr>
					 <th class="tds1">추천 상품</th>
					 <td class="tds2"><input type='text' name='cd_recommend_goods' value="<?=$comparison_data[CD_RECOMMEND_GOODS]?>"></td>
			     </tr>
				 <tr>
					 <td class="tds2" colspan="2">
					 <button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" style="width:150px; margin:5px; float:right;" onclick="siteAdd()"> <i class="fas fa-plus-circle"></i> 비교 싸이트 추가</button>
						<table cellspacing="1" cellpadding="0" class="table-style" id='site_table'>
							<tr>
								<th  style='width:150px !important;'>싸이트</th>
								<th  style='width:50px !important;'>정렬 </th>
								<th>가격 / 배송비 / 부가 정보</th>
								<th  style='width:40px !important;'>삭제</th>
							</tr>
							<? if( $_mode != "modify" ){ ?>
							<tr style='margin-bottom:5px;'>
								<td >
								<select name='cl_site[]'>
									<option value=''>Select Site</option>
									<?
							
									$site_query = "select SD_IDX,SD_NAME from "._DB_SITE." where SD_ACTIVE = 'Y' ";
									$site_result = wepix_query_error($site_query); 
									while($site_list = wepix_fetch_array($site_result)){?>
									<option value='<?=$site_list[SD_IDX]?>'  <? if( $site_list[SD_IDX] == $link_list[CL_SD_IDX] ) echo "selected"; ?>><?=$site_list[SD_NAME]?></option>
									<?}?>
								</select>
								</td>
								<td class='tds2'><input type='text' name='cl_sort[]'></td>
								<td class='tds2'>
										<input type='text' name='cl_price[]' value="<?=$link_list[CL_PRICE]?>"  style='width:80px !important;'> 
										<input type='text' name='cl_delivery[]' value="<?=$link_list[CL_DELIVERY_PRICE]?>"  style='width:80px !important;'>
										<input type='text' name='cl_memo[]' value="<?=$link_list[CL_MEMO]?>"  style='width:200px !important;'><br>
										<textarea name='cl_path[]' style="margin-top:5px; height:60px; background-color:#f7f7f7; border:1px solid #999;"><?=$link_list[CL_PATH]?></textarea>
									</td>
								<td class='tds2' ><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs m-t-3" onclick="sitedataDel('<?=$link_list[CL_IDX]?>');"><i class="far fa-trash-alt"></i> </button></td>
								</tr>
							<?}else{?>

								<?
									$link_result = wepix_query_error("select * from "._DB_COMPARISON_LINK." where CL_CD_IDX = '".$_idx."'");	
									$link_num = 20;
									while($link_list = wepix_fetch_array($link_result)){
									$link_num++;	
								?>
								<tr id='option_tr_<?=$link_num?>'>
									<td >
									<select name='cl_site[]'>
										<option value=''>Select Site</option>
										<?
										$site_query = "select SD_IDX,SD_NAME from "._DB_SITE." where SD_ACTIVE = 'Y' ";
										$site_result = wepix_query_error($site_query); 
										while($site_list = wepix_fetch_array($site_result)){?>
										<option value='<?=$site_list[SD_IDX]?>' <? if( $site_list[SD_IDX] == $link_list[CL_SD_IDX] ) echo "selected"; ?>><?=$site_list[SD_NAME]?></option>
										<?}?>
									</select>
									</td>
									<input type='hidden' name='cl_idx[]' value="<?=$link_list[CL_IDX]?>">
									<td class='tds2'><input type='text' name='cl_sort[]' value="<?=$link_list[CL_SORT_NUM]?>"></td>

									<td class='tds2'>
										<input type='text' name='cl_price[]' value="<?=$link_list[CL_PRICE]?>"  style='width:80px !important;'> 
										<input type='text' name='cl_delivery[]' value="<?=$link_list[CL_DELIVERY_PRICE]?>"  style='width:80px !important;'>
										<input type='text' name='cl_memo[]' value="<?=$link_list[CL_MEMO]?>"  style='width:200px !important;'><br>
										<textarea name='cl_path[]' style="margin-top:5px; height:60px; background-color:#f7f7f7; border:1px solid #999;"><?=$link_list[CL_PATH]?></textarea>
									</td>
									<td class='tds2' ><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs m-t-3" onclick="sitedataDel('<?=$link_list[CL_IDX]?>');"><i class="far fa-trash-alt"></i> </button></td>
								</tr>

								<?}?>

							<?}?>
						</table>
					 </td>
				 </tr>
					
				</table>
				
			</form>
			</div>

				<div class="page-btn-wrap">
					<ul class="page-btn-left">
						<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_COMPARISON_LIST?>'" > 
							<i class="fas fa-arrow-left"></i>
							목록으로
						</button>
					</ul>
					<ul class="page-btn-right">
						<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="goSubmit();" > 
							<i class="far fa-check-circle"></i>
							<?=$submit_btn_text?>
						</button>
					</ul>
				</div>

			</ul>
			<ul class="display-table-cell">

				<div id="comment">
				</div>

			</ul>
		</div>


		
	</div>
</div>

<script type="text/javascript"> 
<!-- 
function showComment(pn){
	if( pn== "" ) pn ="";
	$.ajax({
		url: "ajax.comparison_comment.php",
		data: {
			"key":"<?=$_idx?>",
			"pn":pn
		},
		type: "POST",
		dataType: "html",
		success: function(getdata){
			$('#comment').html(getdata);
		},
		error: function(){
		}
	});
}
showComment();
//--> 
</script> 

<?
include "../layout/footer.php";
exit;
?>