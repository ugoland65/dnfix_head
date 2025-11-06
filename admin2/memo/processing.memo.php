<?
	include "../lib/inc_common.php";

	$_action_mode = securityVal($a_mode);
	$_am_mode = securityVal($am_mode);
	$_am_target_idx = securityVal($am_target_idx);

	$_am_memo = securityBoBodyVal($am_memo);

////////////////////////////////////////////////////////////////////////////////////////////////
// 신규메모작성
////////////////////////////////////////////////////////////////////////////////////////////////
if( $_action_mode == "new" ){

	$query = "insert ADMIN_MEMO set
				AM_MODE = '".$_am_mode."',
				AM_TARGET_IDX = '".$_am_target_idx."',
				AM_MEMO = '".$am_memo."',
				AM_ID = '".$_sess_id."',
				AM_DATE = '".time()."' "; 
	wepix_query_error($query);

	msg("","popup.memo.php?mode=".$_am_mode."&idx=".$_am_target_idx);

////////////////////////////////////////////////////////////////////////////////////////////////
// 게시물 수정
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_action_mode == "modify" ){

	if ( !$_b_key ){
		if( $_ajax_mode == "on" ){
			echo "|Erorr|잘못된 접근입니다.|";
			exit;
		}else{
			msg("잘못된 접근입니다.","");
			exit;
		}
	}

	$_modify_board_thumbnail = securityVal($modify_board_thumbnail);

	if (substr_count($board_body, '&#') > 50) {
		if( $_ajax_mode == "on" ){
			echo "|Erorr|내용에 올바르지 않은 코드가 다수 포함되어 있습니다.|";
			exit;
		}else{
			msg("내용에 올바르지 않은 코드가 다수 포함되어 있습니다.","");
			exit;
		}
	}



	if ( !$_board_subject ){
		if( $_ajax_mode == "on" ){
			echo "|Erorr|제목을 입력해주세요|";
			exit;
		}else{
			msg("제목을 입력해주세요","");
			exit;
		}
	}

	$bo_data = wepix_fetch_array(wepix_query_error("select * from "._DB_BOARD." where UID = '".$_b_key."'"));

	if( !$bo_data[UID] ){
		msg("글이 존재하지 않습니다.\\n\\n글이 삭제되었거나 이동된 경우입니다.","");
		exit;
	}

	//등록시간 지정시
	if( $_board_date_modify == "Y" ){
		$_ary_board_date_day = explode("-", $_board_date_day);
		$yy = $_ary_board_date_day[0];
		$mm = $_ary_board_date_day[1];
		$dd = $_ary_board_date_day[2];
		$hh = $_board_date_h;
		$ii = $_board_date_s;
		$_register_date = mktime($hh, $ii, 0, $mm, $dd, $yy);
	}else{
		$_register_date = $bo_data[BOARD_DATE];
	}

	$_board_name = $bo_data[BOARD_WITER_NAME];
	if( $_board_mode == "IG" ){
		$_board_name = $_board_witer_name;
	}

	if ( !$_board_name ){
		if( $_ajax_mode == "on" ){
			echo "|Erorr|이름을 입력해주세요|";
			exit;
		}else{
			msg("이름을 입력해주세요","");
			exit;
		}
	}

	if ( !$_board_body ){
		msg("내용을 입력해주세요","");
		exit;
	}


	$_inst_img_temp_dir = "../../data/board/temp/".$_temp_folder;
	$_inst_img_move_dir = "../../data/board/".$_b_code."/".$_temp_folder;

	$_board_thumbnail_name = "";

	//썸네일 업로드 기능
	if( $_show_bc_thumbnail_active == "Y" ){

		//썸네일 파일이 있을경우
		if ( $_FILES['board_thumbnail']['name'] ) {
			if ( !$_FILES['board_thumbnail']['error'] ) {

				$thumbnail_tmp_file = $_FILES['board_thumbnail']['tmp_name'];
				$thumbnail_timg = @getimagesize($thumbnail_tmp_file);
				$thumbnail_size = filesize($thumbnail_tmp_file);
				$_ary_thumbnail_ext = explode('.', $_FILES['board_thumbnail']['name']); //확장자 분리
				$_thumbnail_ext_index = count($_ary_thumbnail_ext) - 1; //파일명에 ( . ) 들어갔을경우

				//이미지인지 체크
				if(in_array($thumbnail_timg[2] , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP))){
					$_inst_thumbnail_name = $_b_token."_thumbnail.".$_ary_thumbnail_ext[1];
					$thumbnail_destination = $_inst_img_move_dir."/".$_inst_thumbnail_name;
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
					fileReg( $_inst_img_move_dir, $_inst_thumbnail_name, $_b_code, $_b_token, $thumbnail_timg[0], $thumbnail_timg[1], $thumbnail_size, "board", "thumb" );

				}else{
					msg("이미지만 등록 가능합니다.","");
					exit;
				}

			} //if ( !$_FILES['board_thumbnail']['error'] ) {
		} //if ( $_FILES['board_thumbnail']['name'] ) {
	} //if( $_show_bc_thumbnail_active == "Y" ){

	if( $_board_thumbnail_name == "" AND $_modify_board_thumbnail ){
		$_board_thumbnail_name = $_modify_board_thumbnail;
	}


	//업로드한 이미지가 있을경우
	if( count($_uploadImageFileName) > 0){
		if( !$_b_token ){
			msg("토큰 오류","");
			exit;
		}
		if( !$_temp_folder ){
			msg("템프 폴더 오류","");
			exit;
		}
		for ($i=0; $i<count($_uploadImageFileName); $i++){
			$_inst_img_check = strstr($_board_body, $_uploadImageFileName[$i]);
			$_ary_img_ext = ""; //배열 초기화

			//이미지를 사용했을경우 경로이동
			if( $_inst_img_check ){

				//임시 폴더 였던것을 정상으로 이름변경해준다.
				rename($_inst_img_temp_dir."/".$_uploadImageFileName[$i], $_inst_img_move_dir."/".$_uploadImageFileName[$i]);

				//본문에 이미지 경로를 수정한다
				$_board_body = str_replace($_inst_img_temp_dir."/".$_uploadImageFileName[$i], $_inst_img_move_dir."/".$_uploadImageFileName[$i], $_board_body);
			
				$_inst_img_info = @getimagesize($_inst_img_move_dir."/".$_uploadImageFileName[$i]);
				$_inst_img_size = filesize($_inst_img_move_dir."/".$_uploadImageFileName[$i]);
				$_ary_img_ext = explode('.', $_uploadImageFileName[$i]); //확장자 분리
				$_inst_img_ext_index = count($_ary_img_ext) - 1; //파일명에 ( . ) 들어갔을경우 씨부랄

				//파일이 이동됬으면 DB에 기록한다
				fileReg( $_inst_img_move_dir, $_uploadImageFileName[$i], $_b_code, $_b_token, $_inst_img_info[0], $_inst_img_info[1], $_inst_img_size, "board", "img" );

				//오토 썸네일이고 썸네일 이미지가 없을경우
				if( $_show_bc_thumbnail_auto_active == "Y" AND $_board_thumbnail_name == "" ){
					//썸네일 리사이징
					if( $_inst_img_info[0] > $_show_bc_thumbnail_w or $_inst_img_info[1] > $_show_bc_thumbnail_h ){
						$image = new SimpleImage();
						$image->load($_inst_img_move_dir."/".$_uploadImageFileName[$i]);
						$image->resize($_show_bc_thumbnail_w, $_show_bc_thumbnail_h);
						$image->save($_inst_img_move_dir."/".$_b_token."_thumbnail.".$_ary_img_ext[$_inst_img_ext_index]);

						$_board_thumbnail_name = $_inst_img_move_dir."/".$_b_token."_thumbnail.".$_ary_img_ext[$_inst_img_ext_index];
						$_board_thumbnail_file_name = $_b_token."_thumbnail.".$_ary_img_ext[$_inst_img_ext_index];
						$_board_thumbnail_size = filesize($_board_thumbnail_name);

						//썸네일이 저장되었으면 DB에 저장한다
						fileReg( $_inst_img_move_dir, $_board_thumbnail_file_name, $_b_code, $_b_token, $_show_bc_thumbnail_w, $_show_bc_thumbnail_h, $_board_thumbnail_size, "board", "thumb" );
					}
				} //if( $_board_thumbnail_name == "" ){

			//없으면 삭제한다
			}else{
				@unlink($_inst_img_temp_dir."/".$_uploadImageFileName[$i]);
			}
		} //for END
	}


	//등록된 이미지가 본문에서 제거되었는지 체크할것
	$file_result = wepix_query_error("select * from "._DB_FILE." where FILE_B_CODE= '".$_b_code."' AND FILE_B_TOKEN= '".$_b_token."' AND FILE_MODE = 'img' order by FILE_KEY asc ");
	while($file_list = wepix_fetch_array($file_result)){
		$_inst_img_check = strstr($_board_body, $file_list[FILE_DIR]."/".$file_list[FILE_NAME]);
		//이미지를 사용했을경우 경로이동
		if( $_inst_img_check ){
		}else{
			@unlink($file_list[FILE_DIR]."/".$file_list[FILE_NAME]);
			wepix_query_error(" delete from "._DB_FILE." where FILE_KEY = '".$file_list[FILE_KEY]."' ");
		}
	}

	//상품연동
	$_board_pd_name = $bo_data[BOARD_PD_NAME];
	if( $_show_bc_product_active == "Y" && $bo_data[BOARD_PD_IDX] != $_board_pd_idx ){
		if( $_show_bc_product_mode == "comparison" ){ //가격비교 그룹일경우
			$comparison_data = wepix_fetch_array(wepix_query_error("select CD_NAME from "._DB_COMPARISON." where CD_IDX = '".$_board_pd_idx."' "));
			$_board_pd_name = $comparison_data[CD_NAME];
		}
	}

	$query = "update "._DB_BOARD." set
				BOARD_MODE = '".$_board_mode."',
				BOARD_SUBJECT = '".$_board_subject."',
				BOARD_WITER_NAME = '".$_board_name."',
				BOARD_BODY = '".$_board_body."',
				BOARD_CATEGORY = '".$_board_category."',
				BOARD_DATE = '".$_register_date."',
				BOARD_HIT = '".$_board_hit."',
				BOARD_THUMBNAIL = '".$_board_thumbnail_name."',
				BOARD_MODIFY_ID = '".$_ad_id."',
				BOARD_MODIFY_DATE = '".$check_time."',
				BOARD_PD_IDX = '".$_board_pd_idx."',
				BOARD_PD_NAME = '".$_board_pd_name."',
				BOARD_LINK_NAME = '".$_board_link_name."',
				BOARD_LINK_URL = '".$_board_link_url."',
				BOARD_GRADE = '".$_board_grade."'
				where UID = '".$_b_key."' ";
	wepix_query_error($query);

	//상품연동
	if( $_show_bc_product_active == "Y" ){
	
		//가격비교 그룹일경우
		if( $_show_bc_product_mode == "comparison" ){
		//상품연관 컨텐츠
		}elseif( $_show_bc_product_mode == "comparison_relation" ){
			for ($i=0; $i<count($_ary_relation_pd_idx); $i++){
				if( $_ary_relation_pd_idx > 0 ){
					
					$relation_data = wepix_fetch_array(wepix_query_error("select BR_IDX from BOARD_A_RELATION where 
						BR_TOKEN = '".$_b_token."' and 
						BR_BOARD_CODE = '".$_b_code."' and
						BR_PD_IDX = '".$_ary_relation_pd_idx[$i]."' and 
						BR_MODE = 'comparison_relation'  "));

					if( !$relation_data[BR_IDX] ){
						$query = "insert BOARD_A_RELATION set BR_TOKEN = '".$_b_token."', BR_BOARD_CODE = '".$_b_code."', BR_PD_IDX = '".$_ary_relation_pd_idx[$i]."', BR_MODE = 'comparison_relation' "; 
						wepix_query_error($query);
					}
				}
			}
			//삭제
			for ($i=0; $i<count($_ary_relation_pd_idx_del); $i++){
				wepix_query_error(" delete from BOARD_A_RELATION where BR_IDX = '".$_ary_relation_pd_idx_del[$i]."' ");
			}
		}

	}


	msg("",_A_PATH_BOARD_VIEW."?b_code=".$_b_code."&b_key=".$_b_key);


////////////////////////////////////////////////////////////////////////////////////////////////
// 게시물 삭제
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_action_mode == "boardDel" ){

	if ( !$_b_key ){
		if( $_ajax_mode == "on" ){
			echo "|Erorr|잘못된 접근입니다.|";
			exit;
		}else{
			msg("잘못된 접근입니다.","");
			exit;
		}
	}

	$bo_data =  wepix_fetch_array(wepix_query_error("select * from "._DB_BOARD." where UID = '".$_b_key."'"));
	$_b_token = $bo_data[BOARD_TOKEN];

	if( $bo_data[UID] ){
		wepix_query_error(" delete from "._DB_BOARD." where UID = '".$_b_key."' ");

		// 총 갯수 감소
		wepix_query_error("update "._DB_BOARD_A_CONFIG." set BAC_TOTAL_RECORD = BAC_TOTAL_RECORD - 1 where BOARD_CODE = '".$_b_code."' " );

		// 등록된 이미지 제거 및 파일DB에서 삭제
		$file_result = wepix_query_error("select * from "._DB_FILE." where FILE_B_CODE= '".$_b_code."' AND FILE_B_TOKEN= '".$_b_token."' order by FILE_KEY asc ");
		while($file_list = wepix_fetch_array($file_result)){
			@unlink($file_list[FILE_DIR]."/".$file_list[FILE_NAME]);
			wepix_query_error(" delete from "._DB_FILE." where FILE_KEY = '".$file_list[FILE_KEY]."' ");
			//echo $file_list[FILE_DIR]."/".$file_list[FILE_NAME]."<br>";
		}

		//상품연동
		if( $_show_bc_product_active == "Y" ){
			if( $_show_bc_product_mode == "comparison" ){ //가격비교 그룹일경우
				if( $_show_bc_grade_active == "Y" ){ //평점기능을 사용중일경우 
					if( $_board_pd_idx ){
						//$total_count = wepix_counter($this_board_table, $search_sql);
						$pd_review_sum = wepix_fetch_array(wepix_query_error("select 
							format(avg(BOARD_GRADE),1) as value1,
							count(*) as value2 
							from "._DB_BOARD." where BOARD_PD_IDX = '".$_board_pd_idx."' "));
						
						wepix_query_error("update "._DB_COMPARISON." set 
							CD_REVIEW = '".$pd_review_sum[value2]."',
							CD_SCORE = '".$pd_review_sum[value1]."' 
							where CD_IDX = '".$_board_pd_idx."' ");
					}
				}
			//상품연관 컨텐츠
			}elseif( $_show_bc_product_mode == "comparison_relation" ){
				wepix_query_error(" delete from BOARD_A_RELATION where BR_BOARD_CODE='".$_b_code."' and BR_TOKEN='".$_b_token."' ");
				//$bo_relation_result = wepix_query_error("select * from BOARD_A_RELATION where BR_BOARD_CODE='".$_b_code."' and BR_TOKEN='".$_b_token."' order by BR_IDX desc");
			}
		}

	}

	msg("삭제완료",_A_PATH_BOARD_LIST."?b_code=".$_b_code);

}
?>