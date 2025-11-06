<form id="work_comment_big_form">
<input type="hidden" name="a_mode" value="work_comment_reg" >
<input type="hidden" name="work_comment_mode" value="log" >
<input type="hidden" name="kind" value="B" >
<input type="hidden" name="tidx" value="<?=$_tidx?>" >

<textarea name="comment" id="summernote" ><?=$data['body']?></textarea>

</form>

<div class="submitBtnWrap">
	<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="workCommentBig.commentWrite(this);" > 
		<i class="far fa-check-circle"></i> 등록
	</button>
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

var workCommentBig = function() {

	var B;

	var C = function() {
	};

	return {
		init : function() {

		},
		commentWrite : function( obj ) {

			var _comment = $("#summernote").val();

			if( !_comment ){
				showAlert("Error", "댓글내용을 입력해주세요!", "alert2" );
				return false;			
			}

			var formData = $("#work_comment_big_form").serializeArray();

			$.ajax({
				url: "/ad/processing/work",
				data : formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						alert("답변등록 완료!");
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

		},
	};

}();
//--> 
</script> 