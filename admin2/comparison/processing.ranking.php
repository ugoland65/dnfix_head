<?
	include "../lib/inc_common.php";

	$_action_mode = securityVal($a_mode);
	$_ajax_mode = securityVal($ajax_mode);

////////////////////////////////////////////////////////////////////////////////////////////////
// 신규랭킹
////////////////////////////////////////////////////////////////////////////////////////////////
if( $_action_mode == "new_ranking" ){

	$_rank_subject = securityVal($rank_subject);
	$_ranking_key = securityVal($ranking_key);
	$_ranking_change = securityVal($ranking_change);

	$_rank_prd = implode("|", $_ranking_key);
	$_rank_change = implode("|", $_ranking_change);

	$query = "insert prd_ranking set
				rank_subject = '".$rank_subject."',
				rank_prd = '".$_rank_prd."',
				rank_change = '".$_rank_change."',
				rank_date = '".$check_time."' ";
	wepix_query_error($query);

	msg("","ranking_req.php");
	exit;
////////////////////////////////////////////////////////////////////////////////////////////////
// 랭킹 수정
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_action_mode == "modify_ranking" ){

	$_rank_subject = securityVal($rank_subject);
	$_ranking_key = securityVal($ranking_key);
	$_ranking_change = securityVal($ranking_change);
	$_rank_key = securityVal($rank_key);

	$_rank_prd = implode("|", $_ranking_key);
	$_rank_change = implode("|", $_ranking_change);


	$query = "UPDATE prd_ranking SET
					rank_subject = '".$rank_subject."',
					rank_prd = '".$_rank_prd."',
					rank_change = '".$_rank_change."'
				WHERE rank_idx = ".$_rank_key;
	wepix_query_error($query);

	msg("수정 완료","ranking_req.php?mode=modify&key=".$_rank_key);
	exit;

////////////////////////////////////////////////////////////////////////////////////////////////
// 랭킹 삭제
////////////////////////////////////////////////////////////////////////////////////////////////
}elseif( $_action_mode == "rankDel" ){
	$_rank_key = securityVal($idx);
	$query = "delete from prd_ranking WHERE rank_idx = ".$_rank_key;
	wepix_query_error($query);
	echo "|Processing_Complete|삭제완료|";
	exit;
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
exit;
?>