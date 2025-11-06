<?
$pageGroup = "member";
$pageName = "popup_brand_view";

include "../lib/inc_common.php";


	//$_mode = securityVal($mode);
	$_mode = "modify";
	$_idx = securityVal($idx);
	$_bd_kind_code = securityVal($bd_kind_code);

	$_token = make_token(5,"brand");

		$brand_data = wepix_fetch_array(wepix_query_error("select * from "._DB_BRAND." where BD_IDX = '".$_idx."' "));
		$_bd_kind_code = $brand_data[BD_KIND_CODE];

		$_brand_token = $brand_data[BD_TOKEN];
		if( !$_brand_token ){
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