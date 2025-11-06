<?
	$query_field = "*";

	$query = "select ".$query_field."
		from "._DB_COMPARISON." A 
		left join prd_contents B ON (B.cd_idx = A.CD_IDX) WHERE A.CD_IDX = '".$_idx."' ";

	$pv_data = wepix_fetch_array(wepix_query_error($query));

	if($pv_data[CD_IMG] == ''){
		$img_path = '/test_pd_img.jpg';
	}else{
		$img_path = '/dist/img/'.$pv_data[CD_IMG];
	}

	$_img_class= "img-blur";
	if( $pv_data[c19] == "N"){
		$_img_class= "";
	}
/*
$pv_data = wepix_fetch_array(wepix_query_error("select * from "._DB_COMPARISON." where CD_IDX = '".$_idx."' "));

$prd_cont_data = wepix_fetch_array(wepix_query_error("select * from prd_contents where cd_idx = '".$_idx."' "));
*/

	if($pv_data[CD_IMG] == ''){
		$img_path = '/test_pd_img.jpg';
	}else{
		$img_path = '/dist/img/'.$pv_data[CD_IMG];
	}


?>
<STYLE TYPE="text/css">



</STYLE>
<link rel="stylesheet" href="/dist/css/prd_view.css?ver=<?=$check_time?>">

<div class="pv-wrap">

	<div class="pv-info">
		<ul class="pd-name"><?=$pv_data[CD_NAME]?></ul>
		<? if( $pv_data[CD_NAME_OG] ){ ?>
		<ul class="pd-name-og"><?=$pv_data[CD_NAME_OG]?></ul>
		<? } ?>
		<ul class="pd-info display-none">

		</ul>
	</div>

	<div class="pv-pd display-table">
		<ul class="img display-table-cell v-align-top">
			<div class="pv-pd-wrap">
				<ul><div class="img-box"><img src="<?=$img_path?>" class="<?=$_img_class?>"></div></ul>
			</div>
		</ul>
		<ul class="display-table-cell v-align-top">
			<div class="prd-detail-info">
<? if( $pv_data[CD_NAME_OG] ){ ?>
				<ul>
					<li class="sname">원상품명 :</li>
					<li class="svalue"><?=$pv_data[CD_NAME_OG]?></li>
				</ul>
<? } ?>

<? if( $pv_data[CD_NAME_EN] ){ ?>
				<ul>
					<li class="sname">해외명 :</li>
					<li class="svalue"><?=$pv_data[CD_NAME_EN]?></li>
				</ul>
<? } ?>

<?
if( $pv_data[CD_RELEASE_DATE] ){
?>
				<ul>
					<li class="sname">출시일 :</li>
					<li class="svalue"><?=$pv_data[CD_RELEASE_DATE]?></li>
				</ul>
<? } ?>

<?
if($pv_data[CD_BRAND_IDX]){
?>
				<ul>
					<li class="sname">브랜드 :</li>
					<li class="svalue"><a href="/front/product/brand_view.php?idx=<?=$pv_data[CD_BRAND_IDX]?>" target="_blank"><?=$_view_brand_name?></a></li>
				</ul>
<? } ?>

<?
if($pv_data[CD_SIZE]){
?>
				<ul>
					<li class="sname">패키지 :</li>
					<li class="svalue"><?=$pv_data[CD_SIZE]?></li>
				</ul>
<? } ?>

<?
if($pv_data[CD_SIZE2]){
	if($pv_data[CD_KIND_CODE] == "GEL" ){
		$_sname_cd_size2 = "용량";
		$_sunit_cd_size2 = "ml";
	}else{
		$_sname_cd_size2 = "내부길이";
		$_sunit_cd_size2 = "cm";
	}
?>
				<ul>
					<li class="sname"><?=$_sname_cd_size2?> :</li>
					<li class="svalue"><?=$pv_data[CD_SIZE2]?> ( <?=$_sunit_cd_size2?> )</li>
				</ul>
<? } ?>

<?
if($pv_data[CD_WEIGHT]){

?>
				<ul>
					<li class="sname">제품중량 :</li>
					<li class="svalue"><?=$pv_data[CD_WEIGHT]?>  ( g )</li>
				</ul>
<? } ?>

<?
if($pv_data[CD_WEIGHT2]){
?>
				<ul>
					<li class="sname">전체중량 :</li>
					<li class="svalue"><?=$pv_data[CD_WEIGHT2]?> ( g )</li>
				</ul>
<? } ?>

			</div>

		</ul>
		<ul class="graph display-table-cell v-align-top">

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>

<script>
$(function(){
	var color = Chart.helpers.color;
	var config = {
		type: 'radar',
		data: {
			labels: [['자극','(0)'], ['유지관리','(0)'], ['조임','(0)'],['냄새','(0)'],['유분','(0)']],
			datasets: [{
				label: '항목평점',
				backgroundColor: 'rgba(54, 162, 235, 0.05)',
				borderColor: 'rgb(54, 162, 235)',
				pointBackgroundColor: 'rgb(54, 162, 235)',
				data: ['0','0','0','0','0']
			}]
		},
		options: {
			legend: {
				position: 'bottom',
				labels: {
					fontColor: 'tomato',
					fontSize: 12
				},
				display: false
			},
			title: {
				display: true,
				text: '',
				fontSize: 13,
				padding: 0,
				fontColor: 'tomato'
			},
			scale: {
				ticks: {
					beginAtZero: true,
					max: 12,
					min: 0,
					stepSize: 2,
					fontColor: 'gray'
				},
				pointLabels:{
					fontSize:13,
					fontColor:'#172032',
				}
			},
			tooltips: {
				position: 'nearest',
				mode: 'index',
				intersect: false,
				yPadding: 20,
				xPadding: 20,
				caretSize: 8
			}
		}
	};

	window.myRadar = new Chart(document.getElementById('eval_canvas'), config);
});
</script>
<STYLE TYPE="text/css">
#eval_canvas{ padding:3px 0 10px 10px; box-sizing:border-box; }
</STYLE>
			<div class="pd-graph-wrap">
				<canvas id="eval_canvas" style="width:100%; height:350px; "></canvas>
			</div>
		</ul>
	</div>

</div>


<div class="fixed-menu">
	<div class="fixed-menu-wrap">
		<ul>
			<div class="fixed-menu-pd-info display-table">
				<ul class="display-table-cell fixed-menu-pd-img"><img src="<?=$img_path?>" alt=""></ul>
				<ul class="display-table-cell v-align-top">
					<li class="fixed-menu-pd-info-name"><?=$pv_data[CD_NAME]?></li>
					<? if( $pv_data[CD_PRICE] > 0 ){ ?>
					<li class="fixed-menu-pd-info-price">최저가 : <?=number_format($pv_data[CD_PRICE])?> 원</li>
					<? } ?>
				</ul>
				<ul class="display-table-cell" style="padding-left:20px;">
					<? if( $pv_data[CD_LINK] ){ ?>
					<a href="<?=$pv_data[CD_LINK]?>" target="_blank"><button type="button" class="basic-btn basic-btn-blue basic-btn-sm" style="height:40px !important;">최저가 사러가기</button></a>
					<? } ?>
				</ul>
			</div>

			<div class="pv-view-tap">
<?
if( $pv_data[CD_LINK_COUNT] > 0 ){
?>
				<ul class="active"><a target="#C_detail6">가격비교 <span class="pv-view-site-count">(<?=$pv_data[CD_LINK_COUNT]?>)</span></a></ul>
<? } ?>
				<ul><a target="#C_detail1">상품리뷰</a></ul>
				<ul><a target="#C_detail2">상품 한줄평</a></ul>
				<ul><a target="#C_detail3">제품정보</a></ul>
				<ul><a target="#C_detail4">연관 컨텐츠</a></ul>
				<ul><a target="#C_detail5">추천상품</a></ul>
			</div>

		</ul>
		<ul></ul>
	</div>
</div>

<div class="pv-title"  id="C_detail2">
	상품 한줄평
</div>

<STYLE TYPE="text/css">
.comment-write{ border:1px solid #dedede; background-color:#f5f8f9; box-sizing:border-box; padding:10px; margin:0; }
.comment-write-wrap{ width:100%; display:table; }
.comment-write-wrap ul{ display:table-cell; vertical-align:top; }
.comment-write-wrap ul textarea{ width:100%; height:85px; border:1px solid #d0d0d0; box-sizing:border-box; padding:10px; }
.comment-write-btn{ width:95px; box-sizing:border-box; padding-left:10px; }
.comment-write-btn button{ width:85px; height:85px; color:#fff; background-color:#444444; border:1px solid #222; box-sizing:border-box;}
#comm_name, #comm_pw{ width:200px; }
.comment-write-name-wrap{ margin-bottom:3px; }
</STYLE>

	<div id="pv_comment">
	</div>

	<div class="display-table" style="width:100%;">
		<div class="display-table-cell v-align-top" style="width:50%;">
			
		</div>
		<div class="display-table-cell v-align-top">
			<ul class="comment-write" id="comment_form">
		<? if( $_sess_id ){ ?>
		<? }else{ ?>
				<div class="comment-write-name-wrap">
					<input type='text' name='comm_name' id='comm_name' placeholder="익명">
					<input type='password' name='comm_pw' id='comm_pw' placeholder="비밀번호">
				</div>
		<? } ?>
				<div class="comment-write-wrap">
					<ul><textarea name="comm_body" id="comm_body"></textarea></ul>
					<ul class="comment-write-btn"><button onclick="commentSubmit();"> <i class="fa fa-pencil m-r-5" ></i> 등록  </button></ul>
				</div>
			</ul>
		</div>
	</div>



	<div style="height:50px;">
	</div>


<script type="text/javascript"> 
<!-- 
var kind_code = "<?=$rg_data[CD_KIND_CODE]?>";

function showReviewDetail(key){

	$.ajax({
		url: "ajax.review_detail.php",
		data: {
			"key":key
		},
		type: "POST",
		dataType: "html",
		success: function(getdata){
			$('#popup_body').html(getdata);
			showPopup('650','700','ajax');
		},
		error: function(){
		}
	});
}

function showReview(pn, action){
	$.ajax({
		url: "ajax.review.php",
		data: {
			"key":"<?=$_idx?>",
			"pd_img":"<?=$img_path?>",
			"pn":pn
		},
		type: "POST",
		dataType: "html",
		success: function(getdata){
			$('#pv_review').html(getdata);
		},
		error: function(){
		}
	});

	if( action=="paging"){
		$('html, body').animate({scrollTop: $('#C_detail1').position().top-99}, 300);
	}
}

function showComment(pn){
	$.ajax({
		url: "ajax.comment.php",
		data: {
			"key":"<?=$_idx?>",
			"pn":pn,
			"listnum":"15"
		},
		type: "POST",
		dataType: "html",
		success: function(getdata){
			$('#pv_comment').html(getdata);
		},
		error: function(){
		}
	});
}

function showRelation(pn, action){
	$.ajax({
		url: "ajax.relation.php",
		data: {
			"key":"<?=$_idx?>",
			"pn":pn
		},
		type: "POST",
		dataType: "html",
		success: function(getdata){
			$('#pv_relation').html(getdata);
		},
		error: function(){
		}
	});
/*
	if( action=="paging"){
		$('html, body').animate({scrollTop: $('#C_detail1').position().top-99}, 300);
	}
*/
}

//showReview();
showComment();
//showRelation();

function commentSubmit(mode, key){
	//ajaxLoading();

	if( mode == "modify" ){
		var comm_body = $("#comm_body_"+ key).val();
		var a_mode = "commModify";
		var cKey = key;
	}else{
		var comm_body = $("#comm_body").val();
		var a_mode = "commWrite";
		var cKey = "";
	}

	if( ws_user_id == "" ){

		var commName = $("#comm_name").val();
		var commPw = $("#comm_pw").val();

		if( commName == "" ) commName = "익명";

		if( commPw == "" ){
			$('#modal_alert_msg').html('비밀번호를 입력해주세요.');
			$('#modal-alert').modal({show: true,backdrop:'static'});
			$("#comm_pw").focus();
			return false;
		}

	}else{
	}


	$.ajax({
		url: "processing.comment.php",
		data: {
			"a_mode":a_mode,
			"ajax_mode":"on",
			"pd_key":"<?=$_idx?>",
			"comm_name":commName,
			"comm_pw":commPw,
			"comm_body":comm_body,
			"comm_kind_code":kind_code
		},
		type: "POST",
		dataType: "text",
		success: function(getdata){
			if (getdata){
				redatawa = getdata.split('|');
				ckcode = redatawa[1];
				ckmsg = redatawa[2];
				if(ckcode == "Processing_Complete"){
					//ajaxLoadingClose(ckmsg);
					showComment();
					$('#comm_body').val("");
				}else if(ckcode == "Erorr"){
					//ajaxLoadingErorrClose();
					$('#modal_alert_msg').html(ckmsg);
					$('#modal-alert').modal({show: true,backdrop:'static'});
				}else{
					//return false;
				}
			}
		},
		error: function(){
			$('#modal_alert_msg').html('에러');
			$('#modal-alert').modal({show: true,backdrop:'static'});
		}
	});

}
//--> 
</script>