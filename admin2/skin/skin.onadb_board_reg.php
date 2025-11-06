<?
$_idx = $_get1;
if( $_idx ){

	$data = sql_fetch_array(sql_query_error("select * from board WHERE idx = '".$_idx."' "));
	
	$_file_data = json_decode($data['file'], true);
}
?>
<style type="text/css">
#file_list_wrap{  }
.file-list-wrap{ width:500px; display:inline-block; }
.file-list{ padding:5px; margin-bottom:5px; border:1px solid #ddd; border-radius:5px; }
</style>
<div id="contents_head">
	<h1>게시판 등록</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<form id="form_board">

		<? if( $_idx ){ ?>
			<input type="hidden" name="a_mode" value="board_modify" >
			<input type="hidden" name="idx" value="<?=$_idx?>" >
		<? }else{ ?>
			<input type="hidden" name="a_mode" value="board_reg" >
		<? } ?>

		<input type="hidden" name="site" value="onadb" >

		<table class="table-reg th-150">
			<tr>
				<th>구분</th>
				<td><input type="hidden" name="kind" value="notice">공지사항</td>
			</tr>
			<!-- 
			<tr>
				<th>분류</th>
				<td>
					<select name="category" onchange="boardReg.categoryOnChange(this.value)">
					<?
						for ($i=0; $i<count($_board_cate); $i++){
							$selected = ( $data['category'] == $_board_cate[$i]['name'] ) ? "selected" : "";
							echo "<option value=\"".$_board_cate[$i]['name']."\" ".$selected.">".$_board_cate[$i]['name']."</option>";
					} 
					?>
					</select>
				</td>
			</tr>
			-->

			<tr>
				<th>제목</th>
				<td><input type='text' name='subject' id='subject' value="<?=$data['subject']?>" class="width-full"></td>
			</tr>

			<!-- 
			<tr>
				<th>첨부파일</th>
				<td>
					
					<div>
					<div class="file-list-wrap">
						<? for ($i=0; $i<count($_file_data['file_name']); $i++){ ?>
						<div class="file-list">
							<a href="/data/board/<?=$_file_data['file_name'][$i]?>"><?=$_file_data['file_name'][$i]?></a>
							<input type="hidden" name="old_board_file[]" value="<?=$_file_data['file_name'][$i]?>" >
						</div>
						<? } ?>
					</div>
					</div>

					<div class="file-list-wrap" id="file_list_wrap">
						<div class="file-list">
							<input name="board_file[]" type="file" >
						</div>
					</div>
					<div>
						<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="boardReg.addFile();" >첨부파일 추가</button>
					</div>

				</td>
			</tr>
			-->

			<tr>
				<th>내용</th>
				<td>
					<textarea name="body" id="summernote" ><?=$data['body']?></textarea>
				</td>
			</tr>
		</table>
		</form>

		<div class="submitBtnWrap">

				<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='/ad/staff/board'" > 
					<i class="fas fa-arrow-left"></i> 목록
				</button>

				<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="boardReg.save(this);" > 
					<i class="far fa-check-circle"></i> 등록
				</button>

		</div>

	</div>
</div>
<div id="contents_bottom">

</div>

<link href="/plugins/summernote/summernote.min.css" rel="stylesheet">
<script src="/plugins/summernote/summernote.min.js"></script>
<script src="/plugins/summernote/summernote-ko-KR.min.js"></script>

<script type="text/javascript"> 
<!-- 

$('#summernote').summernote({
	lang: 'ko-KR',
	height: 400,                 // set editor height
	minHeight: null,             // set minimum height of editor
	maxHeight: null,             // set maximum height of editor
	focus: true,                  // set focus to editable area after initializing summernote
	dialogsInBody: true,

	toolbar: [
		// [groupName, [list of button]]
		['fontname', ['fontname']],
		['fontsize', ['fontsize']],
		['style', ['bold', 'italic', 'underline','strikethrough', 'clear']],
		['color', ['forecolor','color']],
		['table', ['table']],
		['para', ['ul', 'ol', 'paragraph']],
		['height', ['height']],
		['insert',['picture','link','video']],
		['view', ['fullscreen', 'help']],
		['view', ['codeview']],
	],
	popover: {
	  image: [
		['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
		['float', ['floatLeft', 'floatRight', 'floatNone']],
		['remove', ['removeMedia']]
	  ],
	  link: [
		['link', ['linkDialogShow', 'unlink']]
	  ],
	  table: [
		['add', ['addRowDown', 'addRowUp', 'addColLeft', 'addColRight']],
		['delete', ['deleteRow', 'deleteCol', 'deleteTable']],
	  ],
	  air: [
		['color', ['color']],
		['font', ['bold', 'underline', 'clear']],
		['para', ['ul', 'paragraph']],
		['table', ['table']],
		['insert', ['link', 'picture']]
	  ]
	}

});

var boardReg = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},

		categoryOnChange : function(category) {
			
			if( category == "업무일지" || category == "공지사항" ){
				$("#target_mb_id_div").hide();
			}else{
				$("#target_mb_id_div").show();
			}

		},

		addFile : function() {
			
			var shtml = '<div class="file-list">'
				+ '<input name="board_file[]" type="file" >'
				+ '</div>';

			$("#file_list_wrap").append(shtml);	

		},

		save : function( obj ) {

			var form = $('#form_board')[0];
			var formData = new FormData(form);

			$(obj).attr('disabled', true);
			$.ajax({
				url: "/ad/processing/board",
				data: formData,
				type: "POST",
				dataType: "json",
				contentType : false,
				processData : false,
				success: function(res){
					if ( res.success == true ){
						alert("저장되었습니다.");
						location.href='/ad/onadb/onadb_board_view/'+ res.key;
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