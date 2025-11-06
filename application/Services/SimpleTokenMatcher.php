<?php
declare(strict_types=1);

namespace App\Services;

class SimpleTokenMatcher
{

    //불용어
    private $stop = ['색상랜덤','색상 랜덤','신상','최저가'];

    //치환
    private $synonyms = [
        '리보' => 'rebo',
    ];

    // Site별 치환 규칙
    private $siteTransforms = [
        'byedam' => [
            '업코' => 'UPKO',
            '새티스파이어' => 'SATISFYER',
            '잘로' => 'ZALO',
            '리보' => 'LIBO',
            '딜도' => 'DILDO',
            "프리미엄" => "PREMIUM",
            "세트" => "SET",
    "리얼"     => "REAL",
    "모델"     => "MODEL",
    "클래식"   => "CLASSIC",
    "컬러"     => "COLOR",
    "크리스탈" => "CRYSTAL",
    "시크릿"   => "SECRET",
    "퍼스트"   => "FIRST",
    "노티"     => "NAUGHTY",
    "볼드"     => "BOLD",
    "베이비"   => "BABY",
    "바디"     => "BODY",
    "키스"     => "KISS",
    "파워"     => "POWER",
    "프로"     => "PRO",
    "맥스"     => "MAX",
    "미니"     => "MINI",
    "플렉스"   => "FLEX",
    "에어"     => "AIR",
    "라이트"   => "LIGHT",
    "블랙"     => "BLACK",
    "화이트"   => "WHITE",
    "핑크"     => "PINK",
    "레드"     => "RED",
    "블루"     => "BLUE",
    "골드"     => "GOLD",
    "실버"     => "SILVER",
    "스트롱"   => "STRONG",
    "슈퍼"     => "SUPER",
    "울트라"   => "ULTRA",
    "내추럴"   => "NATURAL",
    "스킨"     => "SKIN",
    "터치"     => "TOUCH",
    "필"       => "FEEL",
    "러브"     => "LOVE",
    "핫"       => "HOT",
    "쿨"       => "COOL",
    "펀"       => "FUN",
    "해피"     => "HAPPY",
    "퀸"       => "QUEEN",
        ],
        // 다른 사이트 규칙도 추가 가능
        'mobe' => [
            // mobe용 치환 규칙
        ]
    ];

    /**
     * Site별 문자열 치환 적용
     * 
     * @param string $text 원본 텍스트
     * @param string|null $site 사이트 키 (byedam, mobe 등)
     * @return string 치환된 텍스트
     */
    private function applySiteTransform(string $text, ?string $site): string
    {
        if ($site === null || !isset($this->siteTransforms[$site])) {
            return $text;
        }

        $transforms = $this->siteTransforms[$site];
        
        // 각 치환 규칙을 적용
        foreach ($transforms as $from => $to) {
            $text = str_replace($from, $to, $text);
        }

        return $text;
    }

    /**
     * 문자열 전처리 & 토큰화
     */
    public function tokens(string $s): array
    {
        $s = mb_strtolower($s, 'UTF-8');

        // 괄호 기호만 제거(내용은 보존)
        $s = preg_replace('/[\[\]\(\)]/u', ' ', $s);
        if ($s === null) $s = '';

        // === 여기 추가 ===
        // 한글+숫자, 영문+숫자, 한글+영문 등을 분리
        $s = preg_replace('/([가-힣]+)([0-9]+)/u', '$1 $2', $s);
        $s = preg_replace('/([a-z]+)([0-9]+)/u', '$1 $2', $s);
        $s = preg_replace('/([가-힣]+)([a-z]+)/u', '$1 $2', $s);
        $s = preg_replace('/([a-z]+)([가-힣]+)/u', '$1 $2', $s);
        // ================

        // 한글/영문/숫자/공백만 남김
        $s = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $s);
        if ($s === null) $s = '';

        // 공백 정리
        $s = preg_replace('/\s+/u', ' ', trim($s));
        if ($s === null) $s = '';

        // 공백 분리 → 토큰
        $tokens = $s === '' ? [] : explode(' ', $s);

        // 불용어 제거
        $tokens = array_values(array_filter($tokens, function ($t) {
            return !in_array($t, $this->stop, true);
        }));

        // === Synonym 치환 ===
        $tokens = array_map(function ($t) {
            return $this->synonyms[$t] ?? $t;
        }, $tokens);

        // 중복 제거 + 정렬
        $tokens = array_values(array_unique($tokens));
        sort($tokens, SORT_STRING);

        return $tokens;
    }


    /**
     * DB2 역색인 생성
     * @param array $db2Rows 각 행: ['idx'=>..., 'name'=>...]
     * @return array ['index'=>token=>[idx...], 'tokensById'=>idx=>tokens[]]
     */
    public function buildInvertedIndex(array $db2Rows): array
    {
        $index = [];
        $tokensById = [];

        foreach ($db2Rows as $r) {
            if (!isset($r['idx']) || !isset($r['name'])) {
                continue;
            }
            $idx  = (string)$r['idx'];
            $toks = $this->tokens((string)$r['name']);

            $tokensById[$idx] = $toks;

            foreach ($toks as $t) {
                if (!isset($index[$t])) {
                    $index[$t] = [];
                }
                $index[$t][] = $idx;
            }
        }

        foreach ($index as $t => $ids) {
            $index[$t] = array_values(array_unique($ids));
        }

        return ['index' => $index, 'tokensById' => $tokensById];
    }

    /** |A∩B| / |A| */
    private function coverage(array $a, array $b): float
    {
        if (!$a) return 0.0;
        $inter = count(array_intersect($a, $b));
        return $inter / count($a);
    }

    /** |A∩B| / |A∪B| */
    private function jaccard(array $a, array $b): float
    {
        if (!$a && !$b) return 0.0;
        $inter = count(array_intersect($a, $b));
        $union = count(array_unique(array_merge($a, $b)));
        return $union ? ($inter / $union) : 0.0;
    }

    /**
     * DB1 한 건 매칭
     * @param string $db1Name
     * @param array  $db2Index buildInvertedIndex() 결과
     * @param int    $maxCandidates
     * @return array|null ['idx'=>..., 'score'=>float, 't2'=>string[]]
     */
    public function matchOne(string $db1Name, array $db2Index, int $maxCandidates = 200): ?array
    {
        $index = isset($db2Index['index']) ? $db2Index['index'] : [];
        $tokensById = isset($db2Index['tokensById']) ? $db2Index['tokensById'] : [];

        $t1 = $this->tokens($db1Name);
        if (!$t1) return null;

        // 희소 토큰부터 후보 수집
        $tokenFreq = [];
        foreach ($t1 as $t) {
            $tokenFreq[$t] = isset($index[$t]) ? count($index[$t]) : PHP_INT_MAX;
        }
        asort($tokenFreq);

        $candidatesSet = [];
        foreach (array_keys($tokenFreq) as $t) {
            if (!isset($index[$t])) continue;
            foreach ($index[$t] as $idx) {
                $candidatesSet[$idx] = true;
            }
            if (count($candidatesSet) >= $maxCandidates) break;
        }
        $candidates = array_keys($candidatesSet);
        if (!$candidates) return null;

        // 점수 계산
        $best = null;
        foreach ($candidates as $idx) {
            $t2 = isset($tokensById[$idx]) ? $tokensById[$idx] : [];
            $cov = $this->coverage($t1, $t2);
            $jac = $this->jaccard($t1, $t2);
            $score = $cov + 0.4 * $jac;

            if ($best === null || $score > $best['score']) {
                $best = ['idx' => $idx, 'score' => $score, 't2' => $t2];
            }
        }

        return $best;
    }

    /**
     * DB2에서 idx로 전체 row 찾기
     */
    private function getRowFromDb2($idx, array $db2Rows): ?array
    {
        foreach ($db2Rows as $row) {
            if (isset($row['idx']) && (string)$row['idx'] === (string)$idx) {
                return $row;
            }
        }
        return null;
    }

    /**
     * 여러 건 일괄 매칭
     * @param array $db1Rows 각 행: ['idx'=>..., 'name'=>...]
     * @param array $db2Rows 각 행: ['idx'=>..., 'name'=>...]
     * @param string|null $site 사이트 키 (byedam, mobe 등) - site별 치환 규칙 적용용
     */
    public function matchAll(array $db1Rows, array $db2Rows, ?string $site = null): array
    {
        $db2Index = $this->buildInvertedIndex($db2Rows);
        $out = [];

        foreach ($db1Rows as $row) {

            $db1Idx = isset($row['idx']) ? (string)$row['idx'] : null;
            $db1Name = isset($row['name']) ? (string)$row['name'] : null;
            $db1ImgSrc = isset($row['img_src']) ? (string)$row['img_src'] : null;
            $db1BrandName = isset($row['brand_name']) ? (string)$row['brand_name'] : null;

            // Site별 치환 규칙 적용
            $transformedDb1Name = $db1Name !== null ? $this->applySiteTransform($db1Name, $site) : null;

            $m = $transformedDb1Name !== null ? $this->matchOne($transformedDb1Name, $db2Index) : null;
            $matchedRow = $m && isset($m['idx']) ? $this->getRowFromDb2($m['idx'], $db2Rows) : null;

            $out[] = [
                'db1_idx' => $db1Idx,
                'db1_name' => $db1Name,
                'db1_name_transformed' => $transformedDb1Name, // 치환된 이름 추가
                'db1_img_src' => $db1ImgSrc,
                'db1_brand_name' => $db1BrandName,
                'prd_data' => $row,
                'match_data' => $matchedRow,
                'score' => $m && isset($m['score']) ? $m['score'] : 0.0,
            ];
        }

        return $out;
    }


    /**
     * DB1 문자열과 DB2 후보 전체를 비교해 점수 높은 순으로 반환
     *
     * @param string $db1Name
     * @param array $db2Rows 각 행: ['idx'=>..., 'name'=>...]
     * @param int $limit 상위 몇 개까지 가져올지
     * @return array [['idx'=>..., 'name'=>..., 'score'=>float], ...]
     */
    public function matchCandidates(string $db1Name, array $db2Rows, int $limit = 10, ?string $site = null): array
    {
        // 빈 문자열이나 null 체크
        if (empty($db1Name) || trim($db1Name) === '') {
            return [];
        }

        // Site별 치환 규칙 적용
        $db1Name = $this->applySiteTransform($db1Name, $site);

        $db2Index = $this->buildInvertedIndex($db2Rows);

        $t1 = $this->tokens($db1Name);
        if (!$t1) return [];

        $results = [];

        foreach ($db2Rows as $row) {
            if (!isset($row['idx'], $row['name'])) continue;

            $t2 = $this->tokens((string)$row['name']);
            if (!$t2) continue;

            $cov = $this->coverage($t1, $t2);
            $jac = $this->jaccard($t1, $t2);
            $score = $cov + 0.4 * $jac;

            $results[] = [
                'idx'   => (string)$row['idx'],
                'name'  => (string)$row['name'],
                'score' => $score,
                'match_data' => $row,
            ];
        }

        // 점수 높은 순으로 정렬, 동일 점수일 때는 가격 낮은 순으로 정렬
        usort($results, function ($a, $b) {
            // 점수 비교 (높은 순)
            $scoreCompare = $b['score'] <=> $a['score'];
            
            // 점수가 같으면 가격 비교 (낮은 순)
            if ($scoreCompare === 0) {
                $priceA = (float)($a['match_data']['price'] ?? PHP_FLOAT_MAX);
                $priceB = (float)($b['match_data']['price'] ?? PHP_FLOAT_MAX);
                return $priceA <=> $priceB;
            }
            
            return $scoreCompare;
        });

        // limit 적용
        return array_slice($results, 0, $limit);
    }



}
