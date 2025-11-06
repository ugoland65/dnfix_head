<?
// if( !$popup_browser_title ) $popup_browser_title = _A_GLOB_BROWSER_TITEL;
?>
<!doctype html>
<html lang="ko">
<head>
<title><?=$popup_browser_title ?? '팝업창'?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="content-style-type" content="text/css" />
	<meta http-equiv="content-script-type" content="text/javascript" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta http-equiv="imagetoolbar" content="no" />
<!-- 
	<link rel="stylesheet" type="text/css" href="/admin2/css/common.css?ver=<?=$wepix_now_time?>" />
	<link rel="stylesheet" type="text/css" href="/admin2/css/layout.css?ver=<?=$wepix_now_time?>" />
	<link rel="stylesheet" type="text/css" href="/admin2/css/page.css?ver=<?=$wepix_now_time?>" />
 -->


	<!-- FontAwesome -->
	<link href="/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

	<script src="/plugins/jquery/jquery-3.6.0.min.js"></script>

	<script src="/plugins/jquery/jquery.confirm-action.js"></script>

	<!-- jqueryui -->

	<link rel="stylesheet" href="/plugins/jquery-ui-1.13.2/jquery-ui.min.css">
	<script src="/plugins/jquery-ui-1.13.2/jquery-ui.min.js"></script>
<!-- 
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
 -->

	<link rel="stylesheet" href="/plugins/jquery-confirm-v3.3.4/jquery-confirm.min.css">
	<script src="/plugins/jquery-confirm-v3.3.4/jquery-confirm.min.js"></script>

	<!--toastr -->
	<link href="/plugins/toastr/toastr.min.css" rel="stylesheet">
    <script src="/plugins/toastr/toastr.min.js"></script>

	<!-- bootstrap -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

	<script src="/assets/js/common.ad.js?ver=<?=$wepix_now_time?>"></script>
	<script src="/admin2/js/common.js?ver=<?=$wepix_now_time?>"></script>

	<script src="/assets/js/global.js?t=<?=time()?>"></script>

	<link href="/admin2/css/common.css?ver=<?=$wepix_now_time?>" rel="stylesheet" >
	<link href="/admin2/css/layout.css?ver=<?=$wepix_now_time?>" rel="stylesheet" >
	<link href="/admin2/css/page.css?ver=<?=$wepix_now_time?>" rel="stylesheet" >

	<link href="/admin2/css/v2-style.css?t=<?=time()?>" rel="stylesheet" >

	<STYLE TYPE="text/css">
	/* bootstrap 재가공 */ 

	/* 라디오 버튼 */
	.radio-form label{ height:28px !important; font-size:12px !important; border:1px solid #9096a3 !important; }
	.radio-form label.active{ color:#fff !important; text-shadow:none !important; font-weight:bold !important; background-color:#008cd4 !important; }
	</STYLE>

<STYLE TYPE="text/css">
/* Popup */
#popup { position:relative; min-width:400px; box-sizing:border-box; }
#popup #wrap { width:100%; padding:50px 15px 60px; box-sizing:border-box; }
#popup #wrap2 { width:100%; padding:80px 15px 60px; box-sizing:border-box; }
#popup #footer { position:fixed; left:0px; bottom:0px; z-index:300; width:100%; height:40px; padding:5px 0; text-align:center; border-top:1px solid #d6d6d6; background-color:#f5f5f5; }

/* mFixNav */
.mFixNav { padding:0 0 5px !important; top:15px; background:url("//img.echosting.cafe24.com/suio/bg_fixnav.png") repeat-x 0 bottom; }
.mFixNav.fixed { z-index:300; position:fixed; top:0px; left:0px; right:0px; width:100% !important; }
.mFixNav .info { width:100%; height:40px; line-height:40px; font-size:13px; box-sizing:border-box; padding:0 18px; color:#ffffff; background-color:#4b5567; }
.mFixNav .nav { display:table; table-layout:fixed; width:100%; border-bottom:3px solid #85868a; border-right:1px solid #c4c4c4; background-color:#f5f5f5; box-sizing:border-box;  }
.mFixNav .nav li { display:table-cell; vertical-align:middle; border-left:1px solid #c4c4c4; }
.mFixNav .nav a { overflow:hidden; display:inline-block; width:100%; height:43px; line-height:43px; color:#7f7f7f; text-align:center; vertical-align:middle; white-space:nowrap; text-overflow:ellipsis; border-left:1px solid #fcfcfc; background-color:#f5f5f5; box-sizing:border-box; padding:0 !important; }
.mFixNav .nav a:hover { text-decoration:none; color:#000; }
.mFixNav .nav li.br a { padding:6px 0 0 1px; line-height:1.25; }
.mFixNav .nav li.selected { z-index:1; position:relative; }
.mFixNav .nav li.selected a { text-decoration:none; font-weight:bold; color:#fff; border-color:#8c9093; border-right:1px solid #8c9093; letter-spacing:-1px; 
/*
background:#8c9093 url("//img.echosting.cafe24.com/suio/bg_fixnav_selected.gif") repeat-x 0 0; 
*/
	background-color:#2070db; 
	background: -webkit-linear-gradient(180deg, #0088cc, #0044cc);
	background:    -moz-linear-gradient(180deg, #0088cc, #0044cc);
	background:     -ms-linear-gradient(180deg, #0088cc, #0044cc);
	background:      -o-linear-gradient(180deg, #0088cc, #0044cc);
	background:         linear-gradient(180deg, #0088cc, #0044cc);
}

.table-style{ width:100% !important }
.table-style tr th { box-sizing:border-box; padding:9px 0 !important; }
.table-style tr td { box-sizing:border-box; padding:9px !important; }
.tds11 { width:13%; }
.tds22 { width:37%; }

.checkbox-td{ width:25px; }

.prd-quick-left{ width:200px; height:100%; background-color:#fff; border-right:1px solid #9c9fae;  position:fixed; }
.prd-img{ text-align:center; padding-top:20px; }

.prd-quick-info{ margin:0; padding:0; }
.prd-quick-info > ul{ margin:0; padding:3px 0; box-sizing:border-box;text-align:center; }
.prd-brand-name{ padding-top:10px !important; text-align:center;  }
.prd-name{ padding:5px 10px 0 !important; text-align:center; }
.prd-name-en{  text-align:center; } 
.prd-stock-code{ padding-top:7px !important;  text-align:center; }
.prd-stock-code span{ font-size:18px;font-weight:200; color:#999; }
.prd-stock-code b{ font-size:20px; }
.prd-stock-code-make{ text-align:center; } 
.prd-memo{ text-align:center; color:#ff0000; }

.crm-menu{ width:100%; border-top:1px solid #9c9fae; }
.crm-menu ul{  height:35px; line-height:35px; padding:0 0 0 15px; margin:0 !important; box-sizing:border-box; border-bottom:1px solid #9c9fae; cursor:pointer; background-color:#eee;  }
.crm-menu ul.active {
	color:#fff;
	font-weight:bold;
	background-color:#2070db; 
	background: -webkit-linear-gradient(180deg, #0088cc, #0044cc);
	background:    -moz-linear-gradient(180deg, #0088cc, #0044cc);
	background:     -ms-linear-gradient(180deg, #0088cc, #0044cc);
	background:      -o-linear-gradient(180deg, #0088cc, #0044cc);
	background:         linear-gradient(180deg, #0088cc, #0044cc);
}

.crm-wrap{ width:100%; height:calc(100% - 30px); display:table; table-layout: fixed; }
.crm-wrap > ul{ display:table-cell; vertical-align:top; }
.crm-menu-wrap{ width:200px; border-right:1px solid #9c9fae; }
.crm-gap{ width:5px; border-right:1px solid #9c9fae; }
.crm-body{ padding:20px 20px 20px; box-sizing:border-box; background-color:#dddddd; }

.stock-write-box{ padding:15px 15px 0 15px; }
.stock-write-box ul{ font-size:15px; }

.table-style{}
.table-style th{ text-align:center; }
.table-style td.none-bg{ 
	background-color:#dddddd; border:none !important; padding: 0 !important; 
	div{
		display:flex;
		padding:0  0 5px 0;
		>ul{
			&.right{
				margin-left:auto;
			}
		}
		
	}
}
.table-style td.title{}
.table-style td h1{ display:inline-block; font-size:16px; font-weight:600; padding:5px; }
.table-style input[type=text]{ width:100%; }
.img-upload-wrap{ font-size:0; }
.img-upload-wrap > ul{ width:25%; text-align:center; display:inline-block; padding:4px; vertical-align:top; }
.img-upload-wrap > ul > div.img-box{ border:1px solid #ddd; padding:10px; }

.button-wrap-back{ height:60px; }
.button-wrap{ width:calc(100% - 205px); height:60px; line-height:60px; text-align:center; background:rgba(0,0,0,.4); border-top:1px solid #000; position:fixed; bottom:0; right:0;  }
</STYLE>

</head>
<body id="popup">
