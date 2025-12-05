<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


include "../lib/inc_common.php";
include('../../class/image.php'); //이미지 처리 클래스

	//넘어온 변수 전체 검열
	foreach($_POST as $key => $val){
		${"_".$key} = securityVal($val);
	}

/*
$_a_mode = securityVal($a_mode);
$_idx = securityVal($idx);
*/
	$_inst_img_dir = "../../data/brand_logo/";

	//이동할 폴더가 없을경우 생성한다
	if(!is_dir($_inst_img_dir)){
		@mkdir($_inst_img_dir, 0777);
		@chmod($_inst_img_dir, 0777);
	}

/*
	$_bd_name = securityVal($bd_name);
	$_bd_name_en = securityVal($bd_name_en);
	$_bd_name_group = securityVal($bd_name_group);
	$_bd_name_en_group = securityVal($bd_name_en_group);

	$_bd_code = securityVal($bd_code);
	$_bd_maker = securityVal($bd_maker);
	$_bd_kind_code = securityVal($bd_kind_code);
	$_brand_token = securityVal($brand_token);
	$_bd_introduce = securityVal($bd_introduce);
	$_bd_domain = securityVal($bd_domain);
	$_bd_active = securityVal($bd_active);
	$_bd_list_active = securityVal($bd_list_active);

	$_modify_bd_logo = securityVal($modify_bd_logo);
*/

// ******************************************************************************************************************
// 브랜드 등록
// ******************************************************************************************************************
if($_a_mode == "brandNew"){



	$_file = $_FILES['bd_logo'];

	if ( $_file['name'] ) {
		if ( !$_file['error'] ) {

			if( file_image_check($_file) == "false" ){
				msg("이미지만 등록 가능합니다.","");
				exit;
			}

			$_tmp_file = $_file['tmp_name'];
			$_file_info = @getimagesize($_tmp_file);
			$_file_size = filesize($_tmp_file);

			$_save_file_name = file_change_name($_file, $_brand_token);
			move_uploaded_file($_tmp_file, $_inst_img_dir."/".$_save_file_name);

			//리사이징

			//DB에 저장한다
			fileReg( $_inst_img_dir, $_save_file_name, "", $_brand_token, $_file_info[0], $_file_info[1], $_file_size, "brand_logo", "img" );

			$_bd_logo = $_save_file_name;
		}
	}

	$_bd_api_info_ary = array(
		'active' => $_bd_api_active ?? '',
		'name' => $_bd_api_name ?? '',
		'name_en' => $_bd_api_name_en ?? '',
		'logo' => $_bd_api_logo ?? '',
		'logo_mobile' => $_bd_api_logo_mobile ?? '',
		'bg' => $_bd_api_bg ?? '',
		'bg_rgb' => $_bd_api_bg_rgb ?? '',
		'info_class' => $_bd_api_info_class ?? '',
		'bg_mobile' => $_bd_api_bg_mobile ?? ''
	);

	$_bd_api_info = json_encode($_bd_api_info_ary, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
	//$_bd_api_info = addslashes($_bd_api_info);

	// 변수 초기화
	$_bd_kind_ona = $_bd_kind_ona ?? "N";
	$_bd_kind_breast = $_bd_kind_breast ?? "N";
	$_bd_kind_gel = $_bd_kind_gel ?? "N";
	$_bd_kind_condom = $_bd_kind_condom ?? "N";
	$_bd_kind_annal = $_bd_kind_annal ?? "N";
	$_bd_kind_prostate = $_bd_kind_prostate ?? "N";
	$_bd_kind_care = $_bd_kind_care ?? "N";
	$_bd_kind_dildo = $_bd_kind_dildo ?? "N";
	$_bd_kind_vibe = $_bd_kind_vibe ?? "N";
	$_bd_kind_suction = $_bd_kind_suction ?? "N";
	$_bd_kind_man = $_bd_kind_man ?? "N";
	$_bd_kind_nipple = $_bd_kind_nipple ?? "N";
	$_bd_kind_cos = $_bd_kind_cos ?? "N";
	$_bd_kind_perfume = $_bd_kind_perfume ?? "N";
	$_bd_kind_bdsm = $_bd_kind_bdsm ?? "N";

	if( $_bd_kind_ona !="Y" ) $_bd_kind_ona = "N";
	if( $_bd_kind_breast !="Y" ) $_bd_kind_breast = "N";
	if( $_bd_kind_gel !="Y" ) $_bd_kind_gel = "N";
	if( $_bd_kind_condom !="Y" ) $_bd_kind_condom = "N";
	if( $_bd_kind_annal !="Y" ) $_bd_kind_annal = "N";
	if( $_bd_kind_prostate !="Y" ) $_bd_kind_prostate = "N";
	if( $_bd_kind_care !="Y" ) $_bd_kind_care = "N";
	if( $_bd_kind_dildo !="Y" ) $_bd_kind_dildo = "N";
	if( $_bd_kind_vibe !="Y" ) $_bd_kind_vibe = "N";
	if( $_bd_kind_suction !="Y" ) $_bd_kind_suction = "N";
	if( $_bd_kind_man !="Y" ) $_bd_kind_man = "N";
	if( $_bd_kind_nipple !="Y" ) $_bd_kind_nipple = "N";
	if( $_bd_kind_cos !="Y" ) $_bd_kind_cos = "N";
	if( $_bd_kind_perfume !="Y" ) $_bd_kind_perfume = "N";
	if( $_bd_kind_bdsm !="Y" ) $_bd_kind_bdsm = "N";

	$_ary_bd_kind = array(
		'ona' => $_bd_kind_ona,
		'breast' => $_bd_kind_breast,
		'gel' => $_bd_kind_gel,
		'condom' => $_bd_kind_condom,
		'annal' => $_bd_kind_annal,
		'prostate' => $_bd_kind_prostate,
		'care' => $_bd_kind_care,
		'dildo' => $_bd_kind_dildo,
		'vibe' => $_bd_kind_vibe,
		'suction' => $_bd_kind_suction,
		'man' => $_bd_kind_man,
		'nipple' => $_bd_kind_nipple,
		'cos' => $_bd_kind_cos,
		'perfume' => $_bd_kind_perfume,
		'bdsm' => $_bd_kind_bdsm
	);

	$_bd_kind = json_encode($_ary_bd_kind);

	// 변수 초기화 및 타입 캐스팅
	$_bd_maker = (int)($_bd_maker ?? 0);
	$_bd_logo = $_bd_logo ?? '';

	$query = "insert into  "._DB_BRAND." set
		BD_MD_IDX ='".$_bd_maker."',
		BD_NAME = '".$_bd_name."',
		BD_NAME_EN = '".$_bd_name_en."',
		BD_NAME_GROUP = '".$_bd_name_group."',
		BD_NAME_EN_GROUP = '".$_bd_name_en_group."',
		BD_LOGO = '".$_bd_logo."',
		BD_INTRODUCE = '".$_bd_introduce."',
		BD_DOMAIN = '".$_bd_domain."',
		BD_ACTIVE ='".$_bd_active."',
		BD_LIST_ACTIVE ='".$_bd_list_active."',
		BD_CODE	 = '".$_bd_code."',
		BD_KIND_CODE  = '".$_bd_kind_code."',
		BD_TOKEN  = '".$_brand_token."',
		bd_cate_no  = '".$_bd_cate_no."',
		bd_matching_cate  = '".$_bd_matching_cate."',
		bd_matching_brand  = '".$_bd_matching_brand."',
		bd_api_info  = '".$_bd_api_info."',
		bd_api_introduce  = '".$_bd_api_introduce."',
		bd_kind  = '".$_bd_kind."' ";
	wepix_query_error($query);

	msg("등록 완료!",_A_PATH_BRAND_LIST."?mode=".$_bd_kind_code);


////////////////////////////////////////////////////////////////////////////////////////////////
// 브랜드 수정
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif($_a_mode == "brandModify"){
	
	// 기존 브랜드 데이터 조회
	$brand_data = wepix_fetch_array(wepix_query_error("SELECT * FROM "._DB_BRAND." WHERE BD_IDX = '".$_idx."'"));
	
	// 배열 검증
	if (!is_array($brand_data) || empty($brand_data)) {
		$brand_data = ['BD_LOGO' => ''];
	}
	
	// 기존 이미지 경로 초기화
	$_modify_bd_logo = $brand_data['BD_LOGO'] ?? '';
    
	$_file = $_FILES['bd_logo'];

	if ( $_file['name'] ) {
		if ( !$_file['error'] ) {

			if( file_image_check($_file) == "false" ){
				msg("이미지만 등록 가능합니다.","");
				exit;
			}

			$_tmp_file = $_file['tmp_name'];
			$_file_info = @getimagesize($_tmp_file);
			$_file_size = filesize($_tmp_file);

			$_save_file_name = file_change_name($_file, $_brand_token);
			move_uploaded_file($_tmp_file, $_inst_img_dir."/".$_save_file_name);

			//리사이징

			//DB에 저장한다
			fileReg( $_inst_img_dir, $_save_file_name, "", $_brand_token, $_file_info[0], $_file_info[1], $_file_size, "brand_logo", "img" );

			$_modify_bd_logo = $_save_file_name;
		}
	}

	$_bd_api_info_ary = array(
		'active' => $_bd_api_active ?? '',
		'name' => $_bd_api_name ?? '',
		'name_en' => $_bd_api_name_en ?? '',
		'logo' => $_bd_api_logo ?? '',
		'logo_mobile' => $_bd_api_logo_mobile ?? '',
		'bg' => $_bd_api_bg ?? '',
		'bg_rgb' => $_bd_api_bg_rgb ?? '',
		'info_class' => $_bd_api_info_class ?? '',
		'bg_mobile' => $_bd_api_bg_mobile ?? ''
	);

	$_bd_api_info = json_encode($_bd_api_info_ary, JSON_UNESCAPED_UNICODE);

	// 변수 초기화
	$_bd_kind_ona = $_bd_kind_ona ?? "N";
	$_bd_kind_breast = $_bd_kind_breast ?? "N";
	$_bd_kind_gel = $_bd_kind_gel ?? "N";
	$_bd_kind_condom = $_bd_kind_condom ?? "N";
	$_bd_kind_annal = $_bd_kind_annal ?? "N";
	$_bd_kind_prostate = $_bd_kind_prostate ?? "N";
	$_bd_kind_care = $_bd_kind_care ?? "N";
	$_bd_kind_dildo = $_bd_kind_dildo ?? "N";
	$_bd_kind_vibe = $_bd_kind_vibe ?? "N";
	$_bd_kind_suction = $_bd_kind_suction ?? "N";
	$_bd_kind_man = $_bd_kind_man ?? "N";
	$_bd_kind_nipple = $_bd_kind_nipple ?? "N";
	$_bd_kind_cos = $_bd_kind_cos ?? "N";
	$_bd_kind_perfume = $_bd_kind_perfume ?? "N";
	$_bd_kind_bdsm = $_bd_kind_bdsm ?? "N";
	
	if( $_bd_kind_ona !="Y" ) $_bd_kind_ona = "N";
	if( $_bd_kind_breast !="Y" ) $_bd_kind_breast = "N";
	if( $_bd_kind_gel !="Y" ) $_bd_kind_gel = "N";
	if( $_bd_kind_condom !="Y" ) $_bd_kind_condom = "N";
	if( $_bd_kind_annal !="Y" ) $_bd_kind_annal = "N";
	if( $_bd_kind_prostate !="Y" ) $_bd_kind_prostate = "N";
	if( $_bd_kind_care !="Y" ) $_bd_kind_care = "N";
	if( $_bd_kind_dildo !="Y" ) $_bd_kind_dildo = "N";
	if( $_bd_kind_vibe !="Y" ) $_bd_kind_vibe = "N";
	if( $_bd_kind_suction !="Y" ) $_bd_kind_suction = "N";
	if( $_bd_kind_man !="Y" ) $_bd_kind_man = "N";
	if( $_bd_kind_nipple !="Y" ) $_bd_kind_nipple = "N";
	if( $_bd_kind_cos !="Y" ) $_bd_kind_cos = "N";
	if( $_bd_kind_perfume !="Y" ) $_bd_kind_perfume = "N";
	if( $_bd_kind_bdsm !="Y" ) $_bd_kind_bdsm = "N";

	$_ary_bd_kind = array(
		'ona' => $_bd_kind_ona,
		'breast' => $_bd_kind_breast,
		'gel' => $_bd_kind_gel,
		'condom' => $_bd_kind_condom,
		'annal' => $_bd_kind_annal,
		'prostate' => $_bd_kind_prostate,
		'care' => $_bd_kind_care,
		'dildo' => $_bd_kind_dildo,
		'vibe' => $_bd_kind_vibe,
		'suction' => $_bd_kind_suction,
		'man' => $_bd_kind_man,
		'nipple' => $_bd_kind_nipple,
		'cos' => $_bd_kind_cos,
		'perfume' => $_bd_kind_perfume,
		'bdsm' => $_bd_kind_bdsm
	);

	$_bd_kind = json_encode($_ary_bd_kind);

	// 변수 초기화 및 타입 캐스팅
	$_bd_maker = (int)($_bd_maker ?? 0);

	$query = "update "._DB_BRAND." set
		BD_MD_IDX ='".$_bd_maker."',
		BD_NAME = '".$_bd_name."',
		BD_NAME_EN = '".$_bd_name_en."',
		BD_NAME_GROUP = '".$_bd_name_group."',
		BD_NAME_EN_GROUP = '".$_bd_name_en_group."',
		BD_LOGO = '".$_modify_bd_logo."',
		BD_INTRODUCE = '".$_bd_introduce."',
		BD_DOMAIN = '".$_bd_domain."',
		BD_ACTIVE ='".$_bd_active."',
		BD_LIST_ACTIVE ='".$_bd_list_active."',
		BD_CODE	 ='".$_bd_code."',
		BD_KIND_CODE  = '".$_bd_kind_code."',
		BD_TOKEN  = '".$_brand_token."',
		bd_cate_no  = '".$_bd_cate_no."',
		bd_matching_cate  = '".$_bd_matching_cate."',
		bd_matching_brand  = '".$_bd_matching_brand."',
		bd_api_info  = '".$_bd_api_info."',
		bd_api_introduce  = '".$_bd_api_introduce."',
		bd_kind  = '".$_bd_kind."'
		where BD_IDX = '".$_idx."'";
	wepix_query_error($query);

if( $_load_page == "popup_brand_view" ){
	msg("수정완료","/admin2/product2/popup.brand_view.php?idx=".$_idx);
}else{

	msg("수정완료",_A_PATH_BRAND_REG."?mode=modify&key=".$_idx);
}

// ******************************************************************************************************************
// 브랜드 삭제
// ******************************************************************************************************************
}elseif($_a_mode == "brandDel"){

	 $query = "delete from "._DB_BRAND." where BD_IDX = '".$_idx."'";

	 $result = wepix_query_error($query);
		echo "|Processing_Complete|처리완료|";
// ******************************************************************************************************************
// 제조사 등록
// ******************************************************************************************************************
}elseif($_a_mode == "makerNew"){
    
		$_md_name = securityVal($md_name);
		$_md_code = securityVal($md_code);

	 $query = "insert into  "._DB_MAKER." set
            MD_NAME = '".$_md_name."',
			MD_CODE	 ='".$_md_code."'";

	 $result = wepix_query_error($query);
	 msg("등록 완료!",_A_PATH_MAKER_LIST);

// ******************************************************************************************************************
// 제조사 수정
// ******************************************************************************************************************
}elseif($_a_mode == "makerModify"){
    
		$_md_name = securityVal($md_name);
		$_md_code = securityVal($md_code);
		
		$query = "update "._DB_MAKER." set
			MD_NAME = '".$_md_name."',
			MD_CODE	 ='".$_md_code."'
		where MD_IDX = '".$_idx."'";
	
	  wepix_query_error($query);

	 msg("수정완료",_A_PATH_MAKER_REG."?mode=modify&key=".$_idx);
// ******************************************************************************************************************
// 제조사 삭제
// ******************************************************************************************************************
}elseif($_a_mode == "makerDel"){

	 $query = "delete from "._DB_MAKER." where MD_IDX = '".$_idx."'";

	 $result = wepix_query_error($query);
	 	echo "|Processing_Complete|처리완료|";
		
// ******************************************************************************************************************
// 구조 등록
// ******************************************************************************************************************
}elseif($_a_mode == "structureNew"){

	$_st_name = securityVal($st_name);
	$_st_code = securityVal($st_code);
	$_st_haed = securityVal($st_haed);
	$_st_active = securityVal($st_active);
	$_st_memo = securityVal($st_memo);
	$_tg_code = securityVal($tg_code);
	$_st_rgb = securityVal($st_rgb);

	if($_st_haed != 1){



		//$st_dat2 = wepix_fetch_array(wepix_query_error("select TG_CODE from "._DB_TAG." where TG_PARENT_IDX = '".$_st_code."' "));
		$st_data = wepix_fetch_array(wepix_query_error("select TG_CODE, max(TG_SORT_NUM) as sort_num from "._DB_TAG." where TG_PARENT_IDX = '".$_st_code."' "));
		
		// 배열 검증
		if (!is_array($st_data) || empty($st_data)) {
			$st_data = ['sort_num' => 0, 'TG_CODE' => ''];
		}
		
		$_st_parent_idx = $_st_code;
		$_st_sort_num = ($st_data['sort_num'] ?? 0) + 1;
		$_st_code = $st_data['TG_CODE'] ?? '';

/*
		echo $st_name."/".$_st_code;
		exit;
*/
	}else{
		$_st_parent_idx = 0;
	}

	 $query = "insert into  "._DB_TAG." set
 			TG_CODE  = '".$_tg_code."' ,
			TG_NAME  = '".$_st_name."' ,
			TG_HEADER  = '".$_st_haed."' ,
			TG_PARENT_IDX = '".$_st_parent_idx."',
			TG_SORT_NUM = '".$_st_sort_num."',
			TG_MEMO = '".$_st_memo."',
			TG_RGB = '".$_st_rgb."',
			TG_ACTIVE  = '".$_st_active."'";

	 $result = wepix_query_error($query);

	 msg("등록 완료!",_A_PATH_STRUCTURE_LIST."?tg_code=".$_st_code);

// ******************************************************************************************************************
// 구조 수정
// ******************************************************************************************************************
}elseif($_a_mode == "structureModify"){

	$_st_name = securityVal($st_name);
	$_st_code = securityVal($st_code);
	$_st_haed = securityVal($st_haed);
	$_st_active = securityVal($st_active);
	$_st_memo = securityVal($st_memo);
	$_st_rgb = securityVal($st_rgb);
	
	$_st_sort_num = securityVal($st_sort_num);

	if($_st_haed != 1){
		$st_data = wepix_fetch_array(wepix_query_error("select TG_CODE, max(TG_SORT_NUM) as sort_num from "._DB_TAG." where TG_PARENT_IDX = '".$_st_code."' "));
		
		// 배열 검증
		if (!is_array($st_data) || empty($st_data)) {
			$st_data = ['TG_CODE' => ''];
		}
		
		$_st_parent_idx = $_st_code;
		$_st_code = $st_data['TG_CODE'] ?? '';
	}else{
		$_st_parent_idx = 0;
	}

		
		$query = "update "._DB_TAG." set
			TG_CODE  = '".$_st_code."' ,
			TG_NAME  = '".$_st_name."' ,
			TG_HEADER  = '".$_st_haed."' ,
			TG_PARENT_IDX = '".$_st_parent_idx."',
			TG_SORT_NUM = '".$_st_sort_num."',
			TG_MEMO = '".$_st_memo."',
			TG_RGB = '".$_st_rgb."',
			TG_ACTIVE  = '".$_st_active."'
		where TG_IDX = '".$_idx."'";
	
	  wepix_query_error($query);

	 msg("수정완료",_A_PATH_STRUCTURE_REG."?mode=modify&key=".$_idx);
		
// ******************************************************************************************************************
// 구조 삭제
// ******************************************************************************************************************
}elseif($_a_mode == "structureDel"){

	$query = "delete from "._DB_TAG." where TG_IDX = '".$_idx."'";
	wepix_query_error($query);

	$query = "delete from "._DB_COMPARISON_TAG." where CT_TG_IDX = '".$_idx."'";
	wepix_query_error($query);

	echo "|Processing_Complete|처리완료|";

// ******************************************************************************************************************
// 카테고리 신규등록
// ******************************************************************************************************************
}elseif( $_action_mode == "category_new" ){







// ******************************************************************************************************************
// 카테고리 수정
// ******************************************************************************************************************
}elseif( $_action_mode == "category_modify" ){

	$_modify_key = securityVal($modify_key);
	$_pdc_name = securityVal($pdc_name);
	$_pdc_skin = securityVal($pdc_skin);
	$_pdc_skin_mo = securityVal($pdc_skin_mo);

    $query = "update "._DB_PRODUCT_CATAGORY_TRAVEL." set 
		PDC_NAME = '".$_pdc_name."',
		PDC_SKIN = '".$_pdc_skin."',
		PDC_SKIN_MO = '".$_pdc_skin_mo."'
		where PDC_IDX = '".$_modify_key."'";
	wepix_query_error($query);

	msg("수정완료","category_list.php?key=".$_modify_key);
// ******************************************************************************************************************
// 구조 순서 수정
// ******************************************************************************************************************
}elseif($_a_mode == "changeSortStructure"){

	$_tg_idx = securityVal($tg_idx);
	$_tg_sort = securityVal($tg_sort);
	$_tg_name = securityVal($tg_name);

	// 배열 검증
	if (!is_array($_tg_idx)) {
		$_tg_idx = [];
	}
	if (!is_array($_tg_sort)) {
		$_tg_sort = [];
	}
	if (!is_array($_tg_name)) {
		$_tg_name = [];
	}
	
	for($i=0;$i<count($_tg_idx);$i++){
		$tg_idx = $_tg_idx[$i] ?? '';
		$tg_sort = $_tg_sort[$i] ?? '';
		$tg_name = $_tg_name[$i] ?? '';
		
		$query = "update "._DB_TAG." set
			  TG_SORT_NUM = '".$tg_sort."',
			  TG_NAME = '".$tg_name."'
		 where TG_IDX = '".$tg_idx."'";
		
		wepix_query_error($query);
	}
	msg("수정 완료.",_A_PATH_STRUCTURE_LIST);

// ******************************************************************************************************************
// 브랜드 순서 수정
// ******************************************************************************************************************

}elseif($_a_mode == "changeSortBrand"){

	$_sort_mode = securityVal($sort_mode);
	$_ary_bd_idx = securityVal($bd_idx);
	
	// 배열 검증
	if (!is_array($_ary_bd_idx)) {
		$_ary_bd_idx = [];
	}
	
	for($i=0;$i<count($_ary_bd_idx);$i++){
		$_sort_num = $i+1;
		$bd_idx = $_ary_bd_idx[$i] ?? '';
		
		$query = "update  "._DB_BRAND." set
					BD_SORT = '".$_sort_num."'
				  where BD_IDX = '".$bd_idx."'";
		$result = wepix_query_error($query);
	}


	msg("수정 완료.",_A_PATH_BRAND_LIST."?mode=".$_sort_mode);



}elseif($_a_mode == "newKeyWord"){

	$_keyword = securityVal($keyword);
	$key = trim($_keyword);
	$key_word_data =  wepix_fetch_array(wepix_query_error("SELECT * FROM keyword WHERE kw_word = '".$key."'"));
	
	// 배열 검증
	if (!is_array($key_word_data)) {
		$key_word_data = [];
	}
	
	if(!isset($key_word_data['kw_id'])){
		$query = "insert into keyword set kw_word = '".$key."'";
		 $result = wepix_query_error($query);
		 echo "|Processing_Complete|처리완료|";

	}else{
		 echo "|Processing_cancel|처리실패|";
	}

	 
}elseif($_a_mode == "delKeyWord"){

		$_key = securityVal($key);

	 $query = "delete from keyword where kw_id = '".$_key."'";

	 $result = wepix_query_error($query);
	 echo "|Processing_Complete|처리완료|";
}




exit;
?>
