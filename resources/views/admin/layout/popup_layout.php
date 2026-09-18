<!doctype html>
<html>
<head>
	<title><?= $headTitle ?? '인트라넷' ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="content-style-type" content="text/css" />
	<meta http-equiv="content-script-type" content="text/javascript" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta http-equiv="imagetoolbar" content="no" />
	<meta name="referrer" content="no-referrer">

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

.prd-quick-left{ width:200px; height:100%; background-color:#fff; border-right:1px solid #9c9fae;  
	position:fixed; padding-top:20px; z-index:99; }
.on_sale_label_wrap{ text-align:center; padding:0 0 5px 0; }
.prd-img{ text-align:center; }

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
	background: -moz-linear-gradient(180deg, #0088cc, #0044cc);
	background: -ms-linear-gradient(180deg, #0088cc, #0044cc);
	background: -o-linear-gradient(180deg, #0088cc, #0044cc);
	background: linear-gradient(180deg, #0088cc, #0044cc);
}

.crm-wrap{ width:100%; height:calc(100% - 30px); display:table; table-layout: fixed; /*position:relative;*/ }
.crm-wrap > ul{ display:table-cell; vertical-align:top; }
.crm-menu-wrap{ width:200px; border-right:1px solid #9c9fae; }
.crm-gap{ width:5px; border-right:1px solid #9c9fae; }
.crm-body{ padding:20px; box-sizing:border-box; background-color:#dddddd; position:relative; }
.crm-body.has-top-menu{ padding: var(--crm-body-top-padding, 90px) 20px 20px; }
.crm-top-menu-wrap{
	width:calc(100% - 205px); 
	background-color:#fff; height:70px; position:fixed; 
	top:0; left:205px; right:0; 
	z-index:101; border-bottom:1px solid #9c9fae; 
	display:flex; 
	align-items:center; 
	gap:12px;
	padding:0 20px;
	box-sizing:border-box;

	> ul{
		dl{
			dt{ font-size:12px; font-weight:500; color:#777; }
			dd{
				b{ font-size:16px; font-weight:600; color:#000; }
			}
		}

		&.warning-text{
			color:#ff0000;
			font-size:12px;
			font-weight:500;
		}

		&.right{
			margin-left:auto;
		}
	}
}
/* 상단 정보영역 ul 사이 구분선 */
.crm-top-menu-wrap > ul + ul{
	border-left:1px solid #d9dce7;
	padding-left:12px;
}

.stock-write-box{ padding:15px 15px 0 15px; }
.stock-write-box ul{ font-size:15px; }




.cd-img-text-wrap{
	width:100%;
	max-width:100%;
	white-space:normal;
	word-break:break-word;
	overflow-wrap:anywhere;
	line-height:1.4;
}

.button-wrap-back{ height:60px; }
.button-wrap{ width:calc(100% - 205px); height:60px; line-height:60px; text-align:center; background:rgba(0,0,0,.4); border-top:1px solid #000; position:fixed; bottom:0; right:0;  }
</STYLE>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var crmBodies = document.querySelectorAll('.crm-body');
    crmBodies.forEach(function (bodyEl) {
        var hasTopMenu = !!bodyEl.querySelector('.crm-top-menu-wrap');
        bodyEl.classList.toggle('has-top-menu', hasTopMenu);
    });
});
</script>
</head>
<body id="popup">
    <?= $content ?? '' ?>

	<script type="text/javascript"> 
<!-- 
const footerGlobal = (function() {

	/*
	const API_ENDPOINTS = {
		wishListDel: "/user2/proc/WishList/delWishlist",
	};
	*/

	return {
		// 초기화
		init() {
			console.log('wishList module initialized.');
		},
		comment(mode="", idx="", dayCode="") {
			var width = "1000px";
			openDialog("/ad/ajax/comment_main",{ mode, idx, dayCode  },"Comment",width); 
		},
	}

})();	
//--> 
</script> 

<script src="/admin2/js/admin_footer.js?ver=<?=time()?>"></script>
<script src="/assets/js/common.js?ver=<?=time()?>"></script>
</body>
</html>