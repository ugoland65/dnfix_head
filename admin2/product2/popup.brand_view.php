<?
ini_set('display_errors', 1);
error_reporting(E_ALL);

$pageGroup = "member";
$pageName = "popup_brand_view";

include "../lib/inc_common.php";

	// 변수 초기화
	$mode = $_GET['mode'] ?? $_POST['mode'] ?? "";
	$idx = $_GET['idx'] ?? $_POST['idx'] ?? "";
	$bd_kind_code = $_GET['bd_kind_code'] ?? $_POST['bd_kind_code'] ?? "";

	//$_mode = securityVal($mode);
	$_mode = "modify";
	$_idx = securityVal($idx);
	$_bd_kind_code = securityVal($bd_kind_code);

	$_token = make_token(5,"brand");

	$brand_data = sql_fetch_array(sql_query_error("select * from "._DB_BRAND." where BD_IDX = '".$_idx."' "));
	
	// 배열 검증
	if (!is_array($brand_data) || empty($brand_data)) {
		$brand_data = ['BD_KIND_CODE' => '', 'BD_TOKEN' => ''];
	}
	
	$_bd_kind_code = $brand_data['BD_KIND_CODE'] ?? "";

	$_brand_token = $brand_data['BD_TOKEN'] ?? "";
	if( empty($_brand_token) ){
		$_brand_token = $_token;
	}

	$page_title_text = "브랜드 수정";
	$submit_btn_text = "브랜드 수정";



include "../layout/header_popup.php";
?>
<style type="text/css">
.brand-form-wrap{ padding:30px; }
</style>
<div class="brand-form-wrap">
<? include "inc.brand_form.php"; ?>
</div>

<?
include "../layout/footer_popup.php";
exit;
?>