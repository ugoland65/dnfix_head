<?
	// 변수 초기화
	$_load_page = $_GET['load_page'] ?? $_POST['load_page'] ?? "";
	$_prd_idx = $_GET['prd_idx'] ?? $_POST['prd_idx'] ?? "";
	$_pn = $_GET['pn'] ?? $_POST['pn'] ?? $_pn ?? 1;
	$_where = "";

	if( $_load_page == "prdInfo" ){

		if( $_where ){ $_where .= "AND"; }else{ $_where .= "WHERE"; }
		$_where .= " pc_pd_idx = '".$_prd_idx."' ";

		$data_total = sql_fetch_array(sql_query_error("select * from prd_score where ps_pd_idx = '".$_prd_idx."' AND ps_mode = 'total' "));
		
		if (!is_array($data_total)) {
			$data_total = [];
		}

		if( !($data_total['ps_idx'] ?? '') ){
			$query = "insert prd_score set
				ps_pd_idx = '".$_prd_idx."',
				ps_mode = 'total',
				ps_score = '',
				ps_grade = 0,
				ps_grade_count = 0,
				ps_grade_total = 0,
				ps_count = 0,
				ps_score_total = 0,
				ps_grade_data = '' ";
			sql_query_error($query);
			
			$data_total = sql_fetch_array(sql_query_error("select * from prd_score where ps_pd_idx = '".$_prd_idx."' AND ps_mode = 'total' "));
			
			if (!is_array($data_total)) {
				$data_total = [];
			}
		}

	}

	if( $_pn == "" ) $_pn = 1;

	$total_count = sql_counter("prd_comment", $_where);
	
	$list_num = 100;
	$page_num = 10;

	$total_page  = @ceil($total_count / $list_num);

	$from_record = ($_pn - 1) * $list_num;
    $counter = $total_count - (($_pn - 1) * $list_num);

	$view_page = publicAjaxPaging($_pn, $total_page, $list_num, $page_num, "onaDBprdComment.list", "");

	$query = "select 
		*
		from prd_comment A 
		left join "._DB_COMPARISON." B ON (B.CD_IDX = A.pc_pd_idx) 
		left join user C ON (C.user_idx = A.pc_user_idx) 
		".$_where." ORDER BY pc_idx desc limit ".$from_record.", ".$list_num;
	$result = sql_query_error($query);

	$_score_mode['before'] = "일반 한줄평";
	$_score_mode['after'] = "사용자 한줄평";

	$_reg_mode['BG'] = "일반";
	$_reg_mode['IG'] = "가상";
	$_reg_mode['AD'] = "어드민";

?>
<style type="text/css">
.table-style{}
.table-style tr {}
.table-style tr th{ text-align:center; }
.no-image{ display:inline-block; width:70px; height:70px; line-height:70px; box-sizing:border-box; border:1px solid #ddd; background-color:#eee; 
	color:#999; font-size:11px;
}
.score-mode-box{ display:inline-block; font-size:12px; padding:3px; border-radius:5px; }
.score-mode-box.before{ background-color:#eee; border:1px solid #aaa; }
.score-mode-box.after{ background-color:#c5e6ff; border:1px solid #40a6f5; }
.score-box-wrap{}
.score-box{ display:inline-block; font-size:12px; background-color:#eee; border:1px solid #aaa; padding:3px 5px; margin:2px 0; border-radius:5px;  }

.comment-write{ background-color:#fff; border:1px solid #aaa; padding:20px; margin-bottom:20px; }
</style>

<? if( $_load_page == "prdInfo" ){ ?>
	<div class="comment-write" id="comment_form">
		
		<form id="form1">
		<input type="hidden" name="a_mode" value="onadb_commWrite" >
		<input type="hidden" name="pd_idx" value="<?=$_prd_idx ?? ''?>" >
		
		<div class="">
			<select name="pc_score_mode" id="pc_score_mode" class="m-r-10">
				<option value="before">일반 한줄평</option>
				<option value="after">사용자 한줄평</option>
			</select>
			<? 
				if (!isset($_gva_koedge_onadb_score_option) || !is_array($_gva_koedge_onadb_score_option)) {
					$_gva_koedge_onadb_score_option = [];
				}
				for ($i=0; $i<count($_gva_koedge_onadb_score_option); $i++){ 
					$_ii = $i + 1;
			?>
			<select name="pc_score_<?=$_ii?>" id="pc_score_<?=$_ii?>" disabled>
				<option value="1"><?=$_gva_koedge_onadb_score_option[$i] ?? ''?></option>
				<? 
					for ($z=0; $z<10; $z++){ 
						$_zz = $z + 1;
				?>
				<option value="<?=$_zz?>"><?=$_zz?>점</option>
				<? } ?>
			</select>
			<? } ?>
		</div>

		<div class="m-t-5">
			<input type='text' name='name' id='comm_name' placeholder="익명">

			<select name="pc_grade" id="pc_grade" class="m-l-10">
				<option>개인평점</option>
				<? for ($z=1; $z<=10; $z++){ ?>
				<option value="<?=$z?>"><?=$z?>점</option>
				<? } ?>
			</select>
		</div>

		<div class="m-t-5">
			<ul><textarea name="body" id="body" style="height:70px"></textarea></ul>
			<ul><button type="button" id="comment_write_btn" class="btnstyle1 btnstyle1-primary btnstyle1-sm" onclick="prdCommentList.comment();">코멘트 등록</button></ul>
		</div>

		</form>

		<div class="m-t-10">
			개인평점 : <b><?=$data_total['ps_grade'] ?? 0?></b> | 
			개인평점 카운터 : <b><?=$data_total['ps_grade_count'] ?? 0?></b> |
			개인평점 총합 : <b><?=$data_total['ps_grade_total'] ?? 0?></b>
			<button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-xs" onclick="prdCommentList.gradeReset('<?=$_prd_idx ?? ''?>');" >개인평점 갱신</button>
		</div>

		<div class="m-t-10">
			<? 
/*
{"score":{"1":{"name":"자극/기믹","count":2,"score_sum":18,"score_avg":9},"2":{"name":"유지관리","count":2,"score_sum":13,"score_avg":6.5},"3":{"name":"냄새/유분/소재","count":2,"score_sum":17,"score_avg":8.5},"4":{"name":"조임/탄력","count":2,"score_sum":17,"score_avg":8.5},"5":{"name":"마감/내구성","count":2,"score_sum":18,"score_avg":9},"6":{"name":"조형/패키지","count":2,"score_sum":15,"score_avg":7.5},"7":{"name":"진공","count":2,"score_sum":13,"score_avg":6.5}},"total":7.9}
*/

				$_ps_score_data = json_decode($data_total['ps_score'] ?? '{}', true);
				if (!is_array($_ps_score_data)) {
					$_ps_score_data = [];
				}
				
				$score_data = $_ps_score_data['score'] ?? [];
				if (!is_array($score_data)) {
					$score_data = [];
				}

				for ($i=1; $i<=count($score_data); $i++){ 
					if (!isset($score_data[$i]) || !is_array($score_data[$i])) continue;
			?>
				<span class="score-box"><?=$score_data[$i]['name'] ?? ''?> : 
					count <b><?=$score_data[$i]['count'] ?? 0?></b> | 
					sum <b><?=$score_data[$i]['score_sum'] ?? 0?></b> | 
					avg <b><?=$score_data[$i]['score_avg'] ?? 0?></b>
				</span>
			<? } ?>
		</div>

	</div>

<? } ?>



<div class="total">Total : <span><b><?=number_format($total_count ?? 0)?></b></span> &nbsp; | &nbsp;  <span><b><?=$_pn ?? 1?></b></span> / <?=$total_page ?? 1?> page</div>

<table class="table-style m-t-6">	
	<tr>
		<th class="tl-check"><input type="checkbox" name="" onclick="select_all()"></th>
		<th class="tl-idx">고유번호</th>

		<? if( $_load_page != "prdInfo" ){ ?>
		<th class="" style="width:80px;">이미지</th>
		<th class="" style="width:80px;">아이콘</th>
		<th width="200px">상품명</th>
		<? } ?>

		<th class="">내용</th>
		<th style="width:70px;">개인평점</th>
		<th style="width:90px;">닉네임</th>
		<th style="width:110px;">등록정보</th>
		<th style="width:70px;">관리</th>
	</tr>
	<?
	while($list = wepix_fetch_array($result)){
		
		if (!is_array($list)) continue;

		$img_path = "";
		$img_path2 = "";
		
		if( $list['CD_IMG'] ?? '' ){
			$img_path = '/data/comparion/'.$list['CD_IMG'];
		}

		if( $list['CD_IMG2'] ?? '' ){
			$img_path2 = '/data/comparion/'.$list['CD_IMG2'];
		}

		$_pc_reg_info = json_decode($list['pc_reg_info'] ?? '{}', true);
		if (!is_array($_pc_reg_info)) {
			$_pc_reg_info = [];
		}

		if( $list['pc_user_idx'] ?? '' ){
			$_user_name = '<i style="font-size:16px; color:#999;" class="fas fa-user-circle"></i> <b>'.($list['user_nick'] ?? '').'</b> ('.($list['user_id'] ?? '').')';
		}else{
			$_user_name = "비회원 : ".($_pc_reg_info['name'] ?? '');
		}

		$_pc_score = json_decode($list['pc_score'] ?? '{}', true);
		if (!is_array($_pc_score)) {
			$_pc_score = [];
		}	

	?>
	<tr align="center" id="trid_<?=$list['pc_idx'] ?? ''?>" bgcolor="<?=$trcolor ?? ''?>">
		<td class="tl-check"><input type="checkbox" name="key_check[]" value="<?=$list['pc_idx'] ?? ''?>" ></td>	
		<td class="tl-idx"><?= $list['pc_idx'] ?? ''?></td>
		
		<? if( $_load_page != "prdInfo" ){ ?>
		<td >
			<? if( $list['CD_IMG'] ?? '' ){ ?>
				<img src="<?=$img_path?>" style="height:70px; border:1px solid #eee !important;">
			<? }else{ ?>
				<div class="no-image">No image</div>
			<? } ?>
		</td>
		<td >
			<? if( $list['CD_IMG2'] ?? '' ){ ?>
				<img src="<?=$img_path2?>" style="height:70px; border:1px solid #eee !important;">
			<? }else{ ?>
				<div class="no-image">No image</div>
			<? } ?>
		</td>
		<td class= "text-left">
			<b onclick="onlyAD.prdView('<?=$list['CD_IDX'] ?? ''?>','info');" style="cursor:pointer;" ><?=$list['CD_NAME'] ?? ''?></b>
		</td>
		<? } ?>

		<td class= "text-left">
			<div class="score-mode-box <?=$list['pc_score_mode'] ?? ''?>"><?=$_score_mode[$list['pc_score_mode'] ?? ''] ?? ''?></div>
			<div class="m-t-5"><?=$list['pc_body'] ?? ''?></div>

			<? if( $list['pc_score'] ?? '' ){ 
				$score_array = $_pc_score['score'] ?? [];
				if (!is_array($score_array)) {
					$score_array = [];
				}
			?>
			<div class="m-t-7">
				<ul class="score-box-wrap ">
				<? 
				for ($i=1; $i<=count($score_array); $i++){ 
					if( $score_array[$i] ?? '' ){
				?>
					<span class="score-box"><?=$score_array[$i]['name'] ?? ''?> : <b><?=$score_array[$i]['score'] ?? ''?></b></span>
				<? } } ?>
				</ul>
				<ul class="m-t-7">
					종합평균 : <b><?=round(($_pc_score['score_avg'] ?? 0),1)?></b>
				</ul>
			</div>
			<? } ?>

		</td>
		<td>
			<b><?=$list['pc_grade'] ?? ''?></b>
		</td>
		<td>
			<div>
				<ul><?=$_reg_mode[$list['pc_reg_mode'] ?? ''] ?? ''?></ul>
				<ul><?=$_user_name?></ul>
			</div>
		</td>
		<td>
		<?
			/*
				echo "<pre>";
				print_r($_pc_reg_info);
				echo "</pre>";
			*/
		?>
			<div>
				<ul><?=!empty($list['pc_reg_date']) ? date("y.m.d <b>H:i:s</b>", strtotime($list['pc_reg_date'])) : ''?></ul>
				<ul class="m-t-3 f-s-11">( <?= $_pc_reg_info['ip'] ?? ''?> )</ul>
				<ul class="m-t-3"><?= $_pc_reg_info['domain'] ?? ''?></ul>
			</div>
		</td>
		<td>
			<div>
				<ul><button type="button" id="" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="prdCommentList.modify('<?= $list['pc_idx'] ?? ''?>')" >수정</button></ul>
				<ul class="m-t-3"><button type="button" class="btnstyle1 btnstyle1-danger btnstyle1-sm" onclick="prdCommentList.del(this, '<?= $list['pc_idx'] ?? ''?>', '<?=$_load_page?>')" >삭제</button></ul>
			</div>
		</td>
	<tr>
	<? }?>
</table>

<div id="hidden_pageing_ajax_data" style="display:none;"><?=$view_page ?? ''?></div>
<script type="text/javascript"> 

var prdCommentList = function() {

	var cmtWindow;

	var C = function() {
	};

	return {
		init : function() {

		},

		comment : function() {

			var formData = $("#form1").serializeArray();

			if( $('#pc_grade').val() == "0" ){
				showAlert("NOTICE", "개인평점 스코어를 입력해 주세요.", "dialog" );
				return false;
			}

			if( $("#body").val() == "" ){
				showAlert("Error", "내용을 입력해 주세요.", "alert2" );
				return false;
			}

			$.ajax({
				url: "/ad/processing/prd",
				data : formData,
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						prdInfo.mode('', 'onadb_comment');
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
					//$(obj).attr('disabled', false);

				}
			});

		},

		modify : function( idx ) {

			var width = "800px";

			cmtWindow = $.alert({
				boxWidth : width,
				useBootstrap : false,
				title : "onaDB 상품 코멘트 수정",
				backgroundDismiss: true,
				closeIcon: true,
				closeIconClass: 'fas fa-times',
				content:function () {
					var self = this;
					return $.ajax({
						url: '/ad/ajax/onadb_prd_comment_info',
						data: { "idx": idx },
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
							
						}
					},
				}
			});

		},

		modifyClose : function() {
			cmtWindow.close();
		},

		//개인평점 갱신
		gradeReset : function( idx ) {
			
			$.ajax({
				url: "/ad/processing/prd",
				data : {"a_mode":"onadb_gradeReset", "prd_idx":idx },
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						prdInfo.mode('', 'onadb_comment');
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
					//$(obj).attr('disabled', false);
				}
			});

		},

		//한줄평 삭제
		del : function( obj, idx, load_page ) {

			$.ajax({
				url: "/ad/processing/prd",
				data : {"a_mode":"onadb_commentDel", "idx":idx },
				type: "POST",
				dataType: "json",
				success: function(res){
					if (res.success == true ){
						
						$(obj).parent().parent().parent().parent().remove();
						if( load_page == "prdInfo" ){
							prdInfo.mode('', 'onadb_comment');
						}

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
					//$(obj).attr('disabled', false);
				}
			});

		}

	};

}();

pageingAjaxShow();

$('#search_reset').click(function(){
	//$("#search_value").val("");
	//orderSheetMain.list();
});

$(function(){

	$("#pc_score_mode").change(function(){
		if($(this).val() == "before"){
			$("#pc_score_1").attr("disabled", true);
			$("#pc_score_2").attr("disabled", true);
			$("#pc_score_3").attr("disabled", true);
			$("#pc_score_4").attr("disabled", true);
			$("#pc_score_5").attr("disabled", true);
			$("#pc_score_6").attr("disabled", true);
			$("#pc_score_7").attr("disabled", true);

		} else if($(this).val() == "after"){
			$("#pc_score_1").attr("disabled", false);
			$("#pc_score_2").attr("disabled", false);
			$("#pc_score_3").attr("disabled", false);
			$("#pc_score_4").attr("disabled", false);
			$("#pc_score_5").attr("disabled", false);
			$("#pc_score_6").attr("disabled", false);
			$("#pc_score_7").attr("disabled", false);

		}
	});

});

$('#comment_write_btn').click(function(){
		
	if( $('#score_mode').val() == "after" ){
		if( $('#score_1').val() == "0" ){
			showAlert("NOTICE", "자극/기믹 스코어를 입력해 주세요.", "dialog" );
			return false;
		}
		if( $('#score_2').val() == "0" ){
			showAlert("NOTICE", "유지관리 스코어를 입력해 주세요.", "dialog" );
			return false;
		}
		if( $('#score_3').val() == "0" ){
			showAlert("NOTICE", "냄새/유분/소재 스코어를 입력해 주세요.", "dialog" );
			return false;
		}
		if( $('#score_4').val() == "0" ){
			showAlert("NOTICE", "조임/탄력 스코어를 입력해 주세요.", "dialog" );
			return false;
		}
		if( $('#score_5').val() == "0" ){
			showAlert("NOTICE", "마감/내구성 스코어를 입력해 주세요.", "dialog" );
			return false;
		}
		if( $('#score_6').val() == "0" ){
			showAlert("NOTICE", "조형/패키지 스코어를 입력해 주세요.", "dialog" );
			return false;
		}
		if( $('#score_7').val() == "0" ){
			showAlert("NOTICE", "진공 스코어를 입력해 주세요.", "dialog" );
			return false;
		}
	}

});
</script> 