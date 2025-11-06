<?
include "../lib/inc_common.php";
include ('../../class/image.php');
	$_action_mode = securityVal($a_mode);

// ******************************************************************************************************************
// 지역 신규등록
// ******************************************************************************************************************
if( $_action_mode == "area_new" ){

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
// 메인쇼 신규등록
// ******************************************************************************************************************
}elseif( $_action_mode == "main_show_new" ){

	$_mps_title = securityVal($mps_title);
	$_mps_code = securityVal($mps_code);
	$_mps_pd_idx = securityVal($mps_pd_idx);
	$_mps_array = implode("/", $_mps_pd_idx);

	$check_data = wepix_fetch_array(wepix_query_error("select MPS_IDX from "._DB_MAIN_PRODUCT_SHOW." where MPS_CODE = '".$_mps_code."' "));
	if($_mps_pd_idx[0] == ''){
		msg("진열 상품수를 최소 한개이상 등록해주세요.", "/admin2/product2/brand_group_list.php");
	}
	if( $check_data[MPS_IDX] ){
		msg("코드는 중복 불가 입니다.", "/admin2/product2/brand_group_list.php");
	}

	$query = "insert "._DB_MAIN_PRODUCT_SHOW." set
		MPS_CODE = '".$_mps_code."',
		MPS_TITLE = '".$_mps_title."',
		MPS_ARRAY = '".$_mps_array."' ";
    wepix_query_error($query);

	msg("","/admin2/product2/brand_group_list.php");

// ******************************************************************************************************************
// 메인쇼 수정
// ******************************************************************************************************************
}elseif( $_action_mode == "main_show_modify" ){

	$_modify_key = securityVal($modify_key);
	$_mps_title = securityVal($mps_title);
	$_mps_code = securityVal($mps_code);
	$_mps_pd_idx = securityVal($mps_pd_idx);
	$_mps_array = implode("/", $_mps_pd_idx);

    $query = "update "._DB_MAIN_PRODUCT_SHOW." set 
		MPS_TITLE = '".$_mps_title."',
		MPS_ARRAY = '".$_mps_array."'
		where MPS_IDX = '".$_modify_key."' ";
	wepix_query_error($query);

	msg("","/admin2/product2/brand_group_list.php?mps_code=".$_mps_code);

}else if($_action_mode=="product_modify"){
// ******************************************************************************************************************
// 상품 수정
// ******************************************************************************************************************
   $pd_system_code_count = wepix_counter($db_t_PRODUCT_DB, "where PD_SYSTEM_CODE = '".$pd_system_code."'");
    
	if( $pd_system_code_count > 0 ){
		$random = rand(100,999);
		$pd_system_code = date("ymdHis").$random;
	}

	$_mokey = securityVal($mokey);

    $_pd_name = securityVal($pd_name);
    $_pd_name_eg =securityVal($pd_name_eg );
    $_pd_area =securityVal($pd_area);
    $_pd_use_yn =securityVal($pd_use_yn);
    $_pdKind =securityVal($pdKind);
    $_pd_kind2 =securityVal($pd_kind2);
    $_pd_kind3 =securityVal($pd_kind3);
	$_product_ctagory =securityVal($pd_ctagory);
    $_pd_catagory2 = securityVal($pd_catagory2);
    $_product_pdc_id = securityVal($pd_id);
    $_pd_cont =$pd_cont;

    $_pd_view =securityVal($pd_view);
	$_pd_cost_rate =securityVal($pd_cost_rate);
    $_pd_price_o = preg_replace("/[^0-9]/", "",securityVal($pd_price_o));
    $_pd_price_vm = preg_replace("/[^0-9]/", "",securityVal($pd_price_vm));
    $_pd_price_vm_child = preg_replace("/[^0-9]/", "",securityVal($pd_price_vm_child));
    $_pd_price_free = preg_replace("/[^0-9]/", "",securityVal($pd_price_free));
    $_pd_price_free_child = preg_replace("/[^0-9]/", "",securityVal($pd_price_free_child));
    $_pd_cost_price = preg_replace("/[^0-9]/", "",securityVal($pd_cost_price));
    $_pd_cost_price_child = preg_replace("/[^0-9]/", "",$pd_cost_price_child);
    $_pd_option_yn =  securityVal($pd_option_yn);
    
	
    $_option_name_array = securityVal($option_name);
    $_option_price_array = securityVal($option_price);
    $_option_price_child_array =securityVal($option_price_child);
    $_option_price_free_array =securityVal($option_price_free);
    $_option_price_free_child_array =securityVal($option_price_free_child);
    $_option_price_cost_array = securityVal($option_price_cost);
    $_option_price_cost_child_array = securityVal($option_price_cost_child);
    $_option_cont_array = $option_cont;
    $_option_view_yn_array =securityVal($option_view_yn);

    $_pd_inclusion = $pd_inclusion;
    $_pd_tour_story = $pd_tour_story;
    $_pd_detail_cont = $pd_detail_cont;
    $_pd_directions = $pd_directions;
    $_pd_x_gps = securityVal($pd_x_gps);
    $_pd_y_gps =securityVal($pd_y_gps);
    $_pd_video_url = securityVal($pd_video_url);
    $_pd_return_policy =$pd_return_policy;

    $_option_name= implode("│",$_option_name_array);
    $_option_price= implode("│",preg_replace("/[^0-9]/", "",$_option_price_array));
    $_option_price_child= implode("│",preg_replace("/[^0-9]/", "",$_option_price_child_array));
    $_option_price_free= implode("│",preg_replace("/[^0-9]/", "",$_option_price_free_array));
    $_option_price_free_child= implode("│",preg_replace("/[^0-9]/", "",$_option_price_free_child_array));
    $_option_price_cost= implode("│",preg_replace("/[^0-9]/", "",$_option_price_cost_array));
    $_option_price_cost_child= implode("│",preg_replace("/[^0-9]/", "",$_option_price_cost_child_array));
    $_option_cont= implode("&*&",$_option_cont_array);
    $_option_view_yn= implode("│",$_option_view_yn_array);

	$file_dir = "../../uploads/product";
	$file_okext    = array("jpg","jpeg","gif","bmp","png");
	$file_okimage  = array("jpg","jpeg","gif","bmp","png");
	$max_file_size = "20971520";

	$back_url = "product_modify_popup.php?key=".$mokey;

	//사진이 존재 할경우
	if( $_FILES[file_mobile_thum] ){

	
		$file_mobile_thum = $_FILES[file_mobile_thum][tmp_name];
		$file_mobile_thum_name = $_FILES[file_mobile_thum][name];
		$file_mobile_thum_size = $_FILES[file_mobile_thum][size];
		$file_mobile_thum_type = $_FILES[file_mobile_thum][type];

		if( $file_mobile_thum_size > 0 ){

			$imgSize = getimagesize($file_mobile_thum);
			if( $imgSize[0] != 500 ){ msg('모바일 목록 가로사이즈를 500px로 맞춰주세요.','product_modify_popup.php?key='.$_mokey); exit; }
			if( $imgSize[1] != 300 ){ msg('모바일 목록 세로사이즈를 300px로 맞춰주세요.','product_modify_popup.php?key='.$_mokey); exit; }

			$file_ext = explode(".", $file_mobile_thum_name);

			//파일이름 바꾸기
			$file_mobile_thum_name = $pd_system_code."_mobile_thum.".$file_ext[1];
			$file_mobile_thum_name_s = $pd_system_code."_mobile_thum_s.".$file_ext[1];

			$file_mobile_thum_text = $file_dir."/".$file_mobile_thum_name;
			move_uploaded_file($file_mobile_thum, $file_mobile_thum_text);

			$image = new SimpleImage();
			$image->load($file_mobile_thum_text);
			$image->square(100);
			$image->save($file_dir."/".$file_mobile_thum_name_s);
		}

	}

	//모바일 슬라이딩
    for($g=0; $g<count($_FILES["file_mobile_sliding"]["name"]); $g++){
		
		$g2 = $g + 1;

		$file_mobile_sliding = $_FILES["file_mobile_sliding"][tmp_name][$g];
		$file_mobile_sliding_name = $_FILES["file_mobile_sliding"][name][$g];
		$file_mobile_sliding_size = $_FILES["file_mobile_sliding"][size][$g];
		$file_mobile_sliding_type = $_FILES["file_mobile_sliding"][type][$g];

		if( $file_mobile_sliding_size > 0 ){

			$file_ext = explode(".", $file_mobile_sliding_name);

			//파일이름 바꾸기
			$file_mobile_sliding_name = $pd_system_code."_mobile_sliding_".$g2.".".$file_ext[1];
			$file_mobile_sliding_name_s = $pd_system_code."_mobile_sliding_".$g2."_s.".$file_ext[1];

			$file_mobile_sliding_text = $file_dir."/".$file_mobile_sliding_name;
			$file_mobile_sliding_text_array[] = str_replace("../../uploads/product/","",$file_mobile_sliding_text);

			move_uploaded_file($file_mobile_sliding, $file_mobile_sliding_text);

			$image = new SimpleImage();
			$image->load($file_mobile_sliding_text);
			$image->square(100);
			$image->save($file_dir."/".$file_mobile_sliding_name_s);

		}

	}

	$_pd_mobile_sliding = implode("│",$file_mobile_sliding_text_array);

	$query = "update  ".$db_t_PRODUCT_DB."  set 
			PD_SYSTEM_CODE = '".$pd_system_code."',
            PD_NAME = '".$_pd_name."',
            PD_NAME_EG = '".$_pd_name_eg."' ,
            PD_CONT = '".$_pd_cont."',
            PD_AREA  = '".$_pd_area."', 
            PD_USE_YN = '".$_pd_use_yn."',
            PD_KIND = '".$_pdKind."',
			PD_KIND2 = '".$_pd_kind2."',
			PD_KIND3 = '".$_pd_kind3."',
            PD_CATAGORY = '".$_product_ctagory."',
            PD_CATAGORY2 = '".$_pd_catagory2."',

            PD_PRICE_O  = '".$_pd_price_o."',
			PD_COST_RATE = '".$_pd_cost_rate."',
            PD_PRICE_VM = '".$_pd_price_vm."',
            PD_PRICE_VM_CHILD = '".$_pd_price_vm_child."',
            PD_PRICE_FREE = '".$_pd_price_free."',
            PD_PRICE_PREE_CHILD = '".$_pd_price_free_child."',
            PD_COST_PRICE = '".$_pd_cost_price."',
            PD_COST_PRICE_CHILD = '".$_pd_cost_price_child."',

            PD_OPTION_YN = '".$_pd_option_yn."',
            PD_OPTION_DUTY = '".$_pd_option_duty."',
            PD_OPTION_KIND = '".$_pd_option_kind."',
            PD_OPTION = '".$_option_name."' ,
            PD_OPTION_CONT = '".$_option_cont."' ,
            PD_OPTION_PRICE = '".$_option_price."' ,
            PD_OPTION_PRICE_CHILD = '".$_option_price_child."' ,
			PD_OPTION_PRICE_VM  = '".$_option_price."' ,
			PD_OPTION_PRICE_VM_CHILD  = '".$_option_price_child."' ,
            PD_OPTION_PRICE_FREE = '".$_option_price_free."' ,
            PD_OPTION_PRICE_FREE_CHILD = '".$_option_price_free_child."' ,
            PD_OPTION_PRICE_COST =  '".$_option_price_cost."' ,
            PD_OPTION_PRICE_COST_CHILD =  '".$_option_price_cost_child."' ,
            PD_OPTION_VIEW_YN =  '".$_option_view_yn."' ,
            PD_RETURN_POLICY = '".$_pd_return_policy."' , 
            
            PD_PRICE_V_CHILD  = '".$_pd_price_v_child."',
            PD_TOUR_STORY = '".$_pd_tour_story."',
        	PD_MOBILE_THUM = '".$file_mobile_thum_text."',
            PD_MOBILE_SLIDING = '".$_pd_mobile_sliding."',
            PD_X_GPS = '".$_pd_x_gps."' ,
            PD_Y_GPS = '".$_pd_y_gps."' ,
            PD_VIDEO_URL = '".$_pd_video_url."' ,
            PD_INTRODUCE_CONT = '".$_pd_introduce_cont."' ,
            PD_INCLUSION = '".$_pd_inclusion."' , 
            PD_PDC_ID ='".$_product_pdc_id."',
			PD_VIEW = '".$_pd_view."',
			PD_MOD_ID = '".$ad_id."',
			PD_MOD_DATE = '".$wepix_now_time."',
           
            PD_DETAIL_CONT = '".$_pd_detail_cont."',
            PD_DIRECTIONS = '".$_pd_directions."'

            
            where PD_IDX = '".$_mokey."'";
	$result = wepix_query_error($query);

	msg('수정 완료','product_modify_popup.php?key='.$_mokey);



}else if($_action_mode=="product_new"){
// ******************************************************************************************************************
// 상품 등록
// ******************************************************************************************************************
	$pd_system_code_count = wepix_counter($db_t_PRODUCT_DB, "where PD_SYSTEM_CODE = '".$pd_system_code."'");
    
	if( $pd_system_code_count > 0 ){
		$random = rand(100,999);
		$pd_system_code = date("ymdHis").$random;
	}
	$_mokey = securityVal($mokey);

    $_pd_name = securityVal($pd_name);
    $_pd_name_eg =securityVal($pd_name_eg );
    $_pd_area =securityVal($pd_area);
    $_pd_use_yn =securityVal($pd_use_yn);
    $_pdKind =securityVal($pdKind);
    $_pd_kind2 =securityVal($pd_kind2);
    $_pd_kind3 =securityVal($pd_kind3);
	$_product_ctagory =securityVal($pd_ctagory);
    $_pd_catagory2 = securityVal($pd_catagory2);
    $_product_pdc_id = securityVal($pd_id);
    $_pd_cont =$pd_cont;

    $_pd_view =securityVal($pd_view);
	$_pd_cost_rate =securityVal($pd_cost_rate);
    $_pd_price_o = preg_replace("/[^0-9]/", "",securityVal($pd_price_o));
    $_pd_price_vm = preg_replace("/[^0-9]/", "",securityVal($pd_price_vm));
    $_pd_price_vm_child = preg_replace("/[^0-9]/", "",securityVal($pd_price_vm_child));
    $_pd_price_free = preg_replace("/[^0-9]/", "",securityVal($pd_price_free));
    $_pd_price_free_child = preg_replace("/[^0-9]/", "",securityVal($pd_price_free_child));
    $_pd_cost_price = preg_replace("/[^0-9]/", "",securityVal($pd_cost_price));
    $_pd_cost_price_child = preg_replace("/[^0-9]/", "",$pd_cost_price_child);
    $_pd_option_yn =  securityVal($pd_option_yn);
    
	
    $_option_name_array = securityVal($option_name);
    $_option_price_array = securityVal($option_price);
    $_option_price_child_array =securityVal($option_price_child);
    $_option_price_free_array =securityVal($option_price_free);
    $_option_price_free_child_array =securityVal($option_price_free_child);
    $_option_price_cost_array = securityVal($option_price_cost);
    $_option_price_cost_child_array = securityVal($option_price_cost_child);
    $_option_cont_array = $option_cont;
    $_option_view_yn_array =securityVal($option_view_yn);

    $_pd_inclusion = $pd_inclusion;
    $_pd_tour_story = $pd_tour_story;
    $_pd_detail_cont = $pd_detail_cont;
    $_pd_directions = $pd_directions;
    $_pd_x_gps = securityVal($pd_x_gps);
    $_pd_y_gps =securityVal($pd_y_gps);
    $_pd_video_url = securityVal($pd_video_url);
    $_pd_return_policy =$pd_return_policy;

    $_option_name= implode("│",$_option_name_array);
    $_option_price= implode("│",preg_replace("/[^0-9]/", "",$_option_price_array));
    $_option_price_child= implode("│",preg_replace("/[^0-9]/", "",$_option_price_child_array));
    $_option_price_free= implode("│",preg_replace("/[^0-9]/", "",$_option_price_free_array));
    $_option_price_free_child= implode("│",preg_replace("/[^0-9]/", "",$_option_price_free_child_array));
    $_option_price_cost= implode("│",preg_replace("/[^0-9]/", "",$_option_price_cost_array));
    $_option_price_cost_child= implode("│",preg_replace("/[^0-9]/", "",$_option_price_cost_child_array));
    $_option_cont= implode("&*&",$_option_cont_array);
    $_option_view_yn= implode("│",$_option_view_yn_array);

	$file_dir = "../../uploads/product";
	$file_okext    = array("jpg","jpeg","gif","bmp","png");
	$file_okimage  = array("jpg","jpeg","gif","bmp","png");
	$max_file_size = "20971520";

	$back_url = "product_modify_popup.php?key=".$mokey;

	//사진이 존재 할경우
	if( $_FILES[file_mobile_thum] ){

	
		$file_mobile_thum = $_FILES[file_mobile_thum][tmp_name];
		$file_mobile_thum_name = $_FILES[file_mobile_thum][name];
		$file_mobile_thum_size = $_FILES[file_mobile_thum][size];
		$file_mobile_thum_type = $_FILES[file_mobile_thum][type];

		if( $file_mobile_thum_size > 0 ){

			$imgSize = getimagesize($file_mobile_thum);
			if( $imgSize[0] != 500 ){ msg('모바일 목록 가로사이즈를 500px로 맞춰주세요.','product_modify_popup.php?key='.$_mokey); exit; }
			if( $imgSize[1] != 300 ){ msg('모바일 목록 세로사이즈를 300px로 맞춰주세요.','product_modify_popup.php?key='.$_mokey); exit; }

			$file_ext = explode(".", $file_mobile_thum_name);

			//파일이름 바꾸기
			$file_mobile_thum_name = $pd_system_code."_mobile_thum.".$file_ext[1];
			$file_mobile_thum_name_s = $pd_system_code."_mobile_thum_s.".$file_ext[1];

			$file_mobile_thum_text = $file_dir."/".$file_mobile_thum_name;
			move_uploaded_file($file_mobile_thum, $file_mobile_thum_text);

			$image = new SimpleImage();
			$image->load($file_mobile_thum_text);
			$image->square(100);
			$image->save($file_dir."/".$file_mobile_thum_name_s);
		}

	}

	//모바일 슬라이딩
    for($g=0; $g<count($_FILES["file_mobile_sliding"]["name"]); $g++){
		
		$g2 = $g + 1;

		$file_mobile_sliding = $_FILES["file_mobile_sliding"][tmp_name][$g];
		$file_mobile_sliding_name = $_FILES["file_mobile_sliding"][name][$g];
		$file_mobile_sliding_size = $_FILES["file_mobile_sliding"][size][$g];
		$file_mobile_sliding_type = $_FILES["file_mobile_sliding"][type][$g];

		if( $file_mobile_sliding_size > 0 ){

			$file_ext = explode(".", $file_mobile_sliding_name);

			//파일이름 바꾸기
			$file_mobile_sliding_name = $pd_system_code."_mobile_sliding_".$g2.".".$file_ext[1];
			$file_mobile_sliding_name_s = $pd_system_code."_mobile_sliding_".$g2."_s.".$file_ext[1];

			$file_mobile_sliding_text = $file_dir."/".$file_mobile_sliding_name;
			$file_mobile_sliding_text_array[] = str_replace("../../uploads/product/","",$file_mobile_sliding_text);

			move_uploaded_file($file_mobile_sliding, $file_mobile_sliding_text);

			$image = new SimpleImage();
			$image->load($file_mobile_sliding_text);
			$image->square(100);
			$image->save($file_dir."/".$file_mobile_sliding_name_s);

		}

	}

	$_pd_mobile_sliding = implode("│",$file_mobile_sliding_text_array);

	$query = "insert  ".$db_t_PRODUCT_DB."  set 
			PD_SYSTEM_CODE = '".$pd_system_code."',
            PD_NAME = '".$_pd_name."',
            PD_NAME_EG = '".$_pd_name_eg."' ,
            PD_CONT = '".$_pd_cont."',
            PD_AREA  = '".$_pd_area."', 
            PD_USE_YN = '".$_pd_use_yn."',
            PD_KIND = '".$_pdKind."',
			PD_KIND2 = '".$_pd_kind2."',
			PD_KIND3 = '".$_pd_kind3."',
            PD_CATAGORY = '".$_product_ctagory."',
            PD_CATAGORY2 = '".$_pd_catagory2."',

            PD_PRICE_O  = '".$_pd_price_o."',
			PD_COST_RATE = '".$_pd_cost_rate."',
            PD_PRICE_VM = '".$_pd_price_vm."',
            PD_PRICE_VM_CHILD = '".$_pd_price_vm_child."',
            PD_PRICE_FREE = '".$_pd_price_free."',
            PD_PRICE_PREE_CHILD = '".$_pd_price_free_child."',
            PD_COST_PRICE = '".$_pd_cost_price."',
            PD_COST_PRICE_CHILD = '".$_pd_cost_price_child."',

            PD_OPTION_YN = '".$_pd_option_yn."',
            PD_OPTION_DUTY = '".$_pd_option_duty."',
            PD_OPTION_KIND = '".$_pd_option_kind."',
            PD_OPTION = '".$_option_name."' ,
            PD_OPTION_CONT = '".$_option_cont."' ,
            PD_OPTION_PRICE = '".$_option_price."' ,

            PD_OPTION_PRICE_CHILD = '".$_option_price_child."' ,
            PD_OPTION_PRICE_FREE = '".$_option_price_free."' ,
            PD_OPTION_PRICE_FREE_CHILD = '".$_option_price_free_child."' ,
            PD_OPTION_PRICE_COST =  '".$_option_price_cost."' ,
            PD_OPTION_PRICE_COST_CHILD =  '".$_option_price_cost_child."' ,
            PD_OPTION_VIEW_YN =  '".$_option_view_yn."' ,
            PD_RETURN_POLICY = '".$_pd_return_policy."' , 
            
            PD_PRICE_V_CHILD  = '".$_pd_price_v_child."',
            PD_TOUR_STORY = '".$_pd_tour_story."',
        	PD_MOBILE_THUM = '".$file_mobile_thum_text."',
            PD_MOBILE_SLIDING = '".$_pd_mobile_sliding."',
            PD_X_GPS = '".$_pd_x_gps."' ,
            PD_Y_GPS = '".$_pd_y_gps."' ,
            PD_VIDEO_URL = '".$_pd_video_url."' ,
            PD_INTRODUCE_CONT = '".$_pd_introduce_cont."' ,
            PD_INCLUSION = '".$_pd_inclusion."' , 
            PD_PDC_ID ='".$_product_pdc_id."',
			PD_VIEW = '".$_pd_view."',
			PD_MOD_ID = '".$ad_id."',
			PD_MOD_DATE = '".$wepix_now_time."',
           
            PD_DETAIL_CONT = '".$_pd_detail_cont."',
            PD_DIRECTIONS = '".$_pd_directions."'";
	$result = wepix_query_error($query);

	msg('등록 완료',_A_PATH_PRODUCT_LIST);



}
exit;
?>