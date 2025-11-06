<?
	include "../lib/inc_common.php";

	$_idx = securityVal($prd_idx);

	$comparison_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$_idx."' "));

	$stock_data = sql_fetch_array(sql_query_error("SELECT ps_idx, ps_rack_code FROM prd_stock WHERE ps_prd_idx = '".$comparison_data['CD_IDX']."' "));

	$_cd_size_data = json_decode($comparison_data['CD_SIZE'], true);
	$_cd_size_w = $_cd_size_data['W'];
	$_cd_size_h = $_cd_size_data['H'];
	$_cd_size_d = $_cd_size_data['D'];

	$_cd_weight_data = json_decode($comparison_data['cd_weight_fn'], true);
	$_cd_weight_1 = $_cd_weight_data['1'];
	$_cd_weight_2 = $_cd_weight_data['2'];
	$_cd_weight_3 = $_cd_weight_data['3'];

	$_cd_code_data = json_decode($comparison_data['cd_code_fn'], true);

	if( $comparison_data['CD_KIND_CODE'] ){
		$_tg_code = $comparison_data['CD_KIND_CODE'];
	}else{
		$_tg_code = "ONAHOLE";
	}

	$tag_query = "select * from "._DB_TAG." where TG_ACTIVE = 'Y' and TG_CODE = '".$_tg_code."' order by TG_HEADER asc, TG_SORT_NUM asc";
	$tag_result = wepix_query_error($tag_query); 
	while($tag_list = wepix_fetch_array($tag_result)){
		if($tag_list['TG_HEADER'] == '2'){	
			$_ary_two_depth_idx[] = $tag_list['TG_IDX'];
			$_ary_two_depth_name[] = $tag_list['TG_NAME'];
		}elseif($tag_list['TG_HEADER'] == '3'){	
			${"_ary_three_depth_".$tag_list['TG_PARENT_IDX']."_idx"}[] = $tag_list['TG_IDX'];
			${"_ary_three_depth_".$tag_list['TG_PARENT_IDX']."_name"}[] = $tag_list['TG_NAME'];
		}
	}

	$com_tag_result = wepix_query_error("select * from "._DB_COMPARISON_TAG." where CT_CD_IDX = '".$_idx."' ");
	while($com_tag_list = wepix_fetch_array($com_tag_result)){
		$_ary_com_tag[] = $com_tag_list['CT_TG_IDX'];
	}

	$brand_query = "select BD_IDX,BD_NAME from "._DB_BRAND." order by BD_NAME asc ";
	$brand_result = wepix_query_error($brand_query); 
	while($brand_list = wepix_fetch_array($brand_result)){
		$_ary_brand_key[] = $brand_list['BD_IDX'];
		$_ary_brand_name[] = $brand_list['BD_NAME'];
	}
?>
<STYLE TYPE="text/css">
.crm-req-date{ font-size:12px; float:right; }
.img_upload_wrap{ vertical-align:top; }
.img_upload_wrap ul{ width:33%; text-align:center; display:inline-block; border:1px solid #ddd; padding:10px; }
</STYLE>

<div class="crm-title">
	<h3>상품 정보</h3>
	<ul class="crm-req-date">
		등록일 : <b><?=date("y.m.d H:i",$comparison_data['CD_REG_DATE'])?></b> <? if($comparison_data['CD_UPDATE_DATE'] > 0){?> | 마지막 수정일 : <b><?=date("y.m.d H:i",$comparison_data['CD_UPDATE_DATE'])?></b><? } ?>
	</ul>
</div> 

	<form name='form1' id='form1' action='<?=_A_PATH_COMPARISON_OK?>' method='post' enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="a_mode" value="infoModifyPopup">
	<input type="hidden" name="idx" value="<?=$comparison_data['CD_IDX']?>">
	<input type="hidden" name="cd_maching_code" value="<?=$comparison_data['CD_MACHING_CODE']?>">
	<input type='hidden' name="img_name" id="img_name" value="<?=$comparison_data['CD_IMG']?>" >
	<input type='hidden' name="img_name2" id="img_name2" value="<?=$comparison_data['CD_IMG2']?>" >
	<input type='hidden' name="img_name3" id="img_name3" value="<?=$comparison_data['CD_IMG3']?>" >
	<input type='hidden' name="img_name4" id="img_name4" value="<?=$comparison_data['CD_IMG4']?>" >

<div class="crm-detail-info">
	<table class="table-style">
		
		<tr>
			<th class="tds1">상품상태</th>
			<td class="tds2">
				<label><input type="radio" name="cd_sale_state" value="Y" <? if($comparison_data['CD_SALE_STATE'] == "Y" ) echo "checked"; ?>> 판매중</label>
				<label><input type="radio" name="cd_sale_state" value="N" <? if($comparison_data['CD_SALE_STATE'] == "N" ) echo "checked"; ?>> 단종</label>
			</td>
		</tr>

		<tr>
			<th class="tds1">구분</th>
			<td class="tds2">
				<? for($t=0; $t<count($koedge_prd_kind_array); $t++){ ?>
				<label><input type="radio" name="cd_kind_code" value="<?=$koedge_prd_kind_array[$t]['code']?>" <? if($comparison_data['CD_KIND_CODE'] == $koedge_prd_kind_array[$t]['code'] ) echo "checked"; ?>> <?=$koedge_prd_kind_array[$t]['name']?></label>
				<? } ?>
				<!-- 
				<label><input type="radio" name="cd_kind_code" value="ONAHOLE" <? if($comparison_data['CD_KIND_CODE'] == "ONAHOLE" || !$comparison_data['CD_KIND_CODE'] ) echo "checked"; ?>> 오나홀</label>
				<label><input type="radio" name="cd_kind_code" value="REALDOLL" <? if($comparison_data['CD_KIND_CODE'] == "REALDOLL" ) echo "checked"; ?>> 리얼돌</label>
				<label><input type="radio" name="cd_kind_code" value="WOMAN" <? if($comparison_data['CD_KIND_CODE'] == "WOMAN" ) echo "checked"; ?>> 여성용품</label>
				<label><input type="radio" name="cd_kind_code" value="SIDE" <? if($comparison_data['CD_KIND_CODE'] == "SIDE" ) echo "checked"; ?>> 보조용품</label>
				<label><input type="radio" name="cd_kind_code" value="GEL" <? if($comparison_data['CD_KIND_CODE'] == "GEL" ) echo "checked"; ?>> 윤활젤</label>
				<label><input type="radio" name="cd_kind_code" value="CONDOM" <? if($comparison_data['CD_KIND_CODE'] == "CONDOM" ) echo "checked"; ?>> 콘돔</label>
				-->
			</td>
		</tr>

		<tr>
			 <th class="tds1">브랜드</th>
			 <td class="tds2">
				<select name='cl_brand'>
					<option value=''>Select Brand</option>
					<?
					for ($i=0; $i<count($_ary_brand_name); $i++){
					?>
					<option value='<?=$_ary_brand_key[$i]?>'<? if( $_ary_brand_key[$i] == $comparison_data['CD_BRAND_IDX'] ) echo "selected"; ?>><?=$_ary_brand_name[$i]?></option>
					<? } ?>
				</select>

				<select name='cl_brand2'>
					<option value=''>Select Brand</option>
					<?
					for ($i=0; $i<count($_ary_brand_name); $i++){
					?>
					<option value='<?=$_ary_brand_key[$i]?>'<? if( $_ary_brand_key[$i] == $comparison_data['CD_BRAND2_IDX'] ) echo "selected"; ?>><?=$_ary_brand_name[$i]?></option>
					<? } ?>
				</select>
			</td>
		 </tr>

		<?
		$_cd_pd_info = json_decode($comparison_data['CD_PD_INFO'], true);
		?>
		<tr>
			<th class="tds1">사이트 </th>
			<td class="tds2">

				<table class="">
					<tr>
						<th>노출</th>
						<td>
							<label><input type="radio" name="cd_comparison" value="Y" <? if($comparison_data['CD_COMPARISON'] == "Y" || !$comparison_data['CD_COMPARISON'] ) echo "checked"; ?>> 노출</label>
							<label><input type="radio" name="cd_comparison" value="N" <? if($comparison_data['CD_COMPARISON'] == "N" ) echo "checked"; ?>> 비노출</label>
						</td>
						<th>성인상품 여부</th>
						<td>
							<label><input type="radio" name="cd_pd_info_19n_is" value="Y" <? if( $_cd_pd_info['19n']['is'] == "Y"  || !$_cd_pd_info['19n']['is'] ) echo "checked"; ?>> 성인</label>
							<label><input type="radio" name="cd_pd_info_19n_is" value="N" <? if( $_cd_pd_info['19n']['is'] == "N" ) echo "checked"; ?>> 일반</label>
						</td>
						<th>19금 패키지</th>
						<td>
							<label><input type="radio" name="cd_pd_info_19n_package" value="Y" <? if( $_cd_pd_info['19n']['package'] == "Y" || !$_cd_pd_info['19n']['package'] ) echo "checked"; ?>> 19금 패키지</label>
							<label><input type="radio" name="cd_pd_info_19n_package" value="N" <? if( $_cd_pd_info['19n']['package'] == "N" ) echo "checked"; ?>> 노멀 패키지</label>
						</td>
					</tr>
				</table>

				W.P.G (won per gram) 쑈당몰 판매가/상품중량
				<? 
				if( $_cd_weight_1 && $comparison_data['cd_sale_price'] ){
					$_wpg = $comparison_data['cd_sale_price']/$_cd_weight_1;
				?>
					= <?=$_wpg?>
				<? }else{ ?>
					판매가 또는 중량 정보가 없음
				<? }?>
			</td>
		</tr>

		<tr>
			<th class="tds1">상품명</th>
			<td class="tds2"><input type='text' name='cd_name'  size='40' value="<?=$comparison_data['CD_NAME']?>" ></td>
		</tr>
		<tr>
			<th class="tds1">해외 상품명</th>
			<td class="tds2"><input type='text' name='cd_name_og'  size='40' value="<?=$comparison_data['CD_NAME_OG']?>" ></td>
		</tr>
		<tr>
			<th class="tds1">영문명</th>
			<td class="tds2"><input type='text' name='cd_name_en'  size='40' value="<?=$comparison_data['CD_NAME_EN']?>" ></td>
		</tr>
		<tr>
			 <th class="tds1">간략 내용</th>
			 <td class="tds2"><input type='text' name='cd_cont'  value="<?=$comparison_data['CD_CONT']?>"></td>
		 </tr>
		<tr>
			<th class="tds1">메모</th>
			<td class="tds2"><input type='text' name='cd_memo'  value="<?=$comparison_data['CD_MEMO']?>"></td>
		</tr>

		<tr>
			<td colspan="2" style="background-color:#dddddd; border:none !important; padding: 0 !important; height:10px;"></td>
		</tr>

		<tr>
			<th class="tds1">쑈당몰 판매가</th>
			<td class="tds2"><input type='text' name='cd_sale_price' value="<?=number_format($comparison_data['cd_sale_price'])?>" onkeyUP="javascript:is_onlynumeric( this.value, this ); this.value=Comma_int(this.value, this);" style='width:100px;' >원</td>
		</tr>

		<tr>
			<th class="tds1">재고</th>
			<td class="tds2">
				
				<input type="hidden" name="ps_idx" value="<?=$stock_data['ps_idx']?>">
				<table class="">
					<tr>
						<th class="text-center" style="width:100px">재고코드</th>
						<td>
							<b><?=$stock_data['ps_idx']?></b>
						</td>
						<th class="text-center" style="width:100px">랙 코드</th>
						<td>
							<input type='text' name='ps_rack_code' style='width:100px;' value="<?=$stock_data['ps_rack_code']?>">
						</td>
					</tr>
				</table>

			</td>
		</tr>

		<tr>
			<th class="tds1">상품 코드</th>
			<td class="tds2">
				바코드 (JAN) : <input type='text' name='cd_code' style='width:200px;' value="<?=$comparison_data['CD_CODE']?>">
				상품 품번 : <input type='text' name='cd_code2' style='width:100px;' value="<?=$comparison_data['CD_CODE2']?>">
			</td>
		</tr>

<tr>
	<th class="tds1">주문 코드</th>
	<td class="tds2">
		<div>
			N.P.G : <input type='text' name='cd_code_npg' style='width:80px;'  value="<?=$_cd_code_data['npg']?>">		
			라이드재팬 : <input type='text' name='cd_code_rj' style='width:80px;'  value="<?=$_cd_code_data['rj']?>">		
			매직아이즈 : <input type='text' name='cd_code_mg' style='width:80px;'  value="<?=$_cd_code_data['mg']?>">		
		</div>
		<div class="m-t-5">
			핫파워즈 : <input type='text' name='cd_code_hp' style='width:80px;'  value="<?=$_cd_code_data['hp']?>">
			데몬킹 : <input type='text' name='cd_code_dmw' style='width:80px;'  value="<?=$_cd_code_data['dmw']?>">		
			TIS : <input type='text' name='cd_code_tis' style='width:80px;'  value="<?=$_cd_code_data['tis']?>">	
		</div>
		<div class="admin-guide-text">
			- 타마토이즈 상품일경우 상품 품번만 넣어줘도 됨
		</div>
	</td>
</tr>

<!-- 
		<tr>
			<th class="tds1">JAN 코드</th>
			<td class="tds2">
				JAN 코드 : <input type='text' name='cd_code' value="<?=$comparison_data['CD_CODE']?>" style='width:150px;'>
				주문 코드2 (NPG용) : <input type='text' name='cd_code3' value="<?=$comparison_data['CD_CODE3']?>" style='width:150px;'>
				주문 코드 : <input type='text' name='cd_code2' value="<?=$comparison_data['CD_CODE2']?>" style='width:150px;'>
			</td>
		</tr>
 -->

		<tr>
			<th class="tds1">주문 메모</th>
			<td class="tds2"><input type='text' name='cd_memo2' value="<?=$comparison_data['CD_MEMO2']?>"></td>
		</tr>

		<tr>
			<th class="tds1">수입국가</th>
			<td class="tds2">
				<input type="radio" name="cd_national" value="jp" <? if( $comparison_data['cd_national'] == "jp" ) echo "checked"; ?>> 일본
				<input type="radio" name="cd_national" value="cn" <? if( $comparison_data['cd_national'] == "cn" ) echo "checked"; ?>> 중국
				<input type="radio" name="cd_national" value="kr" <? if( $comparison_data['cd_national'] == "kr" ) echo "checked"; ?>>  한국
			</td>
		</tr>
		<tr>
			<th class="tds1">인보이스 이름1 (일어)</th>
			<td class="tds2"><input type='text' name='cd_inv_name1' value="<?=$comparison_data['CD_INV_NAME1']?>"></td>
		</tr>
		<tr>
			<th class="tds1">인보이스 이름2 (영어)</th>
			<td class="tds2"><input type='text' name='cd_inv_name2' value="<?=$comparison_data['CD_INV_NAME2']?>"></td>
		</tr>
		<tr>
			<th class="tds1">인보이스 소재</th>
			<td class="tds2"><input type='text' name='cd_inv_material' value="<?=$comparison_data['CD_INV_MATERIAL']?>" style='width:250px;'></td>
		</tr>
		<tr>
			<th class="tds1">원산지</th>
			<td class="tds2"><input type='text' name='cd_coo' value="<?=$comparison_data['CD_COO']?>" style='width:250px;'></td>
		</tr>
		<tr>
			<td colspan="2" style="background-color:#dddddd; border:none !important; padding: 0 !important; height:10px;"></td>
		</tr>

		<tr>
			<th class="tds1">검색어</th>
			<td class="tds2"><input type='text' name='cd_search_term' value="<?=$comparison_data['CD_SEARCH_TERM']?>" ></td>
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
			<td colspan="2" style="background-color:#dddddd; border:none !important; padding: 0 !important; height:10px;"></td>
		</tr>

		<tr>
			<th class="tds1">이미지</th>
			<td class="tds2">
<?
if($comparison_data['CD_IMG'] ){
	$img_path = '../../data/comparion/'.$comparison_data['CD_IMG'];
}
if($comparison_data['CD_IMG2'] ){
	$img_path2 = '../../data/comparion/'.$comparison_data['CD_IMG2'];
}
if($comparison_data['CD_IMG3'] ){
	$img_path3 = '../../data/comparion/'.$comparison_data['CD_IMG3'];
}

?>
				<div class="img_upload_wrap">
					<ul>
						기본 이미지 302 x 302(px)<br>
						<img src="<?=$img_path?>" style="height:100px; margin-left:20px; border:1px solid #eee !important;"><br>
						( <?=$comparison_data['CD_IMG']?> )<br>
						<input type='file' name='cd_img'><br>
						<!-- 
						<label><input type="checkbox" name="package_change" value="Y"> 패키지 변경</label>
						-->
						<input type='text' name='out_img'  value="" placeholder="URL로 저장"><br>
					</ul>
					<ul>
						아이콘 이미지 100 x 100(px)<br>
						<img src="<?=$img_path2?>" style="height:100px; margin-left:20px; border:1px solid #eee !important;"><br>
						( <?=$comparison_data['CD_IMG2']?> )<br>
						<input type='file' name='cd_img2'>
					</ul>
					<ul>
						인보이스 이미지<br>
						<img src="<?=$img_path3?>" style="height:100px; margin-left:20px; border:1px solid #eee !important;"><br>
						( <?=$comparison_data['CD_IMG3']?> )<br>
						<input type='file' name='cd_img3'>
					</ul>
				</div>

			</td>
		</tr>

<?
if( $comparison_data['CD_IMG4'] ){
	$img_path4 = '../../data/comparion/'.$comparison_data['CD_IMG4'];
?>
		<tr>
			<th class="tds1">패키지<br>변경전<br>이미지</th>
			<td class="tds2">
				<img src="<?=$img_path4?>" style="height:100px; margin-left:20px; border:1px solid #eee !important;">
			</td>
		</tr>
<? } ?>

<!-- 
		<tr>
			 <th class="tds1">기본 외부이미지 URL</th>
			 <td class="tds2"><input type='text' name='out_img'  value=""></td>
		 </tr>
 -->
		<tr>
			<th class="tds1">출시일</th>
			<td class="tds2">
				<div class="calendar-input">
					<input type='text' name='cd_release_date'  value="<?=$comparison_data['CD_RELEASE_DATE']?>" >
				</div>
			</td>
		</tr>

		<tr>
			<th class="tds1">패키지 사이즈</th>
			<td class="tds2">

<? 
if(is_array($_cd_size_data) != 1) { 
	if( $comparison_data['CD_SIZE'] ){
		$_modify_cd_size = preg_replace("/\s+/","",$comparison_data['CD_SIZE']);
		$_modify_cd_size = preg_replace("/mm+/","",$_modify_cd_size);
		$_modify_cd_size = preg_replace("/X+/","x",$_modify_cd_size);
		$_modify_cd_size_array = explode("x", $_modify_cd_size);
		if( count($_modify_cd_size_array) == 3 ){
			$_cd_size_w = $_modify_cd_size_array[0];
			$_cd_size_h = $_modify_cd_size_array[1];
			$_cd_size_d = $_modify_cd_size_array[2];
		}
	}
?>
	<div style="color:#ff0000; line-height:140%;">
		※ 패키지 사이즈가 정확하지 않습니다.
		<? if( $comparison_data['CD_SIZE'] ){ ?>
			<br>등록된 데이터 : <b><?=$comparison_data['CD_SIZE']?></b><br>
			등록된 데이터를 토대로 값을 입력해뒀습니다. 확인 후 이상이 없다면 수정을 눌러주세요<br><br>
		<? } ?>
	</div>
<? } ?>

				세로(H) : <input type='text' name='cd_size_h' value="<?=$_cd_size_h?>" style="width:60px">
				가로(W) : <input type='text' name='cd_size_w' value="<?=$_cd_size_w?>" style="width:60px">
				깊이(D) : <input type='text' name='cd_size_d' value="<?=$_cd_size_d?>" style="width:60px">
				<div class="admin-guide-text">
					- 단위 mm (숫자만 등록할것)
				</div>

			</td>
		</tr>

		<tr>
			<th class="tds1">내부길이</th>
			<td class="tds2">
				<input type='text' name='cd_size2' style='width:100px;'  value="<?=$comparison_data['CD_SIZE2']?>"> ( Cm )
				<br>※ 젤일때는 용량( ml )
			</td>
		</tr>
<!-- 
		<tr>
			<th class="tds1">중량</th>
			<td class="tds2">
				상품중량 : <input type='text' name='cd_weight' style='width:70px;'  value="<?=$comparison_data['CD_WEIGHT']?>"> ( g ) &nbsp;&nbsp;&nbsp;
				전체중량 : <input type='text' name='cd_weight2' style='width:70px;'  value="<?=$comparison_data['CD_WEIGHT2']?>"> ( g ) &nbsp;&nbsp;&nbsp;
				<b>실측중량</b> : <input type='text' name='cd_weight3' style='width:70px;'  value="<?=$comparison_data['CD_WEIGHT3']?>"> ( g ) 
			</td>
		</tr> 
-->

<tr>
	<th class="tds1">중량</th>
	<td class="tds2">
		상품중량 : <input type='text' name='cd_weight_1' style='width:80px;'  value="<?=$_cd_weight_1?>">
		전체중량 : <input type='text' name='cd_weight_2' style='width:80px;'  value="<?=$_cd_weight_2?>">
		실측중량 : <input type='text' name='cd_weight_3' style='width:80px;'  value="<?=$_cd_weight_3?>"> 
		<div class="admin-guide-text">
			- 단위 g (숫자만 등록할것)
		</div>
	</td>
</tr>

	</table>
</div> 

	</form> 

<style type="text/css">
.button-wrap-back{ height:60px; }
.button-wrap{ width:calc(100% - 205px); height:60px; line-height:60px; text-align:center; background:rgba(0,0,0,.4); border-top:1px solid #000; position:fixed; bottom:0; right:0;  }
</style>
<div class="button-wrap-back">
</div>
<div class="button-wrap">
	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-sm btnstyle1-submit" onclick="goSave();" > 
		<i class="far fa-check-circle"></i> 수정
	</button>
</div>

<script type="text/javascript"> 
<!-- 
function goSave(){
	$("#form1").submit();
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

	if( $(".calendar-input input").length ){
		$(".calendar-input input").datepicker(clareCalendar);
	}
//--> 
</script>