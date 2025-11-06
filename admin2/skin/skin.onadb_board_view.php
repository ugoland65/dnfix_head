<?
$_idx = $_get1;
if( $_idx ){

	$data = sql_fetch_array(sql_query_error("select * from board WHERE idx = '".$_idx."' "));
	
	//$_file_data = json_decode($data['file'], true);
}
?>
<style type="text/css">
#file_list_wrap{ width:500px; display:inline-block; }
.file-list{ padding:5px; margin-bottom:5px; border:1px solid #ddd; border-radius:5px; }
</style>
<div id="contents_head">
	<h1>업무 매뉴얼 등록</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<table class="table-reg th-150">
			<tr>
				<th>분류</th>
				<td>
					<?=$data['category']?>
				</td>
			</tr>
			<tr>
				<th>제목</th>
				<td>
					<?=$data['subject']?>
				</td>
			</tr>
			<tr>
				<th>첨부파일</th>
				<td>
					<div id="file_list_wrap">
						<? for ( $i=0; $i<count($_file_data['file_name']); $i++ ){ ?>
						<div class="file-list">
							<a href="/data/board/<?=$_file_data['file_name'][$i]?>"><?=$_file_data['file_name'][$i]?></a>
							<button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-xs" data-filename="<?=$_file_data['file_name'][$i]?>" onclick="workManualView.fileDel(this, '<?=$_idx?>', '<?=$i?>')" ><i class="fas fa-trash-alt"></i> 파일삭제</button>
						</div>
						<? } ?>
					</div>
				</td>
			</tr>

			<tr>
				<th>내용</th>
				<td>
					<?=$data['body']?>
				</td>
			</tr>

			<tr>
				<th>댓글</th>
				<td>
					<div>
						<ul><textarea name="" style="width:800px; height:100px;"></textarea></ul>
					</div>
				</td>
			</tr>

		</table>

		<div class="submitBtnWrap">
			<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg float-left" onclick="location.href='/ad/staff/board'" > 
				<i class="fas fa-arrow-left"></i> 목록
			</button>
			<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="location.href='/ad/onadb/onadb_board_reg/<?=$_idx?>'" > 
				<i class="far fa-check-circle"></i> 수정
			</button>
			<button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-lg float-right" onclick="workManualView.contentsDel(this, '<?=$_idx?>')" >
				<i class="fas fa-trash-alt"></i> 삭제
			</button>
		</div>

	</div>
</div>
<div id="contents_bottom">

</div>

<script type="text/javascript"> 
<!-- 
var workManualView = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},

		fileDel : function( obj, idx, delnum ) {
			
			var filename = $(obj).data("filename");

			$.confirm({
				icon: 'fas fa-exclamation-triangle',
				title: '정말 삭제하시겠습니까?',
				content: '( '+ filename +' ) 파일을 삭제합니다.<br>삭제시 복구되지 않습니다.',
				type: 'red',
				typeAnimated: true,
				closeIcon: true,
				buttons: {
					somethingElse: {
						text: '삭제하기',
						btnClass: 'btn-red',
						action: function(){

							$.ajax({
								url: "/ad/processing/work",
								data : { "a_mode" : "work_file_del", "pmode" : "board", "idx" : idx, "delnum" : delnum   },
								type: "POST",
								dataType: "json",
								success: function(res){
									if ( res.success == true ){
										alert("파일 삭제 완료");
										location.href='/ad/staff/board_view/'+ idx;
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
					},
					cencle: {
						text: '취소',
						action: function(){
						}
					}
				}
			});

		},

		contentsDel : function( obj, idx ) {

			$.confirm({
				icon: 'fas fa-exclamation-triangle',
				title: '정말 삭제하시겠습니까?',
				content: '컨텐츠를 전부 삭제합니다.<br>삭제시 첨부파일도 모두 삭제됩니다.<br>삭제시 복구되지 않습니다.',
				type: 'red',
				typeAnimated: true,
				closeIcon: true,
				buttons: {
					somethingElse: {
						text: '삭제하기',
						btnClass: 'btn-red',
						action: function(){

							$(obj).attr('disabled', true);
							$.ajax({
								url: "/ad/processing/work",
								data: { "a_mode":"workManualDel", "idx":idx },
								type: "POST",
								dataType: "json",
								success: function(res){
									if (res.success == true ){
										alert("삭제가 완료되었습니다.");
										location.href='/ad/staff/board';
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
					},
					cencle: {
						text: '취소',
						action: function(){
						}
					}
				}
			});
		}

	};

}();
//--> 
</script> 