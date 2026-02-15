<style>
    :root {
        --border: #e5e7eb;
        --bg: #f9fafb;
        --text: #111827;
        --muted: #6b7280;
        --primary: #2563eb;
        --danger: #dc2626;
    }

    * {
        box-sizing: border-box;
    }

    .rulebook-wrap {
        max-width: 1500px;
        margin: 0 auto;
        padding: 20px;

        .topbar {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            box-sizing: border-box;
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
            padding: 16px;
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
            border-radius: 10px;
            font-size: 13px;
            outline: none;
            background: #fff;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .row {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border: 1px solid var(--border);
            border-radius: 999px;
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
            padding: 12px;
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
        }

        th,
        td {
            border-top: 1px solid var(--border);
            padding: 10px 8px;
            vertical-align: top;
        }

        th {
            text-align: left;
            font-size: 12px;
            color: var(--muted);
            background: var(--bg);
            border-top: none;
        }

        td input[type="text"],
        td select,
        td textarea {
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 13px;
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
    }
</style>

<div id="contents_head">
    <h1>AI 룰북 설정</h1>
    <h3>관리자 설정 페이지(프롬프트 라이브러리)</h3>
</div>

<div id="contents_body">
	<div id="contents_body_wrap">

        <div class="rulebook-wrap">
            <div class="topbar">
                <div>
                    <h1>상품 상세페이지 </h1>
                    <p class="sub"> — 저장 시 버전이 자동 증가하고 히스토리에 이전 버전이 보관되는 구조</p>
                </div>
                <div class="row">
                    <span class="pill">룰북 코드: <strong id="uiCode">product_description</strong></span>
                    <span class="pill">현재 버전: <strong id="uiVersionNo">1</strong></span>
                    <span class="pill">현재 배포코드: <strong id="uiVersionCode">-</strong></span>
                </div>
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
                            <input id="code" type="text" value="product_description" placeholder="예: product_description" />

                            <label for="kind">Kind</label>
                            <input id="kind" type="text" value="content" placeholder="예: content" />

                            <label for="category">Category</label>
                            <input id="category" type="text" value="product_description" placeholder="예: product_description" />

                            <label for="name">이름</label>
                            <input id="name" type="text" value="상품 상세페이지 기본 룰북" placeholder="관리용 이름" />

                            <label for="description">설명</label>
                            <input id="description" type="text" placeholder="예: 일본 원문 번역/표기 통일 및 톤 고정" />

                            <div class="full hr"></div>

                        </div>
                    </div>

                    <div class="tabs" role="tablist" aria-label="룰북 탭">
                        <button class="tab active" data-tab="style" type="button">Style (문체/톤)</button>
                        <button class="tab" data-tab="glossary" type="button">Glossary (용어집)</button>
                        <button class="tab" data-tab="replacements" type="button">Replacements (치환 규칙)</button>
                        <button class="tab" data-tab="forbidden" type="button">Forbidden (금지 표현)</button>
                        <button class="tab" data-tab="required" type="button">Required (필수 포함)</button>
                        <button class="tab" data-tab="output" type="button">Output (출력/템플릿)</button>
                        <button class="tab" data-tab="preview" type="button">미리보기</button>
                    </div>

                    <div class="tabpanes">
                        <section class="pane active" id="pane-style">
                            <div class="form">
                                <label for="tone_guideline">톤/말투 가이드</label>
                                <textarea id="tone_guideline" placeholder="예: 브랜드 소개형, 담백. 과장/단정 금지. 일본 출처는 일본이라고 표기."></textarea>

                                <label>출력 포맷(섹션)</label>
                                <div>
                                    <div id="formatList"></div>
                                    <div class="row" style="margin-top:8px;">
                                        <input id="formatInput" type="text" placeholder="예: 한줄요약" />
                                        <button class="btn" type="button" id="btnAddFormat">+ 섹션 추가</button>
                                        <button class="btn" type="button" id="btnClearFormat">초기화</button>
                                    </div>
                                    <p class="help">섹션은 순서대로 사용됩니다. 예) 한줄요약 / 제품 특징 / 사용·관리 팁 / 주의사항</p>
                                </div>

                                <label for="examples_good">Good 예시</label>
                                <textarea id="examples_good" placeholder="좋은 예시를 적어두면 AI가 톤을 더 잘 맞춥니다(선택)."></textarea>

                                <label for="examples_bad">Bad 예시</label>
                                <textarea id="examples_bad" placeholder="피해야 할 문장 예시(선택)."></textarea>
                            </div>
                        </section>

                        <section class="pane" id="pane-glossary">
                            <div class="row" style="justify-content:space-between; align-items:center;">
                                <div class="muted">용어/표기 고정(원문 → 표준표기)</div>
                                <button class="btn" type="button" id="btnAddGlossary">+ 행 추가</button>
                            </div>
                            <div style="overflow:auto; margin-top:10px;">
                                <table>
                                    <thead>
                                        <tr>
                                            <th style="width:22%;">원문(src)</th>
                                            <th style="width:22%;">표준표기(dst)</th>
                                            <th style="width:24%;">비고(note)</th>
                                            <th style="width:14%;">scope</th>
                                            <th style="width:10%;">enabled</th>
                                            <th style="width:8%;">삭제</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyGlossary"></tbody>
                                </table>
                            </div>
                            <p class="help">scope는 "*" 또는 "product_description" 처럼 채널명을 넣을 수 있습니다.</p>
                        </section>

                        <section class="pane" id="pane-replacements">
                            <div class="row" style="justify-content:space-between; align-items:center;">
                                <div class="muted">표현 치환/정규화 (from → to)</div>
                                <button class="btn" type="button" id="btnAddReplacement">+ 행 추가</button>
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
                                <div class="muted">금칙어/금지패턴</div>
                                <button class="btn" type="button" id="btnAddForbidden">+ 행 추가</button>
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
                                <div class="muted">필수 포함(누락 방지)</div>
                                <button class="btn" type="button" id="btnAddRequired">+ 행 추가</button>
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
                                    <option value="html_template" selected>HTML 템플릿</option>
                                </select>

                                <label for="template_id">템플릿 ID</label>
                                <input id="template_id" type="text" value="new_goods2_wrap_v1" placeholder="예: new_goods2_wrap_v1" />

                                <label for="content_type">Content-Type</label>
                                <input id="content_type" type="text" value="text/html" placeholder="예: text/html 또는 text/plain" />

                                <label for="maker_comment_join">메이커 코멘트 줄바꿈</label>
                                <select id="maker_comment_join">
                                    <option value="lines_to_br" selected>줄바꿈을 &lt;br&gt;로 변환</option>
                                    <option value="plain_text">그냥 텍스트</option>
                                </select>

                                <label for="escape_text_nodes">HTML Escape</label>
                                <select id="escape_text_nodes">
                                    <option value="1" selected>텍스트는 escape 처리</option>
                                    <option value="0">escape 하지 않음(비추천)</option>
                                </select>

                                <div class="full hr"></div>

                                <div class="full muted">
                                    HTML 템플릿 모드에서는 아래 템플릿에 내용을 채워서 출력합니다. 템플릿은 룰북 버전과 함께 저장됩니다.
                                </div>

                                <label for="template_html">템플릿 HTML</label>
                                <textarea id="template_html" style="min-height:260px;" placeholder="여기에 템플릿 HTML을 넣어주세요.">
<div class="new-goods2-wrap">
	<div class="g2-name-en">{{g2_name_en}}</div>
	<div class="g2-name">{{g2_name_ko}}</div>

	<div class="g2-explanation">
		<p class="highlight">{{highlight}}</p>

		<div class="maker-comment">
			<p class="maker-comment-title">[메이커 코멘트]</p>
			{{maker_comment_html}}
		</div>
	</div>

	<div class="g2-point">
		<ul class="g2-point-title-ul"><div class="g2-point-title">POINT</div></ul>
		<ul class="g2-point-box">
			{{points_li}}
		</ul>
	</div>

	<div class="g2-spec">
		<ul class="g2-spec-title-ul"><div class="g2-spec-title">SPEC</div></ul>
		<ul>
			<li class="brand"><label>브랜드 :</label>{{spec_brand}}</li>
			<li class="type"><label>유형 :</label>{{spec_type}}</li>
			<li class="color"><label>색상 :</label>{{spec_color}}</li>
			<li class="material"><label>소재 :</label>{{spec_material}}</li>
			<li class="weight"><label>중량 :</label>{{spec_weight}}</li>
			<li class="size"><label>사이즈 :</label>{{spec_size}}</li>
			<li class="package"><label>패키지 :</label>{{spec_package}}</li>
		</ul>
	</div>

	<div class="g2-component">
		<ul class="g2-component-title-ul"><div class="g2-component-title">부속품</div></ul>
		<ul class="g2-component-content">{{component}}</ul>
	</div>
</div>
                                </textarea>

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
                    <div class="bd">
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
            maker_comment_join: 'lines_to_br',
            escape_text_nodes: 1,
            template_html: ''
        },
        dirty: false,
        initialSnapshot: null
    };

    function setDirty(v) {
        state.dirty = v;
        const badge = document.getElementById('uiDirty');
        badge.textContent = v ? '변경 있음' : '변경 없음';
        badge.className = 'badge' + (v ? '' : ' ok');
    }

    function escapeHtml(s) {
        return (s ?? '').toString()
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", "&#039;");
    }

    function uid() {
        return Math.random().toString(16).slice(2) + Date.now().toString(16);
    }

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
                renderPreviews();
            });
        });
    }

    function makeToggle(checked) {
        return `<select class="inp-enabled">
        <option value="1" ${checked ? 'selected' : ''}>ON</option>
        <option value="0" ${!checked ? 'selected' : ''}>OFF</option>
      </select>`;
    }

    function makeScope(value) {
        return `<input type="text" class="inp-scope" value="${escapeHtml(value ?? '*')}" placeholder="* 또는 채널명" />`;
    }

    function makeSeverity(value) {
        const v = value ?? 'warn';
        return `<select class="inp-severity">
        <option value="info" ${v==='info'?'selected':''}>info</option>
        <option value="warn" ${v==='warn'?'selected':''}>warn</option>
        <option value="error" ${v==='error'?'selected':''}>error</option>
      </select>`;
    }

    function makeMatch(value) {
        const v = value ?? 'contains';
        return `<select class="inp-match">
        <option value="exact" ${v==='exact'?'selected':''}>exact</option>
        <option value="contains" ${v==='contains'?'selected':''}>contains</option>
        <option value="regex" ${v==='regex'?'selected':''}>regex</option>
      </select>`;
    }

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
            maker_comment_join: document.getElementById('maker_comment_join').value,
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
            release_note,
            rules_json: rulesJson
        };
        document.getElementById('payloadPreview').textContent = JSON.stringify(payload, null, 2);

        document.getElementById('uiCode').textContent = code || '-';
    }

    function snapshotNow() {
        const snap = {
            code: document.getElementById('code').value,
            kind: document.getElementById('kind').value,
            category: document.getElementById('category').value,
            name: document.getElementById('name').value,
            description: document.getElementById('description').value,
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
            maker_comment_join: document.getElementById('maker_comment_join').value,
            escape_text_nodes: document.getElementById('escape_text_nodes').value,
            template_html: document.getElementById('template_html').value,
            release_note: document.getElementById('release_note').value
        };
        return JSON.stringify(snap);
    }

    function initEvents() {
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

        ['code', 'kind', 'category', 'name', 'description', 'tone_guideline', 'examples_good', 'examples_bad', 'release_note', 'output_mode', 'template_id', 'content_type', 'maker_comment_join', 'escape_text_nodes', 'template_html'].forEach(id => {
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
            renderPreviews();
        });

        document.getElementById('btnClearFormat').addEventListener('click', () => {
            state.format = [];
            setDirty(true);
            renderFormat();
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
            document.getElementById('maker_comment_join').value = s.maker_comment_join ?? 'lines_to_br';
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
            const payload = {
                code: document.getElementById('code').value.trim(),
                kind: document.getElementById('kind').value,
                category: document.getElementById('category').value,
                name: document.getElementById('name').value.trim(),
                description: document.getElementById('description').value.trim(),
                release_note: releaseNote,
                rules_json: collectRulesJson()
            };

            console.log('publish payload', payload);

            alert(
                '데모 페이지입니다.\n\n' +
                '여기서 서버로 POST하면 됩니다.\n' +
                '- ai_rulebook 최신을 ai_rulebook_history로 이동\n' +
                '- ai_rulebook 업데이트(version_no+1, version_code 새로)\n\n' +
                'payload는 콘솔(console)에 출력했습니다.'
            );

            setDirty(false);
            state.initialSnapshot = snapshotNow();
            renderPreviews();
        });
    }

    function seedDefaults() {
        state.format = ['한줄요약', '제품 특징', '사용·관리 팁', '주의사항'];
        state.glossary = [{
            _id: uid(),
            src: 'ローター',
            dst: '로터',
            note: '고정 표기',
            scope: '*',
            enabled: 1
        }];
        state.replacements = [{
            _id: uid(),
            from: '국내',
            to: '일본',
            match: 'exact',
            scope: 'product_description',
            severity: 'error',
            priority: 10,
            enabled: 1
        }];
        state.forbidden = [{
            _id: uid(),
            term: '100% 동일',
            match: 'contains',
            scope: 'product_description',
            severity: 'error',
            message: '과장 표현 금지',
            enabled: 1
        }];
        state.required = [{
            _id: uid(),
            term: '주의사항',
            match: 'contains',
            scope: 'product_description',
            severity: 'warn',
            hint: '주의사항 섹션을 포함하세요',
            enabled: 1
        }];
        state.output = {
            mode: 'html_template',
            template_id: 'new_goods2_wrap_v1',
            content_type: 'text/html',
            maker_comment_join: 'lines_to_br',
            escape_text_nodes: 1,
            template_html: document.getElementById('template_html') ? document.getElementById('template_html').value : ''
        };
    }

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