<?
include "../lib/inc_common.php";
include('../../class/image.php'); //이미지 처리 클래스
$_a_mode = securityVal($a_mode);
$_idx = securityVal($idx);


// ******************************************************************************************************************
// 싸이트 등록
// ******************************************************************************************************************
if($_a_mode == "siteNew"){

	$_sd_name = securityVal($sd_name);
	$_sd_logo = securityVal($sd_logo);
	$_sd_domain = securityVal($sd_domain);
	$_sd_view = securityVal($sd_view);
	$_sd_delivery = securityVal($sd_delivery);
	$_sd_memo = securityVal($sd_memo);

	 $query = "insert into  "._DB_SITE." set
            SD_NAME = '".$_sd_name."',
			SD_LOGO = '".$_sd_logo."',
			SD_DOMAIN = '".$_sd_domain."',
			SD_DELIVERY = '".$_sd_delivery."',
			SD_MEMO = '".$_sd_memo."',
			SD_ACTIVE ='".$_sd_view."'";

	 $result = wepix_query_error($query);
	 msg("등록 완료!",_A_PATH_SITE_LIST);

// ******************************************************************************************************************
// 싸이트 수정
// ******************************************************************************************************************
}elseif($_a_mode == "siteModify"){
    
    $_sd_name = securityVal($sd_name);
	$_sd_logo = securityVal($sd_logo);
	$_sd_domain = securityVal($sd_domain);
	$_sd_view = securityVal($sd_view);
	$_sd_delivery = securityVal($sd_delivery);
	$_sd_memo = securityVal($sd_memo);
		
		$query = "update "._DB_SITE." set
			SD_NAME = '".$_sd_name."',
			SD_LOGO = '".$_sd_logo."',
			SD_DOMAIN = '".$_sd_domain."',
			SD_DELIVERY = '".$_sd_delivery."',
			SD_MEMO = '".$_sd_memo."',
			SD_ACTIVE ='".$_sd_view."'
		where SD_IDX = '".$_idx."'";
	
	  wepix_query_error($query);

	 msg("수정완료",_A_PATH_SITE_REG."?mode=modify&key=".$_idx);
// ******************************************************************************************************************
// 싸이트 삭제
// ******************************************************************************************************************
}elseif($_a_mode == "siteDel"){

	$query = "delete from "._DB_SITE." where SD_IDX = '".$_idx."'";

	$result = wepix_query_error($query);
	 	echo "|Processing_Complete|처리완료|";
// ******************************************************************************************************************
// 가격비교 등록
// ******************************************************************************************************************
}elseif($_a_mode == "comparisonNew"){
if ( ! function_exists('getDomainName')){

	function getDomainName($url)	{
		$value = strtolower(trim($url));
		$url_patten = '/^(?:(?:[a-z]+):\/\/)?((?:[a-z\d\-]{2,}\.)+[a-z]{2,})(?::\d{1,5})?(?:\/[^\?]*)?(?:\?.+)?$/i';
		$domain_patten = '/([a-z\d\-]+(?:\.(?:asia|info|name|mobi|com|net|org|biz|tel|xxx|kr|co|so|me|eu|cc|or|pe|ne|re|tv|jp|tw)){1,2})(?::\d{1,5})?(?:\/[^\?]*)?(?:\?.+)?$/i';
		if (preg_match($url_patten, $value))
		{
			preg_match($domain_patten, $value, $matches);
			$host = (!$matches[1]) ? $value : $matches[1];
		}
		return $host;
	}
}

	$_cd_maker = securityVal($cd_maker);
	$_cl_brand = securityVal($cl_brand);
	$_cd_name = securityVal($cd_name);
	$_cd_name_og = securityVal($cd_name_og);
	$_cd_cont = securityVal($cd_cont);
	$_cd_code = securityVal($cd_code);
	$_cd_category = securityVal($cd_category);
	$_cd_img = securityVal($cd_img);
	$_cd_price = securityVal($cd_price);
	$_cd_hashtag = securityVal($cd_hashtag);
	$_cd_link = securityVal($cd_link);
	$_cd_score = securityVal($cd_score);
	$_cd_review = securityVal($cd_review);
	$_cd_keep = securityVal($cd_keep);
	$_cl_site = securityVal($cl_site);
	$_cl_sort = securityVal($cl_sort);
	$_cl_price = securityVal($cl_price);
	$_cl_delivery = securityVal($cl_delivery);
	$_cl_path = securityVal($cl_path);
	$_cl_memo = securityVal($cl_memo);

	

	$_cd_related_goods = securityVal($cd_related_goods);
	$_cd_recommend_goods = securityVal($cd_recommend_goods);

	$_tg_structure = securityVal($tg_structure);
	$_tg_type = securityVal($tg_type);

	$query = "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'ugo65_dgmall' AND TABLE_NAME = '"._DB_COMPARISON."'";
	$result = wepix_query_error($query);
	$tableIdx = wepix_fetch_array($result);
	$_next_idx = $tableIdx[AUTO_INCREMENT];
	$randomNum = mt_rand(1000, 9999);
	$_cd_maching_code = "comparion_".$_next_idx.$randomNum;
	
		//파일이 있을경우
		if ( $_FILES['cd_img']['name'] ) {
			if ( !$_FILES['cd_img']['error'] ) {

				$_show_bc_thumbnail_w = 302;
				$_show_bc_thumbnail_h = 302;
				$_inst_img_move_dir = '../../data/comparion/';
				$_b_token = $_cd_maching_code;

				$thumbnail_tmp_file = $_FILES['cd_img']['tmp_name'];
				$thumbnail_timg = @getimagesize($thumbnail_tmp_file);
				$thumbnail_size = filesize($thumbnail_tmp_file);
				$_ary_thumbnail_ext = explode('.', $_FILES['cd_img']['name']); //확장자 분리
				$_thumbnail_ext_index = count($_ary_thumbnail_ext) - 1; //파일명에 ( . ) 들어갔을경우

				//이미지인지 체크
				if(in_array($thumbnail_timg[2] , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP))){
					$img_name = $_b_token.".".$_ary_thumbnail_ext[1];
					$thumbnail_destination = $_inst_img_move_dir."/".$img_name;
					move_uploaded_file($thumbnail_tmp_file, $thumbnail_destination);
					//썸네일 리사이징
					if( $thumbnail_timg[0] > $_show_bc_thumbnail_w or $thumbnail_timg[1] > $_show_bc_thumbnail_h ){
						$image = new SimpleImage();
						$image->load($thumbnail_destination);
						$image->resize($_show_bc_thumbnail_w, $_show_bc_thumbnail_h);
						$image->save($thumbnail_destination);
					}
					$_board_thumbnail_name = $thumbnail_destination;
					
					//썸네일이 저장되었으면 DB에 저장한다
					fileReg( $_inst_img_move_dir, $img_name,'comparion', $_b_token, $thumbnail_timg[0], $thumbnail_timg[1], $thumbnail_size, "comparion", "img" );

				}else{
					msg("이미지만 등록 가능합니다.","");
					exit;
				}

			} 
		} 

	 $_cd_link_count = count($_cl_site);
	 $total_count = wepix_counter(_DB_COMPARISON,"");
  	 $_cd_sort = $total_count + 1;

	 for($i=0;$i<count($_tg_structure);$i++){
		$tag_data = wepix_fetch_array(wepix_query_error("select * from "._DB_TAG." where TG_IDX = '".$_tg_structure[$i]."'")); 
		$_cd_tag_name[] = $_tg_structure[$i]."/".$tag_data[TG_NAME];
		$query = "insert into  "._DB_COMPARISON_TAG." set
            CT_TYPE = 'COMPARISON',
			CT_CD_IDX = '".$_next_idx."',
			CT_TG_IDX = '".$_tg_structure[$i]."'";
		// $result = wepix_query_error($query);
	 }

		$_ary_cd_tag = implode("│",$_cd_tag_name);

	 $query = "insert into  "._DB_COMPARISON." set
			CD_MACHING_CODE = '".$_cd_maching_code."',
            CD_BRAND_IDX = '".$_cl_brand."',
			CD_NAME = '".$_cd_name."',
			CD_NAME_OG = '".$_cd_name_og."',
			CD_CONT = '".$_cd_cont."',
			CD_LINK  = '".$_cd_link."',
			CD_CODE = '".$_cd_code."',
			CD_CATEGORY = '".$_cd_category."',
			CD_IMG = '".$img_name."',
			CD_PRICE = '".$_cd_price."',
			CD_HASH_TAG = '".$_ary_cd_tag."',
			CD_SORT = '".$_cd_sort."',
			CD_LINK_COUNT = '".$_cd_link_count."',
			CD_RELATED_GOODS = '".$_cd_related_goods."',
			CD_RECOMMEND_GOODS = '".$_cd_recommend_goods."',
			CD_UPDATE_DATE = '".$wepix_now_time."',
			CD_REG_DATE	 ='".$wepix_now_time."'";
	// $result = wepix_query_error($query);

	for($i=0;$i<count($_cl_site);$i++){
		 $_dmain_name = getDomainName($_cl_path[$i]);
		  
		 if($_dmain_name = 'cafe24.com'){
			$_dmain_name = "skswhddk.cafe24.com";
		 }
		 $site_data = wepix_fetch_array(wepix_query_error("select SD_IDX from "._DB_SITE." where SD_ACTIVE = 'Y' and SD_DOMAIN='".$_dmain_name."'"));

		 $query = "insert into  "._DB_COMPARISON_LINK." set
            CL_CD_IDX = '".$_next_idx."',
			CL_SD_IDX = '".$site_data[SD_IDX]."',
			CL_SORT_NUM = '".$_cl_sort[$i]."',
			CL_PRICE = '".$_cl_price[$i]."',
			CL_DELIVERY_PRICE = '".$_cl_delivery[$i]."',
			CL_PATH = '".$_cl_path[$i]."',
			CL_MEMO = '".$_cl_memo[$i]."'";
		//$result = wepix_query_error($query);
	
	}

	 msg("등록 완료!",_A_PATH_COMPARISON_LIST);

// ******************************************************************************************************************
// 가격비교 수정
// ******************************************************************************************************************
}elseif($_a_mode == "comparisonModify"){

	$_cd_maching_code = securityVal($cd_maching_code);
	$_cl_brand = securityVal($cl_brand);
	$_cd_name = securityVal($cd_name);
	$_cd_name_og = securityVal($cd_name_og);
	$_cd_cont = securityVal($cd_cont);
	$_cd_code = securityVal($cd_code);
	$_cd_category = securityVal($cd_category);
	$_cd_img = securityVal($cd_img);
	$_cd_price = securityVal($cd_price);
	$_cd_hashtag = securityVal($cd_hashtag);
	$_cd_link = securityVal($cd_link);
	$_cd_score = securityVal($cd_score);
	$_cd_review = securityVal($cd_review);
	$_cd_keep = securityVal($cd_keep);
	$cd_update_date = securityVal($cd_update_date);
	$_cl_idx = securityVal($cl_idx);
	$_cl_site = securityVal($cl_site);
	$_cl_sort = securityVal($cl_sort);
	$_cl_price = securityVal($cl_price);
	$_cl_delivery = securityVal($cl_delivery);
	$_cl_path = securityVal($cl_path);
	$_cl_memo = securityVal($cl_memo);

	$_tg_structure = securityVal($tg_structure);
	$_tg_type = securityVal($tg_type);

	$_cd_related_goods = securityVal($cd_related_goods);
	$_cd_recommend_goods = securityVal($cd_recommend_goods);

	$dend2 = explode("-",$cd_update_date);
	$_cd_update_date = mktime(0,0,0,$dend2[1],$dend2[2],$dend2[0]);
	
	$com_tag_result = wepix_query_error("select * from "._DB_COMPARISON_TAG." where CT_CD_IDX = '".$_idx."' ");
	
	while($com_tag_list = wepix_fetch_array($com_tag_result)){
		$_ary_com_tag[] = $com_tag_list[CT_TG_IDX];
	}
				 
	$_cd_link_count = count($_cl_site);

		//파일이 있을경우
		if ( $_FILES['cd_img']['name'] ) {
			if ( !$_FILES['cd_img']['error'] ) {
				$is_file_exist = file_exists("../../data/comparion/".$img_name);
				  if ($is_file_exist) {
					@unlink("../../data/comparion/".$img_name); // 사진파일 삭제
					wepix_query_error("delete from "._DB_FILE." where FILE_B_TOKEN = '".$_cd_maching_code."' "); //파일 DB삭제
				  }
				$_show_bc_thumbnail_w = 302;
				$_show_bc_thumbnail_h = 302;
				$_inst_img_move_dir = '../../data/comparion/';
				$_b_token = $_cd_maching_code;

				$thumbnail_tmp_file = $_FILES['cd_img']['tmp_name'];
				$thumbnail_timg = @getimagesize($thumbnail_tmp_file);
				$thumbnail_size = filesize($thumbnail_tmp_file);
				$_ary_thumbnail_ext = explode('.', $_FILES['cd_img']['name']); //확장자 분리
				$_thumbnail_ext_index = count($_ary_thumbnail_ext) - 1; //파일명에 ( . ) 들어갔을경우

				//이미지인지 체크
				if(in_array($thumbnail_timg[2] , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP))){
					$img_name = $_b_token.".".$_ary_thumbnail_ext[1];
					$thumbnail_destination = $_inst_img_move_dir."/".$img_name;
					move_uploaded_file($thumbnail_tmp_file, $thumbnail_destination);
					//썸네일 리사이징
					if( $thumbnail_timg[0] > $_show_bc_thumbnail_w or $thumbnail_timg[1] > $_show_bc_thumbnail_h ){
						$image = new SimpleImage();
						$image->load($thumbnail_destination);
						$image->resize($_show_bc_thumbnail_w, $_show_bc_thumbnail_h);
						$image->save($thumbnail_destination);
					}
					$_board_thumbnail_name = $thumbnail_destination;
					
					//썸네일이 저장되었으면 DB에 저장한다
					fileReg( $_inst_img_move_dir, $img_name,'comparion', $_b_token, $thumbnail_timg[0], $thumbnail_timg[1], $thumbnail_size, "comparion", "img" );

				}else{
					msg("이미지만 등록 가능합니다.","");
					exit;
				}

			} 
		}

		 for($i=0;$i<count($_tg_structure);$i++){
			$tag_data = wepix_fetch_array(wepix_query_error("select * from "._DB_TAG." where TG_IDX = '".$_tg_structure[$i]."'")); 
			$_cd_tag_name[] = $_tg_structure[$i]."/".$tag_data[TG_NAME];
			if(!in_array($_tg_structure[$i], $_ary_com_tag)){
				$query = "insert into  "._DB_COMPARISON_TAG." set
					CT_TYPE = 'COMPARISON',
					CT_CD_IDX = '".$_idx."',
					CT_TG_IDX = '".$_tg_structure[$i]."'";
				 $result = wepix_query_error($query);
			}
		 }

		$_ary_cd_tag = implode("│",$_cd_tag_name);

		$query = "update "._DB_COMPARISON." set
            CD_BRAND_IDX = '".$_cl_brand."',
			CD_NAME = '".$_cd_name."',
			CD_NAME_OG = '".$_cd_name_og."',
			CD_CONT = '".$_cd_cont."',
			CD_CODE = '".$_cd_code."',
			CD_LINK  = '".$_cd_link."',
			CD_CATEGORY = '".$_cd_category."',
			CD_IMG = '".$img_name."',
			CD_PRICE = '".$_cd_price."',
			CD_HASH_TAG = '".$_ary_cd_tag."',
			CD_LINK_COUNT = '".$_cd_link_count."',
			CD_RELATED_GOODS = '".$_cd_related_goods."',
			CD_RECOMMEND_GOODS = '".$_cd_recommend_goods."',
			CD_UPDATE_DATE = '".$wepix_now_time."'
		
		where CD_IDX = '".$_idx."'";
	
	  wepix_query_error($query);

	for($i=0;$i<count($_cl_site);$i++){
		if($_cl_idx[$i] == 0){
			 $query = "insert into  "._DB_COMPARISON_LINK." set
				CL_CD_IDX = '".$_idx."',
				CL_SD_IDX = '".$_cl_site[$i]."',
				CL_SORT_NUM = '".$_cl_sort[$i]."',
				CL_PRICE = '".$_cl_price[$i]."',
				CL_DELIVERY_PRICE = '".$_cl_delivery[$i]."',
				CL_PATH = '".$_cl_path[$i]."',
				CL_MEMO = '".$_cl_memo[$i]."'";
			$result = wepix_query_error($query);
		}elseif($_cl_idx[$i] != 0){
			$query = "update  "._DB_COMPARISON_LINK." set
				CL_CD_IDX = '".$_idx."',
				CL_SD_IDX = '".$_cl_site[$i]."',
				CL_SORT_NUM = '".$_cl_sort[$i]."',
				CL_PRICE = '".$_cl_price[$i]."',
				CL_DELIVERY_PRICE = '".$_cl_delivery[$i]."',
				CL_PATH = '".$_cl_path[$i]."',
				CL_MEMO = '".$_cl_memo[$i]."'
				where CL_IDX = '".$_cl_idx[$i]."'";
			$result = wepix_query_error($query);

		}
	
	}

	 msg("수정완료",_A_PATH_COMPARISON_REG."?mode=modify&key=".$_idx);
// ******************************************************************************************************************
// 가격비교 삭제
// ******************************************************************************************************************
}elseif($_a_mode == "comparisonDel"){

	 $query = "delete from "._DB_COMPARISON." where CD_IDX = '".$_idx."'";

	 $result = wepix_query_error($query);
	 echo "|Processing_Complete|처리완료|";


// ******************************************************************************************************************
// 태그 삭제
// ******************************************************************************************************************
}elseif($_a_mode == "TagDel"){

	 $_cd_idx = securityVal($cd_idx);
	 $_tg_idx = securityVal($tg_idx);

	 $_tag_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON_TAG." where CT_TG_IDX = '".$_tg_idx."' and CT_CD_IDX = '".$_cd_idx."'"));

	 if($_tag_data[CT_IDX]){
		$query = "delete from "._DB_COMPARISON_TAG." where CT_TG_IDX = '".$_tg_idx."' and CT_CD_IDX = '".$_cd_idx."'";
		$result = wepix_query_error($query);
		echo "|Processing_Complete|처리완료|";
	 }else{
	 	 echo "|Value_null|처리완료|";
	 }
	 
	 
// ******************************************************************************************************************
// 가격비교 사이트 링크 삭제
// ******************************************************************************************************************
}elseif($_a_mode == "comparisonLinkDel"){

		$query = "delete from "._DB_COMPARISON_LINK." where CL_IDX = '".$_idx."'";
		$result = wepix_query_error($query);
		echo "|Processing_Complete|처리완료|";


// ******************************************************************************************************************
// 가격비교상품 순서 정렬
// ******************************************************************************************************************
}elseif($_a_mode == "comparisonSort"){
		
		
	$_ary_cd_idx = securityVal($cd_idx);
	for($i=0;$i<count($_ary_cd_idx);$i++){
		$_sort_num = $i+1;
		
		$query = "update  "._DB_COMPARISON." set
					CD_SORT = '".$_sort_num."'
				  where CD_IDX = '".$_ary_cd_idx[$i]."'";
		$result = wepix_query_error($query);
	}

	msg("수정완료",_A_PATH_COMPARISON_SORT);

}elseif($_a_mode == "testtesttesttesttesttest"){
if ( ! function_exists('getDomainName')){

	function getDomainName($url)	{
		$value = strtolower(trim($url));
		$url_patten = '/^(?:(?:[a-z]+):\/\/)?((?:[a-z\d\-]{2,}\.)+[a-z]{2,})(?::\d{1,5})?(?:\/[^\?]*)?(?:\?.+)?$/i';
		$domain_patten = '/([a-z\d\-]+(?:\.(?:asia|info|name|mobi|com|net|org|biz|tel|xxx|kr|co|so|me|eu|cc|or|pe|ne|re|tv|jp|tw)){1,2})(?::\d{1,5})?(?:\/[^\?]*)?(?:\?.+)?$/i';
		if (preg_match($url_patten, $value))
		{
			preg_match($domain_patten, $value, $matches);
			$host = (!$matches[1]) ? $value : $matches[1];
		}
		return $host;
	}
}

	$query = "select * from "._DB_COMPARISON_LINK."";
	$result = wepix_query_error($query);
	echo "-------------------------------------------<br>";
	while($list = wepix_fetch_array($result)){
		 $_dmain_name = getDomainName($list[CL_PATH]);
		
		 $site_data = wepix_fetch_array(wepix_query_error("select * from "._DB_SITE." where SD_ACTIVE = 'Y' and SD_DOMAIN='".$_dmain_name."'"));

		 if($site_data[SD_IDX] != $list[CL_SD_IDX]){
			$pd_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX='".$list[CL_CD_IDX]."'"));
			$site_data2 = wepix_fetch_array(wepix_query_error("select * from "._DB_SITE." where SD_ACTIVE = 'Y' and SD_IDX ='".$list[CL_SD_IDX]."'"));

			echo $list[CL_IDX]." 상품명 (고유번호): ".$pd_data[CD_NAME]." (".$list[CL_CD_IDX].") ".$site_data2[SD_NAME]." (".$site_data2[SD_IDX].") ->".$site_data[SD_NAME]." (".$site_data[SD_IDX].") 변경요망 <br/><br/>";

				
		 }
	}
	echo "-------------------------------------------<br>";

}
     








exit;
?>
