<link rel="stylesheet" type="text/css" href="/public/onadb/css/css.prd_view.css?v=<?=time()?>" />
<div class="page-title">
	<h1>user @<?=$nickname?></h1>
</div>
<div class="u-wrap">
	<div class="u-layout">
		<ul class="u-profile">
			<div class="profile-img"><img src="/public/onadb/img/non-profile.png" /></div>
			<div class="profile-box">
                <ul>
                    <b>@<?=$user['user_nick'] ?? ''?></b>
                </ul>
				<ul>
                    Level : <b><?=$user['user_level'] ?? 0?></b>
                </ul>
				<ul>
                    기여도 점수 : <b><?=$user['user_score'] ?? 0?></b>
                </ul>
			</div>
		</ul>
		<ul class="u-body">

            <div>@<?=$user['user_nick'] ?? ''?>님의 한줄평 (<?=$data_comment['total']?>개)</div>
            <div class="comment">
                <?php
                    foreach($data_comment['data'] as $comment){

                        if( $comment['productSimple']['CD_IMG2'] ){
                            $img_path = '../../data/comparion/'.$comment['productSimple']['CD_IMG2'];
                            $img_class = "thum-icon";
                        }else{
                            $img_path = '../../data/comparion/'.$comment['productSimple']['CD_IMG'];
                            $img_class = "thum-no-icon img-blur";
                        }

                        if( $comment['pc_score_mode'] == "after" ){
                            $_pc_score_mode_text = "<span class='score-mode after'>사용자 한줄평</span>";
                        }else{
                            $_pc_score_mode_text = "<span class='score-mode nonuser'>일반 한줄평</span>";
                        }

                ?>
                    <div class="comment-list">
                        <div class="cm">
                            <div class="cm-img">
                                <div class="img-box">
                                    <a href="/pv/<?=$comment['pc_pd_idx']?>" target="_blank"><img src="<?=$img_path?>" class="<?=$img_class?>"></a>
                                </div>
                                <div class="cm-mobile-info">
                                    <div><?=$comment['productSimple']['CD_NAME']?></div>
                                    <div class="m-t-5"><?=$_pc_score_mode_text?></div>
                                </div>
                            </div>
                            <div class="cm-body">
                                <ul class="cm-pc-info">
                                    <?=$_pc_score_mode_text?>
                                    <p class="pname"><a href="/pv/<?=$comment['pc_pd_idx']?>" target="_blank"><?=$comment['productSimple']['CD_NAME']?></a></p>
                                </ul>
                                <ul class="comment-body">
                                    <?=$comment['pc_body']?>
                                    <?php if( $comment['pc_grade'] > 0 ){ ?>
                                        <span class='score'>개인평점 : ( <b><?=$comment['pc_grade']?></b> )</span>
                                    <?php } ?>
                                </ul>

                                <?php if( $comment['pc_score_mode'] == "after" ){ ?>
                                <ul class="score-box-wrap">
                                    <?php foreach($comment['pc_score']['score'] as $score){ ?>
                                        <span class='score-box'>
                                            <?=$score['name']?> : <b><?=$score['score']?></b>
                                        </span>
                                    <?php } ?>
                                </ul>
                                <?php } ?>

                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="pageing-wrap"><?=$paginationHtml?></div>

        </ul>
    </div>
</div>