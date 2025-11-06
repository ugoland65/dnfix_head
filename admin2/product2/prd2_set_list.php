<?
$pageGroup = "product2";
$pageName = "prd2_set_list";

include "../lib/inc_common.php";

	$_pn = securityVal($pn);

	$search_sql = "";

	$total_count = wepix_counter("prd_set", $search_sql);
	
	$list_num = 60;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($pn == "") $pn = 1;
	$from_record = ($pn - 1) * $list_num;
    $counter = $total_count - (($pn - 1) * $list_num);

	$query_field = "*";

	$query = "select ".$query_field."
		from prd_set
		".$search_sql;

	$sort_query = "pset_idx desc";

	$_query = $query." order by ".$sort_query." limit ".$from_record.", ".$list_num;

	$result = wepix_query_error($_query);

include "../layout/header.php";

?>
<div id="contents_head">
	<h1>세트상품 목록</h1>
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='prd2_set_reg.php'" > 
			<i class="fas fa-plus-circle"></i>
			신규 세트상품 등록
		</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="list-top-btn-wrap">
			<ul class="list-top-total">
				<span class="count">Total <b><?=$total_count?></b> / <b><?=$_pn?></b> Page</span>
			</ul>
			<ul class="list-top-btn"></ul>
		</div>

		<div id="list_box_layout2" class="display-table">
			<ul class="display-table-cell v-align-top">
				<div id="list_box2">
				
					<table class="table-list">
						<tr>
							<th class="tl-check"><input type="checkbox" name="" onclick="select_all()"></th>
							<th style="width:100px;">IDX</th>
							<th style="width:100px;">세트상품 코드</th>
							<th style="width:70px;">이미지</th>
							<th>이름</th>
							<th>상품수</th>
							<th>재고</th>
						</tr>
<?
while($list = wepix_fetch_array($result)){

?>
<tr bgcolor="<?=$_trcolor ?>">
	<td class="tl-check"><input type="checkbox" name="key_check[]" value="<?=$list['pset_idx']?>" ></td>
	<td><?=$list['pset_idx']?></td>
	<td>NSET-<?=$list['pset_idx']?></td>
	<td>

	</td>
	<td class="text-left"><a href="prd2_set_reg.php?mode=modify&idx=<?=$list['pset_idx']?>"><b><?=$list['pset_name']?></b></a></td>
	<td class="text-left"><b><?=$list['pset_count']?></b></td>
	<td class="text-left">
		<? if( $list['pset_stock'] > 0 ){ ?>
		<b><?=$list['pset_stock']?></b>
		<? } ?>
	</td>
</tr>
<? } ?>
					</table>

				</div><!-- #list_box2 -->
			</ul>
			<ul class="display-table-cell width-10"></ul>
			<ul class="display-table-cell width-300 v-align-top">
		
			</ul>
		</div>
		<div class="paging-wrap"><?=$_view_paging?></div>
	</div>
</div>

<script type="text/javascript"> 
<!-- 
$(function(){

	var content22 = '이 페이지(세트상품 목록)는 곧 폐기될 예정입니다.<br>상품관리 v.3에서 관리해주세요.'
		+ '<br>';


	$.confirm({
		boxWidth : "500px",
		useBootstrap : false,
		icon: 'fas fa-exclamation-triangle',
		title: '공지',
		content: content22,
		type: 'red',
		typeAnimated: true,
		closeIcon: true,
		buttons: {
			/*
			cencle: {
				text: '확인/닫기',
				action: function(){
				}
			},
			*/
			move: {
				text: '상품관리 v.3 세트상품 관리로 이동하기',
				action: function(){
					location.href='/ad/prd/set_prd';
				}
			}
		}
	});

});
//--> 
</script> 
<?
include "../layout/footer.php";
exit;
?>

