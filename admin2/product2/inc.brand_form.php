<STYLE TYPE="text/css">
.table-wrap{ width:800px; }
.table-style{ width:100%; }
.filsu{ color:#ff0000; }
</style>
	 
	 <div class="table-wrap">
		<form name='form1' action='<?=_A_PATH_PD_OK?>' method='post' enctype="multipart/form-data" autocomplete="off">

		<? if( $_mode == "modify" ){ ?>
			<input type="hidden" name="a_mode" value="brandModify">
			<input type="hidden" name="idx" value="<?=$brand_data['BD_IDX']?>">
			<input type="hidden" name="modify_bd_logo" value="<?=$brand_data['BD_LOGO']?>">
		<? }else{ ?>
			<input type="hidden" name="a_mode" value="brandNew">
		<? } ?>
		
		<input type="hidden" name="load_page" value="<?=$pageName?>">
		<input type="hidden" name="brand_token" value="<?=$_brand_token?>">

			<table cellspacing="1" cellpadding="0" class="table-style">
<? /*		
			 <tr>
				 <th class="tds1">제조사</th>
				 <td class="tds2">
					<select name='bd_maker'>
						<option value=''>Select Maker</option>
						<?
						$maker_query = "select MD_IDX,MD_NAME from "._DB_MAKER." ";
						$maker_result = wepix_query_error($maker_query); 
						while($maker_list = wepix_fetch_array($maker_result)){?>
						<option value='<?=$maker_list['MD_IDX']?>'<? if( $maker_list['MD_IDX'] == $brand_data['BD_MD_IDX'] ) echo "selected"; ?>><?=$maker_list['MD_NAME']?></option>
						<?}?>
					</select>
				</td>
			 </tr>
*/ ?>
			 <tr>
				 <th class="tds1">이름(국문)<br><b class="filsu">*필수</b></th>
				 <td class="tds2"><input type='text' name='bd_name' id='alliance_shop_name' size='40' value="<?=$brand_data['BD_NAME']?>" ></td>
			 </tr>
			 <tr>
				 <th class="tds1">이름(영문)<br><b class="filsu">*필수</b></th>
				 <td class="tds2"><input type='text' name='bd_name_en' id='alliance_shop_name' size='40' value="<?=$brand_data['BD_NAME_EN']?>" ></td>
			 </tr>
			 <tr>
				 <th class="tds1">이름 그룹<br><b class="filsu">*필수</b></th>
				 <td class="tds2">
					<div>
						한글초성 : <input type='text' name='bd_name_group' id='alliance_shop_name' style="width:40px;" value="<?=$brand_data['BD_NAME_GROUP']?>" >
						ex ) ㄱ,ㄴ,ㄷ
					</div>
					<div class="m-t-5">
						알파벳 초성 : <input type='text' name='bd_name_en_group' id='alliance_shop_name' style="width:40px;" value="<?=$brand_data['BD_NAME_EN_GROUP']?>" >
						ex ) A,B,C 대문자
					</div>
				 </td>
			 </tr>
			<tr>
				<th class="tds1">활성</th>
				<td class="tds2">
					<input type="radio" name='bd_active' value="N" <? if( $site_data['BD_ACTIVE']=="N") echo "checked"; ?>> 비활성
					<input type="radio" name='bd_active' value="Y" <? if( $site_data['BD_ACTIVE']=="Y" OR $site_data['BD_ACTIVE'] =="" ) echo "checked"; ?>> 활성
				</td>
			</tr>
			<tr>
				<th class="tds1">리스트 노출</th>
				<td class="tds2">
					<input type="radio" name='bd_list_active' value="N" <? if( $site_data['BD_LIST_ACTIVE']=="N") echo "checked"; ?>> 비활성
					<input type="radio" name='bd_list_active' value="Y" <? if( $site_data['BD_LIST_ACTIVE']=="Y" OR $site_data['BD_LIST_ACTIVE'] =="" ) echo "checked"; ?>> 활성
				</td>
			</tr>
			 <tr>
				<th class="tds1">로고</th>
				<td class="tds2">
					<input type="file" id="bd_logo" name="bd_logo" >
					<? if( $brand_data['BD_LOGO'] ){ ?>
					<div>
						<img src="../../data/brand_logo/<?=$brand_data['BD_LOGO']?>" alt="">
					</div>
					<? } ?>
				</td>
			 </tr>
			<tr>
				<th class="tds1">홈페이지</th>
				<td class="tds2"><input type='text' name='bd_domain' value="<?=$brand_data['BD_DOMAIN']?>"></td>
			</tr>
			<tr>
				 <th class="tds1">간략소개</th>
				 <td class="tds2"><input type='text' name='bd_introduce' value="<?=$brand_data['BD_INTRODUCE']?>"></td>
			 </tr>
			 <tr>
				 <th class="tds1">COODE</th>
				 <td class="tds2"><input type='text' name='bd_code' value="<?=$brand_data['BD_CODE']?>"></td>
			 </tr>
			 <tr>
				 <th class="tds1">구분코드</th>
				 <td class="tds2"><input type='text' name='bd_kind_code' value="<?=$_bd_kind_code?>"></td>
			 </tr>

<?
$_bd_kind = json_decode($brand_data['bd_kind'], true);
?>
			 <tr>
				 <th class="tds1">브랜드 카테고리<br><b class="filsu">*필수</b></th>
				 <td class="tds2">
					<label><input type="checkbox" name="bd_kind_ona" value="Y" <? if( $_bd_kind['ona'] == "Y" ) echo "checked"; ?>>오나홀</label>
					<label><input type="checkbox" name="bd_kind_breast" value="Y" <? if( $_bd_kind['breast'] == "Y" ) echo "checked"; ?>>가슴형</label>
					<label><input type="checkbox" name="bd_kind_gel" value="Y" <? if( $_bd_kind['gel'] == "Y" ) echo "checked"; ?>>윤활제</label>
					<label><input type="checkbox" name="bd_kind_condom" value="Y" <? if( $_bd_kind['condom'] == "Y" ) echo "checked"; ?>>콘돔</label>
					<label><input type="checkbox" name="bd_kind_annal" value="Y" <? if( $_bd_kind['annal'] == "Y" ) echo "checked"; ?>>애널용품</label>
					<label><input type="checkbox" name="bd_kind_prostate" value="Y" <? if( $_bd_kind['prostate'] == "Y" ) echo "checked"; ?>>전립선자극</label>
					<label><input type="checkbox" name="bd_kind_care" value="Y" <? if( $_bd_kind['care'] == "Y" ) echo "checked"; ?>>관리/보조</label>
					<label><input type="checkbox" name="bd_kind_dildo" value="Y" <? if( $_bd_kind['dildo'] == "Y" ) echo "checked"; ?>>딜도</label>
					<label><input type="checkbox" name="bd_kind_vibe" value="Y" <? if( $_bd_kind['vibe'] == "Y" ) echo "checked"; ?>>바이브</label>
					<label><input type="checkbox" name="bd_kind_suction" value="Y" <? if( $_bd_kind['suction'] == "Y" ) echo "checked"; ?>>흡입토이</label>
					<label><input type="checkbox" name="bd_kind_man" value="Y" <? if( $_bd_kind['man'] == "Y" ) echo "checked"; ?>>남성보조</label>
					<label><input type="checkbox" name="bd_kind_nipple" value="Y" <? if( $_bd_kind['nipple'] == "Y" ) echo "checked"; ?>>니플/유두</label>
					<label><input type="checkbox" name="bd_kind_cos" value="Y" <? if( $_bd_kind['cos'] == "Y" ) echo "checked"; ?>>코스튬/속옷</label>
					<label><input type="checkbox" name="bd_kind_perfume" value="Y" <? if( $_bd_kind['perfume'] == "Y" ) echo "checked"; ?>>향수/목욕</label>
					<label><input type="checkbox" name="bd_kind_bdsm" value="Y" <? if( $_bd_kind['bdsm'] == "Y" ) echo "checked"; ?>>BDSM</label>
				 </td>
			 </tr>
		
<?
$_bd_api_info = json_decode($brand_data['bd_api_info'], true);
/*
	echo "<pre>";
	print_r($_bd_api_info);
	echo "</pre>";
*/
?>

<tr>
	<th class="tds1">쑈당몰 디스플레이</th>
	<td class="tds2">

		<table cellspacing="1" cellpadding="0" class="table-style">
			<tr>
				<th class="tds1">사용여부</th>
				<td class="tds2">
					<label><input type="radio" name="bd_api_active" value="Y" <? if( $_bd_api_info['active'] == "Y" ) echo "checked"; ?>>적용</label>
					<label><input type="radio" name="bd_api_active" value="N" <? if( !$_bd_api_info['active'] || $_bd_api_info['active'] == "N" ) echo "checked"; ?>>비적용</label>
				</td>
			</tr>

			<tr>
				<th class="tds1">[카페24] 매칭 cate_no<br><b class="filsu">*필수</b></th>
				<td class="tds2">
					<input type='text' name='bd_cate_no' value="<?=$brand_data['bd_cate_no']?>" style="width:100px">
					<div class="admin-guide">
						- 카페24 카테고리 넘버
					</div>
				</td>
			</tr>

			<tr>
				<th class="tds1">[고도몰] 매칭<br><b class="filsu">*필수</b></th>
				<td class="tds2">

					<table cellspacing="1" cellpadding="0" class="table-style">
						<tr>
							<th class="tds1">카테고리 코드</th>
							<td class="tds2">
								<input type='text' name='bd_matching_cate' value="<?=$brand_data['bd_matching_cate']?>" style="width:100px">
							</td>
						</tr>
						<tr>
							<th class="tds1">브랜드 코드</th>
							<td class="tds2">
								<input type='text' name='bd_matching_brand' value="<?=$brand_data['bd_matching_brand']?>" style="width:100px">
							</td>
						</tr>
					</table>

				</td>
			</tr>


			<tr>
				<th class="tds1">노출 브랜드명 (국문)<br><b class="filsu">*필수</b></th>
				<td class="tds2">
					<input type='text' name='bd_api_name' value="<?=$_bd_api_info['name']?>">
				</td>
			</tr>
			<tr>
				<th class="tds1">노출 브랜드명 (영문)<br><b class="filsu">*필수</b></th>
				<td class="tds2">
					<input type='text' name='bd_api_name_en' value="<?=$_bd_api_info['name_en']?>">
					<br><b class="filsu">' 따옴표 넣으면 에러남 ㅠㅠ 일단 넣지말길</b>
				</td>
			</tr>
			<tr>
				<th class="tds1">logo 파일위치<br><b class="filsu">*필수</b></th>
				<td class="tds2">
					<?
					if( $_bd_api_info['logo'] ){
					?>
					<div><img src="https://showdang.co.kr/data/<?=$_bd_api_info['logo']?>" style="width:150px"></div>
					<? } ?>
					<input type='text' name='bd_api_logo' value="<?=$_bd_api_info['logo']?>">
					<div class="admin-guide">
						- 사이즈 300 x 300<br>
						- 디렉토리 : /dg_image/brand_image/<br>
						- ex) 파일명이 brand.jpg 일경우 => /dg_image/brand_image/brand.jpg
					</div>
				</td>
			</tr>
			<tr>
				<th class="tds1">배경 이미지 PC</th>
				<td class="tds2">
					<?
					if( $_bd_api_info['bg'] ){
					?>
					<div style="background-color:<?=$_bd_api_info['bg_rgb']?>"><img src="https://showdang.kr/<?=$_bd_api_info['bg']?>" style="width:500px"></div>
					<? } ?>
					<input type='text' name='bd_api_bg' value="<?=$_bd_api_info['bg']?>">
					<div class="admin-guide">
						- 사이즈 1310 x 260<br>
						- 디렉토리 : /dg_image/brand_image/<br>
						- ex) 파일명이 brand_bg.jpg 일경우 => /dg_image/brand_image/brand_bg.jpg
					</div>
				</td>
			</tr>
			<tr>
				<th class="tds1">info_class PC</th>
				<td class="tds2">
					<input type='text' name='bd_api_info_class' value="<?=$_bd_api_info['info_class']?>" style="width:100px">
					<div class="admin-guide">
						- 별도로 클래스 줘서 디자인 다르게 해야할 경우 ( 건들지 말것 )
					</div>
				</td>
			</tr>
			<tr>
				<th class="tds1">배경 컬러 PC</th>
				<td class="tds2">
					<input type='text' name='bd_api_bg_rgb' value="<?=$_bd_api_info['bg_rgb']?>" style="width:100px">
					<div class="admin-guide">
						- #포함한 RGB코드
					</div>
				</td>
			</tr>
			<tr>
				<th class="tds1">모바일 top 로고</th>
				<td class="tds2">

					<?
					if( $_bd_api_info['logo_mobile'] ){
					?>
					<div style="background-color:#111; padding:10px;"><img src="https://showdang.kr/<?=$_bd_api_info['logo_mobile']?>"></div>
					<? } ?>

					<input type='text' name='bd_api_logo_mobile' value="<?=$_bd_api_info['logo_mobile']?>">
					<div class="admin-guide">
						- 사이즈 세로 사이즈 50px / 흰색, 투명<br>
						- 디렉토리 : /dg_image/brand_image/<br>
						- ex) 파일명이 brand_mobile_logo.png 일경우 => /dg_image/brand_image/brand_mobile_logo.png
					</div>
				</td>
			</tr>
			<tr>
				<th class="tds1">배경 이미지 모바일</th>
				<td class="tds2">

					<?
					if( $_bd_api_info['bg_mobile'] ){
					?>
					<div style="background-color:<?=$_bd_api_info['bg_rgb']?>"><img src="https://showdang.kr/<?=$_bd_api_info['bg_mobile']?>" style="width:500px"></div>
					<? } ?>

					<input type='text' name='bd_api_bg_mobile' value="<?=$_bd_api_info['bg_mobile']?>">
					<div class="admin-guide">
						- 사이즈 800 x 490<br>
						- 디렉토리 : /dg_image/brand_image/<br>
						- ex) 파일명이 brand_bg_mobile.jpg 일경우 => /dg_image/brand_image/brand_bg_mobile.jpg
					</div>
				</td>
			</tr>
			<tr>
				<th class="tds1">브랜드 소개<br><b class="filsu">*필수</b></th>
				<td class="tds2">
					<textarea name="bd_api_introduce" rows="" cols=""><?=$brand_data['bd_api_introduce']?></textarea>
					<div class="admin-guide">
						- 줄바꿈시 &#60;br&#62; 사용
					</div>
				</td>
			</tr>
		</table>

	</td>
</tr>

		</table>
				
			</form>
			<div class="page-btn-wrap">
				<ul class="page-btn-left">
					<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_BRAND_LIST?>'" > 
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
		</div>

<script language=javascript>

function goSubmit(){
	var form = document.form1;
	form.submit();
}

</script>