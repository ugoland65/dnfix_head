<?
include "../lib/inc_common.php";

		$_action_mode = securityVal($action_mode);
		$_b_code = securityVal($b_code);
		$_board_key = securityVal($mokey);
		$_wmode_html = securityVal($wmode_html);
		$_useMail = securityVal($useMail);
		$_usePhone = securityVal($usePhone);
		$_imgsavefolder = securityVal($imgsavefolder);
		$_board_subject = securityVal($board_subject);
		$_board_witer_name = securityVal($board_witer_name);		
		$_board_date_s = securityVal($board_date_s);
		$_board_category = securityVal($board_category);
		$_board_notice = securityVal($board_notice);
		$_board_secret = securityVal($board_secret);	
		$_board_hit = securityVal($board_hit);
		$_board_vote = securityVal($board_vote);
		$_board_grade = securityVal($board_grade);
		$_board_body = securityVal($board_body);
		$_board_youtube = securityVal($board_youtube);
		$_file_thum = securityVal($file_thum);
		$_this_board_table = "BOARD_".$_b_code;
        $_this_bcomment_table  = "BOARD_COMMENT_".$_b_code;
        $_board_view_check_table  = "BOARD_VIEW_CHECK";

////////////////////////////////////////////////////////////////////////////////////////////////
// 신규등록
////////////////////////////////////////////////////////////////////////////////////////////////
if( $action_mode=="new"){

   // 공지글 관련해서 번호 정하기, 헤드넘버 정하기
    if( $board_notice == "Y" ) {
        $result = wepix_query_error("select min(headnum) from ".$_this_board_table." where headnum < 1000 and headnum > 1");
        $min_no = wepix_fetch_array($result);
        if($min_no[0]) $temp_headnum = $min_no[0] - 1; else $temp_headnum = 999;
    } else {
        $result = wepix_query_error("select min(headnum) from ".$_this_board_table." where headnum > 1000");
        $min_no = wepix_fetch_array($result);
        if($min_no[0]) $temp_headnum = $min_no[0] - 100; else $temp_headnum = 2000000000;
    }


	
	$temp_depth = 0;

	$board_date_day_array = explode("-", $board_date_day);
	$yy = $board_date_day_array[0];
	$mm = $board_date_day_array[1];
	$dd = $board_date_day_array[2];
	$hh = $board_date_h;
	$ii = $board_date_i;
	$register_date = mktime($hh, $ii, 0, $mm, $dd, $yy);

if( !$register_date ) $register_date = time();

		$query = "insert ".$_this_board_table." set
					HEADNUM = '".$temp_headnum."',
					DEPTH = '".$temp_depth."',
					BOARD_WITER_ID = '".$_ad_id."',
					BOARD_WITER_NAME = '".$_board_witer_name."',
					BOARD_PASS = '',
					BOARD_SUBJECT = '".$_board_subject."',
					BOARD_DATE = '".$register_date."',
					BOARD_BODY = '".$_board_body."',
					BOARD_CATEGORY = '".$_board_category."',
					BOARD_HIT = '".$_board_hit."',
					BOARD_VOTE = '".$_board_vote."',
					BOARD_GRADE = '".$_board_grade."',
					BOARD_NOTICE = '".$_board_notice."',
					BOARD_SECRET = '".$_board_secret."',
					BOARD_YOUTUBE = '".$_board_youtube."',
					BOARD_PRODUCT = '".$_board_product."',
					WMODE_HTML = '".$_wmode_html."' ";
				
		wepix_query_error($query);

		msg("",_A_PATH_BOARD_LIST."?b_code=".$b_code);
}elseif($action_mode=="new_answer"){

			$query = "insert ".$_this_board_table." set
                    BOARD_UID = '".$uid."' ,
                    COMMENT_ID = '".$_ad_id."' ,
                    COMMENT_BODY = '".$answer."' ,
                    COMMENT_DATE = '".$wepix_now_time."' ";

			wepix_query_error($query);
	
			msg("",_A_PATH_BOARD_LIST."?b_code=".$b_code);

}elseif($action_mode=="modify_answer"){

		 $query = "update ".$_this_board_table." set
                    COMMENT_MO_ID = '".$_ad_id."' ,
                    COMMENT_BODY = '".$answer."' ,
                    COMMENT_MO_DATE = '".$wepix_now_time."' 
                    where BOARD_UID = '".$uid."'";

		wepix_query_error($query);

		msg("",_A_PATH_BOARD_LIST."?b_code=".$b_code);

}
?>