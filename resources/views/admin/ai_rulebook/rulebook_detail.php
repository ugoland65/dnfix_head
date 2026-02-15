<style>
    :root {
        --border: #e5e7eb;
        --bg: #f9fafb;
        --text: #111827;
        --muted: #6b7280;
        --primary: #2563eb;
        --danger: #dc2626;
    }

    .rulebook-wrap {
        max-width: 1600px;
        margin: 0 auto;
        padding: 20px;

        .topbar {

            box-sizing: border-box;

            /*
            display: flex;
            gap: 12px;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;

            > div {
                &:first-child {
                    flex: 1;
                }
                &:last-child {
                    width: 500px;
                    flex: 0 0 auto;
                }
            }
            */
        }

        h1 {
            font-size: 18px;
            margin: 0 0 4px;
        }

        .sub {
            color: var(--muted);
            font-size: 12px;
            margin: 0;
        }

        .card {
            border: 1px solid var(--border);
            border-radius: 12px;
            background: #fff;
        }

        .card .hd {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .card .bd {
            padding: 16px 35px;
        }

        .card .bd2 {
            padding: 16px 20px;
        }        

        .grid {
            display: grid;
            grid-template-columns: 1.5fr 0.5fr;
            gap: 16px;
            align-items: start;
        }

        @media (max-width: 960px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }

        .form {
            display: grid;
            grid-template-columns: 160px 1fr;
            gap: 10px 12px;
            align-items: center;
        }

        .form .full {
            grid-column: 1 / -1;
        }

        label {
            font-size: 12px;
            color: var(--muted);
        }

        input[type="text"],
        select,
        textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 13px;
            outline: none;
            background: #fff;
        }

        input[type="text"],
        select{
            height: 35px;
            background: #f9fafb;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
            background: #f9fafb;
        }

        .row {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
            margin:0;
            padding:0;
            box-sizing: border-box;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border: 1px solid var(--border);
            border-radius: 7px;
            background: var(--bg);
            color: var(--muted);
            font-size: 12px;
            box-sizing: border-box;
        }

        .tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            padding: 12px 12px 0;
            justify-content: center;
        }

        .tab {
            border: 1px solid var(--border);
            background: #fff;
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 13px;
            cursor: pointer;
            color: var(--muted);
        }

        .tab.active {
            border-color: rgba(37, 99, 235, .35);
            color: var(--primary);
            background: rgba(37, 99, 235, .06);
        }

        .tabpanes {
            padding: 12px 35px;
        }

        .pane {
            display: none;
        }

        .pane.active {
            display: block;
        }

        .btn {
            border: 1px solid var(--border);
            background: #fff;
            border-radius: 10px;
            padding: 9px 12px;
            font-size: 13px;
            cursor: pointer;
        }

        .btn.primary {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        .btn.danger {
            background: var(--danger);
            border-color: var(--danger);
            color: #fff;
        }

        .btn.small {
            padding: 6px 10px;
            font-size: 12px;
            border-radius: 8px;
        }

        .btn:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        .actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-top: 1px solid var(--border);
        }

        th,
        td {
            border-top: 1px solid var(--border);
            padding: 8px 5px;
            vertical-align: top;
        }

        th {
            font-size: 12px;
            color: var(--muted);
            background: var(--bg);
            border-top: none;
            text-align: center;
        }

        td input[type="text"],
        td select,
        td textarea {
            padding: 8px 10px;
            border-radius: 6px;
            font-size: 12px;
        }

        td textarea {
            min-height: 44px;
        }

        .td-actions {
            white-space: nowrap;
        }

        .muted {
            color: var(--muted);
            font-size: 12px;
        }

        .help {
            font-size: 12px;
            color: var(--muted);
            margin: 8px 0 0;
        }

        .guide-box {
            background: #f8fbff;
            border: 1px solid #dbeafe;
            border-radius: 10px;
            padding: 15px 25px;
            line-height: 1.6;
        }

        .guide-box .guide-title {
            margin: 0 0;
            font-size: 15px;
            font-weight: 700;
            color: #1f2937;
        }

        .guide-box .guide-desc {
            margin: 0 0 8px;
        }

        .guide-box .guide-list {
            margin: 0;
            padding-left: 18px;
        }

        .guide-box .guide-list li {
            margin: 6px 0;
        }

        .guide-box .guide-list>li {
            padding-left: 2px;
        }

        .guide-box .guide-list>li>ul {
            margin: 8px 0 4px 0;
            padding: 6px 0 6px 18px;
            border-left: 2px solid #dbeafe;
            background: rgba(255, 255, 255, 0.55);
            border-radius: 0 6px 6px 0;
        }

        .guide-box .guide-list>li>ul>li {
            margin: 8px 0;
            line-height: 1.65;
        }

        .guide-box .guide-sub {
            display: inline-block;
            margin-top: 2px;
            color: #6b7280;
        }

        .guide-box .guide-tip {
            margin: 8px 0 0;
        }

        .hr {
            height: 1px;
            background: var(--border);
            margin: 12px 0;
        }

        .preview {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        @media (max-width: 960px) {
            .preview {
                grid-template-columns: 1fr;
            }
        }

        .codebox {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 12px;
            white-space: pre-wrap;
            background: #0b1220;
            color: #e5e7eb;
            border-radius: 12px;
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, .08);
            min-height: 140px;
        }

        .badge {
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: #fff;
            color: var(--muted);
        }

        .badge.ok {
            color: #065f46;
            border-color: rgba(16, 185, 129, .25);
            background: rgba(16, 185, 129, .08);
        }

        .add-row-btn{
            width:200px;
        }

        .api-url-copy-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
            width: 100%;
            max-width: 850px;
        }

        .api-url-copy-wrap input[type="text"] {
            flex: 1;
            min-width: 0;
            background:#f9fafb;
            font-size: 15px;
            font-weight: 700;
            color:rgb(36, 103, 247);
        }

        .api-url-copy-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: #fff;
            cursor: pointer;
            color: #4b5563;
            flex: 0 0 auto;
        }

        .api-url-copy-btn:hover {
            background: #f3f4f6;
            color: #111827;
        }

        .rulebook-meta {
            display: grid;
            gap: 8px;
            margin-bottom: 12px;
        }

        .rulebook-meta-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 8px 10px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--bg);
            font-size: 12px;
            color: var(--muted);
        }

        .rulebook-meta-item span {
            width: 120px;
        }

        .rulebook-meta-item strong {
            font-size: 13px;
            color: #111827;
            text-align: right;
        }
    }
</style>

<div id="contents_head">
    <h1>AI 룰북 설정</h1>
    <h3>관리자 설정 페이지(프롬프트 라이브러리) - 현재 작업중입니다.</h3>
</div>

<div id="contents_body">
	<div id="contents_body_wrap">

        <div class="rulebook-wrap">
            <div class="topbar">
                <div>
                    <h1><?= $rulebook['name'] ?? '' ?></h1>
                    <p class="sub"><?= $rulebook['description'] ?? '' ?></p>

                    <div class="m-t-15">
                        <div class="api-url-copy-wrap">
                            <input
                                id="apiRulebookUrl"
                                type="text"
                                readonly
                                value="https://dnfixhead.mycafe24.com/api2/ai/rulebook/<?= urlencode($rulebook['code'] ?? '') ?>?v=<?= date('Ymd', strtotime($rulebook['updated_at'] ?? '')) ?>-<?= urlencode((string)($rulebook['version_no'] ?? '')) ?>"
                            />
                            <button class="api-url-copy-btn" type="button" id="btnCopyApiRulebookUrl" aria-label="API 주소 복사" title="API 주소 복사">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="9" y="9" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2"/>
                                    <path d="M5 15V7C5 5.9 5.9 5 7 5H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                            <span id="apiCopyMessage" class="muted" style="display:none;">복사됨</span>
                        </div>
                    </div>
                </div>

                <?php /*
                <div class="row">
                    <span class="pill">룰북 코드 : <strong id="uiCode"><?= $rulebook['code'] ?? '' ?></strong></span>
                    <span class="pill">현재 버전 : <strong id="uiVersionNo"><?= $rulebook['version_no'] ?? '' ?></strong></span>
                    <span class="pill">현재 배포코드 : <strong id="uiVersionCode"><?= $rulebook['version_code'] ?? '' ?></strong></span>
                </div>
                */ ?>

            </div>

            <div class="grid" style="margin-top:16px;">
                <div class="card">
                    <div class="hd">
                        <strong>기본 정보</strong>
                        <span class="badge" id="uiDirty">변경 없음</span>
                    </div>
                    <div class="bd">
                        <div class="form">
                            <label for="code">룰북 코드(code)</label>
                            <input id="code" type="text" value="<?= $rulebook['code'] ?? '' ?>" placeholder="예: product_description" />

                            <label for="kind">Kind</label>
                            <input id="kind" type="text" value="<?= $rulebook['kind'] ?? '' ?>" placeholder="예: content" />

                            <label for="category">Category</label>
                            <input id="category" type="text" value="<?= $rulebook['category'] ?? '' ?>" placeholder="예: product_description" />

                            <label for="name">이름</label>
                            <input id="name" type="text" value="<?= $rulebook['name'] ?? '' ?>" placeholder="관리용 이름" />

                            <label for="description">설명</label>
                            <input id="description" type="text" value="<?= $rulebook['description'] ?? '' ?>" placeholder="예: 일본 원문 번역/표기 통일 및 톤 고정" />

                        </div>
                    </div>

                    <div class="full hr"></div>

                    <div class="tabs m-t-10" role="tablist" aria-label="룰북 탭">
                        <button class="tab active" data-tab="style" type="button">Style (문체/톤)</button>
                        <button class="tab" data-tab="glossary" type="button">Glossary (고정 표기)</button>
                        <button class="tab" data-tab="replacements" type="button">Replacements (치환 규칙)</button>
                        <button class="tab" data-tab="forbidden" type="button">Forbidden (금지 표현)</button>
                        <button class="tab" data-tab="required" type="button">Required (필수 포함)</button>
                        <button class="tab" data-tab="output" type="button">Output (출력/템플릿)</button>
                        <button class="tab" data-tab="preview" type="button">미리보기</button>
                    </div>

                    <div class="tabpanes">

                        <section class="pane active" id="pane-style">
                            <div class="form m-t-20">
                                <label for="tone_guideline">톤/말투 가이드</label>
                                <textarea id="tone_guideline" placeholder="예: 브랜드 소개형, 담백. 과장/단정 금지. 일본 출처는 일본이라고 표기."><?= $rulebook['tone_guideline'] ?? '' ?></textarea>

                                <label for="examples_good">Good 예시</label>
                                <textarea id="examples_good" placeholder="좋은 예시를 적어두면 AI가 톤을 더 잘 맞춥니다(선택)."><?= $rulebook['examples_good'] ?? '' ?></textarea>

                                <label for="examples_bad">Bad 예시</label>
                                <textarea id="examples_bad" placeholder="피해야 할 문장 예시(선택)."><?= $rulebook['examples_bad'] ?? '' ?></textarea>

                                <label>출력 포맷(섹션)</label>
                                <div class="m-t-20">
                                    <div id="formatList"></div>
                                    <div class="" style="margin-top:8px;">
                                        <input id="formatInput" type="text" style="width:500px;" placeholder="예: 한줄요약" />
                                        <button class="btnstyle1 btnstyle1-primary" type="button" id="btnAddFormat">+ 섹션 추가</button>
                                        <button class="btnstyle1" type="button" id="btnClearFormat">초기화</button>
                                    </div>
                                    <p class="help">섹션은 순서대로 사용됩니다. 예) 한줄요약 / 제품 특징 / 사용·관리 팁 / 주의사항</p>
                                </div>
                            </div>
                        </section>

                        <section class="pane" id="pane-glossary">
                            <div class="row" style="justify-content:space-between; align-items:center;">

                                <div class="muted guide-box">
                                    <p class="guide-title">Glossary (용어/표기 고정)</p>
                                    <p class="guide-desc">AI가 어떤 문장을 만들든, 지정한 용어를 항상 같은 표기로 강제합니다.</p>
                                    <ul class="guide-list">
                                        <li><code>src</code>: 매칭 대상(찾을 문자열)</li>
                                        <li><code>dst</code>: 출력에서 강제할 최종 표기</li>
                                        <li><code>note</code>: 운영 메모(왜 이렇게 정했는지 기록)</li>
                                        <li><code>scope</code>: 적용 범위(채널명), <code>*</code>는 모든 채널 적용</li>
                                    </ul>
                                    <p class="guide-tip">예: <code>ローター</code>를 항상 <code>로터</code>로 고정</p>

                                    <div class="hr"></div>

                                    <p class="guide-title">설정 시 주의사항</p>
                                    <ul class="guide-list">
                                        <li>
                                            <b>중복/충돌 주의</b>:
                                            동일한 <code>src</code>가 여러 번 등록되면 어떤 표기가 적용될지 혼란이 생깁니다.
                                            <span class="guide-sub">동일 <code>src</code>는 1개만 유지하고, 표준표기는 <code>dst</code>로 단일화하세요.</span>
                                        </li>
                                        <li>
                                            <b>겹치는 용어(부분 포함) 주의</b>:
                                            <code>Ride</code>와 <code>Ride Japan</code>처럼 긴 용어/짧은 용어가 겹치면 적용 결과가 달라질 수 있습니다.
                                            <span class="guide-sub">가능하면 더 구체적인(긴) 용어를 우선 등록하고, 필요 시 운영 규칙으로 “긴 용어 우선” 적용을 권장합니다.</span>
                                        </li>
                                        <li>
                                            <b>불필요한 과치환 주의</b>:
                                            너무 짧은 <code>src</code>(예: 1~2글자)는 예상치 못한 문장까지 바뀔 수 있습니다.
                                            <span class="guide-sub">짧은 키워드는 맥락이 명확한 형태로(단어/구문) 등록하는 것을 추천합니다.</span>
                                        </li>
                                        <li>
                                            <b>scope 운영 규칙</b>:
                                            채널마다 표기 정책이 다르면 <code>scope</code>로 분리하세요.
                                            <span class="guide-sub">예: 상품설명에는 적용하지만, 메이커 코멘트(원문 유지 정책)에는 적용 제외하도록 운영 규칙을 명확히 해두는 것을 권장합니다.</span>
                                        </li>
                                        <li>
                                            <b>삭제 대신 비활성 권장</b>:
                                            기존 항목을 지우기보다 OFF로 관리하면, 과거 버전 추적과 롤백이 쉬워집니다.
                                        </li>
                                    </ul>
                                </div>

                                <button class="btnstyle1 btnstyle1-primary btnstyle1-md add-row-btn" type="button" id="btnAddGlossary" >+ 행 추가</button>
                            </div>
                            <div style="overflow:auto; margin-top:10px;">
                                <table>
                                    <thead>
                                        <tr>
                                            <th style="width:22%;">원문<br>(src)</th>
                                            <th style="width:22%;">표준표기<br>(dst)</th>
                                            <th>비고<br>(note)</th>
                                            <th style="width:14%;">scope<br>(범위)</th>
                                            <th style="width:8%;">적용여부<br>(enabled)</th>
                                            <th style="width:5%;">삭제<br>(remove)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyGlossary"></tbody>
                                </table>
                            </div>
                            <!--
                            <p class="help">scope는 "*" 또는 "product_description" 처럼 채널명을 넣을 수 있습니다.</p>
                            -->
                        </section>

                        <section class="pane" id="pane-replacements">
                            <div class="row" style="justify-content:space-between; align-items:center;">

                                <div class="muted guide-box">
                                    <p class="guide-title">Replacements (치환 규칙)</p>
                                    <p class="guide-desc">
                                        자주 틀리거나 통일이 필요한 표현을 <code>from → to</code>로 자동 치환해 문구를 일관되게 유지합니다.
                                        <br>치환은 생성 전(프롬프트)에도 활용할 수 있지만, 최종 결과에서 확실히 반영하려면 후처리(치환 적용)를 함께 사용하는 것을 권장합니다.
                                    </p>

                                    <ul class="guide-list">
                                        <li><code>from</code>: 치환할 원본 문구(찾을 문자열)</li>
                                        <li><code>to</code>: 바꿔 넣을 표준 문구</li>

                                        <li>
                                            <code>match</code>: 매칭 방식(<code>exact</code>/<code>contains</code>/<code>regex</code>)
                                            <ul>
                                                <li>
                                                    <code>exact</code>:
                                                    <b>완전 일치</b>일 때만 치환합니다.
                                                    <span class="guide-sub">단어/짧은 문구처럼 범위를 정확히 제한하고 싶을 때 추천.</span>
                                                    <br><span class="guide-sub">예) <code>국내</code> → <code>일본</code> (정확히 "국내"만 바뀜)</span>
                                                </li>
                                                <li>
                                                    <code>contains</code>:
                                                    문장 안에 <b>포함</b>되면 치환합니다.
                                                    <span class="guide-sub">표현 습관 통일에 가장 많이 사용. 다만 원치 않는 부분까지 바뀔 수 있어 주의.</span>
                                                    <br><span class="guide-sub">예) <code>고객님</code> 포함 시 → <code>고객</code></span>
                                                </li>
                                                <li>
                                                    <code>regex</code>:
                                                    <b>정규식</b> 패턴으로 치환합니다.
                                                    <span class="guide-sub">띄어쓰기/숫자/단위/여러 변형을 한 번에 처리할 때 유용하지만 오작동 위험이 있어 고급 옵션으로 권장.</span>
                                                    <br><span class="guide-sub">예) <code>/\\s+/</code> 같은 패턴으로 연속 공백 정리(운영 정책에 따라)</span>
                                                </li>
                                            </ul>
                                        </li>

                                        <li><code>scope</code>: 적용 범위(채널명), <code>*</code>는 모든 채널 적용</li>

                                        <li>
                                            <code>severity</code>: 중요도(<code>info</code>/<code>warn</code>/<code>error</code>) — 검증/경고 수준에 활용
                                            <ul>
                                                <li>
                                                    <code>info</code>:
                                                    참고 수준. 미적용/잔존해도 치명적이지 않은 통일 규칙.
                                                    <span class="guide-sub">리포트용 또는 점진 도입용으로 추천.</span>
                                                </li>
                                                <li>
                                                    <code>warn</code>:
                                                    경고 수준. 가급적 통일해야 하는 규칙.
                                                    <span class="guide-sub">검증(validate)에서 경고로 표시하고, 운영자가 확인 후 개선.</span>
                                                </li>
                                                <li>
                                                    <code>error</code>:
                                                    오류 수준. 반드시 통일해야 하는 규칙.
                                                    <span class="guide-sub">검증(validate)에서 오류로 처리하여 배포/등록을 막거나 수정 요구에 사용.</span>
                                                </li>
                                            </ul>
                                        </li>

                                        <li>
                                            <code>priority</code>: 적용 순서(숫자가 낮을수록 먼저 적용)
                                            <span class="guide-sub">치환 규칙이 겹칠 때 결과가 달라지므로, 더 구체적인 규칙을 먼저(낮은 숫자) 배치하는 것을 권장합니다.</span>
                                        </li>

                                        <li><code>enabled</code>: 적용 여부(ON/OFF). 삭제 대신 OFF로 관리하면 추적/롤백이 쉽습니다.</li>
                                    </ul>

                                    <div class="hr"></div>

                                    <p class="guide-title">설정 시 주의사항</p>
                                    <ul class="guide-list">
                                        <li>
                                            <b>겹치는 규칙 충돌 주의</b>:
                                            <code>from</code>이 서로 겹치거나, 한 규칙의 <code>to</code>가 다른 규칙의 <code>from</code>에 걸리면 결과가 달라질 수 있습니다.
                                            <span class="guide-sub">이 경우 <code>priority</code>로 적용 순서를 명확히 정해주세요.</span>
                                        </li>
                                        <li>
                                            <b>무한 치환(루프) 위험</b>:
                                            <code>A → B</code>와 <code>B → A</code>처럼 서로 되돌리는 규칙은 피하세요.
                                            <span class="guide-sub">후처리 치환은 보통 1회 패스로 끝내는 것을 권장합니다.</span>
                                        </li>
                                        <li>
                                            <b><code>contains</code> 사용 시 과치환 주의</b>:
                                            의도한 단어 외에 다른 단어 일부까지 바뀔 수 있습니다.
                                            <span class="guide-sub">애매하면 <code>exact</code>로 좁히거나, 더 구체적인 <code>from</code>을 사용하세요.</span>
                                        </li>
                                        <li>
                                            <b><code>regex</code>는 고급 옵션</b>:
                                            패턴이 넓으면 의도치 않은 부분까지 바뀔 수 있습니다.
                                            <span class="guide-sub">적용 전/후 예시를 꼭 테스트하고, 가능하면 관리자만 사용하도록 권장합니다.</span>
                                        </li>
                                        <li>
                                            <b>메이커 코멘트 영역 정책</b>:
                                            메이커 코멘트는 원문 유지 정책이라면, 해당 영역에는 치환을 적용하지 않도록(scope 분리 또는 후처리 제외) 운영 규칙을 정해두세요.
                                        </li>
                                    </ul>

                                    <p class="guide-tip">예: <code>국내</code>를 항상 <code>일본</code>으로 치환해 출처 표기를 통일</p>
                                </div>

                                <button class="btnstyle1 btnstyle1-primary btnstyle1-md add-row-btn" type="button" id="btnAddReplacement">+ 행 추가</button>
                            </div>
                            <div style="overflow:auto; margin-top:10px;">
                                <table>
                                    <thead>
                                        <tr>
                                            <th style="width:18%;">from</th>
                                            <th style="width:18%;">to</th>
                                            <th style="width:12%;">match</th>
                                            <th style="width:14%;">scope</th>
                                            <th style="width:12%;">severity</th>
                                            <th style="width:10%;">priority</th>
                                            <th style="width:10%;">enabled</th>
                                            <th style="width:6%;">삭제</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyReplacements"></tbody>
                                </table>
                            </div>
                            <p class="help">priority는 숫자가 낮을수록 먼저 적용됩니다(기본 100).</p>
                        </section>

                        <section class="pane" id="pane-forbidden">
                            <div class="row" style="justify-content:space-between; align-items:center;">
                                <div class="muted guide-box">
                                    <p class="guide-title">Forbidden (금지 표현)</p>
                                    <p class="guide-desc">
                                        결과 문안에 포함되면 안 되는 단어/문구/패턴을 정의합니다.
                                        <br>검증(validate) 단계에서 위반을 탐지해 경고/오류로 표시하고, 필요 시 배포/등록을 막는 용도로 사용합니다.
                                    </p>

                                    <ul class="guide-list">
                                        <li><code>term/pattern</code>: 금지할 단어/문구 또는 패턴(정규식 포함 가능)</li>
                                        <li>
                                            <code>match</code>: 매칭 방식(<code>exact</code>/<code>contains</code>/<code>regex</code>)
                                            <ul>
                                                <li>
                                                    <code>exact</code>:
                                                    <b>완전 일치</b>일 때만 위반으로 처리합니다.
                                                    <span class="guide-sub">짧은 단어를 과하게 잡지 않게 하려면 exact를 추천.</span>
                                                </li>
                                                <li>
                                                    <code>contains</code>:
                                                    문장에 <b>포함</b>되면 위반으로 처리합니다.
                                                    <span class="guide-sub">가장 많이 사용. 금지 문구가 결과에 섞이는지 빠르게 잡아냅니다.</span>
                                                </li>
                                                <li>
                                                    <code>regex</code>:
                                                    <b>정규식</b> 패턴으로 탐지합니다.
                                                    <span class="guide-sub">여러 변형(띄어쓰기/숫자/기호)을 한 번에 잡을 때 유용하지만 오탐 위험이 있어 고급 옵션으로 권장.</span>
                                                </li>
                                            </ul>
                                        </li>
                                        <li><code>scope</code>: 적용 범위(채널명), <code>*</code>는 모든 채널 적용</li>
                                        <li>
                                            <code>severity</code>: 중요도(<code>info</code>/<code>warn</code>/<code>error</code>) — 검증/경고 수준
                                            <ul>
                                                <li><code>info</code>: 참고용 표시(운영 리포트용)</li>
                                                <li><code>warn</code>: 경고(수정 권장)</li>
                                                <li><code>error</code>: 오류(배포/등록 차단 또는 수정 필수)</li>
                                            </ul>
                                        </li>
                                        <li><code>message</code>: 위반 시 표시할 안내 문구(운영자/작업자에게 이유를 알려주는 용도)</li>
                                        <li><code>enabled</code>: 적용 여부(ON/OFF). 삭제 대신 OFF로 관리하면 추적/롤백이 쉽습니다.</li>
                                    </ul>

                                    <p class="guide-tip">예: <code>100% 동일</code>, <code>완벽</code>, <code>무조건</code> 같은 과장/단정 표현을 금지하고 <code>message</code>로 수정 가이드를 제공합니다.</p>

                                    <div class="hr"></div>

                                    <p class="guide-title">설정 시 주의사항</p>
                                    <ul class="guide-list">
                                        <li>
                                            <b>오탐(잘못 잡힘) 주의</b>:
                                            너무 짧은 금지어(예: 1~2글자)는 정상 문장까지 걸릴 수 있습니다.
                                            <span class="guide-sub">가능하면 문구 단위로 지정하거나 <code>exact</code>를 사용하세요.</span>
                                        </li>
                                        <li>
                                            <b><code>contains</code>는 범위가 넓음</b>:
                                            의도치 않은 단어 일부까지 위반으로 잡힐 수 있습니다.
                                            <span class="guide-sub">애매하면 더 구체적인 <code>term</code>을 쓰거나 <code>regex</code>로 경계를 지정하세요.</span>
                                        </li>
                                        <li>
                                            <b><code>regex</code>는 고급 옵션</b>:
                                            패턴이 넓으면 오탐이 급증합니다.
                                            <span class="guide-sub">적용 전/후 테스트를 꼭 하고, 가능하면 관리자만 사용하도록 권장합니다.</span>
                                        </li>
                                        <li>
                                            <b>scope 운영</b>:
                                            메이커 코멘트는 원문 유지 정책이라면, 해당 영역에는 금지 규칙을 적용할지(적용/미적용)를 명확히 정해두세요.
                                            <span class="guide-sub">보통은 일반 설명에만 엄격 적용하고, 메이커 코멘트는 분리(또는 별도 scope)하는 방식을 권장합니다.</span>
                                        </li>
                                        <li>
                                            <b>message는 행동 지침으로</b>:
                                            단순히 “금지”가 아니라, 어떤 식으로 바꾸면 좋은지 안내 문구를 적어두면 운영 효율이 올라갑니다.
                                        </li>
                                    </ul>
                                </div>

                                <button class="btnstyle1 btnstyle1-primary btnstyle1-md add-row-btn" type="button" id="btnAddForbidden">+ 행 추가</button>
                            </div>
                            <div style="overflow:auto; margin-top:10px;">
                                <table>
                                    <thead>
                                        <tr>
                                            <th style="width:24%;">term/pattern</th>
                                            <th style="width:12%;">match</th>
                                            <th style="width:14%;">scope</th>
                                            <th style="width:12%;">severity</th>
                                            <th style="width:28%;">message</th>
                                            <th style="width:8%;">enabled</th>
                                            <th style="width:6%;">삭제</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyForbidden"></tbody>
                                </table>
                            </div>
                        </section>

                        <section class="pane" id="pane-required">
                            <div class="row" style="justify-content:space-between; align-items:center;">
                                <div class="muted guide-box">
                                    <p class="guide-title">Required (필수 포함)</p>
                                    <p class="guide-desc">
                                        결과 문안에 <b>반드시 포함되어야 하는 요소</b>를 정의합니다.
                                        <br>검증(validate) 단계에서 누락을 탐지해 경고/오류로 표시하고, 필요 시 배포/등록을 막는 용도로 사용합니다.
                                    </p>

                                    <ul class="guide-list">
                                        <li><code>term</code>: 반드시 포함되어야 하는 문구/요소(헤더/키워드/문장 등)</li>

                                        <li>
                                            <code>match</code>: 매칭 방식(<code>exact</code>/<code>contains</code>/<code>regex</code>)
                                            <ul>
                                                <li>
                                                    <code>exact</code>:
                                                    <b>완전 일치</b>가 존재할 때만 통과합니다.
                                                    <span class="guide-sub">정확한 문구가 꼭 있어야 할 때 사용(다만 문구 변경에 취약).</span>
                                                </li>
                                                <li>
                                                    <code>contains</code>:
                                                    문장 안에 <b>포함</b>되면 통과합니다.
                                                    <span class="guide-sub">가장 많이 사용. 표현이 조금 달라도 누락 여부를 체크하기 좋습니다.</span>
                                                </li>
                                                <li>
                                                    <code>regex</code>:
                                                    <b>정규식</b> 패턴이 매칭되면 통과합니다.
                                                    <span class="guide-sub">헤더 형태, 숫자/단위 포함 같은 “형식”을 강제할 때 유용하지만 고급 옵션입니다.</span>
                                                </li>
                                            </ul>
                                        </li>

                                        <li><code>scope</code>: 적용 범위(채널명), <code>*</code>는 모든 채널 적용</li>

                                        <li>
                                            <code>severity</code>: 중요도(<code>info</code>/<code>warn</code>/<code>error</code>) — 누락 시 처리 수준
                                            <ul>
                                                <li><code>info</code>: 참고용(누락 알림만)</li>
                                                <li><code>warn</code>: 경고(수정 권장)</li>
                                                <li><code>error</code>: 오류(배포/등록 차단 또는 수정 필수)</li>
                                            </ul>
                                        </li>

                                        <li><code>hint</code>: 누락 시 표시할 안내/수정 힌트(무엇을 어떻게 넣을지 가이드)</li>
                                        <li><code>enabled</code>: 적용 여부(ON/OFF). 삭제 대신 OFF로 관리하면 추적/롤백이 쉽습니다.</li>
                                    </ul>

                                    <p class="guide-tip">
                                        예: <code>주의사항</code> 섹션이 항상 포함되게 하고, 누락 시 <code>hint</code>로 “주의사항 섹션을 포함하세요”를 안내
                                    </p>

                                    <div class="hr"></div>

                                    <p class="guide-title">설정 시 주의사항</p>
                                    <ul class="guide-list">
                                        <li>
                                            <b>너무 엄격하면 운영이 힘듦</b>:
                                            <code>exact</code>는 문구가 조금만 바뀌어도 누락으로 잡힙니다.
                                            <span class="guide-sub">처음엔 <code>contains</code>로 시작하고, 정말 필수인 것만 <code>error</code>로 올리는 것을 권장합니다.</span>
                                        </li>
                                        <li>
                                            <b>표현 다양성을 고려</b>:
                                            “주의사항”, “주의”, “유의사항”처럼 다양한 표현이 가능하면,
                                            <span class="guide-sub"><code>regex</code>로 묶거나 여러 줄로 term을 추가해 누락 오탐을 줄이세요.</span>
                                        </li>
                                        <li>
                                            <b>scope를 잘 나누기</b>:
                                            채널마다 필수 요소가 다를 수 있습니다.
                                            <span class="guide-sub">예: <code>cs_reply</code>에는 “교환/환불” 필수, <code>product_description</code>에는 “사용·관리 팁” 필수.</span>
                                        </li>
                                        <li>
                                            <b>hint는 행동 지침으로</b>:
                                            누락 시 “무엇을” “어디에” “어떤 톤으로” 넣으면 되는지 짧게 적어두면 수정 속도가 빨라집니다.
                                        </li>
                                        <li>
                                            <b>템플릿/섹션과 연동</b>:
                                            출력 포맷(섹션)을 쓰는 경우, 필수 섹션은 <code>term</code>을 섹션명으로 두면 관리가 쉽습니다.
                                            <span class="guide-sub">예: term=“주의사항”, hint=“주의사항 섹션을 포함하세요”.</span>
                                        </li>
                                    </ul>
                                </div>

                                <button class="btnstyle1 btnstyle1-primary btnstyle1-md add-row-btn" type="button" id="btnAddRequired">+ 행 추가</button>
                            </div>
                            <div style="overflow:auto; margin-top:10px;">
                                <table>
                                    <thead>
                                        <tr>
                                            <th style="width:24%;">term</th>
                                            <th style="width:12%;">match</th>
                                            <th style="width:14%;">scope</th>
                                            <th style="width:12%;">severity</th>
                                            <th style="width:28%;">hint</th>
                                            <th style="width:8%;">enabled</th>
                                            <th style="width:6%;">삭제</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyRequired"></tbody>
                                </table>
                            </div>
                        </section>

                        <section class="pane" id="pane-output">
                            <div class="form">
                                <label for="output_mode">출력 모드</label>
                                <select id="output_mode">
                                    <option value="plain_text">일반 텍스트</option>
                                    <option value="html_template">HTML 템플릿</option>
                                </select>

                                <label for="template_id">템플릿 ID</label>
                                <input id="template_id" type="text" placeholder="예: new_goods2_wrap_v1" />

                                <label for="content_type">Content-Type</label>
                                <input id="content_type" type="text" placeholder="예: text/html 또는 text/plain" />

                                <label for="description_join">설명 줄바꿈</label>
                                <select id="description_join">
                                    <option value="lines_to_br">줄바꿈을 &lt;br&gt;로 변환</option>
                                    <option value="plain_text">그냥 텍스트</option>
                                </select>

                                <label for="escape_text_nodes">HTML Escape</label>
                                <select id="escape_text_nodes">
                                    <option value="1">텍스트는 escape 처리</option>
                                    <option value="0">escape 하지 않음(비추천)</option>
                                </select>

                                <div class="full hr"></div>

                                <div class="full muted">
                                    HTML 템플릿 모드에서는 아래 템플릿에 내용을 채워서 출력합니다. 템플릿은 룰북 버전과 함께 저장됩니다.
                                </div>

                                <label for="template_html">템플릿 HTML</label>
                                <textarea id="template_html" name="template_html" style="min-height:260px;" placeholder="여기에 템플릿 HTML을 넣어주세요."><?php if (!empty($rulebook['template_html'])): ?><?= htmlspecialchars($rulebook['template_html'], ENT_QUOTES, 'UTF-8') ?><?php endif; ?></textarea>

                                <label for="schema_help">스키마 도움말</label>
                                <textarea id="schema_help" name="schema_help" style="min-height:100px;" placeholder="여기에 스키마 도움말을 넣어주세요."><?php if (!empty($rulebook['schema_help'])): ?><?= htmlspecialchars($rulebook['schema_help'], ENT_QUOTES, 'UTF-8') ?><?php endif; ?></textarea>

                                <div class="full help">
                                    권장: AI는 "채울 데이터(JSON)"를 만들고, 서버에서 템플릿에 안전하게 주입(render)하세요.<br>
                                    placeholder 예: <code>{{g2_name_en}}</code>, <code>{{points_li}}</code>, <code>{{maker_comment_html}}</code>
                                </div>

                            </div>
                        </section>

                        <section class="pane" id="pane-preview">
                            <div class="preview">
                                <div>
                                    <div class="muted" style="margin-bottom:8px;">rules_json 미리보기</div>
                                    <div class="codebox" id="jsonPreview"></div>
                                </div>
                                <div>
                                    <div class="muted" style="margin-bottom:8px;">API 저장 payload 예시</div>
                                    <div class="codebox" id="payloadPreview"></div>
                                    <p class="help">실제 저장 시에는 서버에서 version_no 증가 + version_code 생성 + 히스토리로 이전 버전 이동을 처리합니다.</p>
                                </div>
                            </div>
                        </section>

                    </div>

                    <div class="full hr"></div>

                    <div class="card" style="border:none; margin-top:0;">
                        <div class="bd">
                            <div class="form">
                                <label for="release_note">Release note</label>
                                <textarea id="release_note" placeholder="이번 배포에서 바뀐 내용을 간단히 적어주세요. (저장 시 새 버전 배포)"></textarea>
                                <div class="full actions">
                                    <button class="btn" type="button" id="btnReset">변경사항 되돌리기</button>
                                    <button class="btn primary" type="button" id="btnPublish">저장(새 버전 배포)</button>
                                </div>
                                <div class="full" id="msg" class="muted" style="display:none;"></div>
                            </div>
                        </div>
                    </div>

                </div>

                <aside class="card">
                    <div class="hd">
                        <strong>안내</strong>
                        <span class="badge ok" id="uiStatus">설정 페이지</span>
                    </div>
                    <div class="bd2">

                        <div class="rulebook-meta">
                            <div class="rulebook-meta-item">
                                <span>룰북 코드</span>
                                <strong><?= $rulebook['code'] ?? '' ?></strong>
                            </div>
                            <div class="rulebook-meta-item">
                                <span>현재 버전</span>
                                <strong><?= $rulebook['version_no'] ?? '' ?></strong>
                            </div>
                            <div class="rulebook-meta-item">
                                <span>현재 배포코드</span>
                                <strong><?= $rulebook['version_code'] ?? '' ?></strong>
                            </div>
                            <div class="rulebook-meta-item">
                                <span>생성일</span>
                                <strong><?= $rulebook['created_at'] ?? '' ?></strong>
                            </div>
                            <div class="rulebook-meta-item">
                                <span>수정일</span>
                                <strong><?= $rulebook['updated_at'] ?? '' ?></strong>
                            </div>
                        </div>

                        <div class="muted" style="line-height:1.6;">
                            <div style="margin-bottom:10px;"><strong>운영 방식</strong></div>
                            <ol style="margin:0; padding-left:18px;">
                                <li>현재 최종 룰은 <code>ai_rulebook</code>에 저장</li>
                                <li>저장(배포) 시 기존 최종 룰은 <code>ai_rulebook_history</code>로 이동</li>
                                <li>버전은 자동 증가, 배포코드는 매번 새로 생성</li>
                                <li>외부 호출은 숫자 idx가 아니라 <code>code</code>로 조회</li>
                            </ol>
                        </div>

                        <div class="hr"></div>

                        <div class="muted" style="line-height:1.6;">
                            <div style="margin-bottom:8px;"><strong>추천 기본값</strong></div>
                            <ul style="margin:0; padding-left:18px;">
                                <li>Style에 톤/포맷을 먼저 고정</li>
                                <li>Glossary로 용어/표기 통일</li>
                                <li>Replacements로 반복 실수 치환</li>
                                <li>Forbidden/Required는 validate용으로 활용</li>
                            </ul>
                        </div>

                    </div>
                </aside>

            </div>
        </div>

    </div>
</div>
<div id="contents_bottom">

</div>

<script>
    const rulebookIdx = <?= (int)($rulebook['idx'] ?? 0) ?>;
    const rulebookOutputFormatRaw = <?= json_encode($rulebook['output_format'] ?? null, JSON_UNESCAPED_UNICODE) ?>;
    const rulebookTemplateHtmlRaw = <?= json_encode($rulebook['template_html'] ?? null, JSON_UNESCAPED_UNICODE) ?>;
    const rulebookGlossaryRaw = <?= json_encode($rulebook['glossary_json'] ?? null, JSON_UNESCAPED_UNICODE) ?>;
    const rulebookReplacementsRaw = <?= json_encode($rulebook['replacements_json'] ?? null, JSON_UNESCAPED_UNICODE) ?>;
    const rulebookForbiddenRaw = <?= json_encode($rulebook['forbidden_json'] ?? null, JSON_UNESCAPED_UNICODE) ?>;
    const rulebookRequiredRaw = <?= json_encode($rulebook['required_json'] ?? null, JSON_UNESCAPED_UNICODE) ?>;

    /**
     * output_format 원본 데이터를 병합 포맷으로 정규화한다.
     * 지원 포맷:
     * - 레거시: ["섹션1","섹션2", ...]
     * - 병합형: { sections: [...], output: {...} }
     * - 병합형(평면): { output_format:[...], mode, template_id, ... }
     *
     * @param {unknown} raw DB에서 받은 output_format 원본 값
     * @returns {{sections:string[],output:{mode:string,template_id:string,content_type:string,description_join:string,escape_text_nodes:number,template_html:string}}}
     */
    function parseOutputFormat(raw) {
        let parsed = raw;
        let sections = [];
        let outputRaw = {};

        if (typeof parsed === 'string') {
            const trimmed = parsed.trim();
            if (trimmed) {
                try {
                    parsed = JSON.parse(trimmed);
                } catch (e) {
                    parsed = [];
                }
            }
        }

        if (Array.isArray(parsed)) {
            sections = parsed;
        } else if (parsed && typeof parsed === 'object') {
            if (Array.isArray(parsed.sections)) {
                sections = parsed.sections;
            } else if (Array.isArray(parsed.output_format)) {
                sections = parsed.output_format;
            } else if (Array.isArray(parsed.format)) {
                sections = parsed.format;
            }

            if (parsed.output && typeof parsed.output === 'object') {
                outputRaw = parsed.output;
            } else {
                outputRaw = parsed;
            }
        }

        const normalizedSections = sections
            .map(v => (v ?? '').toString().trim())
            .filter(v => v !== '');

        const normalizedOutput = {
            mode: (outputRaw?.mode ?? 'html_template').toString().trim() || 'html_template',
            template_id: (outputRaw?.template_id ?? 'new_goods2_wrap_v1').toString().trim() || 'new_goods2_wrap_v1',
            content_type: (outputRaw?.content_type ?? 'text/html').toString().trim() || 'text/html',
            // legacy key(maker_comment_join) fallback 지원
            description_join: (outputRaw?.description_join ?? outputRaw?.maker_comment_join ?? 'lines_to_br').toString().trim() || 'lines_to_br',
            escape_text_nodes: Number(outputRaw?.escape_text_nodes ?? 1) === 0 ? 0 : 1,
            template_html: (outputRaw?.template_html ?? '').toString()
        };

        return {
            sections: normalizedSections,
            output: normalizedOutput
        };
    }

    /**
     * glossary_json 원본 데이터를 화면 렌더링용 배열로 정규화한다.
     * - 배열/JSON 문자열을 모두 처리
     * - 각 행에 내부 식별자(_id) 부여
     * - 누락 필드 기본값(src/dst/note/scope/enabled) 보정
     *
     * @param {unknown} raw DB에서 받은 glossary_json 원본 값
     * @returns {Array<{_id:string,src:string,dst:string,note:string,scope:string,enabled:number}>}
     */
    function parseGlossary(raw) {
        let list = [];

        if (Array.isArray(raw)) {
            list = raw;
        } else if (typeof raw === 'string') {
            const trimmed = raw.trim();
            if (trimmed) {
                try {
                    const parsed = JSON.parse(trimmed);
                    if (Array.isArray(parsed)) {
                        list = parsed;
                    }
                } catch (e) {
                    list = [];
                }
            }
        }

        return list.map(item => ({
            _id: uid(),
            src: (item?.src ?? '').toString().trim(),
            dst: (item?.dst ?? '').toString().trim(),
            note: (item?.note ?? '').toString().trim(),
            scope: (item?.scope ?? '*').toString().trim() || '*',
            enabled: Number(item?.enabled ?? 1) === 0 ? 0 : 1
        }));
    }

    /**
     * replacements_json 원본 데이터를 화면 렌더링용 배열로 정규화한다.
     * - 배열/JSON 문자열을 모두 처리
     * - 각 행에 내부 식별자(_id) 부여
     * - 누락 필드 기본값(from/to/match/scope/severity/priority/enabled) 보정
     *
     * @param {unknown} raw DB에서 받은 replacements_json 원본 값
     * @returns {Array<{_id:string,from:string,to:string,match:string,scope:string,severity:string,priority:number,enabled:number}>}
     */
    function parseReplacements(raw) {
        let list = [];

        if (Array.isArray(raw)) {
            list = raw;
        } else if (typeof raw === 'string') {
            const trimmed = raw.trim();
            if (trimmed) {
                try {
                    const parsed = JSON.parse(trimmed);
                    if (Array.isArray(parsed)) {
                        list = parsed;
                    }
                } catch (e) {
                    list = [];
                }
            }
        }

        return list.map(item => ({
            _id: uid(),
            from: (item?.from ?? '').toString().trim(),
            to: (item?.to ?? '').toString().trim(),
            match: (item?.match ?? 'contains').toString().trim() || 'contains',
            scope: (item?.scope ?? '*').toString().trim() || '*',
            severity: (item?.severity ?? 'warn').toString().trim() || 'warn',
            priority: Number(item?.priority ?? 100) || 100,
            enabled: Number(item?.enabled ?? 1) === 0 ? 0 : 1
        }));
    }

    /**
     * forbidden_json 원본 데이터를 화면 렌더링용 배열로 정규화한다.
     * - 배열/JSON 문자열을 모두 처리
     * - 각 행에 내부 식별자(_id) 부여
     * - 누락 필드 기본값(term/match/scope/severity/message/enabled) 보정
     *
     * @param {unknown} raw DB에서 받은 forbidden_json 원본 값
     * @returns {Array<{_id:string,term:string,match:string,scope:string,severity:string,message:string,enabled:number}>}
     */
    function parseForbidden(raw) {
        let list = [];

        if (Array.isArray(raw)) {
            list = raw;
        } else if (typeof raw === 'string') {
            const trimmed = raw.trim();
            if (trimmed) {
                try {
                    const parsed = JSON.parse(trimmed);
                    if (Array.isArray(parsed)) {
                        list = parsed;
                    }
                } catch (e) {
                    list = [];
                }
            }
        }

        return list.map(item => ({
            _id: uid(),
            term: (item?.term ?? '').toString().trim(),
            match: (item?.match ?? 'contains').toString().trim() || 'contains',
            scope: (item?.scope ?? '*').toString().trim() || '*',
            severity: (item?.severity ?? 'warn').toString().trim() || 'warn',
            message: (item?.message ?? '').toString().trim(),
            enabled: Number(item?.enabled ?? 1) === 0 ? 0 : 1
        }));
    }

    /**
     * required_json 원본 데이터를 화면 렌더링용 배열로 정규화한다.
     * - 배열/JSON 문자열을 모두 처리
     * - 각 행에 내부 식별자(_id) 부여
     * - 누락 필드 기본값(term/match/scope/severity/hint/enabled) 보정
     *
     * @param {unknown} raw DB에서 받은 required_json 원본 값
     * @returns {Array<{_id:string,term:string,match:string,scope:string,severity:string,hint:string,enabled:number}>}
     */
    function parseRequired(raw) {
        let list = [];

        if (Array.isArray(raw)) {
            list = raw;
        } else if (typeof raw === 'string') {
            const trimmed = raw.trim();
            if (trimmed) {
                try {
                    const parsed = JSON.parse(trimmed);
                    if (Array.isArray(parsed)) {
                        list = parsed;
                    }
                } catch (e) {
                    list = [];
                }
            }
        }

        return list.map(item => ({
            _id: uid(),
            term: (item?.term ?? '').toString().trim(),
            match: (item?.match ?? 'contains').toString().trim() || 'contains',
            scope: (item?.scope ?? '*').toString().trim() || '*',
            severity: (item?.severity ?? 'warn').toString().trim() || 'warn',
            hint: (item?.hint ?? '').toString().trim(),
            enabled: Number(item?.enabled ?? 1) === 0 ? 0 : 1
        }));
    }

    const state = {
        format: [],
        glossary: [],
        replacements: [],
        forbidden: [],
        required: [],
        output: {
            mode: 'html_template',
            template_id: 'new_goods2_wrap_v1',
            content_type: 'text/html',
            description_join: 'lines_to_br',
            escape_text_nodes: 1,
            template_html: ''
        },
        dirty: false,
        initialSnapshot: null
    };

    /**
     * 변경 여부 상태를 갱신하고 상단 배지 문구/스타일을 동기화한다.
     *
     * @param {boolean} v 변경 여부
     * @returns {void}
     */
    function setDirty(v) {
        state.dirty = v;
        const badge = document.getElementById('uiDirty');
        badge.textContent = v ? '변경 있음' : '변경 없음';
        badge.className = 'badge' + (v ? '' : ' ok');
    }

    /**
     * 사용자 입력 문자열을 HTML 특수문자 이스케이프한다.
     * 동적 HTML 템플릿 문자열을 만들 때 XSS/마크업 깨짐을 방지한다.
     *
     * @param {unknown} s 원본 값
     * @returns {string} 이스케이프된 문자열
     */
    function escapeHtml(s) {
        return (s ?? '').toString()
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", "&#039;");
    }

    /**
     * 테이블 행 렌더링/수정 추적용 임시 고유키를 생성한다.
     * (DB PK가 없는 클라이언트 상태 배열에서 사용)
     *
     * @returns {string} 고유 식별자
     */
    function uid() {
        return Math.random().toString(16).slice(2) + Date.now().toString(16);
    }

    /**
     * 출력 포맷(섹션) pills UI를 렌더링한다.
     * - 빈 배열이면 안내 문구 표시
     * - 각 섹션 삭제 버튼 이벤트 바인딩
     * - 섹션 변경 시 scope 선택 UI가 최신 섹션을 반영하도록 연쇄 렌더링
     *
     * @returns {void}
     */
    function renderFormat() {
        const el = document.getElementById('formatList');
        if (!state.format.length) {
            el.innerHTML = '<div class="muted">섹션이 없습니다. 아래에서 추가하세요.</div>';
            return;
        }
        el.innerHTML = state.format.map((t, i) => (
            `<span class="pill" style="margin:0 8px 8px 0;">
          <strong style="color:#111827;">${i+1}.</strong> ${escapeHtml(t)}
          <button class="btn small" type="button" data-action="rm-format" data-idx="${i}">삭제</button>
        </span>`
        )).join('');
        el.querySelectorAll('button[data-action="rm-format"]').forEach(btn => {
            btn.addEventListener('click', () => {
                const idx = Number(btn.getAttribute('data-idx'));
                state.format.splice(idx, 1);
                setDirty(true);
                renderFormat();
                renderGlossary();
                renderReplacements();
                renderForbidden();
                renderRequired();
                renderPreviews();
            });
        });
    }

    /**
     * enabled 컬럼용 ON/OFF select HTML 조각을 생성한다.
     *
     * @param {boolean} checked ON 여부
     * @returns {string} select HTML 문자열
     */
    function makeToggle(checked) {
        return `<select class="inp-enabled">
        <option value="1" ${checked ? 'selected' : ''}>ON</option>
        <option value="0" ${!checked ? 'selected' : ''}>OFF</option>
      </select>`;
    }

    /**
     * scope select 옵션 목록을 생성한다.
     * - 기본 '*' 포함
     * - 현재 output_format 섹션명 목록을 옵션으로 포함
     * - 기존 값이 목록에 없으면 마지막에 추가해 데이터 유실 방지
     *
     * @param {string} currentValue 현재 행에 저장된 scope
     * @returns {string[]} 중복 제거된 scope 옵션 목록
     */
    function getScopeOptions(currentValue) {
        const options = ['*', ...(state.format || [])];
        const normalized = options
            .map(v => (v ?? '').toString().trim())
            .filter(v => v !== '');
        const unique = [...new Set(normalized)];
        const current = (currentValue ?? '*').toString().trim() || '*';

        if (!unique.includes(current)) {
            unique.push(current);
        }

        return unique;
    }

    /**
     * scope 컬럼용 select HTML 조각을 생성한다.
     * 옵션은 getScopeOptions() 결과를 사용한다.
     *
     * @param {string} value 현재 scope 값
     * @returns {string} select HTML 문자열
     */
    function makeScope(value) {
        const current = (value ?? '*').toString().trim() || '*';
        const options = getScopeOptions(current)
            .map(opt => `<option value="${escapeHtml(opt)}" ${opt === current ? 'selected' : ''}>${escapeHtml(opt)}</option>`)
            .join('');
        return `<select class="inp-scope">${options}</select>`;
    }

    /**
     * severity(info/warn/error) select HTML 조각을 생성한다.
     *
     * @param {string} value 현재 severity 값
     * @returns {string} select HTML 문자열
     */
    function makeSeverity(value) {
        const v = value ?? 'warn';
        return `<select class="inp-severity">
        <option value="info" ${v==='info'?'selected':''}>info</option>
        <option value="warn" ${v==='warn'?'selected':''}>warn</option>
        <option value="error" ${v==='error'?'selected':''}>error</option>
      </select>`;
    }

    /**
     * match(exact/contains/regex) select HTML 조각을 생성한다.
     *
     * @param {string} value 현재 match 값
     * @returns {string} select HTML 문자열
     */
    function makeMatch(value) {
        const v = value ?? 'contains';
        return `<select class="inp-match">
        <option value="exact" ${v==='exact'?'selected':''}>exact</option>
        <option value="contains" ${v==='contains'?'selected':''}>contains</option>
        <option value="regex" ${v==='regex'?'selected':''}>regex</option>
      </select>`;
    }

    /**
     * 테이블 한 행(tr)의 입력 이벤트를 공통 바인딩한다.
     * - input/change 시 dirty 처리 + 상태 반영 + 프리뷰 갱신
     * - 삭제 버튼 클릭 시 onChange(true)로 제거 흐름 실행
     *
     * @param {HTMLTableRowElement} tr 대상 행
     * @param {(remove?: boolean) => void} onChange 행 변경 처리 콜백
     * @returns {void}
     */
    function bindRowInputs(tr, onChange) {
        tr.querySelectorAll('input, textarea, select').forEach(inp => {
            inp.addEventListener('input', () => {
                setDirty(true);
                onChange();
                renderPreviews();
            });
            inp.addEventListener('change', () => {
                setDirty(true);
                onChange();
                renderPreviews();
            });
        });
        const btn = tr.querySelector('button[data-action="remove"]');
        if (btn) btn.addEventListener('click', () => {
            setDirty(true);
            onChange(true);
            renderPreviews();
        });
    }

    /**
     * glossary 상태 배열을 표로 렌더링한다.
     * 각 행 입력값을 state.glossary에 즉시 반영한다.
     *
     * @returns {void}
     */
    function renderGlossary() {
        const tbody = document.getElementById('tbodyGlossary');
        tbody.innerHTML = state.glossary.map(item => `
        <tr data-id="${item._id}">
          <td><input type="text" class="inp-src" value="${escapeHtml(item.src)}" placeholder="예: ローター" /></td>
          <td><input type="text" class="inp-dst" value="${escapeHtml(item.dst)}" placeholder="예: 로터" /></td>
          <td><input type="text" class="inp-note" value="${escapeHtml(item.note)}" placeholder="예: 고정 표기" /></td>
          <td>${makeScope(item.scope)}</td>
          <td>${makeToggle(item.enabled !== 0)}</td>
          <td class="td-actions"><button class="btn small danger" type="button" data-action="remove">삭제</button></td>
        </tr>
      `).join('');

        tbody.querySelectorAll('tr').forEach(tr => {
            bindRowInputs(tr, (remove = false) => {
                const id = tr.getAttribute('data-id');
                if (remove) {
                    state.glossary = state.glossary.filter(x => x._id !== id);
                    renderGlossary();
                    return;
                }
                const item = state.glossary.find(x => x._id === id);
                item.src = tr.querySelector('.inp-src').value.trim();
                item.dst = tr.querySelector('.inp-dst').value.trim();
                item.note = tr.querySelector('.inp-note').value.trim();
                item.scope = tr.querySelector('.inp-scope').value.trim() || '*';
                item.enabled = Number(tr.querySelector('.inp-enabled').value);
            });
        });
    }

    /**
     * replacements 상태 배열을 표로 렌더링한다.
     * from/to/match/scope/severity/priority/enabled를 state와 동기화한다.
     *
     * @returns {void}
     */
    function renderReplacements() {
        const tbody = document.getElementById('tbodyReplacements');
        tbody.innerHTML = state.replacements.map(item => `
        <tr data-id="${item._id}">
          <td><input type="text" class="inp-from" value="${escapeHtml(item.from)}" placeholder="예: 국내" /></td>
          <td><input type="text" class="inp-to" value="${escapeHtml(item.to)}" placeholder="예: 일본" /></td>
          <td>${makeMatch(item.match)}</td>
          <td>${makeScope(item.scope)}</td>
          <td>${makeSeverity(item.severity)}</td>
          <td><input type="text" class="inp-priority" value="${escapeHtml(item.priority)}" placeholder="100" /></td>
          <td>${makeToggle(item.enabled !== 0)}</td>
          <td class="td-actions"><button class="btn small danger" type="button" data-action="remove">삭제</button></td>
        </tr>
      `).join('');

        tbody.querySelectorAll('tr').forEach(tr => {
            bindRowInputs(tr, (remove = false) => {
                const id = tr.getAttribute('data-id');
                if (remove) {
                    state.replacements = state.replacements.filter(x => x._id !== id);
                    renderReplacements();
                    return;
                }
                const item = state.replacements.find(x => x._id === id);
                item.from = tr.querySelector('.inp-from').value.trim();
                item.to = tr.querySelector('.inp-to').value.trim();
                item.match = tr.querySelector('.inp-match').value;
                item.scope = tr.querySelector('.inp-scope').value.trim() || '*';
                item.severity = tr.querySelector('.inp-severity').value;
                item.priority = Number(tr.querySelector('.inp-priority').value || 100);
                item.enabled = Number(tr.querySelector('.inp-enabled').value);
            });
        });
    }

    /**
     * forbidden 상태 배열을 표로 렌더링한다.
     * 금지어/패턴 관련 입력값 변경을 state.forbidden에 반영한다.
     *
     * @returns {void}
     */
    function renderForbidden() {
        const tbody = document.getElementById('tbodyForbidden');
        tbody.innerHTML = state.forbidden.map(item => `
        <tr data-id="${item._id}">
          <td><input type="text" class="inp-term" value="${escapeHtml(item.term)}" placeholder="예: 100% 동일" /></td>
          <td>${makeMatch(item.match)}</td>
          <td>${makeScope(item.scope)}</td>
          <td>${makeSeverity(item.severity)}</td>
          <td><input type="text" class="inp-message" value="${escapeHtml(item.message)}" placeholder="예: 과장 표현 금지" /></td>
          <td>${makeToggle(item.enabled !== 0)}</td>
          <td class="td-actions"><button class="btn small danger" type="button" data-action="remove">삭제</button></td>
        </tr>
      `).join('');

        tbody.querySelectorAll('tr').forEach(tr => {
            bindRowInputs(tr, (remove = false) => {
                const id = tr.getAttribute('data-id');
                if (remove) {
                    state.forbidden = state.forbidden.filter(x => x._id !== id);
                    renderForbidden();
                    return;
                }
                const item = state.forbidden.find(x => x._id === id);
                item.term = tr.querySelector('.inp-term').value.trim();
                item.match = tr.querySelector('.inp-match').value;
                item.scope = tr.querySelector('.inp-scope').value.trim() || '*';
                item.severity = tr.querySelector('.inp-severity').value;
                item.message = tr.querySelector('.inp-message').value.trim();
                item.enabled = Number(tr.querySelector('.inp-enabled').value);
            });
        });
    }

    /**
     * required 상태 배열을 표로 렌더링한다.
     * 필수 포함 규칙(term/hint 등) 변경을 state.required에 반영한다.
     *
     * @returns {void}
     */
    function renderRequired() {
        const tbody = document.getElementById('tbodyRequired');
        tbody.innerHTML = state.required.map(item => `
        <tr data-id="${item._id}">
          <td><input type="text" class="inp-term" value="${escapeHtml(item.term)}" placeholder="예: 주의사항" /></td>
          <td>${makeMatch(item.match)}</td>
          <td>${makeScope(item.scope)}</td>
          <td>${makeSeverity(item.severity)}</td>
          <td><input type="text" class="inp-hint" value="${escapeHtml(item.hint)}" placeholder="예: 주의사항 섹션을 포함하세요" /></td>
          <td>${makeToggle(item.enabled !== 0)}</td>
          <td class="td-actions"><button class="btn small danger" type="button" data-action="remove">삭제</button></td>
        </tr>
      `).join('');

        tbody.querySelectorAll('tr').forEach(tr => {
            bindRowInputs(tr, (remove = false) => {
                const id = tr.getAttribute('data-id');
                if (remove) {
                    state.required = state.required.filter(x => x._id !== id);
                    renderRequired();
                    return;
                }
                const item = state.required.find(x => x._id === id);
                item.term = tr.querySelector('.inp-term').value.trim();
                item.match = tr.querySelector('.inp-match').value;
                item.scope = tr.querySelector('.inp-scope').value.trim() || '*';
                item.severity = tr.querySelector('.inp-severity').value;
                item.hint = tr.querySelector('.inp-hint').value.trim();
                item.enabled = Number(tr.querySelector('.inp-enabled').value);
            });
        });
    }

    /**
     * 화면의 현재 입력값 + state 배열을 rules_json 저장 포맷으로 수집한다.
     * - 비어있는 항목은 필터링
     * - 각 규칙 타입별 기본값 보정
     * - output/style/glossary/replacements/forbidden/required 구조 반환
     *
     * @returns {object} API 저장용 rules_json 객체
     */
    function collectRulesJson() {
        const style = {
            tone_guideline: document.getElementById('tone_guideline').value.trim(),
            output_format: state.format.slice(),
            examples_good: document.getElementById('examples_good').value.trim(),
            examples_bad: document.getElementById('examples_bad').value.trim()
        };

        const glossary = state.glossary
            .filter(x => x.src || x.dst)
            .map(x => ({
                src: x.src,
                dst: x.dst,
                note: x.note || '',
                scope: x.scope || '*',
                enabled: x.enabled ?? 1
            }));

        const replacements = state.replacements
            .filter(x => x.from || x.to)
            .map(x => ({
                from: x.from,
                to: x.to,
                match: x.match || 'contains',
                scope: x.scope || '*',
                severity: x.severity || 'warn',
                priority: x.priority ?? 100,
                enabled: x.enabled ?? 1
            }));

        const forbidden = state.forbidden
            .filter(x => x.term)
            .map(x => ({
                term: x.term,
                match: x.match || 'contains',
                scope: x.scope || '*',
                severity: x.severity || 'warn',
                message: x.message || '',
                enabled: x.enabled ?? 1
            }));

        const required = state.required
            .filter(x => x.term)
            .map(x => ({
                term: x.term,
                match: x.match || 'contains',
                scope: x.scope || '*',
                severity: x.severity || 'warn',
                hint: x.hint || '',
                enabled: x.enabled ?? 1
            }));

        const output = {
            mode: document.getElementById('output_mode').value,
            template_id: document.getElementById('template_id').value.trim(),
            content_type: document.getElementById('content_type').value.trim(),
            description_join: document.getElementById('description_join').value,
            escape_text_nodes: Number(document.getElementById('escape_text_nodes').value),
            template_html: document.getElementById('template_html').value
        };

        return {
            output,
            style,
            glossary,
            replacements,
            forbidden,
            required
        };
    }

    /**
     * output_format 컬럼 저장용 병합 포맷을 생성한다.
     * - sections: 출력 섹션 배열
     * - output: 출력 모드/템플릿 관련 상세 설정
     *
     * @returns {{sections:string[],output:{mode:string,template_id:string,content_type:string,description_join:string,escape_text_nodes:number}}}
     */
    function collectOutputFormatMerged() {
        return {
            sections: state.format.slice(),
            output: {
                mode: document.getElementById('output_mode').value,
                template_id: document.getElementById('template_id').value.trim(),
                content_type: document.getElementById('content_type').value.trim(),
                description_join: document.getElementById('description_join').value,
                escape_text_nodes: Number(document.getElementById('escape_text_nodes').value)
            }
        };
    }

    /**
     * rules_json 프리뷰와 저장 payload 프리뷰를 갱신한다.
     * 상단 룰북 코드 뱃지(uiCode)도 현재 입력값으로 동기화한다.
     *
     * @returns {void}
     */
    function renderPreviews() {
        const rulesJson = collectRulesJson();
        const code = document.getElementById('code').value.trim();
        const kind = document.getElementById('kind').value;
        const category = document.getElementById('category').value;
        const name = document.getElementById('name').value.trim();
        const description = document.getElementById('description').value.trim();
        const release_note = document.getElementById('release_note').value.trim();

        document.getElementById('jsonPreview').textContent = JSON.stringify(rulesJson, null, 2);

        const payload = {
            code,
            kind,
            category,
            name,
            description,
            schema_help: document.getElementById('schema_help').value,
            release_note,
            output_format: collectOutputFormatMerged(),
            rules_json: rulesJson
        };
        document.getElementById('payloadPreview').textContent = JSON.stringify(payload, null, 2);

        document.getElementById('uiCode').textContent = code || '-';
    }

    /**
     * 현재 화면 상태를 스냅샷(JSON 문자열)으로 직렬화한다.
     * "변경사항 되돌리기" 시 복원 기준값으로 사용한다.
     *
     * @returns {string} 직렬화된 스냅샷 JSON
     */
    function snapshotNow() {
        const snap = {
            code: document.getElementById('code').value,
            kind: document.getElementById('kind').value,
            category: document.getElementById('category').value,
            name: document.getElementById('name').value,
            description: document.getElementById('description').value,
            schema_help: document.getElementById('schema_help').value,
            tone_guideline: document.getElementById('tone_guideline').value,
            output_format: state.format.slice(),
            examples_good: document.getElementById('examples_good').value,
            examples_bad: document.getElementById('examples_bad').value,
            glossary: JSON.parse(JSON.stringify(state.glossary)),
            replacements: JSON.parse(JSON.stringify(state.replacements)),
            forbidden: JSON.parse(JSON.stringify(state.forbidden)),
            required: JSON.parse(JSON.stringify(state.required)),
            output_mode: document.getElementById('output_mode').value,
            template_id: document.getElementById('template_id').value,
            content_type: document.getElementById('content_type').value,
            description_join: document.getElementById('description_join').value,
            escape_text_nodes: document.getElementById('escape_text_nodes').value,
            template_html: document.getElementById('template_html').value,
            release_note: document.getElementById('release_note').value
        };
        return JSON.stringify(snap);
    }

    /**
     * 페이지의 모든 이벤트를 초기 바인딩한다.
     * - 탭 전환
     * - 입력 변경 감지(dirty + preview)
     * - 섹션/규칙 행 추가/초기화
     * - 되돌리기/저장 동작
     *
     * @returns {void}
     */
    function initEvents() {
        document.getElementById('btnCopyApiRulebookUrl').addEventListener('click', async () => {
            const input = document.getElementById('apiRulebookUrl');
            const message = document.getElementById('apiCopyMessage');
            const text = input.value;

            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(text);
                } else {
                    input.focus();
                    input.select();
                    document.execCommand('copy');
                }

                message.textContent = '복사됨';
                message.style.display = 'inline';
                setTimeout(() => { message.style.display = 'none'; }, 1500);
            } catch (e) {
                message.textContent = '복사 실패';
                message.style.display = 'inline';
            }
        });

        document.querySelectorAll('.tab').forEach(btn => {
            btn.addEventListener('click', () => {
                const key = btn.getAttribute('data-tab');
                document.querySelectorAll('.tab').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                document.querySelectorAll('.pane').forEach(p => p.classList.remove('active'));
                document.getElementById('pane-' + key).classList.add('active');
                renderPreviews();
            });
        });

        ['code', 'kind', 'category', 'name', 'description', 'schema_help', 'tone_guideline', 'examples_good', 'examples_bad', 'release_note', 'output_mode', 'template_id', 'content_type', 'description_join', 'escape_text_nodes', 'template_html'].forEach(id => {
            document.getElementById(id).addEventListener('input', () => {
                setDirty(true);
                renderPreviews();
            });
            document.getElementById(id).addEventListener('change', () => {
                setDirty(true);
                renderPreviews();
            });
        });

        document.getElementById('btnAddFormat').addEventListener('click', () => {
            const v = document.getElementById('formatInput').value.trim();
            if (!v) return;
            state.format.push(v);
            document.getElementById('formatInput').value = '';
            setDirty(true);
            renderFormat();
            renderGlossary();
            renderReplacements();
            renderForbidden();
            renderRequired();
            renderPreviews();
        });

        document.getElementById('btnClearFormat').addEventListener('click', () => {
            state.format = [];
            setDirty(true);
            renderFormat();
            renderGlossary();
            renderReplacements();
            renderForbidden();
            renderRequired();
            renderPreviews();
        });

        document.getElementById('btnAddGlossary').addEventListener('click', () => {
            state.glossary.push({
                _id: uid(),
                src: '',
                dst: '',
                note: '',
                scope: '*',
                enabled: 1
            });
            setDirty(true);
            renderGlossary();
            renderPreviews();
        });

        document.getElementById('btnAddReplacement').addEventListener('click', () => {
            state.replacements.push({
                _id: uid(),
                from: '',
                to: '',
                match: 'contains',
                scope: 'product_description',
                severity: 'warn',
                priority: 100,
                enabled: 1
            });
            setDirty(true);
            renderReplacements();
            renderPreviews();
        });

        document.getElementById('btnAddForbidden').addEventListener('click', () => {
            state.forbidden.push({
                _id: uid(),
                term: '',
                match: 'contains',
                scope: 'product_description',
                severity: 'warn',
                message: '',
                enabled: 1
            });
            setDirty(true);
            renderForbidden();
            renderPreviews();
        });

        document.getElementById('btnAddRequired').addEventListener('click', () => {
            state.required.push({
                _id: uid(),
                term: '',
                match: 'contains',
                scope: 'product_description',
                severity: 'warn',
                hint: '',
                enabled: 1
            });
            setDirty(true);
            renderRequired();
            renderPreviews();
        });

        document.getElementById('btnReset').addEventListener('click', () => {
            if (!state.initialSnapshot) return;
            const s = JSON.parse(state.initialSnapshot);

            document.getElementById('code').value = s.code;
            document.getElementById('kind').value = s.kind;
            document.getElementById('category').value = s.category;
            document.getElementById('name').value = s.name;
            document.getElementById('description').value = s.description;
            document.getElementById('schema_help').value = s.schema_help ?? '';

            document.getElementById('tone_guideline').value = s.tone_guideline;
            state.format = s.output_format || [];
            document.getElementById('examples_good').value = s.examples_good;
            document.getElementById('examples_bad').value = s.examples_bad;

            state.glossary = s.glossary || [];
            state.replacements = s.replacements || [];
            state.forbidden = s.forbidden || [];
            state.required = s.required || [];
            document.getElementById('output_mode').value = s.output_mode ?? 'html_template';
            document.getElementById('template_id').value = s.template_id ?? 'new_goods2_wrap_v1';
            document.getElementById('content_type').value = s.content_type ?? 'text/html';
            document.getElementById('description_join').value = s.description_join ?? s.maker_comment_join ?? 'lines_to_br';
            document.getElementById('escape_text_nodes').value = s.escape_text_nodes ?? '1';
            document.getElementById('template_html').value = s.template_html ?? '';

            document.getElementById('release_note').value = s.release_note;

            renderFormat();
            renderGlossary();
            renderReplacements();
            renderForbidden();
            renderRequired();

            setDirty(false);
            renderPreviews();
        });

        document.getElementById('btnPublish').addEventListener('click', async () => {
            const releaseNote = document.getElementById('release_note').value.trim();
            if (!releaseNote) {
                alert('Release note를 입력해주세요.');
                return;
            }

            const rulesJson = collectRulesJson();
            const outputFormatMerged = collectOutputFormatMerged();
            const payload = {
                idx: rulebookIdx || undefined,
                code: document.getElementById('code').value.trim(),
                kind: document.getElementById('kind').value,
                category: document.getElementById('category').value,
                name: document.getElementById('name').value.trim(),
                description: document.getElementById('description').value.trim(),
                schema_help: document.getElementById('schema_help').value,
                release_note: releaseNote,
                tone_guideline: document.getElementById('tone_guideline').value.trim(),
                examples_good: document.getElementById('examples_good').value.trim(),
                examples_bad: document.getElementById('examples_bad').value.trim(),
                output_format: outputFormatMerged,
                glossary_json: rulesJson.glossary,
                replacements_json: rulesJson.replacements,
                forbidden_json: rulesJson.forbidden,
                required_json: rulesJson.required,
                template_html: document.getElementById('template_html').value,
                rules_json: rulesJson
            };

            try {
                const form = new FormData();
                Object.entries(payload).forEach(([key, value]) => {
                    if (value === undefined || value === null) return;
                    if (typeof value === 'object') {
                        form.append(key, JSON.stringify(value));
                    } else {
                        form.append(key, String(value));
                    }
                });

                const response = await fetch('/admin/ai/rulebook/save', {
                    method: 'POST',
                    body: form
                });

                const responseText = await response.text();
                let result = null;
                try {
                    result = responseText ? JSON.parse(responseText) : null;
                } catch (e) {
                    result = null;
                }

                if (!result) {
                    throw new Error(responseText || '서버 응답(JSON)을 파싱하지 못했습니다.');
                }

                if (!response.ok || !result.success) {
                    throw new Error(result.message || '저장 중 오류가 발생했습니다.');
                }

                alert(result.message || '저장 완료');
                setDirty(false);
                state.initialSnapshot = snapshotNow();
                renderPreviews();
            } catch (error) {
                alert(error.message || '저장 중 오류가 발생했습니다.');
            }
        });
    }

    /**
     * 초기 상태(state)를 기본값/DB값으로 채운다.
     * - output_format, glossary_json, replacements_json, forbidden_json, required_json은 DB값 우선 사용
     * - 나머지 규칙은 샘플 기본값으로 초기화
     *
     * @returns {void}
     */
    function seedDefaults() {
        const outputFormatData = parseOutputFormat(rulebookOutputFormatRaw);
        state.format = outputFormatData.sections.length ? outputFormatData.sections : ['템플릿 순서대로 출력'];
        const glossaryFromDb = parseGlossary(rulebookGlossaryRaw);
        state.glossary = glossaryFromDb.length ? glossaryFromDb : [{
            _id: uid(),
            src: 'ローター',
            dst: '로터',
            note: '고정 표기',
            scope: '*',
            enabled: 1
        }];
        const replacementsFromDb = parseReplacements(rulebookReplacementsRaw);
        state.replacements = replacementsFromDb.length ? replacementsFromDb : [{
            _id: uid(),
            from: '국내',
            to: '일본',
            match: 'exact',
            scope: 'product_description',
            severity: 'error',
            priority: 10,
            enabled: 1
        }];
        const forbiddenFromDb = parseForbidden(rulebookForbiddenRaw);
        state.forbidden = forbiddenFromDb.length ? forbiddenFromDb : [{
            _id: uid(),
            term: '100% 동일',
            match: 'contains',
            scope: 'product_description',
            severity: 'error',
            message: '과장 표현 금지',
            enabled: 1
        }];
        const requiredFromDb = parseRequired(rulebookRequiredRaw);
        state.required = requiredFromDb.length ? requiredFromDb : [{
            _id: uid(),
            term: '주의사항',
            match: 'contains',
            scope: 'product_description',
            severity: 'warn',
            hint: '주의사항 섹션을 포함하세요',
            enabled: 1
        }];

        const templateHtmlFromTextarea = document.getElementById('template_html') ? document.getElementById('template_html').value : '';
        const templateHtmlFromColumn = (rulebookTemplateHtmlRaw ?? '').toString();

        state.output = {
            mode: outputFormatData.output.mode || 'html_template',
            template_id: outputFormatData.output.template_id || 'new_goods2_wrap_v1',
            content_type: outputFormatData.output.content_type || 'text/html',
            description_join: outputFormatData.output.description_join || 'lines_to_br',
            escape_text_nodes: Number(outputFormatData.output.escape_text_nodes ?? 1) === 0 ? 0 : 1,
            // template_html은 전용 컬럼 값을 최우선으로 사용한다.
            // output_format 내부 template_html은 과거 축약/샘플값이 남아있을 수 있어 fallback으로만 사용.
            template_html: templateHtmlFromColumn || outputFormatData.output.template_html || templateHtmlFromTextarea
        };

        document.getElementById('output_mode').value = state.output.mode;
        document.getElementById('template_id').value = state.output.template_id;
        document.getElementById('content_type').value = state.output.content_type;
        document.getElementById('description_join').value = state.output.description_join;
        document.getElementById('escape_text_nodes').value = String(state.output.escape_text_nodes);
        document.getElementById('template_html').value = state.output.template_html;
    }

    /**
     * 페이지 부트스트랩 진입점.
     * 초기 상태 구성 -> 각 UI 렌더 -> 이벤트 바인딩 -> 프리뷰/스냅샷 생성 순서로 실행한다.
     *
     * @returns {void}
     */
    function boot() {
        seedDefaults();
        state.output.template_html = document.getElementById('template_html') ? document.getElementById('template_html').value : '';
        renderFormat();
        renderGlossary();
        renderReplacements();
        renderForbidden();
        renderRequired();
        initEvents();
        renderPreviews();
        setDirty(false);
        state.initialSnapshot = snapshotNow();
    }

    boot();
</script>