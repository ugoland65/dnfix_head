<?
	include "../lib/inc_common.php";

	$_action_mode = securityVal($a_mode);
	$_ajax_mode = securityVal($ajax_mode);

	$_pd_key = securityVal($pd_key);
	$_reply_mode = securityVal($reply_mode);
	$_reply_key = securityVal($reply_key);

	$_comm_mode = securityVal($comm_mode);
	$_comm_write_name = securityVal($comm_write_name);
	$_comm_date_modify = securityVal($comm_date_modify);
	$_comm_date_day = securityVal($comm_date_day);
	$_comm_date_h = securityVal($comm_date_h);
	$_comm_date_s = securityVal($comm_date_s);


	if (substr_count($comm_body, '&#') > 50) {
		if( $_ajax_mode == "on" ){
			echo "|Erorr|내용에 올바르지 않은 코드가 다수 포함되어 있습니다.|";
			exit;
		}else{
			msg("내용에 올바르지 않은 코드가 다수 포함되어 있습니다.","");
			exit;
		}
	}

	$_comm_body = securityBoBodyVal($comm_body);

////////////////////////////////////////////////////////////////////////////////////////////////
// 댓글 신규작성
////////////////////////////////////////////////////////////////////////////////////////////////
if( $_action_mode == "commWrite" ){

	$_comm_name = securityVal($_ad_nick);
	$_comment_admin = "Y";

	if( $_comm_mode == "IG" ){
		$_comm_name = securityVal($_comm_write_name);
		$_comment_admin = "N";
	}

	if ( !$_comm_name ){
		if( $_ajax_mode == "on" ){
			echo "|Erorr|작성자를 입력해주세요|";
			exit;
		}else{
			msg("작성자를 입력해주세요","");
			exit;
		}
	}

	if ( !$_comm_body ){
		if( $_ajax_mode == "on" ){
			echo "|Erorr|내용을 입력해주세요|";
			exit;
		}else{
			msg("내용을 입력해주세요","");
			exit;
		}
	}

	//답변달기 모드일경우
	if( $_reply_mode == "on" AND $_reply_key ){

		//원글 불러오기
		$reply_data = wepix_fetch_array(wepix_query_error("select HEADNUM, DEPTH from COMPARISON_COMMENT WHERE COMMENT_IDX = '".$_reply_key."' "));

		$first_headnum = $reply_data[HEADNUM];
		$last_headnum = intval($first_headnum/100);
		$last_headnum = ($last_headnum * 100) + 99;

		$max_no = wepix_fetch_array(wepix_query_error("select max(HEADNUM) from COMPARISON_COMMENT where COMMENT_IDX = '".$_reply_key."' AND HEADNUM > ".$first_headnum." AND HEADNUM < ".$last_headnum." "));
		$temp_headnum = ( $max_no[0] ) ?  $max_no[0] + 1 : $reply_data[HEADNUM] + 1;

		$temp_depth = $reply_data[DEPTH] + 1;
/*
		echo "|Processing_Complete|".$reply_data[HEADNUM]."(".$temp_headnum.")/".$reply_data[DEPTH]."(".$temp_depth.")|";
		exit;
*/
	}else{

		if(!$temp_depth) $temp_depth = 0;
		if(!$_mom_key) $_mom_key = 0;

		if( $temp_depth == 1 && $_mom_headnum ){
			$first_headnum = $_mom_headnum;
			$last_headnum = $_mom_headnum + 100;
			$max_no = wepix_fetch_array(wepix_query_error("select max(HEADNUM) from COMPARISON_COMMENT where PD_UID = '".$_pd_key."' AND HEADNUM > ".$first_headnum." AND HEADNUM < ".$last_headnum." "));
			$temp_headnum = ( $max_no[0] ) ?  $max_no[0] + 1 : $_mom_headnum + 1;
		}else{
			$min_no = wepix_fetch_array(wepix_query_error("select min(HEADNUM) from COMPARISON_COMMENT where PD_UID = '".$_pd_key."' AND HEADNUM > 1000"));
			$temp_headnum = ( $min_no[0] ) ? $min_no[0] - 100 : 2000000000;
		}
	}

	//등록시간 지정시
	if( $_comm_date_modify == "Y" ){
		$_ary_comm_date_day = explode("-", $_comm_date_day);
		$yy = $_ary_comm_date_day[0];
		$mm = $_ary_comm_date_day[1];
		$dd = $_ary_comm_date_day[2];
		$hh = $_comm_date_h;
		$ii = $_comm_date_s;
		$_register_date = mktime($hh, $ii, 0, $mm, $dd, $yy);
		
	}else{
		$_register_date = $check_time;
		
	}

	$query = "insert COMPARISON_COMMENT set
				HEADNUM = '".$temp_headnum."',
				DEPTH = '".$temp_depth."',
				COMMENT_MODE = '".$_comm_mode."',
				PD_UID = '".$_pd_key."',
				COMMENT_ID = '".$_ad_id."',
				COMMENT_NAME = '".$_comm_name."',
				COMMENT_BODY = '".$_comm_body."',
				COMMENT_IP = '".$check_ip."',
				COMMENT_DATE = '".$_register_date."',
				COMMENT_ADMIN = '".$_comment_admin."' ";
	wepix_query_error($query);

    // 코멘트 총 갯수 넣기
    //wepix_query_error("update "._DB_BOARD." set BOARD_COMMENT = BOARD_COMMENT + 1 where UID = '".$_pd_key."' " );

    if( $_ajax_mode == "on" ){
		echo "|Processing_Complete|등록완료|";
		exit;
    }else{
		exit;
	}

////////////////////////////////////////////////////////////////////////////////////////////////
// 코멘트 삭제
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $a_mode=="commDel" ){

	if ( !$_c_key ){
		if( $_ajax_mode == "on" ){
			echo "|Erorr|잘못된 접근입니다.|";
			exit;
		}else{
			msg("잘못된 접근입니다.","");
			exit;
		}
	}

	$comment_data = wepix_fetch_array(wepix_query_error("select COMMENT_IDX from COMPARISON_COMMENT WHERE COMMENT_IDX = '".$_c_key."' "));

	//데이터가 존재 할경우만
	if( $comment_data[COMMENT_IDX] ){

		wepix_query_error(" delete from COMPARISON_COMMENT where COMMENT_IDX = '".$_c_key."' ");

		// 코멘트 총 갯수 감소
		//ttt_query_error("update "._DB_BOARD." set board_comment = board_comment - 1 where uid = '".$_board_key."' " );

		//코멘트 다시 카운팅
		$comment_count = wepix_counter(_DB_BOARD_COMMENT, " WHERE PD_UID='".$_pd_key."' ");
		wepix_query_error("update "._DB_BOARD." set BOARD_COMMENT='".$comment_count."' where UID = '".$_pd_key."' " );

	}

    if( $_ajax_mode == "on" ){
		echo "|Processing_Complete|삭제완료|";
		exit;
    }else{
		exit;
	}

}
?>