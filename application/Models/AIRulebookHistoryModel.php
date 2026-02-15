<?php
namespace App\Models;

use App\Core\BaseModel;

class AIRulebookHistoryModel extends BaseModel
{
    protected $table = 'ai_rulebook_history';
    protected $primaryKey = 'idx';

    protected $fillable = [
        'rulebook_idx',
        'code',
        'kind',
        'category',
        'name',
        'description',
        'version_no',
        'version_code',
        'checksum',

        'tone_guideline',
        'examples_good',
        'examples_bad',
        'release_note',

        'output_format',
        'glossary_json', // Glossary(고정 표기) 목록 JSON 배열
        'replacements_json', // Replacements(치환 규칙) 목록 JSON 배열
        'forbidden_json', // Forbidden(금지 규칙) 목록 JSON 배열
        'required_json', // Required(필수 포함 규칙) 목록 JSON 배열

        'template_html', // HTML 템플릿 내용

        'rules_json',
        'schema_help', // 스키마 도움말 내용
        
        'archived_by',
        'archived_at',
    ];
}
