<?
$pageGroup = "booking";
$pageName = "booking_wented";

include "../lib/inc_common.php";

	$_wp_idx = securityVal($key);
	$wp_data = wepix_fetch_array(wepix_query_error("select * from "._DB_WANTED." where WP_IDX = '".$_wp_idx."' "));

	$_view_wp_kind = $wp_data[WP_KIND];
	
	if($_view_wp_kind == 'CONFIRM'){
		$_view_find_kind = 'confirm';
		$_show_img_sub = substr( $wp_data[WP_IMG_DATA], 0,7);
		if($_view_find_kind == $_show_img_sub){
			$_view_img_data = "../../data/booking/confirm/".$wp_data[WP_IMG_DATA];
		}else{
			$_view_img_data = $wp_data[WP_IMG_DATA];
		}
	}elseif($_view_wp_kind == 'WANTED'){
		$_view_find_kind = 'wanted';
		$_show_img_sub = substr( $wp_data[WP_IMG_DATA], 0,6);
		if($_view_find_kind == $_show_img_sub){
			$_view_img_data = "../../data/booking/wanted/".$wp_data[WP_IMG_DATA];
		}else{
			$_view_img_data = $wp_data[WP_IMG_DATA];
		}
	}
	
	



	


include "../layout/header_popup.php";
?>
<STYLE TYPE="text/css">
.page-btn-wrap{ text-align:center; }
</STYLE>

<div id="wrap">
	<img style='width:100%' src='<?=$_view_img_data?>'>
</div>



<script type='text/javascript'>
	function wentid_close(){
		window.close();
	}
</script>

<?
include "../layout/footer_popup.php";
exit;
?>