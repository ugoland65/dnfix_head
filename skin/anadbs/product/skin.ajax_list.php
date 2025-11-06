<?

$_tg_code = "ONAHOLE";

$search_sql = " WHERE A.CD_KIND_CODE = '".$_tg_code."' AND A.CD_COMPARISON = 'Y' ";
$search_sql_count = " WHERE CD_KIND_CODE = '".$_tg_code."' AND CD_COMPARISON = 'Y' ";

$total_count = wepix_counter(_DB_COMPARISON, $search_sql_count);

	$query_field = "A.CD_IDX, A.CD_NAME, A.CD_BRAND_IDX, A.CD_IMG, A.CD_IMG2";
	$query_field .= ", B.BD_NAME";

	$query = "select ".$query_field."
		from "._DB_COMPARISON." A 
		left join "._DB_BRAND." B ON (B.BD_IDX = A.CD_BRAND_IDX) ".$search_sql;

	$list_num = 40;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num); // 전체 페이지 계산
	
	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
	$counter = $total_count - (($pn - 1) * $list_num);

	$view_page = publicAjaxPaging($_pn, $total_page, $list_num, $page_num, "bettingList.list", "");

	$sort_query = "CD_IDX DESC";

	$_query = $query." order by ".$sort_query." limit ".$from_record.", ".$list_num;

	$result = wepix_query_error($_query);
?>

<style type="text/css">
.top-wrap{ width:100%; border-bottom:1px solid #000 !important; box-sizing:border-box; height:36px; margin-top:15px; }
.total-count{ }
.total-count > ul{ width:180px; height:36px; line-height:36px; color:#eee; padding-left:15px; }
.total-count > ul span{ color:#0093f2; font-weight:bold; }

.comparison-list { width:100%; font-size:0; margin-top:20px; }
.comparison-list > ul{ width:25%; display:inline-block; box-sizing:border-box; margin-bottom:40px; }
.comparison-list-box{ display:table; } 
.comparison-list-box ul.img{ width:90px; display:table-cell; vertical-align:top; }
.comparison-list-box ul.info{ display:table-cell; vertical-align:top; padding: 0 20px 0 0; box-sizing:border-box; }

.comparison-list-box ul.img .thum{ width:80px; height:80px; overflow:hidden; border:4px solid #000; border-radius:10px; }
.comparison-list-box ul.img .thum .thum-icon{ width:76px; }
.comparison-list-box ul.img .thum .thum-no-icon{ width:180px; margin-top:-45px; margin-left:-45px; }

.comparison-list-box ul.info .pd-info{ padding-top:10px; }
.comparison-list-box ul.info .pd-name{ font-family: 'Noto Sans KR', sans-serif;  font-size:14px; line-height:135%; }
.comparison-list-box ul.info .pd-name a{ color:#f5f5f5; }
.comparison-list-box ul.info .pd-brand-info{ padding-top:5px; line-height:120%;  }
.comparison-list-box ul.info .pd-brand-info a{ color:#798499; }
</style>

<div class="top-wrap">
	<div class="total-count">
		<ul>Total <span><?=$total_count?></span></ul>
	</div>
</div>

<div class="comparison-list">
<?	
while($list = wepix_fetch_array($result)){ 

/*
	$_view_score_icon = "";
	for ($i=0; $i<$list[CD_SCORE]; $i++){
		$_view_score_icon .= "<i class='fas fa-star on-star'></i>";
	}
	$_view_score_icon_empty = "";
	for ($i=0; $i<(5-$list[CD_SCORE]); $i++){
		$_view_score_icon_empty .= "<i class='far fa-star off-star'></i>";
	}
*/

	if( $list[CD_IMG2] ){
		$img_path = '../../data/comparion/'.$list[CD_IMG2];
		$img_class = "thum-icon";
	}else{
		$img_path = '../../data/comparion/'.$list[CD_IMG];
		$img_class = "thum-no-icon";
	}

	//$brand_data = wepix_fetch_array(wepix_query_error("select BD_NAME from "._DB_BRAND." where BD_IDX = '".$list[CD_BRAND_IDX]."' "));
?>
<ul class="">
	<div class="comparison-list-box">
		<ul class="img">
			<div class="thum"><a href="/pv/<?=$list[CD_IDX]?>" ><img src="<?=$img_path?>" class="<?=$img_class?>"></a></div>
		</ul>
		<ul class="info">
			<div class="pd-info">
				<ul class="pd-name"><a href="/pv/<?=$list[CD_IDX]?>" ><?=$list[CD_NAME]?></a></ul>
				<ul class="pd-brand-info"><a href="/front/product/brand_view.php?idx=<?=$list[CD_BRAND_IDX]?>" target="_blank"><?=$list[BD_NAME]?></a></ul>
			</div>
		</ul>
	</div>
</ul>
<? } ?>
</div>