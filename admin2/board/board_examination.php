<?
$pageGroup = "board";
$pageName = "board_examination";

	include "../lib/inc_common.php";

	$_view_body = "";

	$bo_c_where = "  ";
	$bo_c_query = "select BAC_NAME, BOARD_CODE from "._DB_BOARD_A_CONFIG." ".$bo_c_where."order by UID desc ";
	$bo_c_result = wepix_query_error($bo_c_query);
	while($bo_c_list = wepix_fetch_array($bo_c_result)){

		//상품 idx 저장필드 검사
		$result = $mysqli->query("SHOW COLUMNS FROM `BOARD_".$bo_c_list[BOARD_CODE]."` LIKE 'BOARD_PD_IDX' ");
		$exist = $result->num_rows > 0;
		if (empty($exist)) { 
			$_view_body .= $bo_c_list[BAC_NAME]." - 상품 idx 저장필드 - (BOARD_PD_IDX) 생성<br>";
			wepix_query_error(" ALTER TABLE `BOARD_".$bo_c_list[BOARD_CODE]."` ADD `BOARD_PD_IDX` int(11) unsigned default '0' not null "); 
		}else{
			$_view_body .= "<br>";
		}

		//상품 상품명 저장필드 검사
		$result = $mysqli->query("SHOW COLUMNS FROM `BOARD_".$bo_c_list[BOARD_CODE]."` LIKE 'BOARD_PD_NAME' ");
		$exist = $result->num_rows > 0;
		if (empty($exist)) { 
			$_view_body .= $bo_c_list[BAC_NAME]." - 상품명 저장필드 - (BOARD_PD_NAME) 생성<br>";
			wepix_query_error(" ALTER TABLE `BOARD_".$bo_c_list[BOARD_CODE]."` ADD `BOARD_PD_NAME` varchar(255) default '' not null "); 
		}else{
			$_view_body .= "<br>";
		}


		//평점 저장필드 검사
		$result = $mysqli->query("SHOW COLUMNS FROM `BOARD_".$bo_c_list[BOARD_CODE]."` LIKE 'BOARD_GRADE' ");
		$exist = $result->num_rows > 0;
		if (empty($exist)) { 
			$_view_body .= $bo_c_list[BAC_NAME]." - 평점 저장필드 - (BOARD_GRADE) 생성<br>";
			wepix_query_error(" ALTER TABLE `BOARD_".$bo_c_list[BOARD_CODE]."` ADD `BOARD_GRADE` tinyint(2) not null "); 
		}else{
			$_view_body .= "<br>";
		}

		//링크 네임 저장필드 검사
		$result = $mysqli->query("SHOW COLUMNS FROM `BOARD_".$bo_c_list[BOARD_CODE]."` LIKE 'BOARD_LINK_NAME' ");
		$exist = $result->num_rows > 0;
		if (empty($exist)) { 
			$_view_body .= $bo_c_list[BAC_NAME]." - 링크 네임 저장필드 - (BOARD_LINK_NAME) 생성<br>";
			wepix_query_error(" ALTER TABLE `BOARD_".$bo_c_list[BOARD_CODE]."` ADD `BOARD_LINK_NAME` varchar(100) default '' not null "); 
		}else{
			$_view_body .= "<br>";
		}

		//링크 URL 저장필드 검사
		$result = $mysqli->query("SHOW COLUMNS FROM `BOARD_".$bo_c_list[BOARD_CODE]."` LIKE 'BOARD_LINK_URL' ");
		$exist = $result->num_rows > 0;
		if (empty($exist)) { 
			$_view_body .= $bo_c_list[BAC_NAME]." - 링크 URL 저장필드 - (BOARD_LINK_URL) 생성<br>";
			wepix_query_error(" ALTER TABLE `BOARD_".$bo_c_list[BOARD_CODE]."` ADD `BOARD_LINK_URL` varchar(255) default '' not null "); 
		}else{
			$_view_body .= "<br>";
		}

		//비회원 노출아이피
		$result = $mysqli->query("SHOW COLUMNS FROM `BOARD_".$bo_c_list[BOARD_CODE]."` LIKE 'BOARD_IP_SHOW' ");
		$exist = $result->num_rows > 0;
		if (empty($exist)) { 
			$_view_body .= $bo_c_list[BAC_NAME]." - 비회원 노출아이피 - (BOARD_IP_SHOW) 생성<br>";
			wepix_query_error(" ALTER TABLE `BOARD_".$bo_c_list[BOARD_CODE]."` ADD `BOARD_IP_SHOW` varchar(100) default '' not null AFTER `BOARD_IP` "); 
		}else{
			$_view_body .= "<br>";
		}

/*
		//추천수
		$result = $mysqli->query("SHOW COLUMNS FROM `BOARD_".$bo_c_list[BOARD_CODE]."` LIKE 'BOARD_RECOM' ");
		$exist = $result->num_rows > 0;
		if (empty($exist)) { 
			$_view_body .= $bo_c_list[BAC_NAME]." - 추천수 - (BOARD_RECOM) 생성<br>";
			wepix_query_error(" ALTER TABLE `BOARD_".$bo_c_list[BOARD_CODE]."` ADD `BOARD_RECOM` SMALLINT(6) NOT NULL AFTER `BOARD_HIT` "); 
		}else{
			$_view_body .= "<br>";
		}
*/
		//비추천수
		$result = $mysqli->query("SHOW COLUMNS FROM `BOARD_".$bo_c_list[BOARD_CODE]."` LIKE 'BOARD_BAD' ");
		$exist = $result->num_rows > 0;
		if (empty($exist)) { 
			$_view_body .= $bo_c_list[BAC_NAME]." - 비추천수 - (BOARD_BAD) 생성<br>";
			wepix_query_error(" ALTER TABLE `BOARD_".$bo_c_list[BOARD_CODE]."` ADD `BOARD_BAD` SMALLINT(6) NOT NULL AFTER `BOARD_RECOM` "); 
		}else{
			$_view_body .= "<br>";
		}

/*
		$result = $mysqli->query("SHOW COLUMNS FROM `BOARD_".$bo_c_list[BOARD_CODE]."` LIKE 'BOARD_RECOM' ");
		$exist = $result->num_rows > 0;
		if (empty($exist)) { 
			$_view_body .= $bo_c_list[BAC_NAME]." - 게시판 추천수 필드명 수정 <br>";
			wepix_query_error(" ALTER TABLE `BOARD_".$bo_c_list[BOARD_CODE]."` CHANGE `BOARD_RECOM` `BOARD_LIKE` SMALLINT(6) NOT NULL "); 
		}else{
			$_view_body .= "<br>";
		}
*/
/*
			$_view_body .= $bo_c_list[BAC_NAME]." - 게시판 매니저 글 구분 수정 - (BOARD_ADMIN) 'Y','N','M1','M2'<br>";
			wepix_query_error(" ALTER TABLE `BOARD_".$bo_c_list[BOARD_CODE]."` CHANGE `BOARD_ADMIN` `BOARD_ADMIN` ENUM('Y','N','M1','M2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'N' "); 
*/




		//댓글 테이블 비회원 노출아이피
		$result = $mysqli->query("SHOW COLUMNS FROM `BOARD_".$bo_c_list[BOARD_CODE]."_COMMENT` LIKE 'COMMENT_IP_SHOW' ");
		$exist = $result->num_rows > 0;
		if (empty($exist)) { 
			$_view_body .= $bo_c_list[BAC_NAME]." - 비회원 노출아이피 - (COMMENT_IP_SHOW) 생성<br>";
			wepix_query_error(" ALTER TABLE `BOARD_".$bo_c_list[BOARD_CODE]."_COMMENT` ADD `COMMENT_IP_SHOW` varchar(100) default '' not null AFTER `COMMENT_IP` "); 
		}else{
			$_view_body .= "<br>";
		}

		//댓글 테이블 비회원 노출아이피
		$result = $mysqli->query("SHOW COLUMNS FROM `BOARD_".$bo_c_list[BOARD_CODE]."_COMMENT` LIKE 'COMMENT_IP_SHOW' ");
		$exist = $result->num_rows > 0;
		if (empty($exist)) { 
			$_view_body .= $bo_c_list[BAC_NAME]." - 비회원 노출아이피 - (COMMENT_IP_SHOW) 생성<br>";
			wepix_query_error(" ALTER TABLE `BOARD_".$bo_c_list[BOARD_CODE]."_COMMENT` ADD `COMMENT_IP_SHOW` varchar(100) default '' not null AFTER `COMMENT_IP` "); 
		}else{
			$_view_body .= "<br>";
		}

		//댓글 테이블 비회원 노출아이피
		$result = $mysqli->query("SHOW COLUMNS FROM `BOARD_".$bo_c_list[BOARD_CODE]."_COMMENT` LIKE 'COMMENT_IP_SHOW' ");
		$exist = $result->num_rows > 0;
		if (empty($exist)) { 
			$_view_body .= $bo_c_list[BAC_NAME]." - 비회원 노출아이피 - (COMMENT_IP_SHOW) 생성<br>";
			wepix_query_error(" ALTER TABLE `BOARD_".$bo_c_list[BOARD_CODE]."_COMMENT` ADD `COMMENT_IP_SHOW` varchar(100) default '' not null AFTER `COMMENT_IP` "); 
		}else{
			$_view_body .= "<br>";
		}

		//댓글 테이블 패스워드
		$result = $mysqli->query("SHOW COLUMNS FROM `BOARD_".$bo_c_list[BOARD_CODE]."_COMMENT` LIKE 'COMMENT_PASS' ");
		$exist = $result->num_rows > 0;
		if (empty($exist)) { 
			$_view_body .= $bo_c_list[BAC_NAME]." - 비회원 패스워드 - (COMMENT_PASS) 생성<br>";
			wepix_query_error(" ALTER TABLE `BOARD_".$bo_c_list[BOARD_CODE]."_COMMENT` ADD `COMMENT_PASS` varchar(100) default '' not null AFTER `COMMENT_NAME` "); 
		}else{
			$_view_body .= "<br>";
		}

/*
			$_view_body .= $bo_c_list[BAC_NAME]." - 댓글 게시판 매니저 글 구분 수정 - (COMMENT_ADMIN) 'Y','N','M1','M2'<br>";
			wepix_query_error(" ALTER TABLE `BOARD_".$bo_c_list[BOARD_CODE]."_COMMENT` CHANGE `COMMENT_ADMIN` `COMMENT_ADMIN` ENUM('Y','N','M1','M2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'N' "); 

			$_view_body .= $bo_c_list[BAC_NAME]." - 게시판 모드 가상 비회원 추가 - (BOARD_MODE) 'BS', 'NT', 'IG', 'IG2'<br>";
			wepix_query_error(" ALTER TABLE `BOARD_".$bo_c_list[BOARD_CODE]."` CHANGE `BOARD_MODE` `BOARD_MODE` ENUM('BS', 'NT', 'IG', 'IG2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'BS' "); 

			$_view_body .= $bo_c_list[BAC_NAME]." - 게시판 모드 가상 비회원 추가 - (COMMENT_MODE) 'BS', 'IG', 'IG2'<br>";
			wepix_query_error(" ALTER TABLE `BOARD_".$bo_c_list[BOARD_CODE]."_COMMENT` CHANGE `COMMENT_MODE` `COMMENT_MODE` ENUM('BS', 'IG', 'IG2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'BS' "); 
*/

		//댓글에 리플
		$result = $mysqli->query("SHOW COLUMNS FROM `BOARD_".$bo_c_list[BOARD_CODE]."_COMMENT` LIKE 'COMMENT_REPLY' ");
		$exist = $result->num_rows > 0;
		if (empty($exist)) { 
			$_view_body .= $bo_c_list[BAC_NAME]." - 댓글에 리플수 - (COMMENT_REPLY) 생성<br>";
			wepix_query_error(" ALTER TABLE `BOARD_".$bo_c_list[BOARD_CODE]."_COMMENT` ADD `COMMENT_REPLY` TINYINT(2) NOT NULL DEFAULT '0' "); 
		}else{
			$_view_body .= "<br>";
		}

		//댓글 보기상태
		$result = $mysqli->query("SHOW COLUMNS FROM `BOARD_".$bo_c_list[BOARD_CODE]."_COMMENT` LIKE 'COMMENT_SHOW' ");
		$exist = $result->num_rows > 0;
		if (empty($exist)) { 
			$_view_body .= $bo_c_list[BAC_NAME]." - 댓글 보기상태 - (COMMENT_SHOW) 생성<br>";
			wepix_query_error(" ALTER TABLE `BOARD_".$bo_c_list[BOARD_CODE]."_COMMENT` ADD `COMMENT_SHOW` ENUM('view', 'blind', 'del') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'view' AFTER `COMMENT_MODE` "); 
		}else{
			$_view_body .= "<br>";
		}

			$_view_body .= $bo_c_list[BAC_NAME]." - 댓글 보기상태 수정 - (COMMENT_SHOW) 생성<br>";
			wepix_query_error(" ALTER TABLE `BOARD_".$bo_c_list[BOARD_CODE]."_COMMENT` CHANGE `COMMENT_SHOW` `COMMENT_SHOW` ENUM('view', 'blind', 'del', 'delview') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'view' "); 


	}

include "../layout/header.php";
?>
<div id="contents_head">
	<h1>게시판 컬럼 검사</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<?=$_view_body?>

	</div>
</div>
<?
include "../layout/footer.php";
exit;
?>