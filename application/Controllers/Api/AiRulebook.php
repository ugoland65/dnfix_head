<?php
namespace App\Controllers\Api;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Models\AiRulebookModel;

class AiRulebook extends BaseClass
{
    /**
     * AI Rulebook 조회 API (code 기준)
     *
     * - route param: /api2/ai/rulebook/{code}
     * - query param fallback: /api2/ai/rulebook?code=xxx
     *
     * @param Request $request
     * @param string|null $code
     * @return \App\Core\Response
     */
    public function aiRulebookApi(Request $request, ?string $code = null)
    {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');

        try{
            $code = trim((string)($code ?? $request->input('code', '')));
            if ($code === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'code 값이 필요합니다.',
                ], 400);
            }

            $query = AiRulebookModel::where('code', $code)->first();
            if (empty($query)) {
                return response()->json([
                    'success' => false,
                    'message' => '룰북을 찾을 수 없습니다.',
                ], 404);
            }

            $row = is_array($query) ? $query : $query->toArray();

            $outputFormat = $this->decodeJsonField($row['output_format'] ?? null, []);
            $glossary = $this->decodeJsonField($row['glossary_json'] ?? null, []);
            $replacements = $this->decodeJsonField($row['replacements_json'] ?? null, []);
            $forbidden = $this->decodeJsonField($row['forbidden_json'] ?? null, []);
            $required = $this->decodeJsonField($row['required_json'] ?? null, []);
            $sections = [];
            $output = [];
            if (is_array($outputFormat)) {
                if ($this->isListArray($outputFormat)) {
                    $sections = $outputFormat;
                } else {
                    $sections = is_array($outputFormat['sections'] ?? null)
                        ? $outputFormat['sections']
                        : (is_array($outputFormat['output_format'] ?? null) ? $outputFormat['output_format'] : []);

                    $output = is_array($outputFormat['output'] ?? null)
                        ? $outputFormat['output']
                        : $outputFormat;
                }
            }

            // template_html은 컬럼 우선
            $templateHtml = (string)($row['template_html'] ?? '');
            if ($templateHtml === '') {
                $templateHtml = (string)($output['template_html'] ?? '');
            }

            $defaultValidation = [
                'strict_template' => true,
                'allow_only_variable_replacement' => true,
                'verify_before_output' => true,
            ];
            $rawValidation = $this->decodeJsonField($output['validation'] ?? null, []);
            $validation = is_array($rawValidation)
                ? array_merge($defaultValidation, $rawValidation)
                : $defaultValidation;

            $validation['strict_template'] = true;
            $validation['allow_only_variable_replacement'] = true;
            $validation['verify_before_output'] = true;

            $validation['template_html_is_output_itself'] = true;
            $validation['output_html_must_match_template_html_char_level'] = true;
            $validation['keep_structure_when_content_empty'] = true;

            $validation['copy_template_then_replace_variables'] = true;

            $validation['keep_tag_names'] = true;
            $validation['keep_class_names'] = true;
            $validation['keep_attributes'] = true;
            $validation['keep_tag_order'] = true;
            $validation['keep_wrapper'] = true;
            $validation['keep_comments'] = true;
            $validation['keep_indentation'] = true;
            $validation['keep_line_breaks'] = true;

            $validation['forbid_new_html'] = true;
            $validation['forbid_new_tag'] = true;
            $validation['forbid_new_class'] = true;
            $validation['forbid_new_attribute'] = true;
            $validation['forbid_delete_tag'] = true;
            $validation['forbid_change_tag'] = true;

            $validation['rules'] = [
                'template_html은 참고용이 아니라 출력 결과 그 자체이다.',
                'template_html을 새로 작성하지 말고 그대로 복사한 후 {{변수}}만 치환한다.',
                '출력 HTML은 template_html과 문자 단위까지 동일해야 한다.',
                '모든 태그명(div, ul, li, p, span 등)은 template_html과 동일해야 한다.',
                '모든 class명은 template_html과 동일해야 한다.',
                '모든 attribute(class, id, style 등)는 template_html과 동일해야 한다.',
                '태그의 순서와 계층 구조를 변경하지 않는다.',
                'HTML 주석은 유지한다.',
                '들여쓰기와 개행은 유지한다.',
                '{{변수}} 앞뒤에 새로운 HTML을 추가하지 않는다.',
                'template_html에 존재하지 않는 태그, class, id, style, attribute를 생성하지 않는다.',
                '내용이 없어도 HTML 구조는 그대로 유지한다.',
                '검증에 실패하면 template_html 기준으로 다시 생성한다.',
                'template_html을 수정하는 것은 오답이며, {{변수}}만 수정하는 것이 정답이다.'
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => (int)($row['idx'] ?? 0),
                    'code' => (string)($row['code'] ?? ''),
                    'kind' => (string)($row['kind'] ?? ''),
                    'category' => (string)($row['category'] ?? ''),
                    'name' => (string)($row['name'] ?? ''),
                    'description' => (string)($row['description'] ?? ''),
                    // LLM이 먼저 읽기 쉽도록 상위에 별도 노출

                    'version_no' => (int)($row['version_no'] ?? 0),
                    'version_code' => (string)($row['version_code'] ?? ''),
                    'checksum' => (string)($row['checksum'] ?? ''),
                    'updated_at' => (string)($row['updated_at'] ?? ''),

                    // ChatGPT/LLM이 바로 쓰기 좋은 정규화된 규칙 구조
                    'rulebook' => [
                        // rulebook 내부에서도 동일 값 유지(호환)
                        'schema_help' => (string)($row['schema_help'] ?? ''),
                        'style' => [
                            'tone_guideline' => (string)($row['tone_guideline'] ?? ''),
                            'output_format' => $sections,
                            'examples_good' => (string)($row['examples_good'] ?? ''),
                            'examples_bad' => (string)($row['examples_bad'] ?? ''),
                        ],
                        'output' => [
                            'mode' => (string)($output['mode'] ?? 'html_template'),
                            'template_id' => (string)($output['template_id'] ?? ''),
                            'content_type' => (string)($output['content_type'] ?? 'text/html'),
                            'description_join' => (string)($output['description_join'] ?? ($output['maker_comment_join'] ?? 'lines_to_br')),
                            'escape_text_nodes' => ((int)($output['escape_text_nodes'] ?? 1)) === 0 ? 0 : 1,
                            'template_html' => $templateHtml,
                            'validation' => $validation,
                        ],
                        // 레거시/외부 소비자 호환용 alias
                        'rules_json' => [
                            'output' => [
                                'validation' => $validation,
                            ],
                        ],
                        'glossary' => is_array($glossary) ? $glossary : [],
                        'replacements' => is_array($replacements) ? $replacements : [],
                        'forbidden' => is_array($forbidden) ? $forbidden : [],
                        'required' => is_array($required) ? $required : [],
                    ]
                ],
                'server_time' => date('Y-m-d\TH:i:sP'),
            ]);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * JSON 컬럼 값을 배열로 디코딩
     *
     * @param mixed $value
     * @param mixed $default
     * @return mixed
     */
    private function decodeJsonField($value, $default = [])
    {
        if (is_array($value)) {
            return $value;
        }
        if (!is_string($value) || trim($value) === '') {
            return $default;
        }
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $default;
    }

    /**
     * 배열이 순차 인덱스 리스트인지 확인
     *
     * @param array $arr
     * @return bool
     */
    private function isListArray(array $arr): bool
    {
        if (function_exists('array_is_list')) {
            return array_is_list($arr);
        }
        return array_keys($arr) === range(0, count($arr) - 1);
    }
}