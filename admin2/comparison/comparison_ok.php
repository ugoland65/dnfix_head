<?
include "../lib/inc_common.php";
include('../../class/image.php'); //이미지 처리 클래스

	//넘어온 변수 전체 검열
	while(list($key,$value)= each($_POST)){
		${"_".$key} = securityVal($value);
	}

	$_inst_img_dir = "../../data/site_logo/";

	//이동할 폴더가 없을경우 생성한다
	if(!is_dir($_inst_img_dir)){
		@mkdir($_inst_img_dir, 0777);
		@chmod($_inst_img_dir, 0777);
	}

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

	$_cd_price_th = (int)str_replace(',','', $_cd_price_th);

	$_cd_supply_price_1 = (int)str_replace(',','', securityVal($cd_supply_price_1)); //
	$_cd_supply_price_2 = (int)str_replace(',','', securityVal($cd_supply_price_2)); //
	$_cd_supply_price_3 = (int)str_replace(',','', securityVal($cd_supply_price_3)); //
	$_cd_supply_price_4 = (int)str_replace(',','', securityVal($cd_supply_price_4)); //
	$_cd_supply_price_5 = (int)str_replace(',','', securityVal($cd_supply_price_5)); //
	$_cd_supply_price_6 = (int)str_replace(',','', securityVal($cd_supply_price_6)); //
	$_cd_supply_price_7 = (int)str_replace(',','', securityVal($cd_supply_price_7)); //
	$_cd_supply_price_8 = (int)str_replace(',','', securityVal($cd_supply_price_8)); //
	$_cd_supply_price_9 = (int)str_replace(',','', securityVal($cd_supply_price_9)); //

	$_cd_sale_price = (int)str_replace(',','', securityVal($cd_sale_price)); //
	$_cd_price = (int)str_replace(',','', securityVal($cd_price)); //

	$_cd_out_price_1 = (int)str_replace(',','', securityVal($cd_out_price_1)); //
	$_cd_out_price_2 = (int)str_replace(',','', securityVal($cd_out_price_2)); //
	$_cd_out_price_3 = (int)str_replace(',','', securityVal($cd_out_price_3)); //
	$_cd_out_price_4 = (int)str_replace(',','', securityVal($cd_out_price_4)); //

	//원산지
	$_cd_coo =  strtolower($_cd_coo);
	
	$_show_bc_thumbnail_w = 302;
	$_show_bc_thumbnail_h = 302;


//이미지 통합변수
$_img_save_dir = "../../data/comparion/";
$_inst_img_move_dir = '../../data/comparion/'; //지워

////////////////////////////////////////////////////////////////////////////////////////////////
// 가격비교 등록
////////////////////////////////////////////////////////////////////////////////////////////////
if($_a_mode == "comparisonNew"){

	$query = "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'ugo65_dgmall' AND TABLE_NAME = '"._DB_COMPARISON."'";
	$result = wepix_query_error($query);
	$tableIdx = wepix_fetch_array($result);
	$_next_idx = $tableIdx[AUTO_INCREMENT];
	$randomNum = mt_rand(1000, 9999);
	$_cd_maching_code = "comparion_".$_next_idx.$randomNum;
	$_b_token = $_cd_maching_code;

	//파일이 있을경우
	if ( $_FILES['cd_img']['name'] ) {
		if ( !$_FILES['cd_img']['error'] ) {

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

	// ---------------- 외부 이미지 일경우 ---------------------
	}elseif( $_out_img ){
	
		// 확장자 가져오기
		$ext = strtolower(pathinfo($_out_img, PATHINFO_EXTENSION));

		// 저장할 이미지명을 정한다.
		$_img_name = $_b_token.'.'.$ext;

		$img_name = "../../data/comparion/".$_img_name;

		$fp = fopen($img_name, 'w'); // 저장할 이미지 위치 및 파일명

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $_out_img );
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		$contents = curl_exec($ch);
		curl_close($ch);

		fwrite($fp,$contents); // 가져올 외부이미지 주소
		fclose($fp);

		$thumbnail_timg = @getimagesize($img_name);
		$thumbnail_size = filesize($img_name);

		//썸네일 리사이징
		if( $thumbnail_timg[0] > $_show_bc_thumbnail_w or $thumbnail_timg[1] > $_show_bc_thumbnail_h ){
			$image = new SimpleImage();
			$image->load($img_name);
			$image->resize($_show_bc_thumbnail_w, $_show_bc_thumbnail_h);
			$image->save($img_name);
		}

		//썸네일이 저장되었으면 DB에 저장한다
		fileReg( $_inst_img_move_dir, $img_name,'comparion', $_b_token, $thumbnail_timg[0], $thumbnail_timg[1], $thumbnail_size, "comparion", "img" );

	}

	// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	// 비교사이트 등록
	//$_cd_link_count = count($_cl_site);
	/*
	//원소중 제일 작은값 찾기
	$choice_cd_price = "";
	$choice_cd_link = "";
	$_cd_link_count = 0;
	for($i=0;$i<count($_cl_site);$i++){
		
		$_this_cl_path = $_cl_path[$i]; //경로
		$_this_cl_price = (int)str_replace(',','',$_cl_price[$i]); //가격
		$_this_cl_sort = $_cl_sort[$i]; //정렬
		$_this_cl_delivery = (int)str_replace(',','',$_cl_delivery[$i]); //배송비
		$_this_cl_memo = $_cl_memo[$i]; //이벤트 사항
		$_this_cl_kind = $_cl_kind[$i]; //구분
		$_this_price_min_best = $_price_min_best[$i]; //최저가 선택

		if( $_this_cl_path ){
			$_dmain_name = getDomainName($_this_cl_path);
			if(strpos($_dmain_name, "loma.xyz") !== false) {  
				$_dmain_name = "loma.xyz";
			}elseif(strpos($_dmain_name, "hongkonggo.kr")){
				$_dmain_name = "hongkonggo.kr";
			}elseif(strpos($_dmain_name, "showdang.kr")){
				$_dmain_name = "dgmall.kr";
			}
		}

		if( $_dmain_name ){
			$site_data = wepix_fetch_array(wepix_query_error("select SD_IDX from "._DB_SITE." where SD_ACTIVE = 'Y' and SD_DOMAIN='".$_dmain_name."'"));
			if($site_data[SD_IDX] && $_this_cl_price > 0 ){
				$_cd_link_count++;

				$query = "insert into  "._DB_COMPARISON_LINK." set
					CL_CD_IDX = '".$_next_idx."',
					CL_SD_IDX = '".$site_data[SD_IDX]."',
					CL_SORT_NUM = '".$_this_cl_sort ."',
					CL_PRICE = '".$_this_cl_price."',
					CL_KIND = '".$_this_cl_kind."',
					CL_DELIVERY_PRICE = '".$_this_cl_delivery."',
					CL_PATH = '".$_this_cl_path."',
					CL_MEMO = '".$_this_cl_memo."'";
				wepix_query_error($query);

				if( $_this_price_min_best == "ok" && $_this_cl_kind != "odp-jpy" ){

					$link_data = wepix_fetch_array(wepix_query_error("select CL_IDX from "._DB_COMPARISON_LINK." where 
						CL_CD_IDX = '".$_next_idx."' and
						CL_SD_IDX = '".$site_data[SD_IDX]."' and
						CL_PRICE = '".$_this_cl_price."' and
						CL_KIND = '".$_this_cl_kind."' and
						CL_DELIVERY_PRICE = '".$_this_cl_delivery."' and
						CL_PATH = '".$_this_cl_path."'
					"));

					$choice_cd_price = $_this_cl_price;
					$choice_cd_link = $_this_cl_path;
					$choice_cd_link_idx = $link_data[CL_IDX];

				}

			}
		}

	}

	if( $choice_cd_price && $choice_cd_link && $choice_cd_link_idx ){
		$_cd_price = $choice_cd_price;
		$_cd_link = $choice_cd_link;
		$_cd_link_idx = $choice_cd_link_idx;
	}
	*/
	// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

	 $total_count = wepix_counter(_DB_COMPARISON,"");
  	 $_cd_sort = $total_count + 1;

	 for($i=0;$i<count($_tg_structure);$i++){
		$tag_data = wepix_fetch_array(wepix_query_error("select * from "._DB_TAG." where TG_IDX = '".$_tg_structure[$i]."'")); 
		$_cd_tag_name[] = $_tg_structure[$i]."/".$tag_data[TG_NAME];
		$query = "insert into  "._DB_COMPARISON_TAG." set
            CT_TYPE = 'COMPARISON',
			CT_CD_IDX = '".$_next_idx."',
			CT_TG_IDX = '".$_tg_structure[$i]."'";
		 $result = wepix_query_error($query);
	 }

		$_ary_cd_tag = implode("│",$_cd_tag_name);

	/* 최저가 노출가격이 없거나 바로가기 링크가 없을경우 */

	/*
				CD_PRICE = '".$_cd_price."',
				CD_LINK  = '".$_cd_link."',
				CD_LINK_COUNT = '".$_cd_link_count."',
				CD_LINK_IDX = '".$_cd_link_idx."',
	*/

	$_cd_size_data = array(
		'W' => $_cd_size_w,
		'H' => $_cd_size_h,
		'D' => $_cd_size_d
	);
	$_cd_size = json_encode($_cd_size_data);


	$_cd_weight_data = array(
		'1' => $_cd_weight_1,
		'2' => $_cd_weight_2,
		'3' => $_cd_weight_3
	);
	$_cd_weight_fn = json_encode($_cd_weight_data);


	$_cd_code_data = array(
		'jan' => $_cd_code,
		'pcode' => $_cd_code2,
		'npg' => $cd_code_npg,
		'rj' => $_cd_code_rj,
		'mg' => $_cd_code_mg,
		'hp' => $_cd_code_hp,
		'dmw' => $_cd_code_dmw
	);
	$_cd_code_fn = json_encode($_cd_code_data);

	if( $_cd_price_th ){
		$_cd_price_data['th'] = $_cd_price_th;
	}
	
	$_cd_price_fn = json_encode($_cd_price_data);

	$query = "insert into  "._DB_COMPARISON." set
		CD_MACHING_CODE = '".$_cd_maching_code."',
		CD_BRAND_IDX = '".$_cl_brand."',
		CD_BRAND2_IDX = '".$_cl_brand2."',
		CD_NAME = '".$_cd_name."',
		CD_NAME_OG = '".$_cd_name_og."',
		CD_NAME_EN = '".$_cd_name_en."',
		CD_KIND_CODE = '".$_cd_kind_code."',
		CD_CONT = '".$_cd_cont."',

		cd_code_fn = '".$_cd_code_fn."',
		CD_CODE = '".$_cd_code."',
		CD_CODE2 = '".$_cd_code2."',
		CD_CATEGORY = '".$_cd_category."',
		CD_SIZE = '".$_cd_size."',
		CD_SIZE2 = '".$_cd_size2."',

		cd_weight_fn = '".$_cd_weight_fn."',

		CD_WEIGHT = '".$_cd_weight."',
		CD_WEIGHT2 = '".$_cd_weight2."',
		CD_COLOR = '".$_cd_color."',
		CD_MEMO = '".$_cd_memo."',
		CD_SUPPLEMENT = '".$_cd_supplement."',
		CD_IMG = '".$img_name."',

		cd_price_fn = '".$_cd_price_fn."',
		CD_SUPPLY_PRICE_1 = '".$_cd_supply_price_1."',
		CD_SUPPLY_PRICE_2 = '".$_cd_supply_price_2."',
		CD_SUPPLY_PRICE_3 = '".$_cd_supply_price_3."',
		CD_SUPPLY_PRICE_4 = '".$_cd_supply_price_4."',
		CD_SUPPLY_PRICE_5 = '".$_cd_supply_price_5."',
		CD_SUPPLY_PRICE_6 = '".$_cd_supply_price_6."',
		CD_SUPPLY_PRICE_7 = '".$_cd_supply_price_7."',
		CD_SUPPLY_PRICE_8 = '".$_cd_supply_price_8."',
		CD_SUPPLY_PRICE_9 = '".$_cd_supply_price_9."',
		cd_sale_price = '".$_cd_sale_price."',

		CD_SEARCH_TERM = '".$_cd_search_term."',
		CD_OUT_PRICE_1 = '".$_cd_out_price_1."',
		CD_OUT_PRICE_2 = '".$_cd_out_price_2."',
		CD_OUT_PRICE_3 = '".$_cd_out_price_3."',
		CD_OUT_PRICE_4 = '".$_cd_out_price_4."',

		CD_HASH_TAG = '".$_ary_cd_tag."',
		CD_SORT = '".$_cd_sort."',

		CD_RELATED_GOODS = '".$_cd_related_goods."',
		CD_RECOMMEND_GOODS = '".$_cd_recommend_goods."',
		CD_UPDATE_DATE = '".$wepix_now_time."',
		CD_REG_DATE = '".$wepix_now_time."',
		CD_RELEASE_DATE = '".$_cd_release_date."',
		CD_COMPARISON = '".$_cd_comparison."' ";
	wepix_query_error($query);

	/*
	for($i=0;$i<count($_cl_site);$i++){

		if( ($_cl_path[$i] ){

			$_dmain_name = getDomainName($_cl_path[$i]);
			if(strpos($_dmain_name, "loma.xyz") !== false) {  
				$_dmain_name = "loma.xyz";
			}elseif(strpos($_dmain_name, "hongkonggo.kr")){
				$_dmain_name = "hongkonggo.kr";
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
			$result = wepix_query_error($query);
		}
	}
	*/

	msg("등록 완료!",_A_PATH_COMPARISON_LIST);


////////////////////////////////////////////////////////////////////////////////////////////////
// 팝업창 정보 수정
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "infoModifyPopup" ){

	// 매칭코드
	if(!$_cd_maching_code){
		$randomNum = mt_rand(1000, 9999);
		$_cd_maching_code = "comparion_".$_idx.$randomNum;
	}

	$_b_token = $_cd_maching_code;

	//----------------------------------------------------------------------------------------------------------------------------------------------------------
	// 파일이 있을경우
	if ( $_FILES['cd_img2']['name'] ) {
		if ( !$_FILES['cd_img2']['error'] ) {
			$_img_name2 = imageUpload($_FILES['cd_img2'], "modify", $_img_save_dir ,"icon" , $_img_name2, "comparion2", $_cd_maching_code,"", "");
		}
	}
	
	if ( $_FILES['cd_img3']['name'] ) {
		if ( !$_FILES['cd_img3']['error'] ) {
			$_img_name3 = imageUpload($_FILES['cd_img3'], "modify", $_img_save_dir ,"invoice" , $_img_name3, "comparion3", $_cd_maching_code,"", "");
		}
	}

	if ( $_FILES['cd_img']['name'] ) {
		if ( !$_FILES['cd_img']['error'] ) {

			$not_del = "";

			//패키지 변경으로 인한 이미지 변경일경우
			if( $_package_change == "Y" ){
				$not_del = "not_del";

				$_change_img_name = str_replace('../../data/comparion/','', $_img_name);
				$_ary_change_img_name = explode(".", $_change_img_name);
				$_img_name4 = $_ary_change_img_name[0]."_package_0.".$_ary_change_img_name[1];

				rename($_img_name, "../../data/comparion/".$_img_name4);
			}

			$_img_name = imageUpload($_FILES['cd_img'], "modify", $_img_save_dir ,"" , $_img_name, "comparion", $_cd_maching_code, "302", "302", $not_del);
			
			/*
			$is_file_exist = file_exists($existing_file);

			//파일이 있을경우 파일삭제
			if ($is_file_exist) {
				@unlink("../../data/comparion/".$_img_name); // 사진파일 삭제
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
				$_img_name = $_b_token.".".$_ary_thumbnail_ext[1];
				$thumbnail_destination = $_inst_img_move_dir."/".$_img_name;
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
				fileReg( $_inst_img_move_dir, $_img_name,'comparion', $_b_token, $thumbnail_timg[0], $thumbnail_timg[1], $thumbnail_size, "comparion", "img" );

			}else{
				msg("이미지만 등록 가능합니다.","");
				exit;
			}
			*/
		} 

	// ---------------- 외부 이미지 일경우 ---------------------
	}elseif( $_out_img ){

		// 확장자 가져오기
		$ext = strtolower(pathinfo($_out_img, PATHINFO_EXTENSION));

		// 저장할 이미지명을 정한다.
		$_img_name = $_b_token.'.'.$ext;

		$img_name = "../../data/comparion/".$_img_name;


		if( $_package_change == "Y" ){

			$not_del = "not_del";

			$_change_img_name = str_replace('../../data/comparion/','', $_img_name);
			$_ary_change_img_name = explode(".", $_change_img_name);
			$_img_name4 = $_ary_change_img_name[0]."_package_0.".$_ary_change_img_name[1];

			rename("../../data/comparion/".$_img_name, "../../data/comparion/".$_img_name4);

		}

		if( $not_del != "not_del" ){

			$is_file_exist = file_exists($_img_name);
			//파일이 있을경우 파일삭제
			if ($is_file_exist) {
				wepix_query_error("delete from ".$_db_file." where FILE_B_CODE ='comparion' and FILE_B_TOKEN = '".$_cd_maching_code."' "); //파일 DB삭제
				@unlink($_img_name); // 사진파일 삭제
			}

		}

		$fp = fopen($img_name, 'w'); // 저장할 이미지 위치 및 파일명

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $_out_img );
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		$contents = curl_exec($ch);
		curl_close($ch);

		fwrite($fp,$contents); // 가져올 외부이미지 주소
		fclose($fp);

		$thumbnail_timg = @getimagesize($img_name);
		$thumbnail_size = filesize($img_name);

		//썸네일 리사이징
		if( $thumbnail_timg[0] > $_show_bc_thumbnail_w or $thumbnail_timg[1] > $_show_bc_thumbnail_h ){
			$image = new SimpleImage();
			$image->load($img_name);
			$image->resize($_show_bc_thumbnail_w, $_show_bc_thumbnail_h);
			$image->save($img_name);
		}

		//썸네일이 저장되었으면 DB에 저장한다
		fileReg( $_inst_img_move_dir, $img_name,'comparion', $_b_token, $thumbnail_timg[0], $thumbnail_timg[1], $thumbnail_size, "comparion", "img" );

	}
	//----------------------------------------------------------------------------------------------------------------------------------------------------------

	$com_tag_result = wepix_query_error("select CT_TG_IDX from "._DB_COMPARISON_TAG." where CT_CD_IDX = '".$_idx."' ");
	while($com_tag_list = wepix_fetch_array($com_tag_result)){
		$_ary_com_tag[] = $com_tag_list[CT_TG_IDX];
	}

	for($i=0;$i<count($_tg_structure);$i++){
		$tag_data = wepix_fetch_array(wepix_query_error("select * from "._DB_TAG." where TG_IDX = '".$_tg_structure[$i]."'")); 
		$_cd_tag_name[] = $_tg_structure[$i]."/".$tag_data[TG_NAME];
		if(!in_array($_tg_structure[$i], $_ary_com_tag)){
			$query = "insert into  "._DB_COMPARISON_TAG." set
				CT_TYPE = 'COMPARISON',
				CT_CD_IDX = '".$_idx."',
				CT_TG_IDX = '".$_tg_structure[$i]."'";
			wepix_query_error($query);
		}
	}

	/*
	if( $_img_name ){
		$ext = strtolower(pathinfo($_img_name, PATHINFO_EXTENSION));
	
		if(strpos($ext,'?') !== false){
			$_ext2 = explode("?", $ext);
			$ext = $_ext2[0];
		}

		$_ary_ck_img_name = explode(".", $_img_name);
		$_img_name = $_ary_ck_img_name[0].".".$ext."?v=".time();
	}
	*/

	$_ary_cd_tag = implode("│",$_cd_tag_name);

	$_cd_size_data = array(
		'W' => $_cd_size_w,
		'H' => $_cd_size_h,
		'D' => $_cd_size_d
	);
	$_cd_size = json_encode($_cd_size_data);

	$_cd_weight_data = array(
		'1' => $_cd_weight_1,
		'2' => $_cd_weight_2,
		'3' => $_cd_weight_3
	);
	$_cd_weight_fn = json_encode($_cd_weight_data);


	$_cd_code_data = array(
		'jan' => $_cd_code,
		'pcode' => $_cd_code2,
		'npg' => $cd_code_npg,
		'rj' => $_cd_code_rj,
		'mg' => $_cd_code_mg,
		'hp' => $_cd_code_hp,
		'dmw' => $_cd_code_dmw,
		'tis' => $_cd_code_tis
	);
	$_cd_code_fn = json_encode($_cd_code_data);


	$_cd_pd_info_data = array(
		'19n' => array(
			'is' => $_cd_pd_info_19n_is,
			'package' => $_cd_pd_info_19n_package
		)
	);
	$_cd_pd_info = json_encode($_cd_pd_info_data);


	$query = "update "._DB_COMPARISON." set
		
		CD_MACHING_CODE = '".$_cd_maching_code."',
		CD_KIND_CODE = '".$_cd_kind_code."',
		cd_sale_price = '".$_cd_sale_price."',
		
		cd_code_fn = '".$_cd_code_fn."',
		CD_CODE = '".$_cd_code."',
		CD_CODE2 = '".$_cd_code2."',
		CD_CODE3 = '".$_cd_code3."',
		CD_MEMO = '".$_cd_memo."',
		CD_MEMO2 = '".$_cd_memo2."',
		CD_BRAND_IDX = '".$_cl_brand."',
		CD_BRAND2_IDX = '".$_cl_brand2."',
		CD_COMPARISON = '".$_cd_comparison."',
		CD_NAME = '".$_cd_name."',
		CD_NAME_OG = '".$_cd_name_og."',
		CD_NAME_EN = '".$_cd_name_en."',
		CD_CONT = '".$_cd_cont."',
		CD_SEARCH_TERM = '".$_cd_search_term."',
		CD_IMG = '".$_img_name."',
		CD_IMG2 = '".$_img_name2."',
		CD_IMG3 = '".$_img_name3."',
		CD_IMG4 = '".$_img_name4."',
		CD_RELEASE_DATE = '".$_cd_release_date."',
		CD_SIZE = '".$_cd_size."',
		CD_SIZE2 = '".$_cd_size2."',
		cd_weight_fn = '".$_cd_weight_fn."',
		CD_WEIGHT = '".$_cd_weight."',
		CD_WEIGHT2 = '".$_cd_weight2."',
		CD_WEIGHT3 = '".$_cd_weight3."',

		CD_PD_INFO = '".$_cd_pd_info."',

		CD_HASH_TAG = '".$_ary_cd_tag."',
		CD_INV_NAME1 = '".$_cd_inv_name1."',
		CD_INV_NAME2 = '".$_cd_inv_name2."',
		CD_INV_MATERIAL = '".$_cd_inv_material."',
		CD_UPDATE_DATE = '".$wepix_now_time."',
		CD_SALE_STATE = '".$_cd_sale_state."',
		CD_COO = '".$_cd_coo."',
		cd_national = '".$_cd_national."'
		where CD_IDX = '".$_idx."'";
	wepix_query_error($query);

	$query = "UPDATE prd_stock SET
		ps_rack_code = '".$_ps_rack_code."'
		where ps_idx = '".$_ps_idx."' ";
	sql_query_error($query);

	//msg("수정 완료!", "popup.comparison_modify.php?idx=".$_idx."&vmode=info&parent_reload=ok");
	msg("수정 완료!", "popup.comparison_modify.php?idx=".$_idx."&vmode=info");




////////////////////////////////////////////////////////////////////////////////////////////////
// 팝업창 가격수정
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_a_mode == "priceModifyPopup" ){

	$_cd_weight_data = array(
		'1' => $_cd_weight_1,
		'2' => $_cd_weight_2,
		'3' => $_cd_weight_3
	);
	$_cd_weight_fn = json_encode($_cd_weight_data);

	if( $_cd_price_th ){
		$_cd_price_data['th'] = $_cd_price_th;
	}
	
	$_cd_price_fn = json_encode($_cd_price_data);

	$query = "update "._DB_COMPARISON." set
		cd_weight_fn = '".$_cd_weight_fn."',
		cd_sale_price = '".$_cd_sale_price."',
		cd_price_fn = '".$_cd_price_fn."',
		cd_national = '".$_cd_national."'
		where CD_IDX = '".$_idx."'";
	wepix_query_error($query);

	//echo "|Processing_Complete|처리완료|";
	//msg("수정 완료!", "popup.comparison_modify.php?idx=".$_idx."&vmode=price");

	$response = array(
		'success' => true,
		'msg' => "완료"
	);

	header('Content-Type: application/json');
	echo json_encode($response); 
	exit;

// ******************************************************************************************************************
// 가격비교 수정
// ******************************************************************************************************************
}elseif($_a_mode == "comparisonModify"){

	$dend2 = explode("-",$cd_update_date);
	$_cd_update_date = mktime(0,0,0,$dend2[1],$dend2[2],$dend2[0]);
	
	$_cd_link_count = count($_cl_site);

//----------------------------------------------------------------------------------------------------------------------------------------------------------
// 파일이 있을경우
	if ( $_FILES['cd_img']['name'] ) {
		if ( !$_FILES['cd_img']['error'] ) {

			$is_file_exist = file_exists("../../data/comparion/".$_img_name);
			
			//파일이 있을경우 파일삭제
			if ($is_file_exist) {
				@unlink("../../data/comparion/".$_img_name); // 사진파일 삭제
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
				$_img_name = $_b_token.".".$_ary_thumbnail_ext[1];
				$thumbnail_destination = $_inst_img_move_dir."/".$_img_name;
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
				fileReg( $_inst_img_move_dir, $_img_name,'comparion', $_b_token, $thumbnail_timg[0], $thumbnail_timg[1], $thumbnail_size, "comparion", "img" );

			}else{
				msg("이미지만 등록 가능합니다.","");
				exit;
			}

		} 
	}
//----------------------------------------------------------------------------------------------------------------------------------------------------------

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// 비교사이트 등록

	$choice_cd_price = "";
	$choice_cd_link = "";
	$_cd_link_count = 0;
/*
	$test = implode("@", $_price_min_best);
	echo $test;
	exit;
*/
	for($i=0;$i<count($_cl_site);$i++){

		$_this_cl_path = $_cl_path[$i]; //경로
		$_this_cl_price = (int)str_replace(',','',$_cl_price[$i]); //가격
		$_this_cl_sort = $_cl_sort[$i]; //정렬
		$_this_cl_delivery = (int)str_replace(',','',$_cl_delivery[$i]); //배송비
		$_this_cl_memo = $_cl_memo[$i]; //이벤트 사항
		$_this_cl_kind = $_cl_kind[$i]; //구분
		$_this_price_min_best = $_price_min_best[$i]; //최저가 선택

		if( $_this_cl_path ){
			$_dmain_name = getDomainName($_this_cl_path);
			if(strpos($_dmain_name, "loma.xyz") !== false) {  
				$_dmain_name = "loma.xyz";
			}elseif(strpos($_dmain_name, "hongkonggo.kr") !== false){
				$_dmain_name = "hongkonggo.kr";
			}elseif(strpos($_dmain_name, "showdang.kr") !== false){
				$_dmain_name = "dgmall.kr";
			}
		}

		if( $_dmain_name ){
		
			$site_data = wepix_fetch_array(wepix_query_error("select SD_IDX from "._DB_SITE." where SD_ACTIVE = 'Y' and SD_DOMAIN='".$_dmain_name."' "));
			
			if($site_data[SD_IDX] && $_this_cl_price > 0 ){
				$_cd_link_count++;

				if( $_cl_idx[$i] == 0 ){
					
					$query = "insert into  "._DB_COMPARISON_LINK." set
						CL_CD_IDX = '".$_idx."',
						CL_SD_IDX = '".$site_data[SD_IDX]."',
						CL_SORT_NUM = '".$_this_cl_sort ."',
						CL_PRICE = '".$_this_cl_price."',
						CL_KIND = '".$_this_cl_kind."',
						CL_DELIVERY_PRICE = '".$_this_cl_delivery."',
						CL_PATH = '".$_this_cl_path."',
						CL_MEMO = '".$_this_cl_memo."'";
					wepix_query_error($query);

					if( $_this_price_min_best == "ok" && $_this_cl_kind != "odp-jpy" ){

						$link_data = wepix_fetch_array(wepix_query_error("select CL_IDX from "._DB_COMPARISON_LINK." where 
							CL_CD_IDX = '".$_next_idx."' and
							CL_SD_IDX = '".$site_data[SD_IDX]."' and
							CL_PRICE = '".$_this_cl_price."' and
							CL_KIND = '".$_this_cl_kind."' and
							CL_DELIVERY_PRICE = '".$_this_cl_delivery."' and
							CL_PATH = '".$_this_cl_path."'
						"));

						$choice_cd_price = $_this_cl_price;
						$choice_cd_link = $_this_cl_path;
						$choice_cd_link_idx = $link_data[CL_IDX];

					}

				}elseif( $_cl_idx[$i] != 0 ){

					$query = "update  "._DB_COMPARISON_LINK." set
						CL_CD_IDX = '".$_idx."',
						CL_SD_IDX = '".$site_data[SD_IDX]."',
						CL_SORT_NUM = '".$_this_cl_sort ."',
						CL_PRICE = '".$_this_cl_price."',
						CL_KIND = '".$_this_cl_kind."',
						CL_DELIVERY_PRICE = '".$_this_cl_delivery."',
						CL_PATH = '".$_this_cl_path."',
						CL_MEMO = '".$_this_cl_memo."'
						where CL_IDX = '".$_cl_idx[$i]."'";
					wepix_query_error($query);

					if( $_this_price_min_best == "ok" && $_this_cl_kind != "odp-jpy" ){

						$choice_cd_price = $_this_cl_price;
						$choice_cd_link = $_this_cl_path;
						$choice_cd_link_idx = $_cl_idx[$i];

					}

				}

			}
		}

	}

	if( $choice_cd_price && $choice_cd_link && $choice_cd_link_idx ){
		$_cd_price = $choice_cd_price;
		$_cd_link = $choice_cd_link;
		$_cd_link_idx = $choice_cd_link_idx;
	}

		$com_tag_result = wepix_query_error("select CT_TG_IDX from "._DB_COMPARISON_TAG." where CT_CD_IDX = '".$_idx."' ");
		while($com_tag_list = wepix_fetch_array($com_tag_result)){
			$_ary_com_tag[] = $com_tag_list[CT_TG_IDX];
		}

		for($i=0;$i<count($_tg_structure);$i++){
			$tag_data = wepix_fetch_array(wepix_query_error("select * from "._DB_TAG." where TG_IDX = '".$_tg_structure[$i]."'")); 
			$_cd_tag_name[] = $_tg_structure[$i]."/".$tag_data[TG_NAME];
			if(!in_array($_tg_structure[$i], $_ary_com_tag)){
				$query = "insert into  "._DB_COMPARISON_TAG." set
					CT_TYPE = 'COMPARISON',
					CT_CD_IDX = '".$_idx."',
					CT_TG_IDX = '".$_tg_structure[$i]."'";
				wepix_query_error($query);
			}
		}

		$_ary_cd_tag = implode("│",$_cd_tag_name);

		$query = "update "._DB_COMPARISON." set
            CD_BRAND_IDX = '".$_cl_brand."',
			CD_NAME = '".$_cd_name."',
			CD_NAME_OG = '".$_cd_name_og."',
			CD_NAME_EN = '".$_cd_name_en."',

			CD_KIND_CODE = '".$_cd_kind_code."',
			CD_CONT = '".$_cd_cont."',
			CD_CODE = '".$_cd_code."',
			CD_CODE2 = '".$_cd_code2."',

			CD_CATEGORY = '".$_cd_category."',
			CD_SIZE = '".$_cd_size."',
			CD_SIZE2 = '".$_cd_size2."',
			CD_WEIGHT = '".$_cd_weight."',
			CD_WEIGHT2 = '".$_cd_weight2."',
			CD_COLOR = '".$_cd_color."',
			CD_MEMO = '".$_cd_memo."',
			CD_SUPPLEMENT = '".$_cd_supplement."',
			CD_IMG = '".$_img_name."',

			CD_SUPPLY_PRICE_1 = '".$_cd_supply_price_1."',
			CD_SUPPLY_PRICE_2 = '".$_cd_supply_price_2."',
			CD_SUPPLY_PRICE_3 = '".$_cd_supply_price_3."',
			CD_SUPPLY_PRICE_4 = '".$_cd_supply_price_4."',
			CD_SUPPLY_PRICE_5 = '".$_cd_supply_price_5."',
			CD_SUPPLY_PRICE_6 = '".$_cd_supply_price_6."',
			CD_SUPPLY_PRICE_7 = '".$_cd_supply_price_7."',
			CD_SUPPLY_PRICE_8 = '".$_cd_supply_price_8."',
			CD_SUPPLY_PRICE_9 = '".$_cd_supply_price_9."',

			cd_sale_price = '".$_cd_sale_price."',
			CD_PRICE = '".$_cd_price."',
			CD_LINK  = '".$_cd_link."',
			CD_LINK_COUNT = '".$_cd_link_count."',
			CD_LINK_IDX = '".$_cd_link_idx."',

			CD_SEARCH_TERM = '".$_cd_search_term."',
			CD_OUT_PRICE_1 = '".$_cd_out_price_1."',
			CD_OUT_PRICE_2 = '".$_cd_out_price_2."',
			CD_OUT_PRICE_3 = '".$_cd_out_price_3."',
			CD_OUT_PRICE_4 = '".$_cd_out_price_4."',

			CD_HASH_TAG = '".$_ary_cd_tag."',

			CD_RELATED_GOODS = '".$_cd_related_goods."',
			CD_RECOMMEND_GOODS = '".$_cd_recommend_goods."',
			CD_UPDATE_DATE = '".$wepix_now_time."',
			CD_RELEASE_DATE = '".$_cd_release_date."',
			CD_COMPARISON = '".$_cd_comparison."'
			where CD_IDX = '".$_idx."'";
		wepix_query_error($query);


	 msg("수정완료",_A_PATH_COMPARISON_REG."?mode=modify&key=".$_idx."&return_query_string_list=".urlencode($_return_query_string_list));

// ******************************************************************************************************************
// 가격비교 수정
// ******************************************************************************************************************
}elseif( $_a_mode == "comparisonModifyOut" ){

	$_cl_site = securityVal($cl_site);
	$_cl_idx = securityVal($cl_idx);
	$_cl_sort = securityVal($cl_sort);
	$_cl_price = securityVal($cl_price);
	$_cl_delivery = securityVal($cl_delivery);
	$_cl_path = securityVal($cl_path);
	$_cl_memo = securityVal($cl_memo);
	$_cl_kind = securityVal($cl_kind);

// ******************************************************************************************************************
// 가격비교 삭제
// ******************************************************************************************************************
}elseif($_a_mode == "comparisonDel"){

	$comparison_data = wepix_fetch_array(wepix_query_error("select CD_MACHING_CODE from "._DB_COMPARISON." where CD_IDX = '".$_idx."' "));
	$stock_data = wepix_fetch_array(wepix_query_error("select ps_idx from prd_stock where ps_prd_idx = '".$_idx."' "));

	if( $stock_data[ps_idx] ){
		$stock_unit_count = wepix_counter("prd_stock_unit", " where psu_stock_idx = '".$stock_data[ps_idx]."'" );
		//일일 데이터가 존재함
		if( $stock_unit_count > 0 ){
			echo "|Processing_cancel|삭제불가 일일 데이터가 존재함/".$_idx."/".$stock_data[ps_idx]."/".$stock_unit_count."|";
			exit;
		}
		//재고 데이터 삭제
		//wepix_query_error("delete from "._DB_COMPARISON." where CD_IDX = '".$_idx."'");
	}

	//관련 이미지 삭제
	$result = wepix_query_error("select * from "._DB_FILE." where FILE_B_CODE = 'comparion' and FILE_B_TOKEN = '".$comparison_data[CD_MACHING_CODE]."' order by FILE_KEY desc");
	while($list = wepix_fetch_array($result)){
		$is_file_exist = file_exists($list[FILE_DIR].$list[FILE_NAME]);
		//파일이 있을경우 파일삭제
		if ($is_file_exist) {
			@unlink($list[FILE_DIR].$list[FILE_NAME]); // 사진파일 삭제
			wepix_query_error("delete from "._DB_FILE." where FILE_KEY ='".$list[FILE_KEY]); //파일 DB삭제
		}
	}

	wepix_query_error("delete from "._DB_COMPARISON." where CD_IDX = '".$_idx."'");

	echo "|Processing_Complete|처리완료|";
	exit;

// ******************************************************************************************************************
// 가격비교 목록 선택 수정
// ******************************************************************************************************************
}elseif($_a_mode == "comparisonListModify"){
	
	$idxArrayText = securityVal($idxArrayText);
	$idxArray = explode(',', $idxArrayText);

	for($i=0;$i<count($idxArray);$i++){

		$_cd_code = ${'cd_code_'.$idxArray[$i]};
		$_cd_code2 = ${'cd_code2_'.$idxArray[$i]};

		$query = "update  "._DB_COMPARISON." set
					CD_COMPARISON = '".$cd_comparison[$i]."',
					CD_CODE = '".$_cd_code."',
					CD_CODE2 = '".$_cd_code2."'
					where CD_IDX = '".$idxArray[$i]."'";
		wepix_query_error($query);
	}

/*
		CD_SIZE = '".$cd_size[$i]."',
		CD_SIZE2 = '".$cd_size2[$i]."',
		CD_WEIGHT = '".$cd_weight[$i]."',
		CD_MEMO = '".$cd_memo[$i]."'
*/

	msg("수정 완료",_A_PATH_COMPARISON_LIST2."?".$_return_url);

// ******************************************************************************************************************
// 가격비교 목록 일괄 변경
// ******************************************************************************************************************
}elseif($_a_mode == "comparisonListModify2"){

	$idxArrayText = securityVal($idxArrayText);
	$idxArray = explode(',', $idxArrayText);
	$_batch_kind_code = securityVal($batch_kind_code);

/*
	$query_text = "";
	if( $_batch_kind_code != "" ){
		$query_text = "";
	}
*/
	if( $_batch_kind_code != "" ){
		for($i=0;$i<count($idxArray);$i++){
			$query = "update  "._DB_COMPARISON." set
						CD_KIND_CODE = '".$_batch_kind_code."'
						where CD_IDX = '".$idxArray[$i]."'";
			wepix_query_error($query);
		}
	}

	msg("수정 완료",_A_PATH_COMPARISON_LIST2."?".$_return_url);

// ******************************************************************************************************************
// 가격비교 목록 연관상품 묶기
// ******************************************************************************************************************
}elseif($_a_mode == "comparisonListRelated"){

	$idxArrayText = securityVal($idxArrayText);
	$_good_mode = securityVal($good_mode);

	$idxArray = explode('|', $idxArrayText);

	if( $_good_mode == "related" ){
		$_colum = "CD_RELATED_GOODS";
	}elseif( $_good_mode == "recommend" ){
		$_colum = "CD_RECOMMEND_GOODS";
	}

	for($i=0;$i<count($idxArray);$i++){

		$_cd_idx = $idxArray[$i];

		$comparison_data = wepix_fetch_array(wepix_query_error("select ".$_colum." from "._DB_COMPARISON." where CD_IDX = '".$_cd_idx."' "));

		$_set_related_goods = $comparison_data[$_colum]."|".$idxArrayText;

		$_ary_related_goods = explode("|", $_set_related_goods);
		//$_ary_related_goods = array_unique($_ary_related_goods);
		
		for($z=0; $z<count($_ary_related_goods); $z++){
			if( $_ary_related_goods[$z] != $_cd_idx ){
				${"_new_related_goods_".$_cd_idx}[] = $_ary_related_goods[$z];
			}else{

			}
		}

		${"_new_related_goods_".$_cd_idx} = array_unique(${"_new_related_goods_".$_cd_idx}); 
		${"_new_related_goods_".$_cd_idx} = array_filter(${"_new_related_goods_".$_cd_idx}); 

		$_related_goods = implode("|", ${"_new_related_goods_".$_cd_idx});

		wepix_query_error("update  "._DB_COMPARISON." set ".$_colum." = '".$_related_goods."' where CD_IDX = '".$_cd_idx."'");
	}

	echo "|Processing_Complete|처리완료|";
	exit;
	 
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
}elseif( $_a_mode == "comparisonSort" ){
		
	$_sort_mode = securityVal($sort_mode);
	$_ary_cd_idx = securityVal($cd_idx);
	$_brand_idx = securityVal($brand_idx);

	//브랜드 순위
	if( $_brand_idx ){

		for($i=0;$i<count($_ary_cd_idx);$i++){
			$_sort_num = $i+1;	
			$query = "update  "._DB_COMPARISON." set
				CD_BRAND_RANK = '".$_sort_num."'
				where CD_IDX = '".$_ary_cd_idx[$i]."'";
			wepix_query_error($query);
		}
		msg("수정완료",_A_PATH_COMPARISON_SORT."?brand_idx=".$_brand_idx);

	}else{

		for($i=0;$i<count($_ary_cd_idx);$i++){
			$_sort_num = $i+1;	
			$query = "update  "._DB_COMPARISON." set
				CD_SORT = '".$_sort_num."'
				where CD_IDX = '".$_ary_cd_idx[$i]."'";
			wepix_query_error($query);
		}
		msg("수정완료",_A_PATH_COMPARISON_SORT."?mode=".$_sort_mode);
	}

// ******************************************************************************************************************
// 퀵 매입가,중량 수정
// ******************************************************************************************************************
}elseif( $_a_mode == "quickPriceModify" ){

	$query = "update "._DB_COMPARISON." set
		CD_WEIGHT = '".$_cd_weight."',
		CD_WEIGHT2 = '".$_cd_weight2."',
		CD_SUPPLY_PRICE_1 = '".$_cd_supply_price_1."',
		CD_SUPPLY_PRICE_2 = '".$_cd_supply_price_2."',
		CD_SUPPLY_PRICE_3 = '".$_cd_supply_price_3."',
		CD_SUPPLY_PRICE_4 = '".$_cd_supply_price_4."',
		CD_SUPPLY_PRICE_5 = '".$_cd_supply_price_5."',
		CD_SUPPLY_PRICE_6 = '".$_cd_supply_price_6."',
		CD_SUPPLY_PRICE_7 = '".$_cd_supply_price_7."',
		CD_SUPPLY_PRICE_8 = '".$_cd_supply_price_8."',
		CD_SUPPLY_PRICE_9 = '".$_cd_supply_price_9."'
		where CD_IDX = '".$_idx."'";
	wepix_query_error($query);

	echo "|Processing_Complete|처리완료|";





// ******************************************************************************************************************
// 팝업창 가격비교 수정
// ******************************************************************************************************************
}elseif( $_a_mode == "comparisonModifyPopup" ){

	$choice_cd_price = "";
	$choice_cd_link = "";
	$_cd_link_count = 0;


	for($i=0;$i<count($_cl_site);$i++){
	
		$_this_cl_path = $_cl_path[$i]; //경로
		$_this_cl_price = (int)str_replace(',','',$_cl_price[$i]); //가격

		$_this_cl_sort = $_cl_sort[$i]; //정렬
		$_this_cl_delivery = (int)str_replace(',','',$_cl_delivery[$i]); //배송비
		$_this_cl_memo = $_cl_memo[$i]; //이벤트 사항
		$_this_cl_kind = $_cl_kind[$i]; //구분
		$_this_price_min_best = $_price_min_best[$i]; //최저가 선택

		if( $_this_cl_path ){
			$_dmain_name = getDomainName($_this_cl_path);
			if(strpos($_dmain_name, "loma.xyz") !== false) {  
				$_dmain_name = "loma.xyz";
			}elseif(strpos($_dmain_name, "hongkonggo.kr") !== false){
				$_dmain_name = "hongkonggo.kr";
			}elseif(strpos($_dmain_name, "dgmall.kr") !== false){
				$_dmain_name = "showdang.kr";
			}
		}


	
		if( $_dmain_name ){
		
			$site_data = wepix_fetch_array(wepix_query_error("select SD_IDX from "._DB_SITE." where SD_ACTIVE = 'Y' and SD_DOMAIN='".$_dmain_name."' "));

		//echo "수정완료".$site_data[SD_IDX]."/".$_dmain_name."/".$_this_cl_price."/".$_cl_idx[$i];

			if( $site_data[SD_IDX] && $_this_cl_price > 0 ){
				$_cd_link_count++;

				if( $_cl_idx[$i] == 0 ){

					$query = "insert into  "._DB_COMPARISON_LINK." set
						CL_CD_IDX = '".$_idx."',
						CL_SD_IDX = '".$site_data[SD_IDX]."',
						CL_SORT_NUM = '".$_this_cl_sort ."',
						CL_PRICE = '".$_this_cl_price."',
						CL_KIND = '".$_this_cl_kind."',
						CL_DELIVERY_PRICE = '".$_this_cl_delivery."',
						CL_PATH = '".$_this_cl_path."',
						CL_MEMO = '".$_this_cl_memo."'";
					wepix_query_error($query);

					wepix_query_error("update "._DB_SITE." set SD_CLINK_COUNT = SD_CLINK_COUNT + 1 where SD_IDX = '".$site_data[SD_IDX]."' ");

					if( $_this_price_min_best == "ok" && $_this_cl_kind != "odp-jpy" ){

						$link_data = wepix_fetch_array(wepix_query_error("select CL_IDX from "._DB_COMPARISON_LINK." where 
							CL_CD_IDX = '".$_next_idx."' and
							CL_SD_IDX = '".$site_data[SD_IDX]."' and
							CL_PRICE = '".$_this_cl_price."' and
							CL_KIND = '".$_this_cl_kind."' and
							CL_DELIVERY_PRICE = '".$_this_cl_delivery."' and
							CL_PATH = '".$_this_cl_path."'
						"));

						$choice_cd_price = $_this_cl_price;
						$choice_cd_link = $_this_cl_path;
						$choice_cd_link_idx = $link_data[CL_IDX];

					}

				}elseif( $_cl_idx[$i] != 0 ){

					$query = "update  "._DB_COMPARISON_LINK." set
						CL_CD_IDX = '".$_idx."',
						CL_SD_IDX = '".$site_data[SD_IDX]."',
						CL_SORT_NUM = '".$_this_cl_sort ."',
						CL_PRICE = '".$_this_cl_price."',
						CL_KIND = '".$_this_cl_kind."',
						CL_DELIVERY_PRICE = '".$_this_cl_delivery."',
						CL_PATH = '".$_this_cl_path."',
						CL_MEMO = '".$_this_cl_memo."'
						where CL_IDX = '".$_cl_idx[$i]."'";
					wepix_query_error($query);



					if( $_this_price_min_best == "ok" && $_this_cl_kind != "odp-jpy" ){

						$choice_cd_price = $_this_cl_price;
						$choice_cd_link = $_this_cl_path;
						$choice_cd_link_idx = $_cl_idx[$i];

					}
				
				}
			}
		}

	} // for END


	//삭제
	if( count($_cl_site_del) > 0 ){
		for($i=0; $i<count($_cl_site_del); $i++){
			if( $_cl_site_del[$i] ){
				$comparison_link_data = wepix_fetch_array(wepix_query_error("select CL_SD_IDX from "._DB_COMPARISON_LINK." where CL_IDX='".$_cl_site_del[$i]."' "));
				wepix_query_error("update "._DB_SITE." set SD_CLINK_COUNT = SD_CLINK_COUNT - 1 where SD_IDX = '".$comparison_link_data[CL_SD_IDX]."' ");
				wepix_query_error("delete from "._DB_COMPARISON_LINK." where CL_IDX = '".$_cl_site_del[$i]."' ");
				$_cd_link_count--;
			}
		} // for END
	}

	if( $choice_cd_price && $choice_cd_link && $choice_cd_link_idx ){
		$_cd_price = $choice_cd_price;
		$_cd_link = $choice_cd_link;
		$_cd_link_idx = $choice_cd_link_idx;
	}


	$query = "update "._DB_COMPARISON." set
		CD_PRICE = '".$_cd_price."',
		CD_LINK  = '".$_cd_link."',
		CD_LINK_COUNT = '".$_cd_link_count."',
		CD_LINK_IDX = '".$_cd_link_idx."'
		where CD_IDX = '".$_idx."'";
	wepix_query_error($query);

	//wepix_query_error("update "._DB_COMPARISON." set CD_LINK_COUNT = '".$_cd_link_count."'  where CD_IDX = '".$_idx."'");

	msg("수정 완료!", "popup.comparison_modify.php?idx=".$_idx."&vmode=comparison");


// ******************************************************************************************************************
// 싸이트 등록
// ******************************************************************************************************************
}elseif($_a_mode == "siteNew"){

	$_sd_name = securityVal($sd_name);
	$_sd_rank = securityVal($sd_rank);
    $_sd_kind= securityVal($sd_kind);
	//$_sd_logo = securityVal($sd_logo);
	$_sd_domain = securityVal($sd_domain);
	$_sd_view = securityVal($sd_view);
	$_sd_delivery = securityVal($sd_delivery);
	$_sd_memo = securityVal($sd_memo);
	$_sd_list_active = securityVal($sd_list_active);
	$_sd_join_coupon = securityVal($sd_join_coupon);
	$_sd_delivery_free = (int)str_replace(',', '', securityVal($sd_delivery_free));
	$_sd_delivery_time = securityVal($sd_delivery_time);

	$_file = $_FILES['sd_logo'];

	if ( $_file['name'] ) {
		if ( !$_file['error'] ) {

			if( file_image_check($_file) == "false" ){
				msg("이미지만 등록 가능합니다.","");
				exit;
			}

			$_tmp_file = $_file['tmp_name'];
			$_file_info = @getimagesize($_tmp_file);
			$_file_size = filesize($_tmp_file);

			$_save_file_name = file_change_name($_file, $_site_token);
			move_uploaded_file($_tmp_file, $_inst_img_dir."/".$_save_file_name);

			//DB에 저장한다
			fileReg( $_inst_img_dir, $_save_file_name, "", $_site_token, $_file_info[0], $_file_info[1], $_file_size, "site_logo", "img" );

			$_sd_logo = $_save_file_name;
		}
	}

	$query = "insert into "._DB_SITE." set
		SD_NAME = '".$_sd_name."',
		SD_RANK = '".$_sd_rank."',
		SD_KIND = '".$_sd_kind."',
		SD_LOGO = '".$_sd_logo."',
		SD_DOMAIN = '".$_sd_domain."',
		SD_DELIVERY = '".$_sd_delivery."',
		SD_DELIVERY_FREE = ".$_sd_delivery_free.",
		SD_DELIVERY_TIME = '".$_sd_delivery_time."',
		SD_MEMO = '".$_sd_memo."',
		SD_ACTIVE ='".$_sd_view."',
		SD_LIST_ACTIVE = '".$_sd_list_active."',
		SD_JOIN_COUPON = '".$_sd_join_coupon."',
		REG_DATE = '".time()."' ";
	wepix_query_error($query);

	msg("등록 완료!",_A_PATH_SITE_LIST);

// ******************************************************************************************************************
// 싸이트 수정
// ******************************************************************************************************************
}elseif($_a_mode == "siteModify"){
    
    $_sd_name = securityVal($sd_name);
	$_sd_rank = securityVal($sd_rank);
    $_sd_kind= securityVal($sd_kind);
	$_sd_domain = securityVal($sd_domain);
	$_sd_view = securityVal($sd_view);
	$_sd_delivery = securityVal($sd_delivery);
	$_sd_memo = securityVal($sd_memo);
	$_sd_list_active = securityVal($sd_list_active);
	$_sd_join_coupon = securityVal($sd_join_coupon);
	$_site_token = securityVal($site_token);
	$_sd_delivery_free = (int)str_replace(',', '', securityVal($sd_delivery_free));
	//$_sd_logo = securityVal($sd_logo);
	$_modify_sd_logo = securityVal($modify_sd_logo);
	$_sd_delivery_time = securityVal($sd_delivery_time);

	$_file = $_FILES['sd_logo'];

	if ( $_file['name'] ) {
		if ( !$_file['error'] ) {

			if( file_image_check($_file) == "false" ){
				msg("이미지만 등록 가능합니다.","");
				exit;
			}

			$_tmp_file = $_file['tmp_name'];
			$_file_info = @getimagesize($_tmp_file);
			$_file_size = filesize($_tmp_file);

			$_save_file_name = file_change_name($_file, $_site_token);
			move_uploaded_file($_tmp_file, $_inst_img_dir."/".$_save_file_name);

			//DB에 저장한다
			fileReg( $_inst_img_dir, $_save_file_name, "", $_site_token, $_file_info[0], $_file_info[1], $_file_size, "site_logo", "img" );

			$_modify_sd_logo = $_save_file_name;
		}
	}

	$query = "update "._DB_SITE." set
		SD_NAME = '".$_sd_name."',
		SD_RANK = '".$_sd_rank."',
		SD_KIND = '".$_sd_kind."',
		SD_LOGO = '".$_modify_sd_logo."',
		SD_DOMAIN = '".$_sd_domain."',
		SD_DELIVERY = '".$_sd_delivery."',
		SD_DELIVERY_FREE = ".$_sd_delivery_free.",
		SD_DELIVERY_TIME = '".$_sd_delivery_time."',
		SD_MEMO = '".$_sd_memo."',
		SD_ACTIVE ='".$_sd_view."',
		SD_LIST_ACTIVE = '".$_sd_list_active."',
		SD_JOIN_COUPON = '".$_sd_join_coupon."',
		MOD_DATE = '".time()."',
		SITE_TOKEN = '".$_site_token."'
		where SD_IDX = '".$_idx."'";
	wepix_query_error($query);

	msg("수정완료",_A_PATH_SITE_REG."?mode=modify&key=".$_idx);

// ******************************************************************************************************************
// 상품 코멘트 쓰기
// ******************************************************************************************************************
}elseif($_a_mode == "commWrite"){

	$_pd_key = securityVal($pd_key);
	$_comm_mode = securityVal($comm_mode);
	$_comm_name = securityVal($comm_name);
	$_comm_body = mysqli_real_escape_string($connect, $comm_body);
	$_comm_ip_show = securityVal($comm_ip_show);

	$min_no = wepix_fetch_array(wepix_query_error("select min(HEADNUM) from COMPARISON_COMMENT where PD_UID = '".$_pd_key."' AND HEADNUM > 1000"));
	$temp_headnum = ( $min_no[0] ) ? $min_no[0] - 100 : 2000000000;
	$temp_depth = 0;

	$query = "insert COMPARISON_COMMENT set
			HEADNUM = '".$temp_headnum."',
			DEPTH = '".$temp_depth."',
			COMMENT_MODE = '".$_comm_mode."',
			PD_UID = '".$_pd_key."',
			COMMENT_ID = '".$_sess_id."',
			COMMENT_NAME = '".$_comm_name."',
			COMMENT_PW = '',
			COMMENT_BODY = '".$_comm_body."',
			COMMENT_IP = '".$check_ip."',
			COMMENT_IP_SHOW = '".$_comm_ip_show."',
			COMMENT_DATE = '".$check_time."',
			COMMENT_ADMIN = 'Y',
			KIND_CODE = '".$_comm_kind_code."',
			DOMAIN = '".$check_domain."' ";
	wepix_query_error($query);

    if( $_ajax_mode == "on" ){
		echo "|Processing_Complete|등록완료|";
		exit;
    }else{
		exit;
	}

// ******************************************************************************************************************
// 싸이트 삭제
// ******************************************************************************************************************
}elseif($_a_mode == "siteDel"){

	$query = "delete from "._DB_SITE." where SD_IDX = '".$_idx."'";

	$result = wepix_query_error($query);
	 	echo "|Processing_Complete|처리완료|";

// ******************************************************************************************************************
// 알람 설정
// ******************************************************************************************************************
}elseif( $_a_mode=="stockAlarmSet" ){

	$_prd_key = securityVal($prd_key);
	$_ps_alarm_yn = securityVal($alarmYN);
	$_ps_alarm_count = securityVal($alarmCount);
	$_ps_alarm_message = securityVal($alarmMassage);
	
	$_ps_alarm_count_text = implode(",",$_ps_alarm_count);
	$_ps_alarm_message_text = implode(",",$_ps_alarm_message);

	$updateQuery = "
		UPDATE prd_stock SET 
			ps_alarm_yn  = '".$_ps_alarm_yn."', 
			ps_alarm_count  = '".$_ps_alarm_count_text."', 
			ps_alarm_message  = '".$_ps_alarm_message_text."'
		WHERE ps_prd_idx = ".$_prd_key."
	   ";	

	   wepix_query_error($updateQuery);
	echo "성공";
}
exit;
?>
