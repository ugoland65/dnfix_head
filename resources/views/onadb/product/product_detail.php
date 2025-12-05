<?php
// 변수 초기화
$productData = $productData ?? [];
$_idx = $productData['CD_IDX'] ?? 0;
$data_score = $data_score ?? ['ps_grade' => 0];
$_gva_koedge_onadb_score_option = $_gva_koedge_onadb_score_option ?? ['자극감', '밀착감', '내구성', '청소편의'];

// 이미지 클래스 설정
$_img_class = !empty($productData['CD_IMG2']) ? 'thum-icon' : 'thum-no-icon';

?>
<link rel="stylesheet" type="text/css" href="/public/onadb/css/css.prd_view.css?v=<?=time()?>" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>

<div class="pv-wrap">
	<div class="pv-info">
		<ul class="pd-name"><h1><?=$productData['CD_NAME'] ?? ''?></h1></ul>
    </div>
</div>
<div class="pv-body">
    <ul class="img">
        <div class="img-box"><img src="/data/comparion/<?=$productData['CD_IMG'] ?? ''?>" class="<?=$_img_class?>"></div>
        <div class="prd-detail-info">
                
            <?php if( !empty($productData['CD_NAME_OG']) ){ ?>
            <ul>
                <label class="sname">원상품명</label>
                <span class="svalue"><?= htmlspecialchars($productData['CD_NAME_OG']) ?></span>
            </ul>
            <?php } ?>

            <?php if( !empty($productData['CD_BRAND_IDX']) ){ ?>
            <ul>
                <label class="sname">브랜드</label>
                <span class="svalue">
                    <a href="/brand/<?= htmlspecialchars($productData['CD_BRAND_IDX']) ?>">
                        <?= htmlspecialchars($productData['BD_NAME'] ?? '') ?>
                    </a>
                    <?php if( !empty($productData['BD_NAME_EN']) ){ ?>
                    ( <a href="/brand/<?= htmlspecialchars($productData['CD_BRAND_IDX']) ?>">
                        <?= htmlspecialchars($productData['BD_NAME_EN']) ?>
                    </a> )
                    <?php } ?>
                </span>
                <a href="/brand/<?= htmlspecialchars($productData['CD_BRAND_IDX']) ?>" class="btn-s1 orange xs">브랜드 전체상품</a>
            </ul>
            <?php } ?>

            <?php if( !empty($productData['CD_RELEASE_DATE']) ){ ?>
            <ul>
                <label class="sname">출시일</label>
                <span class="svalue"><?= htmlspecialchars($productData['CD_RELEASE_DATE']) ?></span>
            </ul>
            <?php } ?>


            <?php if( $productData['CD_SIZE']['W'] || $productData['CD_SIZE']['H'] || $productData['CD_SIZE']['D'] ){ ?>
            <ul>
                <label class="sname">패키지</label>
                <span class="svalue">W : <?= number_format($productData['CD_SIZE']['W']) ?>  / H : <?= number_format($productData['CD_SIZE']['H']) ?>  / D : <?= number_format($productData['CD_SIZE']['D']) ?> ( mm )</span>
            </ul>
            <?php } ?>

            <?php
            if(!empty($productData['CD_SIZE2'])){
                if(($productData['CD_KIND_CODE'] ?? '') == "GEL" ){
                    $_sname_cd_size2 = "용량";
                    $_sunit_cd_size2 = "ml";
                }else{
                    $_sname_cd_size2 = "내부길이";
                    $_sunit_cd_size2 = "cm";
                }
            ?>
            <ul>
                <label class="sname"><?= htmlspecialchars($_sname_cd_size2) ?></label>
                <span class="svalue"><?= htmlspecialchars($productData['CD_SIZE2']) ?> ( <?= htmlspecialchars($_sunit_cd_size2) ?> )</span>
            </ul>
            <?php } ?>

            <?php 
                if( $productData['cd_weight_fn']['1'] > 0 ){ 
                    if( $productData['cd_weight_fn']['1'] > 1000 ){
                        $_show_weight_1 = round(($productData['cd_weight_fn']['1']/1000),1)." ( kg )";
                    }else{
                        $_show_weight_1 = $productData['cd_weight_fn']['1']." ( g )";
                    }
            ?>
            <ul>
                <label class="sname">제품중량</label>
                <span class="svalue"><?= htmlspecialchars($_show_weight_1) ?></span>
            </ul>
            <?php } ?>

            <?php if( $productData['cd_weight_fn']['2'] > 0 ){ ?>
            <ul>
                <label class="sname">전체중량</label>
                <span class="svalue"><?= number_format($productData['cd_weight_fn']['2']) ?> ( g )</span>
            </ul>
            <?php } ?>

            <?php 
            if( $productData['cd_weight_fn']['1'] > 0 && !empty($productData['CD_SALE_PRICE']) ){
                $_wpg = round($productData['CD_SALE_PRICE']/$productData['cd_weight_fn']['1'], 1);
            ?>
            <ul>
                <label class="sname">1g당 평균가</label>
                <span class="svalue">₩ <?= number_format($_wpg, 1) ?></span>
            </ul>
            <?php } ?>
        </div>
    </ul>
    <ul class="info">
        <div class="pv-top-wrap">
            <ul>
                <div class="score-wrap">
                    <?php
                        if( !empty($productData['cd_tier']) && in_array($productData['cd_tier'], ["1", "2", "3"]) ){ 
                    ?>
                    <ul class="tier-icon">
                        <img src="/public/onadb/img/tier_detail_<?= htmlspecialchars($productData['cd_tier']) ?>.png" />
                        <b><?= htmlspecialchars($productData['cd_tier']) ?></b>티어
                    </ul>
                    <?php } ?>
                    <?php if( ($data_score['ps_grade'] ?? 0) > 0 ){ ?>
                    <ul class="grade">
                        <span class="sname">평점</span> <span class="svalue"><?= number_format($data_score['ps_grade'], 1) ?></span>
                    </ul>
                    <?php } ?>
                </div>
            </ul>
            <ul class="graph">
                <div class="pd-graph-wrap">
                    <canvas id="eval_canvas" style="width:100%; height:350px; "></canvas>
                </div>
            </ul>
        </div>

        <div class="comment-wrap">
            
            <div class="comment-guide">
                <span>
                    <img src="/public/onadb/img/icon_1.png" alt="안내">
                </span>
                
                <?php if( $auth['is_logged_in'] == true ){ ?>
                    <span>
                        사용자 한 줄 평을 남겨주시면 기여도 점수를 획득합니다.
                    </span>
                <?php } else { ?>
                    <span>
                        회원 로그인 후 사용자 한 줄 평을 남겨주시면
                    </span>
                    <span>
                        기여도 점수를 획득합니다.
                    </span>
                    <span>
                        <a href="/login" class="btn-s1 mini green">로그인</a>
                        <a href="/join" class="btn-s1 mini purple">회원가입</a>
                    </span>
                <?php } ?>

            </div>

            <div class="comment-btn">
                <span class="btn-s1 blue block" id="comment_btn_on">
                    <?php if( $auth['is_logged_in'] == false ){ ?>비회원 <?php } ?>
                    한줄평 등록
                </span>
            </div>

            <div class="comment-write" id="comment_form_wrap">
                <form id="comment_form">
                <input type="hidden" name="pd_idx" value="<?= htmlspecialchars($pd_idx) ?>" >
                
                <div>
                    <select name="score_mode" id="score_mode">
                        <option value="before">일반 한줄평</option>
                        <option value="after">사용자 한줄평</option>
                    </select>
                </div>
                <div class="m-t-3">
                    <?php 
                        $index = 0;
                        foreach($onahole_score_name as $score_name){
                            $index++;
                    ?>
                    <select name="score_<?=$index?>" id="score_<?=$index?>" disabled>
                        <option value="0"><?= htmlspecialchars($score_name) ?></option>
                        <?php 
                            for ($z=0; $z<10; $z++){ 
                                $_zz = $z + 1;
                        ?>
                        <option value="<?=$_zz?>"><?=$_zz?>점</option>
                        <?php } ?>
                    </select>
                    <?php } ?>
                </div>

                <div class="m-t-13">

                    <?php if( $auth['is_logged_in'] == true ){ ?>
                    <?php } else { ?>
                        <input type='text' name='name' id='comm_name' placeholder="익명" autocomplete="off">
                        <input type='password' name='pw' id='comm_pw' placeholder="비밀번호" autocomplete="off">
                    <?php } ?>

                    <select name="grade" id="grade">
                        <option value="0">개인평점</option>
                        <?php for ($z=1; $z<=10; $z++){ ?>
                        <option value="<?=$z?>"><?=$z?>점</option>
                        <?php } ?>
                    </select>

                </div>

                <div class="m-t-8 comment-write-wrap">
                    <ul><textarea name="body" id="body"></textarea></ul>
                    <ul class="comment-write-btn"><button type="button" id="comment_write_btn" class=""> 등록  </button></ul>
                </div>

                
                </form>
            </div>

            <div id="pv_comment">

                <div class="comment">
                    <?php
                    if( !empty($data_comment['data']) ){
                        foreach($data_comment['data'] as $comment){
                            
                    ?>
                    <div class="comment-list">
                        <ul>
                            <?php if( $comment['pc_score_mode'] == 'after' ){ ?>
                                <span class='score-mode after'>사용자 한줄평</span>
                            <?php } else { ?>
                                <span class='score-mode nonuser'>일반 한줄평</span>
                            <?php } ?>
                            <p class="name">
                                <?php if( $comment['pc_user_idx'] ){ ?>
                                    <span class='level-icon level-<?=$comment['userSimple']['user_level']?>'><?=$comment['userSimple']['user_level']?></span>
                                    <span class='in-user' data-nick='<?=$comment['userSimple']['user_nick']?>'><?=$comment['userSimple']['user_nick']?></span>
                                <?php } else { ?>
                                    <span class='no-member'><?=$comment['pc_reg_info']['name'] ?? '비회원'?></span>
                                <?php } ?>
                            </p>
                        </ul>
                        <ul class="comment-body">
                            <?=nl2br($comment['pc_body'] ?? '')?>
                        </ul>
                        <ul class="m-t-5">
                            <?php foreach($comment['pc_score']['score'] as $score){ ?>
                                <span class='score-box'>
                                    <?=$score['name']?> : <b><?=$score['score']?></b>
                                </span>
                            <?php } ?>
                        </ul>

                    </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            
            </div>

        </div>
    </ul>
    </div>

<script type="text/javascript">
    var prdIdx = "<?=$_idx?>";
    var isLoggedIn = <?php if( $auth['is_logged_in'] == true ){ echo "true"; } else { echo "false"; } ?>;
    var prdScore = {
        "s1" : "<?=$data_score['ps_score']['score']['1']['score_avg'] ?? 0?>",
        "s2" : "<?=$data_score['ps_score']['score']['2']['score_avg'] ?? 0?>",
        "s3" : "<?=$data_score['ps_score']['score']['3']['score_avg'] ?? 0?>",
        "s4" : "<?=$data_score['ps_score']['score']['4']['score_avg'] ?? 0?>",
        "s5" : "<?=$data_score['ps_score']['score']['5']['score_avg'] ?? 0?>",
        "s6" : "<?=$data_score['ps_score']['score']['6']['score_avg'] ?? 0?>",
        "s7" : "<?=$data_score['ps_score']['score']['7']['score_avg'] ?? 0?>",
    };

    const prdView = function() {

        const color = Chart.helpers.color;

        const chart_data = {
            labels: [
                ['자극/기믹','('+ prdScore.s1 +')'], 
                ['유지관리','('+ prdScore.s2 +')'], 
                ['냄새/유분/소재','('+ prdScore.s3 +')'],
                ['조임/탄력','('+ prdScore.s4 +')'],
                ['마감/내구성','('+ prdScore.s5 +')'],
                ['조형/패키지','('+ prdScore.s6 +')'],
                ['진공','('+ prdScore.s7 +')']
            ],
            datasets: [{
                label: '항목평점',
                backgroundColor: 'rgba(54, 162, 235, 0.05)',
                borderColor: 'rgb(54, 162, 235)',
                pointBackgroundColor: 'rgb(54, 162, 235)',
                data: [prdScore.s1,prdScore.s2,prdScore.s3,prdScore.s4,prdScore.s5,prdScore.s6,prdScore.s7]
            }]
        };

        const chart_options = {
            legend: {
                position: 'bottom',
                labels: { fontColor: 'tomato', fontSize: 12 },
                display: false
            },
            title: { display: true, text: '', fontSize: 13, padding: 0, fontColor: 'tomato' },
            scale: {
                ticks: { beginAtZero: true, max: 12, min: 0, stepSize: 2, fontColor: 'gray' },
                pointLabels:{ fontSize:13, fontColor:'#172032', }
            },
            tooltips: { position: 'nearest', mode: 'index', intersect: false, yPadding: 20, xPadding: 20, caretSize: 8 }
        };

        const config = {
            type: 'radar',
            data: chart_data,
            options: chart_options
        };

        const API_ENDPOINTS = {
            comment_write: "/prd-comment",
        };

        var commentFormReset = function() {
            $(".comment-write select option:eq(0)").prop("selected", true);
            $(".comment-write-name-wrap input").val("");
            $(".comment-write-wrap textarea").val("");
        };

        function comment() {
            
            var formData = $("#comment_form").serializeArray();

            ajaxRequest(API_ENDPOINTS.comment_write, formData, {})
                .then(res => {
                    if( res.success == true ){
                        alert('등록완료. 참여해주셔서 감사합니다.');
                        location.reload();
                    }else{
                        showAlert("Error", res.message, "alert2" );
                        return false;
                    }
                })
                .catch(error => {
                    console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                    showAlert("Error", "에러", "alert2" );
                    return false;
                });

            /*
            $.ajax({
                url: "/processing-prd",
                data : formData,
                type: "POST",
                dataType: "json",
                success: function(res){
                    if (res.success == true ){
                        
                        if( res.pc_score_mode == "after" ){
                            myRadar.data.labels = [['자극/기믹','('+ res.ps_score_1 +')'], ['유지관리','('+ res.ps_score_2 +')'], ['냄새/유분/소재','('+ res.ps_score_3 +')'],['조임/탄력','('+ res.ps_score_4 +')'],['마감/내구성','('+ res.ps_score_5 +')'],['조형/패키지','('+ res.ps_score_6 +')'],['가성비','('+ res.ps_score_7 +')']];
                            myRadar.data.datasets.data = [res.ps_score_1, res.ps_score_2, res.ps_score_3, res.ps_score_4, res.ps_score_5, res.ps_score_6, res.ps_score_7];
                            myRadar.update();
                        }
                        //$("#body").val("");
                        prdView.list();
                        
                        if( res.level_up > 0 ){
                            toast("info", "LEVEL UP", "축하드립니다. 레밸( Lv."+ res.level_up +")가 되셨습니다.","","toast-bottom-center");
                        }

                        if ( typeof UC_APP.GLOBAL_USER !== 'undefined' ){
                            var toast_msg = "";
                            if( res.give_score > 0 ){
                                toast_msg += " 기여도 점수 +"+ res.give_score +" 상승 ";
                            }
                            if( res.give_point > 0 ){
                                toast_msg += " | 포인트 +"+ res.give_point +" 획득 ";
                            }
                            toast("info", "코멘트 등록완료", toast_msg,"","toast-bottom-center");
                        }else{
                        }

                        commentFormReset();
                        $(".comment-btn").show();
                        $(".comment-write").hide();

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
            */
        }


        return {

            init : function() {
                window.myRadar = new Chart(document.getElementById('eval_canvas'), config);
            },
            list : function(pn) {

                /*
                $.ajax({
                    url: "/ajax-commentList",
                    data: { "pn":pn,"mode":"pv","idx":prdIdx },
                    type: "POST",
                    dataType: "html",
                    success: function(shtml){
                        $("#pv_comment").html(shtml);
                    },
                    error: function(request, status, error){
                        console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                        showAlert("Error", "에러", "alert2" );
                        return false;
                    },
                    complete: function() {

                    }
                });
                */

            },
            comment
        };

    }();

    $(function(){

        prdView.init();
        //prdView.list();

        $("#score_mode").change(function(){
            if($(this).val() == "before"){
                $("#score_1").attr("disabled", true);
                $("#score_2").attr("disabled", true);
                $("#score_3").attr("disabled", true);
                $("#score_4").attr("disabled", true);
                $("#score_5").attr("disabled", true);
                $("#score_6").attr("disabled", true);
                $("#score_7").attr("disabled", true);
            } else if($(this).val() == "after"){
                $("#score_1").attr("disabled", false);
                $("#score_2").attr("disabled", false);
                $("#score_3").attr("disabled", false);
                $("#score_4").attr("disabled", false);
                $("#score_5").attr("disabled", false);
                $("#score_6").attr("disabled", false);
                $("#score_7").attr("disabled", false);
            }
        });

        $('#comment_btn_on').click(function(){
            $(".comment-btn").hide();
            $(".comment-write").show();
        });

        $('#comment_write_btn').click(function(){

            if( $('#score_mode').val() == "after" ){
                if( $('#score_1').val() == "0" ){
                    showAlert("NOTICE", "자극 스코어를 입력해 주세요.", "dialog" );
                    return false;
                }
                if( $('#score_2').val() == "0" ){
                    showAlert("NOTICE", "유지관리 스코어를 입력해 주세요.", "dialog" );
                    return false;
                }
                if( $('#score_3').val() == "0" ){
                    showAlert("NOTICE", "조임 스코어를 입력해 주세요.", "dialog" );
                    return false;
                }
                if( $('#score_4').val() == "0" ){
                    showAlert("NOTICE", "마감/내구성 스코어를 입력해 주세요.", "dialog" );
                    return false;
                }
                if( $('#score_5').val() == "0" ){
                    showAlert("NOTICE", "조형/패키지 스코어를 입력해 주세요.", "dialog" );
                    return false;
                }
                if( $('#score_6').val() == "0" ){
                    showAlert("NOTICE", "진공 스코어를 입력해 주세요.", "dialog" );
                    return false;
                }
            }

            if( isLoggedIn ){
            }else{
                if( $('#comm_name').val() == "" ){
                    showAlert("NOTICE", "이름을 입력해 주세요.", "dialog" );
                    return false;
                }
                if( $('#comm_pw').val() == "" ){
                    showAlert("NOTICE", "비밀번호를 입력해 주세요.", "dialog" );
                    return false;
                }
            }

            if( $("#body").val() == "" ){
                showAlert("Error", "내용을 입력해 주세요.", "alert2" );
                return false;
            }

            if( $('#grade').val() == "0" ){
                showAlert("NOTICE", "개인평점 스코어를 입력해 주세요.", "alert2" );
                return false;
            }

            prdView.comment();

        });

    });
</script>

<?php 
/*
<?=dump($data_comment);?>
<?=dump($productData);?>
*/
?>