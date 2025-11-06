<?
	include "../lib/inc_common.php";
	//include('../../class/image.php'); //이미지 처리 클래스

	$_action_mode = securityVal($a_mode);
	$_key = securityVal($key);

////////////////////////////////////////////////////////////////////////////////////////////////
// 소환사 댓글 삭제
////////////////////////////////////////////////////////////////////////////////////////////////
if( $_action_mode == "osiCommentDel" ){

	$sc_data = wepix_fetch_array(wepix_query_error("select EAT_IDX, EAT_SUMMONER_NAME from "._DB_OSI_SUMMONER_COMMENT." where EAT_IDX='".$_key."' " ));

	//데이터가 존재 할경우만
	if( $sc_data[EAT_IDX] ){

		wepix_query_error(" delete from "._DB_OSI_SUMMONER_COMMENT." where EAT_IDX = '".$_key."' ");

		$_summoner_name = $sc_data[EAT_SUMMONER_NAME];

		$search_sql = "where EAT_SUMMONER_NAME = '".$_summoner_name."' ";
		$evaluation_sum = wepix_fetch_array(wepix_query_error("select sum(EAT_SCORE) as value6 from "._DB_OSI_SUMMONER_COMMENT." ".$search_sql." "));
		$total_count = wepix_counter(_DB_OSI_SUMMONER_COMMENT, $search_sql);
		$average_grade = round(($evaluation_sum[value6]/$total_count),2);

		$query = "update  "._DB_OSI_SUMMONER." set
			AVERAGE_GRADE = '".$average_grade."',
			COMMENT = '".$total_count."'
			where SUMMONER_NAME = '".$_summoner_name."' ";
		wepix_query_error($query);

	}

	msg("삭제되었습니다.", _A_PATH_OSI_COMMENT_LIST);
	exit;

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

	$_board_body = securityBoBodyVal($board_body);

	if ( !$_board_subject ){
		if( $_ajax_mode == "on" ){
			echo "|Erorr|제목을 입력해주세요|";
			exit;
		}else{
			msg("제목을 입력해주세요","");
			exit;
		}
	}

	$bo_data =  wepix_fetch_array(wepix_query_error("select * from "._DB_BOARD." where UID = '".$_b_key."'"));

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
				$_thumbnail_ext_index = count($_ary_thumbnail_ext) - 1; //파일명에 ( . ) 들어갔을경우 씨부랄

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
		$_inst_img_check = strstr($_board_body, $file_list[file_dir]."/".$file_list[file_name]);
		//이미지를 사용했을경우 경로이동
		if( $_inst_img_check ){
		}else{
			@unlink($file_list[file_dir]."/".$file_list[file_name]);
			wepix_query_error(" delete from "._DB_FILE." where FILE_KEY = '".$file_list[file_key]."' ");
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
				BOARD_MODIFY_DATE = '".$check_time."'
				where UID = '".$_b_key."' ";
	wepix_query_error($query);

	msg("",_A_PATH_BOARD_VIEW."?b_code=".$_b_code."&b_key=".$_b_key);


////////////////////////////////////////////////////////////////////////////////////////////////
// 게시물 삭제
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_action_mode == "boardDel" ){

}
?>