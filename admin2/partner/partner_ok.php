<?
include "../lib/inc_common.php";

	$_action_mode = securityVal($a_mode);
	$_idx = securityVal($idx);

// ******************************************************************************************************************
// 에이전시 노출,비노출
// ******************************************************************************************************************
if( $_action_mode == "agencyActive" ){

	$_ag_view = securityVal($mode);

	$query = "update "._DB_AGENCY." set AG_VIEW = '".$_ag_view."' where AG_IDX = '".$_idx."' ";
	wepix_query_error($query);

	echo "|Processing_Complete|처리완료|";
	exit;

// ******************************************************************************************************************
// 에이전시 등록
// ******************************************************************************************************************
}elseif( $_action_mode == "agencyNew" ){

	$_ag_company = securityVal($ag_company);
	$_ag_kind = securityVal($ag_kind);
	$_ag_co_idx = securityVal($ag_co_idx);
	$_ag_memo = securityVal($ag_memo);
	$_ag_view = securityVal($ag_view);

	$_ag_branch = "";
	if( $_ag_co_idx && $_ag_kind== "B"){
		$ag_data = wepix_fetch_array(wepix_query_error("select AG_COMPANY from "._DB_AGENCY." where AG_IDX = '".$_ag_co_idx."' "));
		$_ag_branch = $ag_data[AG_COMPANY];
	}

	$query = "insert into "._DB_AGENCY." set
		AG_COMPANY  = '".$_ag_company."',
		AG_KIND = '".$_ag_kind."',
		AG_CO_IDX = '".$_ag_co_idx."',
		AG_BRANCH = '".$_ag_branch."',
		AG_MEMO = '".$_ag_memo."',
		AG_VIEW = '".$_ag_view."' ";
	wepix_query_error($query);

	msg("등록 완료!", _A_PATH_PARTNER_AG_LIST);

// ******************************************************************************************************************
// 에이전시 수정
// ******************************************************************************************************************
}elseif( $_action_mode == "agencyModify" ){

	$_ag_idx = securityVal($ag_idx);
	$_ag_company = securityVal($ag_company);
	$_ag_kind = securityVal($ag_kind);
	$_ag_co_idx = securityVal($ag_co_idx);
	$_ag_memo = securityVal($ag_memo);
	$_ag_view = securityVal($ag_view);

	$_ag_branch = "";
	if( $_ag_co_idx && $_ag_kind== "B"){
		$ag_data = wepix_fetch_array(wepix_query_error("select AG_COMPANY from "._DB_AGENCY." where AG_IDX = '".$_ag_co_idx."' "));
		$_ag_branch = $ag_data[AG_COMPANY];
	}

	$query = "update "._DB_AGENCY." set 
		AG_COMPANY = '".$_ag_company."',
		AG_CO_IDX = '".$_ag_co_idx."',
		AG_BRANCH = '".$_ag_branch."',
		AG_MEMO = '".$_ag_memo."',
		AG_VIEW = '".$_ag_view."' 
		where AG_IDX = '".$_ag_idx."' ";
	wepix_query_error($query);

	//만약 본사일경우 지사의 본사명을 전부 수정해준다.
	if( $_ag_kind == "A" ){
		$query = "update "._DB_AGENCY." set AG_BRANCH = '".$_ag_company."' where AG_CO_IDX = '".$_ag_idx."' ";
		wepix_query_error($query);
	}

	msg("수정 완료!", _A_PATH_PARTNER_AG_REG."?mode=modify&key=".$_ag_idx);

// ******************************************************************************************************************
// 에이전시 삭제
// ******************************************************************************************************************
}elseif( $_action_mode == "agencyDel" ){

	$_ag_kind = securityVal($ag_kind);

	$query = "update "._DB_AGENCY." set AG_DEL_YN = 'Y' where AG_IDX = '".$_idx."' ";
	wepix_query_error($query);

	//만약 본사일경우 지사도 모두 삭제해준다
	if( $_ag_kind == "A" ){
		$query = "update "._DB_AGENCY." set AG_DEL_YN = 'Y' where AG_CO_IDX = '".$_idx."' ";
		wepix_query_error($query);
	}

	echo "|Processing_Complete|처리완료|";
	exit;

}elseif( $_action_mode == "allianceNew" ){
// ******************************************************************************************************************
// 제휴샵 등록
// ******************************************************************************************************************
	$_alliance_shop_name = securityVal($alliance_shop_name);
	$_alliance_shop_calculate = securityVal($alliance_shop_calculate);
	$_alliance_shop_memo = securityVal($alliance_shop_memo);
	$_alliance_shop_view = securityVal($alliance_shop_view);

	$query = "insert into "._DB_ALLIANCE_SHOP." set
		AS_NAME = '".$_alliance_shop_name."',
		AS_CALCULATE = '".$_alliance_shop_calculate."',
		AS_MEMO = '".$_alliance_shop_memo."',
		AS_VIEW = '".$_alliance_shop_view."'";
	wepix_query_error($query);

	msg("등록 완료!", _A_PATH_PARTNER_ALLIANCE_LIST);

// ******************************************************************************************************************
// 제휴샵 수정
// ******************************************************************************************************************
}elseif( $_action_mode == "allianceModify" ){

	$_as_idx = securityVal($as_idx);
	$_alliance_shop_name = securityVal($alliance_shop_name);
	$_alliance_shop_calculate = securityVal($alliance_shop_calculate);
	$_alliance_shop_memo = securityVal($alliance_shop_memo);
	$_alliance_shop_view = securityVal($alliance_shop_view);



	$query = "update "._DB_ALLIANCE_SHOP." set 
		AS_NAME = '".$_alliance_shop_name."',
		AS_CALCULATE = '".$_alliance_shop_calculate."',
		AS_MEMO = '".$_alliance_shop_memo."',
		AS_VIEW = '".$_alliance_shop_view."'
		where AS_IDX = '".$_as_idx."' ";
	wepix_query_error($query);

	msg("수정 완료!", _A_PATH_PARTNER_ALLIANCE_REG."?mode=modify&key=".$_as_idx);

// ******************************************************************************************************************
// 제휴샵 삭제
// ******************************************************************************************************************
}elseif( $_action_mode == "allianceDel" ){

	$_as_idx = securityVal($as_idx);

	$query = "update "._DB_ALLIANCE_SHOP." set 
			AS_DEL_YN = 'Y'
		where AS_IDX = '".$_as_idx."' ";
	wepix_query_error($query);

	msg("삭제 완료!", _A_PATH_PARTNER_ALLIANCE_LIST);
}

exit;
?>