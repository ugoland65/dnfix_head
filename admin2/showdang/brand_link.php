<?
$pageGroup = "showdang";
$pageName = "brand_link";

include "../lib/inc_common.php";

	$_mode = $mode ;
	//$_serch_query = "where BD_KIND_CODE = '".$_mode."'";
	$total_count = wepix_counter("brand_link", $_serch_query);
	
	$list_num = 300;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	if ($_pn == "") $_pn = 1;
	$from_record = ($_pn - 1) * $list_num;
    $counter = $total_count - (($_pn - 1) * $list_num);

	//bl_keyword asc
	$query = "select * from brand_link ".$_serch_query." order by bl_idx desc limit ".$from_record.", ".$list_num;
	$result = wepix_query_error($query);

	$page_link_text = "brand_link.php?pn=";
	$view_paging = paging($_pn, $total_page, $list_num, $page_num, $page_link_text);

include "../layout/header.php";
?>
<div id="contents_head">
	<h1>브랜드 링크 관리</h1>
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="showdangBrandLink.newReq()" > 
			<i class="fas fa-plus-circle"></i>
			신규등록
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
		<div class="table-wrap">
			
			<table class="table-style">	
				<tr  id="<?=$list['bl_idx']?>_<?=$list['bl_idx']?>">
					<th class="list-checkbox tds1"><input type="checkbox" name="" onclick="select_all()"></th>
					<th>고유번호</th>
					<th>브랜드 키워드</th>
					<th>링크</th>
					<th>수정</th>
					<th>삭제</th>
				</tr>
			<?
			while($list = wepix_fetch_array($result)){
			?>
				<tr id="<?=$list['bl_idx']?>">
					<td class="list-checkbox tds2">
					</td>
					<td class="">
						<?=$list['bl_idx']?>
					</td>
					<td class="">
						<?=$list['bl_keyword']?>
					</td>
					<td class="">
						<?=$list['bl_link']?>
					</td>
					<td class=""></td>
					<td class=""><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" onclick="showdangBrandLink.del(this,'<?=$list['bl_idx']?>')" >삭제</button></td>
				</tr>
			<? } ?>
			</table>

		</div>

	</div>
</div>
<script type="text/javascript"> 
<!-- 
var showdangBrandLink = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},
		newReq : function() {

			$.alert({
				boxWidth : '600px',
				useBootstrap : false,
				title : "브랜드 링크 신규등록",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				draggable: true,
				content:function () {
					var self = this;
					return $.ajax({
						url: '/admin2/showdang/ajax.php',
						data: { mode:'brandLinkNewReq' },
						dataType: 'html',
						method: 'POST'
					}).done(function (response) {
						self.setContent(response);
					}).fail(function(){
						self.setContent('에러');
					});
				},
				buttons: {
					cancel: {
						text: '닫기',
						action:function () {
						//close
						}
					},
				}
			});
		},
		del : function(obj, idx) {

			$(obj).attr('disabled', true);
			$.ajax({
				url: "processing.showdang.php",
				data: { "a_mode":"brandLinkDel", "idx":idx },
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						toast2("success", "삭제완료", "삭제완료 되었습니다.");
						location.reload();
					}else{
						showAlert("Error", res.msg, "alert2" );
						return false;
					}
				},
				error: function(request, status, error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					showAlert("Error", "에러", "alert2" );
					return false;
				},
				complete: function() {
					$(obj).attr('disabled', false);
				}
			});

		}
	};

}();
//--> 
</script> 
<?
include "../layout/footer.php";
exit;
?>
