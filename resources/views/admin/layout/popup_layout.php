<!doctype html>
<html>
<head>
	<title><?= $headTitle ?? '인트라넷' ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="content-style-type" content="text/css" />
	<meta http-equiv="content-script-type" content="text/javascript" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta http-equiv="imagetoolbar" content="no" />

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

</head>
<body id="popup">
    <?= $content ?? '' ?>
</body>
</html>