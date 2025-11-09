<!doctype html>
<html lang="ko">
<head>

	<title><?=$meta_title ?? '오나디비'?></title>
	<meta charset="utf-8">

	<meta name="Referrer" content="origin">
	<meta name="referrer" contents="always">
	<meta name="robots" content="index, follow" />

	<meta property="og:site_name" content="<?=$meta_site_name ?? '오나디비'?>" />
	<meta property="og:type" content="website">
	<meta property="og:title" content="<?=$og_title ?? '오나디비 - 【최신 오나홀 DB】 평점, 순위, 후기, 리뷰'?>">
	<meta property="og:image" content="<?=$og_image ?? ''?>"/>
	<meta property="og:description" content="<?=$meta_description ?? '오나디비 국내 유일 최대 오나홀 데이터를 활용한 평점과 순위, 사용자의 디테일한 세부 평점을 보실 수 있습니다.'?>">
	<meta property="og:url" content="<?=$meta_url ?? 'https://onadb.net'?>">

	<meta name="twitter:card" content="summary">
	<meta name="twitter:title" content="<?=$meta_title ?? '오나디비'?>">
	<meta name="twitter:description" content="<?=$meta_description ?? '오나디비 국내 유일 최대 오나홀 데이터를 활용한 평점과 순위, 사용자의 디테일한 세부 평점을 보실 수 있습니다.'?>">

	<meta name="title" content="<?=$meta_title ?? '오나디비'?>"/>
	<meta name="subject" content="<?=$meta_title ?? '오나디비'?>"/>
	<meta name="description" content="<?=$meta_description ?? '오나디비 국내 유일 최대 오나홀 데이터를 활용한 평점과 순위, 사용자의 디테일한 세부 평점을 보실 수 있습니다.'?>"/>
	<meta name="keywords" content="<?=$meta_keywords ?? '오나홀, 추천, 평점, 순위, 리뷰, 아키바팜, 프리바디, 쑈당몰, 오나미몰, 리얼맥스, 바나나몰, 프리바디, 오나왕, 딩동몰, 엠즈, 누딩이, 누딩이2, av, 성인용품, 미국오나홀, 마녀의 유혹, 님패트, 성처리, 핸디 후기, 대마왕, 배드드래곤, ㅍㄼㄷ, ㅆㄷ, ㅇㄴㅁ, ㄹㅇㅁㅅ, ㅇㅋㅂㅍ, ㅂㄴㄴ, 좃나나'?>">

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="content-style-type" content="text/css" />
	<meta http-equiv="content-script-type" content="text/javascript" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta http-equiv="imagetoolbar" content="no" />
	<meta name="format-detection" content="telephone=no">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, shrink-to-fit=no">

	<link rel="shortcut icon" href="/public/onadb/favicon/favicon.ico">
	<link rel="icon" type="image/png" sizes="192x192"  href="/public/onadb/favicon/favicon_192.png">
	<link rel="icon" type="image/png" sizes="96x96" href="/public/onadb/favicon/favicon_96.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/public/onadb/favicon/favicon_32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/public/onadb/favicon/favicon_16.png">

	<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">

	<script src="/plugins/jquery/jquery-3.6.0.min.js"></script>

	<link rel="stylesheet" href="/plugins/jquery-confirm-v3.3.4/jquery-confirm.min.css">
	<script src="/plugins/jquery-confirm-v3.3.4/jquery-confirm.min.js"></script>

	<!--toastr -->
	<link rel="stylesheet" href="/plugins/toastr/toastr.min.css" >
    <script src="/plugins/toastr/toastr.min.js"></script>

	<!-- bootstrap -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

	<!-- FontAwesome -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

	<script src="/assets/js/common.js?ver=<?=time()?>"></script>

	<link rel="stylesheet" type="text/css" href="/assets/css/common.css?v=<?=time()?>" />
	<link rel="stylesheet" type="text/css" href="/assets/css/button.css?v=<?=time()?>" />
	<link rel="stylesheet" type="text/css" href="/public/onadb/css/css.layout.css?v=<?=time()?>" />

<?php
/*
<script type="text/javascript"> 
<!-- 
var UC_APP = {};
<? if( $_sess_id ){ ?>
var UC_APP_GLOBAL_USER = { account : "<?=$_sess_id?>" };
<? }else{ ?>

<? } ?>
//--> 
</script>
*/
?>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-R04W3DHFC2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-R04W3DHFC2');
</script>
</head>
<body>
<?php
// 변수 초기화
$_sess_id = $_sess_id ?? null;
$_keyword = $_keyword ?? '';
$_user_nick = $_user_nick ?? '';
$_user_point = $_user_point ?? 0;
$_side_layout_show = $_side_layout_show ?? '';
?>
<div id="header">
	<div class="header-logo-wrap">
		
		<a class="header-btn menu" id="btn-menu"><i class="fa fa-bars"></i></a>
		<div class="header-logo"><a href='/'><img src="/public/onadb/img/header_logo.png" alt="오나디비" /></a></div>
		<a class="header-btn search" ><i class="fas fa-search"></i></a>

		<? if( $_sess_id ){ ?>
			<a class="header-btn my" href="/mypage" ><i class="fas fa-user"></i></a>
		<? }else{ ?>
			<a class="header-btn my" href="/login" ><i class="fas fa-user"></i></a>
		<? } ?>

		<div class="header-search">
			<ul>
				<input type="text" id="header_search_text" name="search_text" value="<?=$_keyword ?? ''?>" >
			</ul>
			<ul class="header-search-btn"><button id="header_search"><i class="fas fa-search"></i></button></ul>
		</div>

	</div> 
</div>
<!-- #header  -->

<div class="gnb-wrap">
	
	<div class="gnb">

		<div class="mobile menu-close"><i class="fas fa-times"></i></div>

		<div class="mobile gnb-logo"><a href='/'><img src="/public/onadb/img/header_logo.png" alt="오나디비" /></a></div>

		<ul><a href="/">전체보기</a></ul>
		<ul><a href="/tier/1">1티어 홀</a></ul>
		<ul><a href="/tier/2">2티어 홀</a></ul>
		<ul><a href="/brandlist">브랜드</a></ul>
		<!-- <ul><a href="/freemarket">프리마켓 <span class="beta">Beta</span></a></ul> -->

		<div class="pc gnb-menu-right">
			<? if( $_sess_id ){ ?>
				<span class="m-r-5"><a href="/logout">로그아웃</a></span> |
				<span class="m-l-5"><i class="fas fa-user"></i> <a href="/mypage"><?=$_user_nick?></a></span>
				<span class="m-l-5"><i class="fab fa-product-hunt"></i> <a href="/mypage"><?=number_format($_user_point)?></a></span>
			<? }else{ ?>
				<span class="m-r-5"><a href="/login">로그인</a></span> |
				<span class="m-l-5"><a href="/join">회원가입</a></span>
			<? } ?>
		</div>

	</div>

</div>

<script type="text/javascript"> 
<!-- 
$(function(){

	$("#btn-menu").click(function(){
		$(".gnb-wrap").animate({"left":"0"},100);
		$('html, body').addClass('sc-hidden');
		//$(".info-box-bg").show();
	});

	$(".menu-close i").click(function(){
		$(".gnb-wrap").animate({"left":"-100%"},100);
		$('html, body').removeClass('sc-hidden');
		$(".info-box-bg").hide();
	});

});
//--> 
</script> 

<div id="contents_wrap" class="contents-wrap <? if( $_side_layout_show == "on" ){ ?>side<? } ?>" >
	<ul>

        <?= $content ?? '' ?>

        </ul>

<? if( $_side_layout_show == "on" ){ ?>
<ul class="left-ul">
    
    <div class="new-comment">
        <h1>최근 한줄평</h1>
        
        <div class="side-comment-list-wrap">
        <?php
        // View Composer에서 자동 주입된 recent_comments 사용
        $recent_comments = $recent_comments ?? [];
        
        if (!empty($recent_comments)) {
            foreach ($recent_comments as $comment) {
        ?>
            <ul class="">
                <div>
                    <ul class="side-comment-prd-name">
                        <a href="/pv/<?= htmlspecialchars($comment['pc_pd_idx'] ?? '') ?>">
                            <?= htmlspecialchars($comment['pc_title'] ?? '제품명') ?>
                        </a>
                    </ul>
                    <ul class="side-comment-body">
                        <a href="/pv/<?= htmlspecialchars($comment['pc_pd_idx'] ?? '') ?>">
                            <?= htmlspecialchars($comment['pc_body'] ?? '') ?>
                        </a>
                    </ul>
                </div>
            </ul>
        <?php
            }
        } else {
        ?>
            <ul class="">
                <div>
                    <ul class="side-comment-body" style="text-align: center; padding: 20px; color: #999;">
                        등록된 한줄평이 없습니다.
                    </ul>
                </div>
            </ul>
        <?php
        }
        ?>
        </div>

    </div>

</ul>
<? } ?>

</div><!-- #wrap -->

<div id="footer">
<div id="footer_wrap">
    <ul>Copyright © <b>ONA DB</b> All rights reserved.</ul>
    <ul>help.onadb@gmail.com</ul>
    <div id="footer_social">

    </div>
</div>
</div><!-- #footer  -->

<div class="display-none">
은지 오나홀 스푸닝 이채담 채승하 승하 인간 소프트 하마사키 마오 남자 일회용 관통형 가성비 진동 av배우 카와 키타 사이카 종류 대마왕 전라생츄 누딩이 누딩이2 적나라 님페트 오나홀 사이트 핸디후기 핸디오나홀
</div>

<div class="loading-mask"></div>
<div class="loading-content">
<span class="dots-flow"></span>
</div>

<form name='login_form' id='login_form' method='post' autocomplete='off'>
<input type="hidden" name="return_url" id="login_return" >
</form>

<script src="/public/onadb/common.js?ver=<?=time()?>"></script>
</body>
</html>