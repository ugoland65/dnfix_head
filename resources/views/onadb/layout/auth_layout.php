<!doctype html>
<html lang="ko">
<head>

	<title><?=$meta_title ?? '오나디비'?></title>
	<meta charset="utf-8">

	<meta name="Referrer" content="origin" />
	<meta name="referrer" contents="always" />
	<meta name="robots" content="index, follow" />

	<meta property="og:site_name" content="<?=$meta_site_name ?? '오나디비'?>" />
	<meta property="og:type" content="website">
	<meta property="og:title" content="<?=$og_title ?? '오나디비 - 【최신 오나홀 DB】 평점, 순위, 후기, 리뷰'?>" />
	<meta property="og:image" content="<?=$og_image ?? ''?>"/>
	<meta property="og:description" content="<?=$meta_description ?? '오나디비 국내 유일 최대 오나홀 데이터를 활용한 평점과 순위, 사용자의 디테일한 세부 평점을 보실 수 있습니다.'?>" />
	<meta property="og:url" content="<?=$meta_url ?? 'https://onadb.net'?>" />

	<meta name="twitter:card" content="summary">
	<meta name="twitter:title" content="<?=$meta_title ?? '오나디비'?>" />
	<meta name="twitter:description" content="<?=$meta_description ?? '오나디비 국내 유일 최대 오나홀 데이터를 활용한 평점과 순위, 사용자의 디테일한 세부 평점을 보실 수 있습니다.'?>" />

	<meta name="title" content="<?=$meta_title ?? '오나디비'?>" />
	<meta name="subject" content="<?=$meta_title ?? '오나디비'?>" />
	<meta name="description" content="<?=$meta_description ?? '오나디비 국내 유일 최대 오나홀 데이터를 활용한 평점과 순위, 사용자의 디테일한 세부 평점을 보실 수 있습니다.'?>" />
	<meta name="keywords" content="<?=$meta_keywords ?? '오나홀, 추천, 평점, 순위, 리뷰, 아키바팜, 프리바디, 쑈당몰, 오나미몰, 리얼맥스, 바나나몰, 프리바디, 오나왕, 딩동몰, 엠즈, 누딩이, 누딩이2, av, 성인용품, 미국오나홀, 마녀의 유혹, 님패트, 성처리, 핸디 후기, 대마왕, 배드드래곤, ㅍㄼㄷ, ㅆㄷ, ㅇㄴㅁ, ㄹㅇㅁㅅ, ㅇㅋㅂㅍ, ㅂㄴㄴ, 좃나나'?>" />

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="content-style-type" content="text/css" />
	<meta http-equiv="content-script-type" content="text/javascript" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta http-equiv="imagetoolbar" content="no" />
	<meta name="format-detection" content="telephone=no" />
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, shrink-to-fit=no" />

	<link rel="shortcut icon" href="/public/onadb/favicon/favicon.ico" />
	<link rel="icon" type="image/png" sizes="192x192" href="/public/onadb/favicon/favicon_192.png" />
	<link rel="icon" type="image/png" sizes="96x96" href="/public/onadb/favicon/favicon_96.png" />
	<link rel="icon" type="image/png" sizes="32x32" href="/public/onadb/favicon/favicon_32.png" />
	<link rel="icon" type="image/png" sizes="16x16" href="/public/onadb/favicon/favicon_16.png" />

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css" />
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js"></script>

	<!-- FontAwesome -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" 
		integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" 
		crossorigin="anonymous">

    <script src="/assets/js/global.js?ver=<?=time()?>"></script>
    <script src="/assets/js/common.js?ver=<?=time()?>"></script>

    <link rel="stylesheet" type="text/css" href="/assets/css/common.css?v=<?=time()?>" />
	<link rel="stylesheet" type="text/css" href="/assets/css/button.css?v=<?=time()?>" />
	<link rel="stylesheet" type="text/css" href="/public/onadb/css/css.layout.css?v=<?=time()?>" />
	<link rel="stylesheet" type="text/css" href="/public/onadb/css/join.css?v=<?=time()?>" />

</head>
<body>
    <?= $content ?? '' ?>
</body>
</html>