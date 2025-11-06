<?
$pageGroup = "board";
$pageName = "board_reg";

	include "../lib/inc_common.php";
	include 'board_inc.php';

	$_b_mode = securityVal($b_mode);

/*
	//게시판 코드가 있을때
	if( $_b_code ) {

		$bo_c_data = wepix_fetch_array(wepix_query_error("select * from "._DB_BOARD_CONFIG." where BOARD_CODE = '".$_b_code."' "));
		$_view_board_name = $bo_c_data[BOARD_NAME];
		if($b_code == 'GUIDE'){
        
		$gd_query = "select * from ".$db_t_GUIDE_MEMBER." where GD_STATE = '1'";
		$gd_result = wepix_query_error($gd_query);
			while($gd_list = wepix_fetch_array($gd_result)){
				$guide_chat_id[] = $gd_list[GD_CHAT_ID];
			}
		 $_guide_chat_id_array=  implode(",",$guide_chat_id);

		}
	}
*/
	if( $_b_mode == "new" ){

		$chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));
		shuffle($chars_array);
		$shuffle = implode('', $chars_array);

		$_board_write_token = substr($shuffle,0,8)."_".time()."_".$_b_code."_".abs(ip2long($_SERVER['REMOTE_ADDR']))."_".$gv_date_code_ym;
		$_show_b_token = $_board_write_token;
		$_show_temp_folder = $gv_date_code_ym;

		$_view_bo_date_ymd = $gv_date_y_m_d;
		$_view_bo_date_hh = date("H", $check_time);
		$_view_bo_date_ii = date("i", $check_time);

		$page_title_text = "게시판 등록";
		$submit_btn_text = "등록";

	}elseif( $_b_mode == "modify" ){

		$bo_data =  wepix_fetch_array(wepix_query_error("select * from "._DB_BOARD." where UID = '".$_b_key."'"));

		$_show_b_token = $bo_data[BOARD_TOKEN];
		$_ary_b_token = explode("_", $bo_data[BOARD_TOKEN]);
		$_show_temp_folder = $_ary_b_token[4];

		$_view_board_category = $bo_data[BOARD_CATEGORY];
		$_show_board_mode = $bo_data[BOARD_MODE];
		$_view_witer_name = $bo_data[BOARD_WITER_NAME];
	
		$_view_bo_date_ymd = date("Y-m-d", $bo_data[BOARD_DATE]);
		$_view_bo_date_hh = date("H", $bo_data[BOARD_DATE]);
		$_view_bo_date_ii = date("i", $bo_data[BOARD_DATE]);

		$_view_hit = $bo_data[BOARD_HIT];
		$_view_grade = $bo_data[BOARD_GRADE];
		$_view_board_subject = $bo_data[BOARD_SUBJECT];
		$_view_board_body = nl2br($bo_data[BOARD_BODY]);

		$_view_pd_idx = $bo_data[BOARD_PD_IDX];
		$_view_link_name = $bo_data[BOARD_LINK_NAME];
		$_view_link_url = $bo_data[BOARD_LINK_URL];

		$_view_board_ip_show = $bo_data[BOARD_IP_SHOW];

		$page_title_text = "게시판 수정";
		$submit_btn_text = "수정"; 
	}


	if( !$_show_board_mode ) $_show_board_mode = "IG2";

include "../layout/header.php";
?>
<STYLE TYPE="text/css">
.ui-datepicker { width: 17em; padding: .2em .2em 0; z-index: 9999 !important; }
#comparison_relation_pd_wrap{}
#comparison_relation_pd_wrap ul{ margin:0 !important; padding:2px !important;}
</STYLE>
<!-- include summernote css/js -->
<link href="/plugins/summernote-master/dist/summernote.css" rel="stylesheet">
<script src="/plugins/summernote-master/dist/summernote.js"></script>

<!-- include summernote-ko-KR -->
<script src="/plugins/summernote-master/dist/lang/summernote-ko-KR.js"></script>

<? /* ?> 
<script type="text/javascript" src="/<?=_A_SYSTEM_SMARTEDITOR?>/js/HuskyEZCreator.js" charset="utf-8"></script>
<? */ ?>

<div id="contents_head">
	<h1><?=$page_title_text?> (<?=$_view_bc_name?>) </h1>
	<div id="head_write_btn">
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

		<div class="table-wrap">

			<form name="bo_write" id="bo_write" action="<?=_A_PATH_BOARD_OK_NEW?>" method="post" enctype="multipart/form-data" autocomplete="off">
<?
if( $_b_mode == "modify" ){
?>
			<input type="hidden" name="a_mode" value="modify" >
			<input type="hidden" name="b_key" value="<?=$_b_key?>" >
			<input type="hidden" name="modify_board_thumbnail" value="<?=$bo_data[BOARD_THUMBNAIL]?>">
<? }else{ ?>
			<input type="hidden" name="a_mode" value="new" >
<? } ?>
			<input type="hidden" name="b_code" value="<?=$_b_code?>">
			<input type="hidden" name="b_token" value="<?=$_show_b_token?>">
			<input type="hidden" name="temp_folder" value="<?=$_show_temp_folder?>">

			<table class="table-reg th-150">
<?
//카테고리 사용시
if( $_show_bc_category_active == "Y" ){
?>
				<tr>
					<th>분류</th>
					<td colspan="3">
						<select name="board_category">
<?
	for($i=1; $i<count($_ary_bc_category); $i++){
		$selected = ( $i == $_view_board_category ) ? "selected" : "";
		echo "<option value=\"".$i."\" ".$selected.">".$_ary_bc_category[$i]."</option>";
	}
?>
						</select>
					</td>
				 </tr>
<? } ?>

				<tr>
					<th>모드</th>
					<td colspan="3">
						<div class="btn-group radio-form" data-toggle="buttons">
							<label class="btn btn-default <? if($_show_board_mode=="BS") echo "active"; ?>" onclick="bo_mode_ig('off');">
								<input type="radio" name="bo_mode" id="bo_mode2" value="BS" autocomplete="off" <? if($_show_board_mode=="BS") echo "checked"; ?>> 일반
							</label>
							<label class="btn btn-default <? if($_show_board_mode=="IG") echo "active"; ?>" onclick="bo_mode_ig('on');">
								<input type="radio" name="bo_mode" id="bo_mode1" value="IG" autocomplete="off" <? if($_show_board_mode=="IG") echo "checked"; ?>> 가상(회원)
							</label>
							<label class="btn btn-default <? if($_show_board_mode=="IG2") echo "active"; ?>" onclick="bo_mode_ig('on');">
								<input type="radio" name="bo_mode" id="bo_mode4" value="IG2" autocomplete="off" <? if($_show_board_mode=="IG2") echo "checked"; ?>> 가상(비회원)
							</label>
							<label class="btn btn-default <? if($_show_board_mode=="NT") echo "active"; ?>" onclick="bo_mode_ig('off');">
								<input type="radio" name="bo_mode" id="bo_mode3" value="NT" autocomplete="off" <? if($_show_board_mode=="NT") echo "checked"; ?>> 공지
							</label>
						</div>
					</td>
				</tr>

				<tr>
					<th>정보</th>
					<td colspan="3">
						<div class="display-table">
							<ul id="board_witer_name_wrap" class="display-table-cell p-r-50 <? if($_show_board_mode == "BS") echo "display-none"; ?>" >
								노출이름 : <input type='text' name='board_witer_name' id='board_witer_name' value="<?=$_view_witer_name?>" style="width:120px;" >
							</ul>
							<ul class="display-table-cell p-r-50">
								작성일 : 
								<input type="text" id="board_date_day" name="board_date_day" value="<?=$_view_bo_date_ymd?>" style="width:80px; cursor:pointer;" class="text-center" />
<? /* ?>
								<select name="board_date_h" style="width:50px;">
		<?
			for($i=0; $i<24; $i++){
				if($i == $gva_nowtime_h){ $selected = "selected"; }else{ $selected = ""; }
				if($i < 10){ $show_i = "0".$i; }else{ $show_i = $i; }
				echo "<option value=\"\" ".$selected.">".$show_i."</option>";
			}
		?>
								</select>
<? */ ?>
								<input type='text' name='board_date_h' id='board_date_h' style="width:30px;" value="<?=$_view_bo_date_hh?>" class="text-center"> :
								<input type='text' name='board_date_s' id='board_date_s' style="width:30px;" value="<?=$_view_bo_date_ii?>" class="text-center">
								<label><input type="checkbox" name="board_date_modify" value="Y" checked> 작성일 지정</label>
							</ul>
							<ul class="display-table-cell p-r-50">
								조회수 : 
								<input type='text' name='board_hit' id='board_hit' style="width:50px;"  value="<?=$_view_hit?>" >
							</ul>
							<ul class="display-table-cell p-r-50">
								노출 IP : 
								<input type='text' name='board_ip_show' id='board_ip_show' style="width:100px;"  value="<?=$_view_board_ip_show?>" >
							</ul>
						</div>
					</td>
				</tr>
<!-- 
				<tr>
					<th>추천수</th>
					<td colspan="3"><input type='text' name='board_vote' id='board_vote' style="width:50px;"  value="<?=$data[USE_ID]?>" ></td>
				</tr>
 -->


<?
//평점 기능 사용시
if( $_show_bc_grade_active == "Y" ){
?>
				<tr>
					<th>평점</th>
					<td colspan="3">
						<label><input type="radio" name="bo_grade" value="5" <? if( !$_view_grade || $_view_grade=="5" ) echo "checked"; ?>> <i class="fas fa-star on"></i><i class="fas fa-star on"></i><i class="fas fa-star on"></i><i class="fas fa-star on"></i><i class="fas fa-star on"></i></label>
						<label><input type="radio" name="bo_grade" value="4" <? if( $_view_grade=="4" ) echo "checked"; ?>> <i class="fas fa-star on"></i><i class="fas fa-star on"></i><i class="fas fa-star on"></i><i class="fas fa-star on"></i></label>
						<label><input type="radio" name="bo_grade" value="3" <? if( $_view_grade=="3" ) echo "checked"; ?>> <i class="fas fa-star on"></i><i class="fas fa-star on"></i><i class="fas fa-star on"></i></label>
						<label><input type="radio" name="bo_grade" value="2" <? if( $_view_grade=="2" ) echo "checked"; ?>> <i class="fas fa-star on"></i><i class="fas fa-star on"></i></label>
						<label><input type="radio" name="bo_grade" value="1" <? if( $_view_grade=="1" ) echo "checked"; ?>> <i class="fas fa-star on"></i></label>
<?
	if( $_b_mode == "modify" ){
?>
	| 등록 평점 : <?=$_view_grade?>
<? } ?>
					</td>
				</tr>
<? } ?>

<?
//상품 기능 사용시 // 연관컨텐츠가 아닐경우
if( $_show_bc_product_active == "Y" && $_show_bc_product_mode != "comparison_relation" ){
?>
				<tr>
					<th>상품</th>
					<td colspan="3">
						<input type='text' name='bo_pd_idx' id='bo_pd_idx' size='20' value="<?=$_view_pd_idx?>" style="width:150px"> 상품 IDX
						<!-- <button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-xs" onclick="showPd();" >상품선택</button> -->
					</td>
				</tr>
<? } ?>

<?
//상품 기능 사용시 // 연관컨텐츠 일경우
if( $_show_bc_product_active == "Y" && $_show_bc_product_mode == "comparison_relation" ){
	$bo_relation_result = wepix_query_error("select * from BOARD_A_RELATION where BR_BOARD_CODE='".$_b_code."' and BR_TOKEN='".$_show_b_token."' order by BR_IDX desc");
?>
				<tr>
					<th>연관컨텐츠 상품 추가</th>
					<td colspan="3">
						<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-xs" onclick="addComparisonRelationPd();" >상품 IDX 추가</button>
						<div id="comparison_relation_pd_wrap" class="m-t-3">
<?
while($bo_relation_list = wepix_fetch_array($bo_relation_result)){
?>
							<ul>
								<input type='text' name='relation_pd_idx[]' id='bo_pd_idx' size='20' value="<?=$bo_relation_list[BR_PD_IDX]?>" style="width:150px;" readonly>
								<label style="margin-left:5px;"><input type="checkbox" name="relation_pd_idx_del[]" value="<?=$bo_relation_list[BR_IDX]?>"> 삭제</label> ( idx : <?=$bo_relation_list[BR_IDX]?> )
							</ul>
<? } ?>
							<ul><input type='text' name='relation_pd_idx[]' id='bo_pd_idx' size='20' value="" style="width:150px"></ul>
						</div>
					</td>
				</tr>
<? } ?>

				<tr>
					<th>제목</th>
					<td colspan="3"><input type='text' name='board_subject' id='board_subject' size='20' value="<?=$_view_board_subject?>" ></td>
				 </tr>
				<tr>
					<th>내용</th>
					<td colspan="3">
<!-- 
						<textarea name="board_body" id="ir1" rows="10" cols="100" style="width:100%; height:400px; display:none;"><?=$_view_board_body?></textarea>
 -->
						<textarea name="board_body" id="summernote" ><?=$_view_board_body?></textarea>
					</td>
				</tr>

				<tr>
					<th>링크</th>
					<td colspan="3">
						<input type='text' name='bo_link_name' id='bo_link_name' size='20' value="<?=$_view_link_name?>" placeholder="링크 이름" style="width:150px; margin-right:5px !important;"> 
						<input type='text' name='bo_link_url' id='bo_link_url' size='20' value="<?=$_view_link_url?>" placeholder="링크 주소"style="width:calc(100% - 160px);"> 
					</td>
				</tr>

<?
//이미지 업로드 기능
if( $_show_bc_image_active == "Y" ){
?>
				<tr>
					<th>이미지 업로드</th>
					<td colspan="3"><input type="file" id="file_thum" name="file_thum" ></td>
				</tr>
<? } ?>

<?
//썸네일 업로드 기능
if( $_show_bc_thumbnail_active == "Y" ){
?>
				<tr>
					<th>썸네일 업로드</th>
					<td colspan="3">
						<input type="file" id="board_thumbnail" name="board_thumbnail" >
<?
if( $_b_mode == "modify" ){
	if( $bo_data[BOARD_THUMBNAIL] ){
?>
						<div class="m-t-10">
							<ul><b>등록된 썸네일</b></ul>
							<ul><img src="<?=$bo_data[BOARD_THUMBNAIL]?>" alt=""></ul>
							<ul><?=$bo_data[BOARD_THUMBNAIL]?></ul>
						</div>
<? } } ?>
					</td>
				</tr>
<? } ?>

			</table>
			<div class="submitBtnWrap">

					<button type="button" id="" class="btnstyle1 btnstyle1-inverse btnstyle1-lg" onclick="location.href='<?=_A_PATH_BOARD_LIST?>?b_code=<?=$_b_code?>'" > 
						<i class="fas fa-arrow-left"></i>
						목록
					</button>

					<button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="submitContents(this,'<?=$_b_code?>');" > 
						<i class="far fa-check-circle"></i>
						등록
					</button>

			</div>
			</form>
			
		</div>
		<div style="height:50px;"></div>
	</div>
</div>

	<form name='imguploadform'  id='imguploadform'>
		<input type="hidden" name="a_mode" value="imgUpload" >
		<input type="hidden" name="b_code" value="<?=$_show_b_code?>" >
		<input type="hidden" name="b_token" value="<?=$_show_b_token?>" >
		<input type="hidden" name="temp_folder" value="<?=$_show_temp_folder?>" >
	</form>


<script type="text/javascript">

	var boardMode = "<?=$_show_board_mode?>";

	$("#board_date_day").datepicker();
/*
$("#board_date_day").datepicker({
    beforeShow: function(input, inst) { 
        inst.dpDiv.css({"z-index":99999999999});
    }
});
*/

$(document).ready(function() {
	$('#summernote').summernote();
});

$('#summernote').summernote({
	lang: 'ko-KR',
	height: 300,                 // set editor height
	minHeight: null,             // set minimum height of editor
	maxHeight: null,             // set maximum height of editor
	focus: true,                  // set focus to editable area after initializing summernote
	dialogsInBody: true,
/*
	toolbar: [
		// [groupName, [list of button]]
		['style', ['bold', 'italic', 'underline', 'clear']],
		['font', ['strikethrough', 'superscript', 'subscript']],
		['fontsize', ['fontsize']],
		['color', ['color']],
		['para', ['ul', 'ol', 'paragraph']],
		['height', ['height']]
	]
*/
	toolbar: [
		['style', ['style']],
		['font', ['bold', 'underline', 'clear']],
		['fontname', ['fontname']],
		['color', ['color']],
		['fontsize', ['fontsize']],
		['para', ['ul', 'ol', 'paragraph']],
		['table', ['table']],
		['insert', ['link', 'video']],
		['view', ['codeview']],
	]
});

$(function(){
	$("#file_thum").on("change" , showImage);
});

function showImage(e){

    var form = $('#imguploadform')[0];
    var imgData = new FormData(form);

	imgData.append("fileObj", $("#file_thum")[0].files[0]);

	$.ajax({
		data : imgData,
		type : "POST",
		url: "processing.image.php",
		contentType : false,
		processData : false,
		success: function(getdata){
			redatawa = getdata.split('|');
			ckcode = redatawa[1];
			ckmsg = redatawa[2];
			ckimg = redatawa[3];
			ckfilename = redatawa[4];
			if(ckcode == "Processing_Complete"){
				$("#bo_write").append("<input type='hidden' name='uploadImageFileName[]' value='"+ckfilename+"'>");
/*
				var sHTML = "<p><img src=\""+ckimg+"\" class='selProductFile' style='max-width:100%'></p>";
				oEditors.getById["ir1"].exec("PASTE_HTML", [sHTML]);
*/
				$('#summernote').summernote('insertImage', ckimg, function ($image) {
					$image.css('max-width', '100%');
					//$image.attr('data-filename', 'retriever');
				});
			}else if(ckcode == "Erorr"){
				//ajaxLoadingErorrClose();
				$('#modal_alert_msg').html(ckmsg);
				$('#modal-alert').modal({show: true,backdrop:'static'});
			}else{
				//return false;
			}
		}
	});
}


/*
************************************************************************************************************************************************
var oEditors = [];

nhn.husky.EZCreator.createInIFrame({
	oAppRef: oEditors,
	elPlaceHolder: "ir1",
	sSkinURI: "/plugins/<?=_A_SYSTEM_SMARTEDITOR?>/SmartEditor2Skin.html",	
	htParams : {
		bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseVerticalResizer : false,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
		fOnBeforeUnload : function(){
			//alert("완료!");
		}
	}, //boolean
	fOnAppLoad : function(){
		//예제 코드
		//oEditors.getById["ir1"].exec("PASTE_HTML", ["로딩이 완료된 후에 본문에 삽입되는 text입니다."]);
	},
	fCreator: "createSEditor2"
});

function pasteHTML() {
	var sHTML = "<span style='color:#FF0000;'>이미지도 같은 방식으로 삽입합니다.<\/span>";
	oEditors.getById["ir1"].exec("PASTE_HTML", [sHTML]);
}

function showHTML() {
	var sHTML = oEditors.getById["ir1"].getIR();
	alert(sHTML);
}

************************************************************************************************************************************************
*/

//상품선택
function showPd() {
	showPopup("500","400","ajax");
}

//연관컨텐츠 상품 추가
function addComparisonRelationPd() {
	var str = "<ul><input type='text' name='relation_pd_idx[]' id='bo_pd_idx' size='20' style='width:150px'></ul>";
	$("#comparison_relation_pd_wrap").append(str);
}

//가상모드로 선택시
function bo_mode_ig(mode) {
	if( mode == "on" ){
		$("#board_witer_name_wrap").removeClass('display-none');
	}else{
		$("#board_witer_name_wrap").addClass('display-none');
	}
/*
	var bo_mode = $(':input:radio[name=bo_mode]:checked').val();
	if( boardMode != "IG" ){
		//$("#board_witer_name_wrap").attr("class", "display-table-cell p-r-50 display-none"); 
		$("#board_witer_name_wrap").removeClass('display-none');
		//alert(bo_mode);
	}else{
		$("#board_witer_name_wrap").addClass('display-none');
	}
	boardMode = bo_mode;
*/
}

function submitContents(elClickedObj,code) {
<?
if ( _GLOB_WS_CODE == 'NIRVANA' ){
?>

	if( code == 'GUIDE' ){
      
        var apiUserId = "wepix.design@gmail.com"; 
		var apiKey = "fc547c63-efe2-11e9-aad3-0a86e61dd930"; 
		var appId = "f08cec4b-03c2-4cdb-b7b2-b40dd4d3d0af"; 

		var messageJson = '{ "messageTitle" : "새로운 공지사항이 등록되었습니다." , "messageContent" : "새로운 공지사항이 등록되었습니다." , "messageLinkUrl" : "http://nirvana.wepix-hosting.co.kr/guide2/board/board_list.php?b_code=GUIDE" }';
		//var sendTargetList = id; 
		//var sendTargetTypeList = "MEMBER"; 
		var sendTargetList = '-1'; 
		var sendTargetTypeList = "ALL_TARGET"; 

			$.ajax({
				url: "http://www.swing2app.co.kr/swapi/push_send",
				type: "post",
				dataType: "json",
				data : {
					app_id : appId,
					send_target_list : sendTargetList,
					send_target_type_list : sendTargetTypeList,
					send_type : 'push' ,
					message_json : messageJson,
					api_user : apiUserId,
					api_key : apiKey
					},success: function (model) {
						console.log("send push message"); 
					}
			 }); 
      
    }

<? } ?>

	if ( $("#board_subject").val() == "" ) {
       // alert("제목을 입력해 주세요.");
        $('#modal_alert_msg').html("제목을 입력해 주세요.");
        $('#modal-alert').modal({show: true,backdrop:'static'});
        $("#board_subject").focus();
        return false;
    }

	if ( $(':input:radio[name=bo_mode]:checked').val() == "IG2" && $("#board_ip_show").val() == "" ) {
        $('#modal_alert_msg').html("가상(비회원)일때 노출 IP는 필수 입니다.");
        $('#modal-alert').modal({show: true,backdrop:'static'});
        $("#board_subject").focus();
        return false;
    }

    $("#bo_write").submit();

/*
************************************************************************************************************************************************
	oEditors.getById["ir1"].exec("UPDATE_CONTENTS_FIELD", []);

    try {
		elClickedObj.form.submit();
	} catch(e) {}
************************************************************************************************************************************************
*/

}

/*
************************************************************************************************************************************************
function setDefaultFont() {
	var sDefaultFont = '궁서';
	var nFontSize = 24;
	oEditors.getById["ir1"].setDefaultFont(sDefaultFont, nFontSize);
}
************************************************************************************************************************************************
*/
</script>
<?
include "../layout/footer.php";
exit;
?>