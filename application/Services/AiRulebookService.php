<?php

namespace App\Services;

use Exception;
use App\Models\AiRulebookModel;
use App\Models\AIRulebookHistoryModel;

class AiRulebookService
{

    /**
     * AI 규칙북 상세 조회
     *
     * @param int $idx
     * @return array
     */
    public function getRulebookDetail($idx)
    {
        $query = AiRulebookModel::find($idx);
        if (!$query) {
            throw new Exception("AI 규칙북을 찾을 수 없습니다.");
        }

        $rulebook = $query->toArray();

        // output_format은 뷰에서 직접 사용하기 쉽도록 배열/객체 형태로 정규화
        if (is_string($rulebook['output_format'] ?? null)) {
            $decoded = json_decode($rulebook['output_format'], true);
            $rulebook['output_format'] = is_array($decoded) ? $decoded : [];
        } else if (!is_array($rulebook['output_format'] ?? null)) {
            $rulebook['output_format'] = [];
        }

        return $rulebook;
    }

    /**
     * AI 규칙북 저장(새 버전 배포)
     * - 트랜잭션은 컨트롤러에서 처리한다는 전제 하에 서비스에서는 수행하지 않음
     *
     * @param array $payload
     * @return array
     * @throws Exception
     */
    public function saveRulebook($payload)
    {
        $idx = !empty($payload['idx']) ? (int)$payload['idx'] : null;
        $code = trim((string)($payload['code'] ?? ''));

        if (empty($idx) && empty($code)) {
            throw new Exception('idx 또는 code가 필요합니다.');
        }

        // 1) 현재 최신 룰북 조회 (idx 우선, 없으면 code)
        if (!empty($idx)) {
            $query = AiRulebookModel::find($idx);
        } else {
            $query = AiRulebookModel::where('code', $code)->first();
        }

        if (empty($query)) {
            throw new Exception('룰북을 찾을 수 없습니다.');
        }

        $current = is_array($query) ? $query : $query->toArray();
        $currentIdx = (int)($current['idx'] ?? 0);
        if (empty($currentIdx)) {
            throw new Exception('유효하지 않은 룰북입니다.');
        }

        // 2) 입력값 정규화
        $nextVersionNo = ((int)($current['version_no'] ?? 0)) + 1;
        $releaseNote = (string)($payload['release_note'] ?? '');
        $updatedBy = !empty($payload['updated_by']) ? (int)$payload['updated_by'] : null;

        $rulesJson = $this->normalizeJsonValue($payload['rules_json'] ?? null, 'object_or_array', []);
        if (!is_array($rulesJson)) {
            $rulesJson = [];
        }

        $style = is_array($rulesJson['style'] ?? null) ? $rulesJson['style'] : [];
        $output = is_array($rulesJson['output'] ?? null) ? $rulesJson['output'] : [];

        $schemaHelp = (string)($payload['schema_help'] ?? ($rulesJson['schema_help'] ?? ($current['schema_help'] ?? '')));
        $schemaHelp = html_entity_decode($schemaHelp, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $toneGuideline = (string)($payload['tone_guideline'] ?? ($style['tone_guideline'] ?? ''));
        $examplesGood = (string)($payload['examples_good'] ?? ($style['examples_good'] ?? ''));
        $examplesBad = (string)($payload['examples_bad'] ?? ($style['examples_bad'] ?? ''));

        $outputFormatMerged = $this->normalizeOutputFormat(
            $payload['output_format'] ?? null,
            $style['output_format'] ?? []
        );
        $currentOutputFormat = $this->normalizeOutputFormat($current['output_format'] ?? null, []);

        // 개별 JSON 컬럼은 payload > rules_json 순으로 사용
        $glossaryJson = $this->normalizeJsonValue($payload['glossary_json'] ?? ($rulesJson['glossary'] ?? null), 'array', []);
        $replacementsJson = $this->normalizeJsonValue($payload['replacements_json'] ?? ($rulesJson['replacements'] ?? null), 'array', []);
        $forbiddenJson = $this->normalizeJsonValue($payload['forbidden_json'] ?? ($rulesJson['forbidden'] ?? null), 'array', []);
        $requiredJson = $this->normalizeJsonValue($payload['required_json'] ?? ($rulesJson['required'] ?? null), 'array', []);

        $templateHtml = (string)($payload['template_html'] ?? ($output['template_html'] ?? ''));
        $templateHtml = html_entity_decode($templateHtml, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // output_format 누락/파싱 실패 시 rules_json 기반으로 보정
        if (empty($outputFormatMerged['sections']) && !empty($style['output_format']) && is_array($style['output_format'])) {
            $outputFormatMerged['sections'] = array_values(array_filter(array_map(function($v) {
                return trim((string)$v);
            }, $style['output_format']), function($v) {
                return $v !== '';
            }));
        }

        // 섹션이 비어 들어오면 기존 값을 보존한다.
        if (empty($outputFormatMerged['sections']) && !empty($currentOutputFormat['sections'])) {
            $outputFormatMerged['sections'] = $currentOutputFormat['sections'];
        }

        if (!empty($output) && is_array($output)) {
            $outputFormatMerged['output']['mode'] = (string)($output['mode'] ?? $outputFormatMerged['output']['mode']);
            $outputFormatMerged['output']['template_id'] = (string)($output['template_id'] ?? $outputFormatMerged['output']['template_id']);
            $outputFormatMerged['output']['content_type'] = (string)($output['content_type'] ?? $outputFormatMerged['output']['content_type']);
            $outputFormatMerged['output']['description_join'] = (string)($output['description_join'] ?? ($output['maker_comment_join'] ?? $outputFormatMerged['output']['description_join']));
            $outputFormatMerged['output']['escape_text_nodes'] = ((int)($output['escape_text_nodes'] ?? $outputFormatMerged['output']['escape_text_nodes'])) === 0 ? 0 : 1;
        }

        // 호환을 위해 구 키도 같이 저장
        $outputFormatMerged['output']['maker_comment_join'] = $outputFormatMerged['output']['description_join'];
        // template_html은 전용 컬럼에만 저장한다.
        unset($outputFormatMerged['output']['template_html']);

        $checksum = hash('sha256', json_encode($rulesJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $rulebookCode = (string)($current['code'] ?? $code);
        $versionCode = $this->makeUniqueVersionCode($rulebookCode, $nextVersionNo);
        $historyVersionCode = $this->makeHistoryVersionCode(
            $rulebookCode,
            (int)($current['version_no'] ?? 1),
            (string)($current['version_code'] ?? ''),
            $currentIdx
        );

        // 3-1) 현재 최신 row를 history로 복사(스냅샷)
        AIRulebookHistoryModel::create([
            'rulebook_idx' => $currentIdx,
            'code' => $current['code'] ?? '',
            'kind' => $current['kind'] ?? '',
            'category' => $current['category'] ?? '',
            'name' => $current['name'] ?? '',
            'description' => $current['description'] ?? '',
            'version_no' => (int)($current['version_no'] ?? 1),
            'version_code' => $historyVersionCode,
            'checksum' => $current['checksum'] ?? null,
            'release_note' => $current['release_note'] ?? null,
            'tone_guideline' => $current['tone_guideline'] ?? null,
            'examples_good' => $current['examples_good'] ?? null,
            'examples_bad' => $current['examples_bad'] ?? null,
            'output_format' => $current['output_format'] ?? null,
            'glossary_json' => $current['glossary_json'] ?? null,
            'replacements_json' => $current['replacements_json'] ?? null,
            'forbidden_json' => $current['forbidden_json'] ?? null,
            'required_json' => $current['required_json'] ?? null,
            'template_html' => $current['template_html'] ?? null,
            'rules_json' => $current['rules_json'] ?? null,
            'schema_help' => $current['schema_help'] ?? null,
            'archived_by' => $updatedBy,
            'archived_at' => date('Y-m-d H:i:s'),
        ]);

        // 3-2) 최신 테이블 업데이트(=새 버전 배포)
        $updateData = [
            'code' => $code ?: ($current['code'] ?? ''),
            'kind' => (string)($payload['kind'] ?? ($current['kind'] ?? '')),
            'category' => (string)($payload['category'] ?? ($current['category'] ?? '')),
            'name' => (string)($payload['name'] ?? ($current['name'] ?? '')),
            'description' => (string)($payload['description'] ?? ($current['description'] ?? '')),
            'version_no' => $nextVersionNo,
            'version_code' => $versionCode,
            'checksum' => $checksum,
            'release_note' => $releaseNote,
            'schema_help' => $schemaHelp,
            'updated_by' => $updatedBy,
            'tone_guideline' => $toneGuideline,
            'examples_good' => $examplesGood,
            'examples_bad' => $examplesBad,
            'output_format' => json_encode($outputFormatMerged, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'glossary_json' => json_encode($glossaryJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'replacements_json' => json_encode($replacementsJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'forbidden_json' => json_encode($forbiddenJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'required_json' => json_encode($requiredJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'template_html' => $templateHtml,
            'rules_json' => json_encode($rulesJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        AiRulebookModel::update(['idx' => $currentIdx], $updateData);

        return [
            'success' => true,
            'idx' => $currentIdx,
            'code' => $updateData['code'],
            'version_no' => $nextVersionNo,
            'version_code' => $versionCode,
            'checksum' => $checksum,
        ];
    }

    /**
     * output_format 병합 포맷을 정규화한다.
     *
     * @param mixed $raw
     * @param mixed $fallbackSections
     * @return array
     */
    private function normalizeOutputFormat($raw, $fallbackSections = [])
    {
        $decoded = $this->normalizeJsonValue($raw, 'object_or_array', []);
        $sections = [];
        $output = [];

        if (is_array($decoded)) {
            // 레거시 배열 포맷: ["섹션1", "섹션2", ...]
            if ($this->isListArray($decoded)) {
                $sections = $decoded;
            } else {
                // 병합 포맷: {sections:[...], output:{...}} 또는 평면 객체
                if (isset($decoded['sections']) && is_array($decoded['sections'])) {
                    $sections = $decoded['sections'];
                } else if (isset($decoded['output_format']) && is_array($decoded['output_format'])) {
                    $sections = $decoded['output_format'];
                }

                $output = isset($decoded['output']) && is_array($decoded['output'])
                    ? $decoded['output']
                    : $decoded;
            }
        }

        if (empty($sections) && is_array($fallbackSections)) {
            $sections = $fallbackSections;
        }

        $sections = array_values(array_filter(array_map(function($v) {
            return trim((string)$v);
        }, $sections), function($v) {
            return $v !== '';
        }));

        return [
            'sections' => $sections,
            'output' => [
                'mode' => trim((string)($output['mode'] ?? 'html_template')) ?: 'html_template',
                'template_id' => trim((string)($output['template_id'] ?? 'new_goods2_wrap_v1')) ?: 'new_goods2_wrap_v1',
                'content_type' => trim((string)($output['content_type'] ?? 'text/html')) ?: 'text/html',
                // 구 키(maker_comment_join)도 읽어 신규 키(description_join)로 정규화
                'description_join' => trim((string)($output['description_join'] ?? ($output['maker_comment_join'] ?? 'lines_to_br'))) ?: 'lines_to_br',
                'escape_text_nodes' => ((int)($output['escape_text_nodes'] ?? 1)) === 0 ? 0 : 1,
                // 호환을 위해 구 키도 유지
                'maker_comment_join' => trim((string)($output['maker_comment_join'] ?? ($output['description_join'] ?? 'lines_to_br'))) ?: 'lines_to_br',
            ],
        ];
    }

    /**
     * payload로 넘어온 JSON 값을 안전하게 배열/객체로 정규화한다.
     *
     * @param mixed $raw
     * @param string $type 'array' | 'object_or_array'
     * @param mixed $default
     * @return mixed
     */
    private function normalizeJsonValue($raw, string $type, $default)
    {
        if ($raw === null || $raw === '') {
            return $default;
        }

        if (is_array($raw)) {
            return $raw;
        }

        if (is_string($raw)) {
            $trimmed = trim($raw);
            if ($trimmed === '') {
                return $default;
            }

            $decoded = json_decode($trimmed, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if ($type === 'array' && !is_array($decoded)) {
                    return $default;
                }
                return $decoded;
            }

            // HTML 엔티티(&quot; 등)로 들어온 JSON 문자열도 한 번 더 복원 파싱
            $decodedHtml = json_decode(html_entity_decode($trimmed, ENT_QUOTES | ENT_HTML5, 'UTF-8'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if ($type === 'array' && !is_array($decodedHtml)) {
                    return $default;
                }
                return $decodedHtml;
            }

            return $default;
        }

        return $default;
    }

    /**
     * 배열이 0..N 순차 인덱스 리스트인지 확인한다.
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

    /**
     * 버전 코드 생성
     *
     * @param string $code
     * @param int $versionNo
     * @return string
     */
    private function makeVersionCode(string $code, int $versionNo): string
    {
        $safeCode = preg_replace('/[^a-zA-Z0-9._-]/', '_', $code);
        $ts = date('YmdHis');
        $rand = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        return "RB-{$safeCode}-{$ts}-" . str_pad((string)$versionNo, 4, '0', STR_PAD_LEFT) . "-{$rand}";
    }

    /**
     * 최신 테이블에 저장할 version_code를 유일하게 생성한다.
     *
     * @param string $code
     * @param int $versionNo
     * @return string
     * @throws Exception
     */
    private function makeUniqueVersionCode(string $code, int $versionNo): string
    {
        for ($i = 0; $i < 10; $i++) {
            $candidate = $this->makeVersionCode($code, $versionNo);
            $existsInCurrent = AiRulebookModel::where('version_code', $candidate)->exists();
            $existsInHistory = AIRulebookHistoryModel::where('version_code', $candidate)->exists();
            if (!$existsInCurrent && !$existsInHistory) {
                return $candidate;
            }
            usleep(1000);
        }

        throw new Exception('version_code 생성에 실패했습니다. 잠시 후 다시 시도해주세요.');
    }

    /**
     * 히스토리로 복사할 version_code를 충돌 없이 준비한다.
     *
     * @param string $code
     * @param int $versionNo
     * @param string $currentVersionCode
     * @param int $currentIdx
     * @return string
     */
    private function makeHistoryVersionCode(string $code, int $versionNo, string $currentVersionCode, int $currentIdx): string
    {
        $trimmed = trim($currentVersionCode);
        if ($trimmed !== '' && !AIRulebookHistoryModel::where('version_code', $trimmed)->exists()) {
            return $trimmed;
        }

        $safeCode = preg_replace('/[^a-zA-Z0-9._-]/', '_', $code);
        if ($safeCode === '') {
            $safeCode = 'UNKNOWN';
        }

        return "HIS-{$safeCode}-" . str_pad((string)$versionNo, 4, '0', STR_PAD_LEFT) . "-{$currentIdx}-" . date('YmdHis');
    }
}
