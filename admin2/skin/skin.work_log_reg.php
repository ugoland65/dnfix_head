<?
$_idx = $_get1;
$data = [];
if( $_idx ){

	$data = sql_fetch_array(sql_query_error("select * from work_log WHERE idx = '".$_idx."' "));
	
	$_file_data = json_decode($data['file'] ?? null, true);
	if (!is_array($_file_data)) {
		$_file_data = ['file_name' => []];
	}
	if (!isset($_file_data['file_name']) || !is_array($_file_data['file_name'])) {
		$_file_data['file_name'] = [];
	}
}else{
	$_file_data = ['file_name' => []];
}
?>
<style type="text/css">
#file_list_wrap{  }
.file-list-wrap{ width:500px; display:inline-block; }
.file-list{ padding:5px; margin-bottom:5px; border:1px solid #ddd; border-radius:5px; }
</style>
<div id="contents_head">
	<h1>업무 게시판 등록</h1>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<form id="form1">

		<? if( $_idx ){ ?>
			<input type="hidden" name="a_mode" value="work_log_modify" >
			<input type="hidden" name="idx" value="<?=$_idx?>" >
		<? }else{ ?>
			<input type="hidden" name="a_mode" value="work_log_reg" >
		<? } ?>

		<table class="table-reg th-150">
			<tr>
				<th>분류</th>
				<td>
					<select name="category" onchange="workLogReg.categoryOnChange(this.value)">
					<?
						if (isset($_work_log_cate) && is_array($_work_log_cate)) {
							for ($i=0; $i<count($_work_log_cate); $i++){
								$selected = ( isset($data['category']) && $data['category'] == $_work_log_cate[$i]['name'] ) ? "selected" : "";
								$cateName = $_work_log_cate[$i]['name'] ?? '';
								echo "<option value=\"".$cateName."\" ".$selected.">".$cateName."</option>";
							}
						}
					?>
					</select>
				</td>
			</tr>

			<tr>
				<th>제목</th>
				<td><input type='text' name='subject' id='subject' value="<?=$data['subject'] ?? ''?>" class="width-full"></td>
			 </tr>

			<tr>
				<th>옵션</th>
				<td><!-- selected -->
					상태 : 
					<select name="state">
						<option value="대기" <?=(isset($data['state']) && $data['state'] == '대기') ? 'selected' : ''?>>대기</option>
						<option value="확인" <?=(isset($data['state']) && $data['state'] == '확인') ? 'selected' : ''?>>확인</option>
						<option value="완료" <?=(isset($data['state']) && $data['state'] == '완료') ? 'selected' : ''?>>완료</option>
						<option value="반려" <?=(isset($data['state']) && $data['state'] == '반려') ? 'selected' : ''?>>반려</option>
					</select>

<?
/*
$_target_mb_id_div_style = "";
if( !$data['category'] || $data['category'] == "업무일지" || $data['category'] == "공지사항" ){
	$_target_mb_id_div_style = "display:none;";
}
*/
?>
<style type="text/css">
.target-mb-id-div{}
.target-mb-id-div label{ border:1px solid #ccc; border-radius:5px; padding:5px; }
</style>
					<div id="target_mb_id_div" class="target-mb-id-div" style="<?=$_target_mb_id_div_style ?? ''?> margin-top:10px;">
						참여자 :
						<label><input type="checkbox" name="target_mb_idx_all" id="target_mb_idx_all" value="" onclick="checkboxAll()"> 전체선택</label>
						<?
							$_where = "";
							$_query = "select * from admin ".$_where." ORDER BY idx DESC";
							$_result = sql_query_error($_query);
							while($_list = wepix_fetch_array($_result)){
								
								$_checked = "";
								$_target_mb_text = "@".$_list['idx'];
								if (isset($data['target_mb']) && strstr($data['target_mb'], $_target_mb_text)){
									$_checked = "checked";
								}
						?>
							<label><input type="checkbox" name="target_mb_idx[]" class="target-mb-id" value="<?=$_list['idx']?>" <?=$_checked?>> <?=$_list['ad_name']?></label>
						<? } ?>
					</div>

				</td>
			</tr>


			<tr>
				<th>첨부파일</th>
				<td>
					
					<div>
					<div class="file-list-wrap">
						<? if (!empty($_file_data['file_name']) && is_array($_file_data['file_name'])) { ?>
							<? for ($i=0; $i<count($_file_data['file_name']); $i++){ ?>
							<div class="file-list">
								<a href="/data/work_log/<?=$_file_data['file_name'][$i]?>"><?=$_file_data['file_name'][$i]?></a>
								<input type="hidden" name="old_work_log_file[]" value="<?=$_file_data['file_name'][$i]?>" >
							</div>
							<? } ?>
						<? } ?>
					</div>
					</div>

					<div class="file-list-wrap" id="file_list_wrap">
						<div class="file-list">
							<input name="work_log_file[]" type="file" >
						</div>
					</div>
					<div>
						<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="workLogReg.addFile();" >첨부파일 추가</button>
					</div>

				</td>
			</tr>

<? 
/*

		<tr id="target_mb_id_tr" style="display:none">
			<th>참여자</th>
			<td>
<?
	$_query = "select * from admin ".$_where." ORDER BY idx DESC";
	$_result = sql_query_error($_query);
	while($_list = wepix_fetch_array($_result)){
		
		$_checked = "";
		$_target_mb_text = "@".$_list['idx'];
		if (strstr($data['target_mb'], $_target_mb_text)){
			$_checked = "checked";
		}
?>
				<label><input type="checkbox" name="target_mb_id[]" value="<?=$_list['idx']?>" <?=$_checked?>> <?=$_list['ad_nick']?></label>
<? } ?>
			</td>
		</tr>

*/ 
?>

			<tr>
				<th>내용</th>
				<td>
					<textarea name="body" id="summernote" ><?=$data['body'] ?? ''?></textarea>
				</td>
			</tr>
		</table>
		</form>

		<div class="submitBtnWrap">

				<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='/ad/staff/work_log'" > 
					<i class="fas fa-arrow-left"></i> 목록
				</button>

				<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="workLogReg.save(this);" > 
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

var workLogReg = function() {

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
				+ '<input name="work_log_file[]" type="file" >'
				+ '</div>';

			$("#file_list_wrap").append(shtml);	

		},

		save : function( obj ) {

			var form = $('#form1')[0];
			var formData = new FormData(form);

			$(obj).attr('disabled', true);
			$.ajax({
				url: "/ad/processing/work",
				data: formData,
				type: "POST",
				dataType: "json",
				contentType : false,
				processData : false,
				success: function(res){
					if ( res.success == true ){
						alert("저장되었습니다.");
						location.href='/ad/staff/work_log_view/'+ res.key;
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

var checkboxAll = function(){

	if( $("#target_mb_idx_all").is(':checked') ) {
		$(".target-mb-id").prop("checked", true);
	} else {
		$(".target-mb-id").prop("checked", false);
	}

};
//--> 
</script> 