<!doctype html>
<html lang="ko">
<head>
<title>디엔픽스 IBS - DNFIX Integrated Business System</title>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="content-style-type" content="text/css" />
	<meta http-equiv="content-script-type" content="text/javascript" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta http-equiv="imagetoolbar" content="no" />
	<meta name="referrer" content="no-referrer">

	<link rel="shortcut icon" href="<?=_A_FOLDER?>/favicon.ico?v4" />
	<!-- 
	<link rel="stylesheet" type="text/css" href="/admin2/css/common.css?ver=<?=time()?>" />
	<link rel="stylesheet" type="text/css" href="/admin2/css/layout.css?ver=<?=time()?>" />
	<link rel="stylesheet" type="text/css" href="/admin2/css/page.css?ver=<?=time()?>" />
	-->

	<!-- FontAwesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" 
		integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" 
		crossorigin="anonymous" referrerpolicy="no-referrer" />

	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

	<!-- jqueryui -->
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.0/themes/base/jquery-ui.css">
	<script
		src="https://code.jquery.com/ui/1.14.0/jquery-ui.min.js"
		integrity="sha256-Fb0zP4jE3JHqu+IBB9YktLcSjI1Zc6J2b6gTjB0LpoM="
		crossorigin="anonymous"></script>

	<link rel="stylesheet" href="/plugins/jquery-confirm-v3.3.4/jquery-confirm.min.css">
	<script src="/plugins/jquery-confirm-v3.3.4/jquery-confirm.min.js"></script>

	<!--toastr -->
	<link href="/plugins/toastr/toastr.min.css" rel="stylesheet">
    <script src="/plugins/toastr/toastr.min.js"></script>

	<!-- Bootstrap -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js"></script>

    <!-- tabulator -->
	<link href="/plugins/tabulator/tabulator.css" rel="stylesheet">
	<script type="text/javascript" src="/plugins/tabulator/tabulator.min.js"></script>

	<script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>

    <!-- select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

	<script src="/assets/js/common.ad.js?ver=<?=time()?>"></script>
	<script src="/admin2/js/common.js?ver=<?=time()?>"></script>
	<script src="/assets/js/global.js?t=<?=time()?>"></script>

	<link href="/admin2/css/common.css?ver=<?=time()?>" rel="stylesheet" >
	<link href="/admin2/css/layout.css?ver=<?=time()?>" rel="stylesheet" >
	<link href="/admin2/css/page.css?ver=<?=time()?>" rel="stylesheet" >
	<link href="/admin2/css/v2-style.css?t=<?=time()?>" rel="stylesheet" >

	<!-- bootstrap 재가공 -->
	<style type="text/css">
	/* 라디오 버튼 */
	.radio-form label{ height:28px !important; font-size:12px !important; border:1px solid #9096a3 !important; }
	.radio-form label.active{ color:#fff !important; text-shadow:none !important; font-weight:bold !important; background-color:#008cd4 !important; }

	label {
		margin-bottom: 0;
		font-weight: 500;
		cursor:pointer;
	}
	</style>

<script language="JavaScript"> 
<!-- 

    $(document).ready(function(){    

		var wContentsWidth = $(window).width() - 210;
        var wContentsHeight = $(window).height() - 81;
        var wContentsHeight2 = $(window).height() - 135;
		
		$("#sc_day_num").datepicker(clareCalendar);
		$("#startDt").datepicker(clareCalendar);
		$("#endDt").datepicker(clareCalendar);
        $("#startDt2").datepicker(clareCalendar);
		$("#endDt2").datepicker(clareCalendar);
		$("#bkp_hot_check_in").datepicker(clareCalendar);
		$("#bkp_hot_check_out").datepicker(clareCalendar);
        $("#search_st").datepicker(clareCalendar);
		$("#search_et").datepicker(clareCalendar);
        $("#booking_date").datepicker(clareCalendar);
        $("#booking_date2").datepicker(clareCalendar);
		$("#_stock_day").datepicker(clareCalendar);
		$(".showCalendar").datepicker(clareCalendar);

		$("img.ui-datepicker-trigger").attr("style","margin-left:5px; vertical-align:middle; cursor:pointer;"); //이미지버튼 style적용
		$("#ui-datepicker-div").hide(); //자동으로 생성되는 div객체 숨김  

	});


    $(window).resize(function(){
		var wContentsWidth = $(window).width() - 210;
        var wContentsHeight = $(window).height() - 85;
        var wContentsHeight2 = $(window).height() - 135;
    });


	showPopup=function(w, h, mode){
		var spWidth = ($(window).width() - (w*1))/2;
        var spHeight = ($(window).height() - (h*1))/2;
		$("#back_mask").fadeTo("300",1 ,function(){
			$('#popup_layer').css({'width':w+'px', 'height':h+'px', 'top':spHeight+'px', 'left':spWidth+'px'}).show();
			$('#popup_layer_body').css({'width':(w - 4)+'px', 'height':(h - 4)+'px'});
			if( mode == "ajax" ){
				$('#popup_layer_body').show();
			}else{
				$('#popup_iframe').show();
				$('#popup_iframe').css({'width':w+'px', 'height':h+'px', 'top':spHeight+'px', 'left':spWidth+'px'});
			}
		});
	};


	closedPopup=function(){
		$('#popup_iframe').hide();
		$('#popup_layer').hide();
		$("#back_mask").hide();
	};

	$(function(){
	 
		//팝업 레이어 닫기
		$('#popup_layer_closed').click(function(){
			closedPopup();
		});
	 
	});


	leftMenuShow=function(){

		var showstate = $("#wrap_left").attr('showstate');

		if( showstate == "show"){
			showstate = $("#wrap_left").attr('showstate','closed');

			var showLeftWidth = $(window).width() - 56;
			$("#wrap_left_body").hide();
			$("#wrap_left").css('background-color','#1f2630'); 
		/*
			$("#wrap_left").stop().animate({"width":"10px"},{queue:false,duration:100}, function(){
			});
		*/
			$("#wrap_left").stop().animate({"width":"6px"}, 100, function(){
				//alert('a');
				$("#left_closed_btn").html('<i class="fa fa-arrow-circle-right" aria-hidden="true"></i>');
			});
			$("#contents").stop().animate({"width":showLeftWidth+"px"},{queue:false,duration:100});
			//alert($("#wrap_left").width()+"/"+showLeftWidth);
		}else{
			showstate = $("#wrap_left").attr('showstate','show');
			var showLeftWidth = $(window).width() - 230;
			$("#wrap_left_body").show();
			$("#wrap_left").css('background-color','#303742'); 
			$("#wrap_left").stop().animate({"width":"179px"}, 100, function(){
				//alert('a');
				$("#left_closed_btn").html('<i class="fa fa-arrow-circle-left" aria-hidden="true"></i>');
			});
			$("#contents").stop().animate({"width":showLeftWidth+"px"},{queue:false,duration:100});
			//alert($("#wrap_left").width()+"/"+showLeftWidth);
		}

	};


	// 언어변경
	function languageChange(val){
		var dis_rate = $('#discount').val();
		$.ajax({
			type: "post",
			url : "<?=_A_PATH_MAIN_OK?>",
			data : { a_mode : "language_change", value : val },
			success: function(getdata) {
				makedata = getdata.split('|');
				ckcode = makedata[1];
				if(ckcode=="Processing_Complete"){
					alert("처리 되었습니다.");
					location.reload();
				}
			}
		});
	}

	// 메모등록
	function memoPopup(mode,idx){
		window.open("/admin2/memo/popup.memo.php?mode="+ mode +"&idx="+ idx, "overlap_"+ mode, "width=800,height=500,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no");
	}

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
//--> 
</script> 

</head>
<body id="basic">

<?php /*
<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>
*/ ?>

<div id="back_mask"></div>
<div id="popup_layer">
	<div id="popup_layer_body" style="display:none;"></div>
	<iframe id="popup_iframe" frameborder="0" marginheight="0" marginwidth="0" scrolling="auto" style="display:none;"></iframe>
	<div id="popup_layer_closed"></div>
</div>

<?
	$_target_mb_text = "@".$_ad_idx;

	$_query = "
		SELECT COUNT(1) AS my_total
		FROM work_comment AS A
		WHERE A.mention_mb LIKE CONCAT('%', '".$_target_mb_text."', '%')
		AND NOT EXISTS (
			SELECT 1
			FROM work_view_check AS B
			WHERE B.mode = A.mode
				AND B.tidx = A.idx
				AND B.mb_idx = '".$_ad_idx."'
		)
	";

	$header_my_count = sql_fetch_array(sql_query_error($_query));

?>

<style type="text/css">
/* 기본 모달 스타일 */
.work-count-modal {
	display:none;
	position: fixed;
	z-index:999999;
	top: 60px;
	transform: translateY(-25px);
	opacity: 0;
	transition: transform 0.2s ease, opacity 0.2s ease;
	right: 40px;
	width: 500px;
	height: calc(100% - 100px);
	overflow-y:scroll;
	padding:20px 30px;
	background-color:#fff;
	border:1px solid #ddd;
	border-radius: 10px;
	box-shadow: 
		0 4px 6px rgba(0, 0, 0, 0.1), 
		0 8px 16px rgba(0, 0, 0, 0.2), 
		0 12px 24px rgba(0, 0, 0, 0.1); /* 여러 단계 그림자 */
}

.work-count-modal::-webkit-scrollbar{ width:6px; height:6px; border-left:solid 1px rgba(255,255,255,.1)}
.work-count-modal::-webkit-scrollbar-thumb{  background:#ddd; border-radius:4px; }

.work-count-modal.show {
 transform: translateY(0);
 opacity: 1;
}
</style>

<div id="work_count_modal" class="work-count-modal">
	
</div>

<script type="text/javascript"> 
<!-- 

$(function(){

	$("#work_count_modal_btn").click(function(){
		$("#work_count_modal").toggleClass('show');

		ajaxRequest("/ad/ajax/main_my_count", { 'cmode':'mention' }, { dataType: 'html' })
			.then((getdata) => {
				$('#work_count_modal').html(getdata); // 데이터 삽입
			})
			.catch((error) => {
				dnAlert('Error', '에러', 'red');
			});
	});

});

//--> 
</script> 

<?
$gnb_lang = "ko";
?>

<div id="header">
	
	<div id="logo"><a href="<?=_A_PATH_MAIN?>"><img src="/admin2/img/logo_dnfix_ibs.png" alt=""></a></div>
	
	<div id="gnb">
		<ul>
            <li class="<?=$gnb_lang?> <? if($pageGroup == "1") echo "active"; ?>"><a href="<?=_A_PATH_MAIN?>">DASHBOARD</a></li><!-- 통계/현황표 -->
            <li class="<?=$gnb_lang?> <?php if( $_pageGroup == "staff" || $pageGroup2 == "staff" ) echo "active"; ?>">
				<a href="/ad/staff/work_log/공지사항">인사/업무</a>
			</li>
			
			<? /*
			<li class="<?=$gnb_lang?> <? if($pageGroup == "product2") echo "active"; ?>"><a href="/admin2/product2/prd2_stock_excel.php">상품관리 v.2</a></li>
            <li class="<?=$gnb_lang?> <? if($pageGroup == "showdang") echo "active"; ?>"><a href="/admin2/showdang/tag.php">쑈당몰</a></li>
			*/ ?>

            <li class="<?=$gnb_lang?> <? if($_pageGroup == "showdang") echo "active"; ?>"><a href="/ad/showdang/brand_group">쑈당몰</a></li>
            <li class="<?=$gnb_lang?> <? if($_pageGroup == "onadb") echo "active"; ?>"><a href="/ad/onadb/onadb_prd_comment">오나디비</a></li>
            <li class="ko <? if($_pageGroup == "prd") echo "active"; ?>"><a href="#"><a href="/ad/prd/prd_db">상품관리</a></li>    
			<li class="ko <? if($_pageGroup == "provider") echo "active"; ?>"><a href="#"><a href="/ad/provider/prd_provider">공급사 관리</a></li>   
            <li class="ko <? if($_pageGroup == "order") echo "active"; ?>"><a href="/ad/order/order_sheet_main">재고/발주</a></li>

			<? /*
			<li class="ko <? if($_pageGroup == "accounting") echo "active"; ?>"><a href="/ad/accounting/payment">회계관리</a></li>
			*/ ?>

            <li class="have-sub-menu">
				<i class="fas fa-external-link-alt" style="font-size:0.6em;"></i> 공유문서
				<div class="sub-menu">
					<ul>
						<li>
							<a href="https://docs.google.com/spreadsheets/d/1k0fVa1n5QrByZ4FHXQuJaYy_EbTxMFIZ8MtaWBvkcU0" target="_blank">
								<i class="fas fa-external-link-alt" ></i> 일정관리
							</a>
						</li>
						<li>
							<a href="https://drive.google.com/drive/folders/1BVhbM8wRXf3YRNBRWPd4m2JmT-LWoPZW" target="_blank">
								<i class="fas fa-external-link-alt"></i> 공유문서
							</a>
						</li>
						<li>
							<a href="https://docs.google.com/spreadsheets/d/1h-A3grHGYlLXg_HoQ6EEtAY-XgyZShgSGSD7_C-ziXI" target="_blank">
								<i class="fas fa-external-link-alt"></i> 공유계정정보
							</a>
						</li>
						<li>
							<a href="https://docs.google.com/spreadsheets/d/1NH0YRjOJviOm8-zAX6syom9YfqlusGly69j7PHeWW4I" target="_blank">
								<i class="fas fa-external-link-alt"></i> 구매 요청서
							</a>
						</li>
					</ul>
				</div>
			</li>
			</li>
			<li class="have-sub-menu">
				<i class="fas fa-external-link-alt" style="font-size:0.6em;"></i> 외부링크
				<div class="sub-menu">
					<ul>
						<li>
							<a href="http://gdadmin.dnfix202439.godomall.com/base/login.php" target="_blank">
								<i class="fas fa-external-link-alt"></i> 고도몰 관리자
							</a>
						</li>
						<li>
							<a href="https://showdang.co.kr" target="_blank">
								<i class="fas fa-external-link-alt"></i> 쑈당몰 PC
							</a>
						</li>
						<li>
							<a href="https://m.showdang.co.kr" target="_blank">
								<i class="fas fa-external-link-alt"></i> 쑈당몰 모바일
							</a>
						</li>
					</ul>
				</div>


			
			</li>
		</ul>
	</div>

	<div class="admin-work-count right" id="work_count_modal_btn">
		<i class="fas fa-bell"></i>
	</div>

	<div class="admin-work-count" onclick="footerGlobal.comment()" >
		<i class="fas fa-comment-dots"></i>
		<? if( $header_my_count['my_total'] > 0 ){ ?><div class="header-my-count"><?=$header_my_count['my_total']?></div><? } ?>
	</div>

	<div class="admin-info" id="admin_info" style="cursor:pointer;" >
		<ul>
			<li>
				<div  class="admin-icon" id="admin_icon">
					<? if( $_ad_image ){ ?>
					<img src="/data/uploads/<?=$_ad_image?>" alt="">
					<? }else{ ?>
					<img src="/admin/img/admin_icon.png" alt="">
					<? } ?>
				</div>
			</li>
			<li>
				<span><?=$_ad_nick?></span>
			</li>
		</ul>
	</div>

	<div class="" id="admin_logout" onclick="location.href='<?=_A_PATH_LOGOUT?>'" style="cursor:pointer;">
        <i class="fa fa-power-off logout-i" aria-hidden="true"></i>
	</div>

	<!-- 
	<div id="admin_language">
		<div id="flag" style="display:none;">
			<ul id="flag-title">LANGUAGE</ul>
			<ul>
				<li onclick="languageChange('usa');"><img src="/data/img/icon/flag/usa.png" alt=""> EN</li>
				<li onclick="languageChange('kor');"><img src="/data/img/icon/flag/kor.png" alt=""> KO</li>
				<li onclick="alert('준비중 입니다.');"><img src="/data/img/icon/flag/tha.png" alt=""> TH</li>
				<li onclick="alert('준비중 입니다.');"><img src="/data/img/icon/flag/vnm.png" alt=""> VN</li>
			</ul>
		</div>
		<div id="my_language"></div>
	</div>
	-->

</div><!-- #header  -->

<div id="wrap">
	<div id="wrap_table">
		<div id="wrap_left_icon" >
			<ul id="wli_dashboard" class="" onclick="location.href='<?=_A_PATH_MAIN?>'" ><i class="fas fa-home"></i></ul>
			<ul id="wli_config" class="<? if($pageGroup == "staff") echo "active"; ?>" onclick="adminLayout.sideIcon('staff')" data-toggle="tooltip" data-placement="right" title="인사/업무"><i class="fas fa-people-carry"></i></ul>
			<ul id="wli_config" class="hold"><i class="fas fa-cog"></i></ul>
			<ul id="wli_member" class="hold"><i class="fas fa-user" ></i></ul>
			<ul id="wli_comparison" class="hold"><i class="fab fa-hackerrank"></i></ul>
			<ul id="wli_product2" class="hold"><i class="fas fa-cube"></i></ul>
			<ul id="wli_board"class="hold"><i class="fas fa-comment-dots"></i></ul>
			<ul id="wli_chart" class="hold"><i class="fas fa-chart-bar"></i></ul>
			<ul id="wli_partner" class="hold"><i class="fas fa-hands-helping"></i></ul>
		</div>
		
		<div id="wrap_left" showstate="show" class="<? if( $_pageN == "main" ) echo "display-none"; ?> ">
			<div id="wrap_left_wrap">
				<div id="left_closed_btn" onclick="leftMenuShow();"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i></div>
				<div id="wrap_left_body">
					<?
					if( $pageGroup ){
						$menu_file = "../".$pageGroup."/".$pageGroup."_menu.php";
						if( file_exists($menu_file) ){
							include $menu_file;
						}
					}
					if( $_pageGroup ){
						$menu_file2 = $_dir."/skin.menu_".$_pageGroup.".php";
						if( file_exists($menu_file2) ){
							include $menu_file2;
						}
					}
					if ($pageGroup2) {
						include($docRoot . "/admin2/skin/skin.menu_" . $pageGroup2 . ".php");
					}
					?>
				</div><!-- #wrap_left_body -->
				<?
				/*
				if( _A_GLOB_D_CAPACITY_TOTAL > 0 ){
					$_capacity_du = `du $docRoot -sk`;
					$_capacity_du = $_capacity_du/1000;
					$_capacity_percentage = round(($_capacity_du/_A_GLOB_D_CAPACITY_TOTAL)*100,2);
				?>
					<div id="remaining_server_capacity">
						<div>
							<ul class="capacity-disk">DISK <?=_A_GLOB_D_CAPACITY_TEXT?> / <?=$_capacity_du?>MB</ul>
						</div>
						<div class="progress">
							<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?=$_capacity_percentage?>%;">
								<?=$_capacity_percentage?>%
							</div>
						</div>
					</div>
				<? 
					}
				*/
				?>
			</div><!-- #wrap_left_wrap -->
		</div><!-- #wrap_left -->

		<div id="contents">