<?
//모바일 접속일 경우
if( $check_mobile == "Y"){
	
	//가격비교 리스트 일경우
	if( $pageName == "comparison_list" ){
		$_tg_code = securityVal($tg_code);
		msg("", "/mobile/comparison/list.php?tg_code=".$_tg_code);

	//가격비교 보기 일경우
	}elseif( $pageName == "comparison_view" ){
		$_key = securityVal($key);
		msg("", "/mobile/comparison/view.php?key=".$_key);

	//검색일경우
	}elseif( $pageName == "comparison_search" ){
		$_search_text = securityVal($search_text);
		msg("", "/mobile/comparison/search.php?search_text=".$_search_text);

	//브랜드 리스트
	}elseif( $pageName == "brand_list" ){
		msg("", "/mobile/brand/brand_list.php");

	//브랜드 보기
	}elseif( $pageName == "brand_view" ){
		$_idx = securityVal($idx);
		msg("", "/mobile/product/brand_view.php?idx=".$_idx);

	//랭킹보기
	}elseif( $pageName == "ranking_view" ){
		$_key = securityVal($key);
		msg("", "/mobile/comparison/ranking.php?key=".$_key);

	//게시판일경우
	}elseif( $pageGroup == "board" ){

		$_b_code = securityVal($b_code);
		$_b_key = securityVal($b_key);

		if( $pageName == "board_list" ){
			msg("", "/mobile/board/list.php?b_code=".$_b_code);
		}elseif( $pageName == "board_view" ){
			msg("", "/mobile/board/view.php?b_code=".$_b_code."&b_key=".$_b_key);
		}

	}

}
?>