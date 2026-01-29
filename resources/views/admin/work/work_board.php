<style>
    .layout-style1 {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        gap: 20px;
        box-sizing: border-box;

        >ul {
            height: 100%;
            padding: 0;
        }

        >ul:first-child {
            width: 350px;
        } 
        >ul:last-child {
            flex: 1;
        }
    }
</style>
<div id="contents_head">
	<h1>업무 게시판 [<?= $category ?? '' ?>]</h1>
    <div id="head_write_btn">
		<button type="button" id="" class="btnstyle1 btnstyle1-danger btnstyle1-lg" onclick="location.href='/admin/work/TaskRequest/create?category=<?= $category ?? '' ?>'" > 
			<i class="fas fa-plus-circle"></i>
			신규 등록
		</button>
	</div>
</div>
<div id="contents_body">
	<div id="contents_body_wrap">

        <div class="layout-style1">
            <ul>
                
                <style>
                    .work-dashboard-title{
                        font-size: 15px;
                        font-weight: 600;
                        color: #333;
                        margin-bottom: 5px;
                        padding-left: 10px;
                    }
                    .work-dashboard-row {
                        display: flex;
                        flex-direction: column;
                        gap:3px;
                        background: #fff;
                        padding: 8px 8px;
                        border-radius: 8px;
                    }
                    .work-dashboard-row dl {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        gap: 12px;
                        margin: 0;
                        padding: 3px 15px;
                        border: 1px solid #eee;
                        border-radius:6px;
                        cursor: pointer;
                    }
                    .work-dashboard-row dl.is-active {
                        background: #f8fafc;
                    }
                    .work-dashboard-row dt {
                        font-size: 12px;
                        color: #444;
                        font-weight: 500;
                    }
                    .work-dashboard-row dd {
                        margin: 0;
                        min-width: 48px;
                        text-align: right;
                    }
                    .work-dashboard-row .count-badge {
                        display: inline-block;
                        min-width: 38px;
                        padding: 2px 8px;
                        text-align: center;
                        font-weight: 600;
                        font-size: 12px;
                        color: #1f2a37;
                        background: #f2f4f7;
                        border: 1px solid #d0d5dd;
                        border-radius: 10px;
                    }
                    .work-dashboard-row .count-badge.is-active {
                        color: #1d4ed8;
                        background: #dbeafe;
                        border-color: #93c5fd;
                    }
                </style>

                <h3 class="work-dashboard-title" data-category="업무요청">
                    업무요청
                    <button type="button" class="btnstyle1 btnstyle1-xs reset-btn">
                        <i class="far fa-trash-alt"></i> 초기화
                    </button>
                </h3>
                <div class="work-dashboard-row" data-category="업무요청">
                    <dl class="work-dashboard-item <?= ($s_scope ?? '') === 'my_all' ? 'is-active' : '' ?>" data-scope="my_all" data-state="전체보기">
                        <dt>내가 작성한 요청</dt>
                        <dd><span class="count-badge <?= ($myDashboardCounts['my']['my_count4'] ?? 0) ? 'is-active' : '' ?>"><?= $myDashboardCounts['my']['my_count4'] ?></span></dd>
                    </dl>
                    <dl class="work-dashboard-item <?= ($s_scope ?? '') === 'my_pending' ? 'is-active' : '' ?>" data-scope="my_pending" data-state="대기/확인">
                        <dt>내가 작성한 요청 미완료</dt>
                        <dd><span class="count-badge <?= ($myDashboardCounts['my']['my_ing_count4'] ?? 0) ? 'is-active' : '' ?>"><?= $myDashboardCounts['my']['my_ing_count4'] ?></span></dd>
                    </dl>
                    <dl class="work-dashboard-item <?= ($s_scope ?? '') === 'inbox_unchecked' ? 'is-active' : '' ?>" data-scope="inbox_unchecked" data-state="대기/확인">
                        <dt>내게 할당된 요청 미체크</dt>
                        <dd><span class="count-badge <?= ($myDashboardCounts['inbox']['my_count4'] ?? 0) ? 'is-active' : '' ?>"><?= $myDashboardCounts['inbox']['my_count4'] ?></span></dd>
                    </dl>
                    <dl class="work-dashboard-item <?= ($s_scope ?? '') === 'inbox_checked' ? 'is-active' : '' ?>" data-scope="inbox_checked" data-state="대기/확인">
                        <dt>내게 할당된 요청 처리중</dt>
                        <dd><span class="count-badge <?= ($myDashboardCounts['inbox']['my_ing_count4'] ?? 0) ? 'is-active' : '' ?>"><?= $myDashboardCounts['inbox']['my_ing_count4'] ?></span></dd>
                    </dl>
                </div>
                
                <h3 class="work-dashboard-title m-t-15" data-category="프로젝트">
                    프로젝트
                    <button type="button" class="btnstyle1 btnstyle1-xs reset-btn">
                        <i class="far fa-trash-alt"></i> 초기화
                    </button>
                </h3>
                <div class="work-dashboard-row" data-category="프로젝트">
                    <dl class="work-dashboard-item <?= ($s_scope ?? '') === 'my_all' ? 'is-active' : '' ?>" data-scope="my_all" data-state="전체보기">
                        <dt>내가 작성한 프로젝트</dt>
                        <dd><span class="count-badge <?= ($myDashboardCounts['my']['my_count2'] ?? 0) ? 'is-active' : '' ?>"><?= $myDashboardCounts['my']['my_count2'] ?></span></dd>
                    </dl>
                    <dl class="work-dashboard-item <?= ($s_scope ?? '') === 'my_pending' ? 'is-active' : '' ?>" data-scope="my_pending" data-state="대기/확인">
                        <dt>내가 작성한 프로젝트 미완료</dt>
                        <dd><span class="count-badge <?= ($myDashboardCounts['my']['my_ing_count2'] ?? 0) ? 'is-active' : '' ?>"><?= $myDashboardCounts['my']['my_ing_count2'] ?></span></dd>
                    </dl>
                    <dl class="work-dashboard-item <?= ($s_scope ?? '') === 'inbox_unchecked' ? 'is-active' : '' ?>" data-scope="inbox_unchecked" data-state="대기/확인">
                        <dt>내게 할당된 프로젝트 미체크</dt>
                        <dd><span class="count-badge <?= ($myDashboardCounts['inbox']['my_count2'] ?? 0) ? 'is-active' : '' ?>"><?= $myDashboardCounts['inbox']['my_count2'] ?></span></dd>
                    </dl>
                    <dl class="work-dashboard-item <?= ($s_scope ?? '') === 'inbox_checked' ? 'is-active' : '' ?>" data-scope="inbox_checked" data-state="대기/확인">
                        <dt>내게 할당된 프로젝트 처리중</dt>
                        <dd><span class="count-badge <?= ($myDashboardCounts['inbox']['my_ing_count2'] ?? 0) ? 'is-active' : '' ?>"><?= $myDashboardCounts['inbox']['my_ing_count2'] ?></span></dd>
                    </dl>
                </div>

                <h3 class="work-dashboard-title m-t-15" data-category="기획안">
                    기획안
                    <button type="button" class="btnstyle1 btnstyle1-xs reset-btn">
                        <i class="far fa-trash-alt"></i> 초기화
                    </button>
                </h3>
                <div class="work-dashboard-row" data-category="기획안">
                    <dl class="work-dashboard-item <?= ($s_scope ?? '') === 'my_all' ? 'is-active' : '' ?>" data-scope="my_all" data-state="전체보기">
                        <dt>내가 작성한 기획안</dt>
                        <dd><span class="count-badge <?= ($myDashboardCounts['my']['my_count3'] ?? 0) ? 'is-active' : '' ?>"><?= $myDashboardCounts['my']['my_count3'] ?></span></dd>
                    </dl>
                    <dl class="work-dashboard-item <?= ($s_scope ?? '') === 'my_pending' ? 'is-active' : '' ?>" data-scope="my_pending" data-state="대기/확인">
                        <dt>내가 작성한 기획안 미완료</dt>
                        <dd><span class="count-badge <?= ($myDashboardCounts['my']['my_ing_count3'] ?? 0) ? 'is-active' : '' ?>"><?= $myDashboardCounts['my']['my_ing_count3'] ?></span></dd>
                    </dl>
                    <dl class="work-dashboard-item <?= ($s_scope ?? '') === 'inbox_unchecked' ? 'is-active' : '' ?>" data-scope="inbox_unchecked" data-state="대기/확인">
                        <dt>내게 할당된 기획안 미체크</dt>
                        <dd><span class="count-badge <?= ($myDashboardCounts['inbox']['my_count3'] ?? 0) ? 'is-active' : '' ?>"><?= $myDashboardCounts['inbox']['my_count3'] ?></span></dd>
                    </dl>
                    <dl class="work-dashboard-item <?= ($s_scope ?? '') === 'inbox_checked' ? 'is-active' : '' ?>" data-scope="inbox_checked" data-state="대기/확인">
                        <dt>내게 할당된 기획안 처리중</dt>
                        <dd><span class="count-badge <?= ($myDashboardCounts['inbox']['my_ing_count3'] ?? 0) ? 'is-active' : '' ?>"><?= $myDashboardCounts['inbox']['my_ing_count3'] ?></span></dd>
                    </dl>
                </div>

            </ul>
            <ul>

                <!-- 검색 영역 -->
                <input type="hidden" name="category" id="category" value="<?= $category ?? '' ?>">
                <input type="hidden" name="s_scope" id="s_scope" value="<?= $s_scope ?? '' ?>">

                <div class="top-search-wrap">
                    <ul class="count-wrap">
                        <span class="count">Total : <b><?=number_format($paginationArray['total']) ?></b></span>
                        <span class="m-l-10"><b><?=$paginationArray['current_page']?></b></span>
                        <span>/</span>
                        <span><b><?=$paginationArray['last_page']?></b> page</span>
                    </ul>
                    <ul class="m-l-10">
                        <select name="s_state" id="s_state" >
                            <option value="전체보기">전체보기</option>
                            <option value="대기" <? if( $s_state == '대기' ) echo "selected";?>>대기</option>
                            <option value="확인" <? if( $s_state == '확인' ) echo "selected";?>>확인</option>
                            <option value="대기/확인" <? if( $s_state == '대기/확인' ) echo "selected";?>>대기 + 확인</option>
                            <option value="반려" <? if( $s_state == '반려' ) echo "selected";?>>반려</option>
                            <option value="완료" <? if( $s_state == '완료' ) echo "selected";?>>완료</option>
                        </select>
                    </ul>
                    <ul>
                        <input type='text' name='s_keyword' id='s_keyword' value="<?= $s_keyword ?? '' ?>" placeholder="검색어" style="min-width: 200px;">
                    </ul>
                    <ul>
                        <button type="button" class="btn btnstyle1 btnstyle1-primary btnstyle1-sm" id="searchBtn">
                            <i class="fas fa-search"></i> 검색
                        </button>
                        <button type="button" class="btnstyle1 btnstyle1-sm reset-btn">
                            <i class="far fa-trash-alt"></i> 초기화
                        </button>
                    </ul>
                </div>

                <div id="list_new_wrap">
                    <div class="table-wrap5 m-t-5">
                        <div class="scroll-wrap">

                            <table class="table-st1">
                                <thead>
                                    <tr>
                                        <th class="list-checkbox"><input type="checkbox" name="" onclick="select_all()"></th>
                                        <th class="list-idx">고유번호</th>
                                        <th class="">분류</th>
                                        <th class="">제목</th>
                                        <th class="">상태</th>
                                        <th class="">작성자</th>
                                        <th class="">참여자</th>
                                        <th class="">등록일</th>
                                        <th class="">댓글</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    foreach ($workLogList['data'] as $row) {

                                        $tr_class = 'tr-normal';
                                        if( $row['state'] == '완료' ){
                                            $tr_class = 'status_end2';
                                        }
                                ?>
                                    <tr class="<?= $tr_class ?>">
                                        <td><input type="checkbox" name="check_idx[]" value="<?= $row['idx'] ?>"></td>
                                        <td class="list-idx"><?= $row['idx'] ?></td>
                                        <td><?= $row['category'] ?></td>
                                        <td><a href="/admin/work/TaskRequestDetail/<?= $row['idx'] ?>"><span style="font-size:13px; font-weight:600;"><?= $row['subject'] ?? '제목없음'?></span></a></td>
                                        <td><?= $row['state'] ?></td>
                                        <td>
                                            <div class="mb-profile-box sm" data-idx="<?= $row['reg_idx'] ?>" >
                                                <?php if( !empty($row['reg_image']) ){ ?>
                                                    <div class="profile-img"><img src="/data/uploads/<?=$row['reg_image']?>" alt=""></div>
                                                <?php } else { ?>
                                                    <div class="profile-img"><i class="far fa-user-circle"></i></div>
                                                <?php } ?>
                                                <span class="profile-name"><?= $row['reg_name'] ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="target-list-wrap">
                                                <?php if( !empty($row['target_list']) ){ ?>
                                                    <div class="mb-profile-box">
                                                        <?php if( !empty($row['target_list'][0]['image']) ){ ?>
                                                            <div class="profile-img"><img src="/data/uploads/<?=$row['target_list'][0]['image']?>" alt=""></div>
                                                        <?php } else { ?>
                                                            <div class="profile-img"><i class="far fa-user-circle"></i></div>
                                                        <?php } ?>
                                                        <span class="profile-name"><?= $row['target_list'][0]['name'] ?></span>
                                                    </div>
                                                    <?php if( count($row['target_list']) > 1 ){ ?>
                                                        <span class="profile-name">+<b><?= count($row['target_list']) - 1 ?></b>명</span>
                                                    <?php } ?>
                                                    <?php
                                                    /* 
                                                    foreach($row['target_list'] as $target){ ?>
                                                        <div class="mb-profile-box">
                                                            <?php if( !empty($target['image']) ){ ?>
                                                            <div class="profile-img"><img src="/data/uploads/<?=$target['image']?>" alt=""></div>
                                                            <?php } ?>
                                                            <span class="profile-name"><?= $target['name'] ?></span>
                                                        </div>
                                                    <?php } 
                                                    */ ?>

                                                <?php } ?>
                                            </div>
                                        </td>
                                        <td><?= date('Y-m-d H:i', strtotime($row['reg_date'])) ?></td>
                                        <td class="text-left">
                                            <button type="button" id="" class="btnstyle1 btnstyle1-xs" onclick="footerGlobal.comment('log','<?=$row['idx']?>')" >
                                                댓글
                                                <? if( $row['cmt_s_count'] > 0 ) { ?> : <b><?=$row['cmt_s_count']?></b><? } ?>
                                            </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

            </ul>
        </div>

    </div>
</div>
<div id="contents_bottom">
   <div class="pageing-wrap"><?=$paginationHtml?></div>
</div>

<script>
    $(function(){
        $(".dn-select2").select2();
        
        // 개별 체크박스 선택 시 행 배경색 변경
        $(document).on('change', 'input[name="check_idx[]"]', function() {
            if($(this).is(':checked')) {
                $(this).closest('tr').addClass('selected-row');
            } else {
                $(this).closest('tr').removeClass('selected-row');
            }
            updateSelectedCount();
        });
        
    });


    // 검색 파라미터 수집 공통 함수
    function getSearchParams(additionalParams) {
        var params = {};
        
        // 각 입력 필드의 값을 가져와서 빈 값이나 undefined가 아닌 경우에만 params 객체에 추가
        var fields = {
            'category': $("#category").val(),
            's_state': $("#s_state").val(),
            's_keyword': $("#s_keyword").val(),
            's_scope': $("#s_scope").val(),
        };

        // 추가 파라미터가 있으면 병합
        if (additionalParams) {
            fields = Object.assign(fields, additionalParams);
        }

        // 유효한 값만 params에 추가
        for (var key in fields) {
            if (fields[key] !== undefined && fields[key] !== null && fields[key] !== '') {
                params[key] = fields[key];
            }
        }

        return params;
    }

    // 검색 파라미터로 페이지 이동
    function navigateWithParams(params) {
        // URL 쿼리 문자열 생성
        var queryString = Object.keys(params)
            .map(function(key) {
                return key + '=' + encodeURIComponent(params[key]);
            })
            .join('&');

        // 페이지 이동
        location.href = '/admin/work/TaskRequest' + (queryString ? '?' + queryString : '');
    }

    $("#searchBtn").on('click',function(){
        // 검색 파라미터 수집
        var params = getSearchParams();
        
        // 페이지 이동
        navigateWithParams(params);
    });

    $(document).on('click', '.work-dashboard-item', function() {
        var scope = $(this).data('scope') || '';
        var state = $(this).data('state') || '';
        var category = $(this).closest('.work-dashboard-row').data('category') || $("#category").val();

        $("#s_scope").val(scope);
        if (state) {
            $("#s_state").val(state);
        }
        if (category) {
            $("#category").val(category);
        }

        var params = getSearchParams({
            's_scope': scope,
            's_state': state || $("#s_state").val(),
            'category': category || $("#category").val(),
        });

        navigateWithParams(params);
    });

    $(".reset-btn").click(function(){
        var url = "?category=<?= $category ?? '' ?>";
        window.location.href = url;
    });

</script>