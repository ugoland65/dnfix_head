<?
$pageGroup = "product2";
$pageName = "brand_list";

include "../lib/inc_common.php";

	$_mode = securityVal($mode);
	$_idx = securityVal($key);
	$_bd_kind_code = securityVal($bd_kind_code);

	$_token = make_token(5,"brand");

	if( $_mode == "modify" ){
		$brand_data = wepix_fetch_array(wepix_query_error("select * from "._DB_BRAND." where BD_IDX = '".$_idx."' "));
		$_bd_kind_code = $brand_data[BD_KIND_CODE];

		$_brand_token = $brand_data[BD_TOKEN];
		if( !$_brand_token ){
			$_brand_token = $_token;
		}

		$page_title_text = "브랜드 수정";
		$submit_btn_text = "브랜드 수정";

	}else{

		$_brand_token = $_token;

		$page_title_text = "브랜드 등록";
		$submit_btn_text = "브랜드 등록";

	}


include "../layout/header.php";
?>

<div id="contents_head">
	<h1><?=$page_title_text?></h1>
    <div id="head_write_btn">

	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">


<?
include "inc.brand_form.php";
?>

	</div>
</div>
<?
include "../layout/footer.php";
exit;
?>