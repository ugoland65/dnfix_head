<?
	$pageGroup = "board";
	$pageName = "board_list";

	include "../lib/inc_common.php";

	$_s_active = securityVal($s_active);
	$_s_kind = securityVal($s_kind);
	$_s_text = securityVal($s_text);
	
	include 'inc.php';

	//권한
	//회원전용 게시판
	if( $_show_bc_access_list_mode == "Member" ){
		if(!$_sess_id){
			msg("회원전용 게시판 입니다.", _PATH_LOGIN."?returnUrl=".$check_request_uri_urlencode);
			exit;
		}
		//접근레벨 설정이 되있을 경우 
		if( $_show_bc_access_list_level > 0 AND $_user_level < $_show_bc_access_list_level ){
			msg("(".$_show_bc_access_list_level."레벨) 이상 회원전용 게시판 입니다.", _PATH_MAIN);
			exit;
		}
	}

	//글쓰기 버튼
	$_show_board_write_btn = "off";
	if( $_show_bc_access_write_mode == "Member" ){
		if( $_show_bc_access_write_level > 0 AND $_user_level >= $_show_bc_access_write_level ){
			$_show_board_write_btn = "on";
		}
	}

	$search_sql = " WHERE UID > 0 ";

	//검색이 있을경우
	if( $_s_active == "on" AND $_s_text != "" ){
		if( $_s_kind == "subject_body" ){
			if (preg_match("/[a-zA-Z]/", $_s_text)){
				$search_sql .= " AND INSTR(LOWER(BOARD_SUBJECT), LOWER('".$_s_text."')) OR INSTR(LOWER(BOARD_BODY), LOWER('".$_s_text."')) ";
			}else{
				$search_sql .= " AND INSTR(LOWER(BOARD_SUBJECT), '".$_s_text."') OR INSTR(LOWER(BOARD_BODY), LOWER('".$_s_text."')) ";
			}
		}elseif( $_s_kind == "subject" ){
			if (preg_match("/[a-zA-Z]/", $_s_text)){
				$search_sql .= " AND INSTR(LOWER(BOARD_SUBJECT), LOWER('".$_s_text."')) ";
			}else{
				$search_sql .= " AND INSTR(LOWER(BOARD_SUBJECT), '".$_s_text."') ";
			}
		}elseif( $_s_kind == "writer_name" ){
			if (preg_match("/[a-zA-Z]/", $_s_text)){
				$search_sql .= " AND INSTR(LOWER(BOARD_WITER_NAME), LOWER('".$_s_text."')) ";
			}else{
				$search_sql .= " AND INSTR(LOWER(BOARD_WITER_NAME), '".$_s_text."') ";
			}
		}
	}

	$total_count = wepix_counter(_DB_BOARD, $search_sql);

	$list_num = $_show_bc_list_num;
	$page_num = 10;

	// 전체 페이지 계산
	$total_page  = @ceil($total_count / $list_num);

	// 페이지가 없으면 첫 페이지 (1 페이지)
	if ($_c_page == "") $_c_page = 1;
	$from_record = ($_c_page - 1) * $list_num;
	$counter = $total_count - (($_c_page - 1) * $list_num);
	$_view_no = $counter;

	$query = "select * from "._DB_BOARD." ".$search_sql." order by HEADNUM asc limit ".$from_record.", ".$list_num;
	$result = wepix_query_error($query);

	$paging_url = "list.php?b_code=".$_bac_code."&ct=".$_show_b_category."&s_active=".$_s_active."&s_kind=".$_s_kind."&s_text=".$_s_text."&c_page=";
	$_view_paging = publicPaging($_c_page, $total_page, $list_num, $page_num, $paging_url);

	if( $_view_foot_list_mode != "on" ){
		include "../layout/header.php";
	}

	include $docRoot."/skin/board_skin/".$_show_board_skin."/list.html";

	if( $_view_foot_list_mode != "on" ){
		include "../layout/footer.php";
	}
?>