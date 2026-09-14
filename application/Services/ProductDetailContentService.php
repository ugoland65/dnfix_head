<?php

namespace App\Services;

use Exception;
use Throwable;
use App\Models\ProductModel;
use App\Models\ProductCategoryMappingModel;
use App\Models\ProductDetailContentModel;
use App\Models\ProductDetailContentDeployLogModel;

class ProductDetailContentService
{
    private const THROUGH_FEATURE_CODE = 'ONAHOLE_FEATURE_THROUGH';

    /**
     * 상품 컨텐츠 관리 페이지 데이터
     */
    public function getPageData(int $prdPk): array
    {
        $product = $this->loadProductContext($prdPk);
        $row = ProductDetailContentModel::where('prd_pk', $prdPk)->first();
        $content = $this->normalizeRow($row, $prdPk, $product);
        $recommended = $this->buildRecommendedSpecs($product);
        $hasSavedSpecs = $content['specs'] !== [];

        $content['specs_saved'] = $hasSavedSpecs;
        $content['specs_is_recommended'] = false;
        if (!$hasSavedSpecs && $recommended['specs'] !== []) {
            $content['specs'] = $recommended['specs'];
            $content['specs_is_recommended'] = true;
        }

        $godoContent = $this->loadGodoDeployStatus($product, $content);
        $canDeploy = !empty($godoContent['has_godo_code'])
            && (int)($content['deploy_version'] ?? 0) > 0
            && trim((string)($content['deploy_version_code'] ?? '')) !== ''
            && empty($godoContent['matches_local']);

        return [
            'prd_pk' => $prdPk,
            'product_name' => (string)($product['CD_NAME'] ?? ''),
            'content' => $content,
            'godo_content' => $godoContent,
            'can_deploy' => $canDeploy,
            'can_recommend_specs' => !empty($recommended['supported']),
        ];
    }

    /**
     * 상품 기본정보로 스펙 추천값을 만든다.
     */
    public function getRecommendedSpecs(int $prdPk): array
    {
        return $this->buildRecommendedSpecs($this->loadProductContext($prdPk));
    }

    /**
     * 상품 상세페이지 컨텐츠 저장
     */
    public function save(int $prdPk, array $data, array $admin = []): array
    {
        $product = $this->requireProduct($prdPk);
        $current = ProductDetailContentModel::where('prd_pk', $prdPk)->first();
        $currentData = $this->rowToArray($current);
        $nextDeployVersion = ((int)($currentData['deploy_version'] ?? 0)) + 1;
        $deployVersionCode = $this->makeDeployVersionCode($prdPk, $nextDeployVersion);

        $now = date('Y-m-d H:i:s');
        $saved = ProductDetailContentModel::updateOrCreate(
            ['prd_pk' => $prdPk],
            [
                'original_name' => $this->clip((string)($data['original_name'] ?? ''), 255),
                'korean_name' => $this->clip((string)($data['korean_name'] ?? ''), 255),
                'title' => $this->clip((string)($data['title'] ?? ''), 500),
                'maker_comment' => (string)($data['maker_comment'] ?? ''),
                'md_comment' => (string)($data['md_comment'] ?? ''),
                'summary_points' => $this->normalizeSummaryPoints($data['summary_points'] ?? []),
                'specs' => $this->normalizeSpecs($data['specs'] ?? []),
                'deploy_version' => $nextDeployVersion,
                'deploy_version_code' => $deployVersionCode,
                'admin_idx' => (int)($admin['idx'] ?? 0) ?: null,
                'admin_name' => (string)($admin['name'] ?? '') ?: null,
                'updated_at' => $now,
            ]
        );

        return $this->normalizeRow($saved, $prdPk, $product);
    }

    /**
     * 현재 저장된 컨텐츠를 고도몰에 실배포하고, 당시 데이터를 배포로그에 백업한다.
     *
     * @return array{ok:bool,stage:string,error_code?:string,message:string,debug?:array,content?:array,godo_code?:int,deploy_version?:int,deploy_version_code?:string}
     */
    public function deployToGodo(int $prdPk, array $admin = []): array
    {
        $debug = [
            'prd_pk' => $prdPk,
            'godo_code' => '',
            'deploy_version' => 0,
            'deploy_version_code' => '',
            'godo_url' => '',
            'godo_http_code' => 0,
            'godo_raw' => '',
            'log_saved' => false,
            'log_error' => '',
        ];

        try {
            $product = $this->requireProduct($prdPk);
            $godoCode = $this->normalizeGodoCode($product['cd_godo_code'] ?? '');
            $debug['godo_code'] = $godoCode;
            if ($godoCode === '') {
                return $this->deployError('validate', 'NO_GODO_CODE', '고도몰 상품번호가 없습니다.', $debug);
            }

            $row = ProductDetailContentModel::where('prd_pk', $prdPk)->first();
            $content = $this->normalizeRow($row, $prdPk, $product);
            $debug['deploy_version'] = (int)($content['deploy_version'] ?? 0);
            $debug['deploy_version_code'] = trim((string)($content['deploy_version_code'] ?? ''));
            if ((int)($content['idx'] ?? 0) <= 0 || (int)($content['deploy_version'] ?? 0) <= 0) {
                return $this->deployError('validate', 'CONTENT_NOT_SAVED', '배포할 컨텐츠가 없습니다. 먼저 저장해 주세요.', $debug);
            }
            if ($debug['deploy_version_code'] === '') {
                return $this->deployError('validate', 'NO_VERSION_CODE', '배포코드가 없습니다. 다시 저장해 주세요.', $debug);
            }

            $payload = $this->buildGodoSyncData($content, $godoCode);
            $godoResult = (new GodoApiService())->deployPrdDetailContent(
                $godoCode,
                (int)$content['deploy_version'],
                (string)$content['deploy_version_code']
            );
            $debug['godo_url'] = (string)($godoResult['url'] ?? '');
            $debug['godo_http_code'] = (int)($godoResult['http_code'] ?? 0);
            $debug['godo_raw'] = $this->clip((string)($godoResult['raw'] ?? ''), 800);
            if (!empty($godoResult['response']['error_code'])) {
                $debug['godo_error_code'] = (string)$godoResult['response']['error_code'];
            }

            try {
                $this->writeDeployLog($content, $payload, $admin, $godoResult);
                $debug['log_saved'] = true;
            } catch (Throwable $e) {
                $debug['log_error'] = $e->getMessage();
                if (!empty($godoResult['success'])) {
                    return $this->deployError(
                        'deploy_log',
                        'DEPLOY_LOG_FAILED',
                        '고도몰 배포는 완료됐지만 배포로그 저장에 실패했습니다. ' . $e->getMessage(),
                        $debug
                    );
                }
            }

            if (empty($godoResult['success'])) {
                return $this->deployError(
                    'godo_api',
                    'GODO_API_FAILED',
                    (string)($godoResult['message'] ?? '고도몰 배포에 실패했습니다.'),
                    $debug
                );
            }

            return [
                'ok' => true,
                'stage' => 'done',
                'message' => (string)($godoResult['message'] ?? '고도몰에 배포했습니다.'),
                'content' => $content,
                'godo_code' => (int)$godoCode,
                'deploy_version' => (int)$content['deploy_version'],
                'deploy_version_code' => (string)$content['deploy_version_code'],
            ];
        } catch (Throwable $e) {
            $debug['exception'] = get_class($e);
            $debug['file'] = basename($e->getFile()) . ':' . $e->getLine();
            return $this->deployError('server', 'SERVER_ERROR', $e->getMessage(), $debug);
        }
    }

    /**
     * 고도몰이 인트라넷에서 받아 DB에 저장할 상품 컨텐츠 페이로드
     *
     * @param array{godo_code?:mixed,deploy_version?:mixed,deploy_version_code?:mixed} $criteria
     * @return array{ok:bool,status:int,error_code?:string,message:string,data?:array,current?:array}
     */
    public function getGodoSyncPayload(array $criteria): array
    {
        $godoCode = $this->normalizeGodoCode($criteria['godo_code'] ?? '');
        $deployVersion = (int)($criteria['deploy_version'] ?? 0);
        $rawDeployVersionCode = trim((string)($criteria['deploy_version_code'] ?? ''));
        $deployVersionCode = $this->normalizeDeployVersionCode($rawDeployVersionCode);

        if ($godoCode === '') {
            return $this->godoSyncError(400, 'INVALID_REQUEST', '고도몰 상품번호가 필요합니다.');
        }
        if ($deployVersion <= 0 || $rawDeployVersionCode === '') {
            return $this->godoSyncError(400, 'INVALID_REQUEST', '배포버전과 배포코드가 필요합니다.');
        }
        if ($deployVersionCode === '') {
            return $this->godoSyncError(400, 'INVALID_REQUEST', '배포코드 형식이 올바르지 않습니다.');
        }

        $products = ProductModel::query()
            ->select(['CD_IDX', 'CD_NAME', 'CD_NAME_OG', 'cd_godo_code'])
            ->where('cd_godo_code', '=', $godoCode)
            ->orderBy('CD_IDX', 'ASC')
            ->get()
            ->toArray();

        if ($products === []) {
            return $this->godoSyncError(404, 'PRODUCT_NOT_FOUND', '고도몰 상품번호에 해당하는 상품이 없습니다.');
        }
        if (count($products) > 1) {
            return $this->godoSyncError(409, 'DUPLICATE_GODO_CODE', '동일한 고도몰 상품번호가 여러 상품에 등록되어 있습니다.');
        }

        $product = is_array($products[0]) ? $products[0] : (array)$products[0];
        $prdPk = (int)($product['CD_IDX'] ?? 0);
        if ($prdPk <= 0) {
            return $this->godoSyncError(404, 'PRODUCT_NOT_FOUND', '고도몰 상품번호에 해당하는 상품이 없습니다.');
        }

        $row = ProductDetailContentModel::where('prd_pk', $prdPk)->first();
        if (empty($row)) {
            return $this->godoSyncError(404, 'CONTENT_NOT_FOUND', '상품 컨텐츠가 아직 저장되지 않았습니다.');
        }

        $content = $this->normalizeRow($row, $prdPk, $product);
        if ((int)($content['idx'] ?? 0) <= 0 || (int)($content['deploy_version'] ?? 0) <= 0) {
            return $this->godoSyncError(404, 'CONTENT_NOT_FOUND', '상품 컨텐츠가 아직 저장되지 않았습니다.');
        }

        $currentVersion = (int)$content['deploy_version'];
        $currentVersionCode = trim((string)$content['deploy_version_code']);
        $versionMatched = $currentVersion === $deployVersion
            && $currentVersionCode !== ''
            && hash_equals($currentVersionCode, $deployVersionCode);

        if (!$versionMatched) {
            return [
                'ok' => false,
                'status' => 409,
                'error_code' => 'VERSION_MISMATCH',
                'message' => '요청한 배포버전과 현재 저장된 배포버전이 다릅니다.',
                'current' => [
                    'godo_code' => (int)$godoCode,
                    'prd_pk' => $prdPk,
                    'deploy_version' => $currentVersion,
                    'deploy_version_code' => $currentVersionCode,
                ],
            ];
        }

        return [
            'ok' => true,
            'status' => 200,
            'message' => '상품 컨텐츠를 조회했습니다.',
            'data' => $this->buildGodoSyncData($content, $godoCode),
        ];
    }

    private function requireProduct(int $prdPk): array
    {
        if ($prdPk <= 0) {
            throw new Exception('상품 번호가 없습니다.');
        }
        $product = ProductModel::find($prdPk);
        if (empty($product)) {
            throw new Exception('상품을 찾을 수 없습니다.');
        }
        return is_array($product) ? $product : $product->toArray();
    }

    private function loadProductContext(int $prdPk): array
    {
        if ($prdPk <= 0) {
            throw new Exception('상품 번호가 없습니다.');
        }

        $productRow = ProductModel::query()
            ->from('COMPARISON_DB as A')
            ->leftJoin('BRAND_DB as B', 'B.BD_IDX', '=', 'A.CD_BRAND_IDX')
            ->where('A.CD_IDX', '=', $prdPk)
            ->select([
                'A.CD_IDX',
                'A.CD_NAME',
                'A.CD_NAME_OG',
                'A.CD_KIND_CODE',
                'A.CD_CATEGORY_CODE',
                'A.CD_BRAND_IDX',
                'A.CD_SIZE2',
                'A.cd_godo_code',
                'A.cd_spec',
                'A.cd_weight_fn',
                'B.BD_NAME',
                'B.BD_NAME_EN',
            ])
            ->first();

        $product = $productRow ? (is_array($productRow) ? $productRow : $productRow->toArray()) : [];
        if ($product === []) {
            throw new Exception('상품을 찾을 수 없습니다.');
        }

        $product['cd_spec'] = $this->decodeJsonObject($product['cd_spec'] ?? []);
        $product['cd_weight_fn'] = $this->decodeJsonObject($product['cd_weight_fn'] ?? []);
        $product['cd_sub_category_codes'] = $this->getSubCategoryCodes((int)($product['CD_IDX'] ?? 0));

        return $product;
    }

    /**
     * @return array{supported:bool,kind_code:string,message:string,specs:array<int,array{code:string,name:string,value:string}>}
     */
    private function buildRecommendedSpecs(array $product): array
    {
        $kindCode = trim((string)($product['CD_KIND_CODE'] ?? ''));
        if ($kindCode !== 'ONAHOLE') {
            return [
                'supported' => false,
                'kind_code' => $kindCode,
                'message' => '오나홀 상품만 추천값을 생성할 수 있습니다.',
                'specs' => [],
            ];
        }

        $spec = (isset($product['cd_spec']) && is_array($product['cd_spec'])) ? $product['cd_spec'] : [];
        $vendor = (isset($spec['vendor_size']) && is_array($spec['vendor_size'])) ? $spec['vendor_size'] : [];
        $measured = (isset($spec['measured_size']) && is_array($spec['measured_size'])) ? $spec['measured_size'] : [];
        $sizeSpecs = $this->buildOnaholeSizeSpecs($vendor, $measured, (string)($product['CD_SIZE2'] ?? ''));

        $specs = [
            $this->specItem('brand', '브랜드', $this->buildBrandValue($product)),
            $this->specItem('type', '유형', $this->buildOnaholeTypeValue($product)),
            $this->specItem('color', '색상', $this->specFieldValue($vendor, $measured, 'color')),
            $this->specItem('material', '소재', $this->specFieldValue($vendor, $measured, 'material')),
        ];
        foreach ($sizeSpecs as $sizeSpec) {
            $specs[] = $sizeSpec;
        }
        $specs[] = $this->specItem('weight', '중량', $this->buildWeightValue($product));

        return [
            'supported' => true,
            'kind_code' => $kindCode,
            'message' => '추천값을 생성했습니다.',
            'specs' => $specs,
        ];
    }

    private function buildBrandValue(array $product): string
    {
        $koreanName = $this->decodeText((string)($product['BD_NAME'] ?? ''));
        $englishName = $this->decodeText((string)($product['BD_NAME_EN'] ?? ''));
        if ($koreanName !== '' && $englishName !== '') {
            return $koreanName . ' (' . $englishName . ')';
        }
        return $koreanName !== '' ? $koreanName : $englishName;
    }

    private function buildOnaholeTypeValue(array $product): string
    {
        $selectedCodes = [];
        foreach ((isset($product['cd_sub_category_codes']) && is_array($product['cd_sub_category_codes'])) ? $product['cd_sub_category_codes'] : [] as $code) {
            $code = trim((string)$code);
            if ($code !== '') {
                $selectedCodes[$code] = true;
            }
        }

        $hasThrough = isset($selectedCodes[self::THROUGH_FEATURE_CODE]);
        $parts = [$hasThrough ? '관통 유형' : '비관통'];

        $configProduct = config('admin.product');
        $groups = $configProduct['sub_categories_by_kind']['ONAHOLE'] ?? [];
        if (!is_array($groups)) {
            return implode(' / ', $parts);
        }

        foreach ($groups as $group) {
            if (!is_array($group)) {
                continue;
            }
            $children = $group['children'] ?? [];
            if (!is_array($children)) {
                continue;
            }
            foreach ($children as $child) {
                if (!is_array($child)) {
                    continue;
                }
                $code = trim((string)($child['code'] ?? ''));
                $name = trim((string)($child['name'] ?? ''));
                if ($code === '' || $code === self::THROUGH_FEATURE_CODE || $name === '') {
                    continue;
                }
                if (isset($selectedCodes[$code])) {
                    $parts[] = $name;
                }
            }
        }

        return implode(' / ', $parts);
    }

    /**
     * @return array<int,array{code:string,name:string,value:string}>
     */
    private function buildOnaholeSizeSpecs(array $vendor, array $measured, string $legacyInnerLength): array
    {
        $length = $this->specFieldValue($vendor, $measured, 'length');
        $height = $this->specFieldValue($vendor, $measured, 'height');
        $vaginaLength = $this->specFieldValue($vendor, $measured, 'inner_length_vagina');
        if ($vaginaLength === '') {
            $vaginaLength = trim($legacyInnerLength);
        }
        $analLength = $this->specFieldValue($vendor, $measured, 'inner_length_anal');

        $outerParts = [];
        if ($length !== '') {
            $outerParts[] = '가로(W)' . $length;
        }
        if ($height !== '') {
            $outerParts[] = '세로 (H) ' . $height;
        }
        $outerSize = implode(' x ', $outerParts);

        $hasVagina = $vaginaLength !== '';
        $hasAnal = $analLength !== '';
        if ($hasVagina && $hasAnal) {
            return [
                $this->specItem('size', '사이즈', $outerSize),
                $this->specItem(
                    'inner_length',
                    '내부길이',
                    '내부길이(질) : ' . $this->withCm($vaginaLength) . ' / 내부길이(애널) : ' . $this->withCm($analLength)
                ),
            ];
        }

        $sizeValue = $outerSize;
        $singleInner = $hasVagina ? $vaginaLength : $analLength;
        if ($singleInner !== '') {
            $innerText = '내부길이 : ' . $this->withCm($singleInner);
            $sizeValue = $sizeValue !== '' ? ($sizeValue . ' / ' . $innerText) : $innerText;
        }

        return [
            $this->specItem('size', '사이즈', $sizeValue),
        ];
    }

    private function buildWeightValue(array $product): string
    {
        $weightFn = (isset($product['cd_weight_fn']) && is_array($product['cd_weight_fn'])) ? $product['cd_weight_fn'] : [];
        $weight = trim((string)($weightFn['1'] ?? ''));
        if ($weight === '') {
            return '';
        }
        if (preg_match('/g$/i', $weight)) {
            return $weight;
        }
        return $weight . 'g';
    }

    /**
     * @return array<int,string>
     */
    private function getSubCategoryCodes(int $productIdx): array
    {
        if ($productIdx <= 0) {
            return [];
        }

        $rows = ProductCategoryMappingModel::query()
            ->select(['category_code'])
            ->where('product_type', '=', 'prdDB')
            ->where('product_idx', '=', $productIdx)
            ->where('category_type', '=', 'sub')
            ->orderBy('display_order', 'ASC')
            ->orderBy('idx', 'ASC')
            ->get()
            ->toArray();

        return array_values(array_filter(array_map(static function ($row): string {
            return trim((string)($row['category_code'] ?? ''));
        }, $rows)));
    }

    /**
     * @return array{code:string,name:string,value:string}
     */
    private function specItem(string $code, string $name, string $value): array
    {
        return [
            'code' => $code,
            'name' => $name,
            'value' => $value,
        ];
    }

    private function specFieldValue(array $vendor, array $measured, string $key): string
    {
        $vendorValue = trim((string)($vendor[$key] ?? ''));
        if ($vendorValue !== '') {
            return $vendorValue;
        }
        return trim((string)($measured[$key] ?? ''));
    }

    private function withCm(string $value): string
    {
        $value = trim($value);
        if ($value === '' || preg_match('/cm$/i', $value)) {
            return $value;
        }
        return $value . 'cm';
    }

    private function decodeText(string $value): string
    {
        return trim(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    private function decodeJsonObject($raw): array
    {
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : [];
        }
        return is_array($raw) ? $raw : [];
    }

    private function normalizeRow($row, int $prdPk, array $product = []): array
    {
        $data = $this->rowToArray($row);
        $hasSavedRow = (int)($data['idx'] ?? 0) > 0;

        $originalName = (string)($data['original_name'] ?? '');
        $koreanName = (string)($data['korean_name'] ?? '');
        if (!$hasSavedRow) {
            if ($originalName === '') {
                $originalName = (string)($product['CD_NAME_OG'] ?? '');
            }
            if ($koreanName === '') {
                $koreanName = (string)($product['CD_NAME'] ?? '');
            }
        }

        return [
            'idx' => (int)($data['idx'] ?? 0),
            'prd_pk' => (int)($data['prd_pk'] ?? $prdPk),
            'original_name' => $originalName,
            'korean_name' => $koreanName,
            'title' => (string)($data['title'] ?? ''),
            'maker_comment' => (string)($data['maker_comment'] ?? ''),
            'md_comment' => (string)($data['md_comment'] ?? ''),
            'summary_points' => $this->normalizeSummaryPoints($data['summary_points'] ?? []),
            'specs' => $this->normalizeSpecs($data['specs'] ?? []),
            'deploy_version' => (int)($data['deploy_version'] ?? 0),
            'deploy_version_code' => trim((string)($data['deploy_version_code'] ?? '')),
            'admin_name' => (string)($data['admin_name'] ?? ''),
            'updated_at' => (string)($data['updated_at'] ?? ''),
        ];
    }

    private function normalizeSummaryPoints($raw): array
    {
        $rows = $this->decodeJsonList($raw);
        $points = [];
        foreach ($rows as $row) {
            if (is_string($row) || is_numeric($row)) {
                $text = trim((string)$row);
            } elseif (is_array($row)) {
                $text = trim((string)($row['text'] ?? $row['content'] ?? $row['point'] ?? ''));
            } else {
                continue;
            }
            if ($text === '') {
                continue;
            }
            $points[] = ['text' => $text];
        }
        return $points;
    }

    private function normalizeSpecs($raw): array
    {
        $rows = $this->decodeJsonList($raw);
        $specs = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $code = trim((string)($row['code'] ?? ''));
            $name = trim((string)($row['name'] ?? $row['label'] ?? ''));
            $value = trim((string)($row['value'] ?? $row['content'] ?? $row['text'] ?? ''));
            if ($value === '') {
                continue;
            }
            $specs[] = [
                'code' => $code,
                'name' => $name,
                'value' => $value,
            ];
        }
        return $specs;
    }

    private function decodeJsonList($raw): array
    {
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : [];
        }
        return is_array($raw) ? $raw : [];
    }

    private function rowToArray($row): array
    {
        if (empty($row)) {
            return [];
        }
        if (is_array($row)) {
            return $row;
        }
        if (is_object($row) && method_exists($row, 'toArray')) {
            return $row->toArray();
        }
        return (array)$row;
    }

    private function makeDeployVersionCode(int $prdPk, int $version): string
    {
        for ($i = 0; $i < 8; $i++) {
            $candidate = 'PDC-' . $prdPk
                . '-V' . str_pad((string)$version, 4, '0', STR_PAD_LEFT)
                . '-' . date('YmdHis')
                . '-' . strtoupper(bin2hex(random_bytes(3)));
            $exists = ProductDetailContentModel::where('deploy_version_code', $candidate)->exists();
            if (!$exists) {
                return $candidate;
            }
            usleep(1000);
        }

        throw new Exception('배포버전 코드 생성에 실패했습니다. 다시 저장해 주세요.');
    }

    private function clip(string $value, int $max): string
    {
        $value = trim($value);
        if ($value === '' || mb_strlen($value) <= $max) {
            return $value;
        }
        return mb_substr($value, 0, $max);
    }

    /**
     * @return array{
     *   godo_code:int,
     *   prd_pk:int,
     *   deploy_version:int,
     *   deploy_version_code:string,
     *   original_name:string,
     *   korean_name:string,
     *   title:string,
     *   maker_comment:string,
     *   md_comment:string,
     *   summary_points:array<int,array{sort:int,text:string}>,
     *   specs:array<int,array{sort:int,code:string,name:string,value:string}>,
     *   html:string,
     *   updated_at:string
     * }
     */
    private function buildGodoSyncData(array $content, string $godoCode): array
    {
        $summaryPoints = [];
        foreach ((isset($content['summary_points']) && is_array($content['summary_points'])) ? $content['summary_points'] : [] as $point) {
            $text = trim((string)($point['text'] ?? ''));
            if ($text === '') {
                continue;
            }
            $summaryPoints[] = [
                'sort' => count($summaryPoints) + 1,
                'text' => $text,
            ];
        }

        $specs = [];
        foreach ((isset($content['specs']) && is_array($content['specs'])) ? $content['specs'] : [] as $spec) {
            if (!is_array($spec)) {
                continue;
            }
            $value = trim((string)($spec['value'] ?? ''));
            if ($value === '') {
                continue;
            }
            $specs[] = [
                'sort' => count($specs) + 1,
                'code' => trim((string)($spec['code'] ?? '')),
                'name' => trim((string)($spec['name'] ?? '')),
                'value' => $value,
            ];
        }

        $payload = [
            'godo_code' => (int)$godoCode,
            'prd_pk' => (int)($content['prd_pk'] ?? 0),
            'deploy_version' => (int)($content['deploy_version'] ?? 0),
            'deploy_version_code' => trim((string)($content['deploy_version_code'] ?? '')),
            'original_name' => (string)($content['original_name'] ?? ''),
            'korean_name' => (string)($content['korean_name'] ?? ''),
            'title' => (string)($content['title'] ?? ''),
            'maker_comment' => (string)($content['maker_comment'] ?? ''),
            'md_comment' => (string)($content['md_comment'] ?? ''),
            'summary_points' => $summaryPoints,
            'specs' => $specs,
            'updated_at' => (string)($content['updated_at'] ?? ''),
        ];
        $payload['html'] = $this->buildDeployHtml($payload);

        return $payload;
    }

    private function buildDeployHtml(array $content): string
    {
        $versionCode = trim((string)($content['deploy_version_code'] ?? ''));
        $html = '<div class="new-goods2-wrap"'
            . ($versionCode !== '' ? ' data-pdc-version="' . $this->escapeHtml($versionCode) . '"' : '')
            . '>';

        if ($versionCode !== '') {
            $html .= '<div class="g2-pdc-version" data-pdc-version="' . $this->escapeHtml($versionCode) . '" hidden></div>';
        }

        $html .= '<div class="g2-name-en">' . $this->escapeHtml((string)($content['original_name'] ?? '')) . '</div>';
        $html .= '<div class="g2-name">' . $this->escapeHtml((string)($content['korean_name'] ?? '')) . '</div>';

        $explanation = '';
        $title = trim((string)($content['title'] ?? ''));
        $makerComment = trim((string)($content['maker_comment'] ?? ''));
        $mdComment = trim((string)($content['md_comment'] ?? ''));
        if ($title !== '') {
            $explanation .= '<p class="highlight">' . $this->escapeHtml($title) . '</p>';
        }
        if ($makerComment !== '') {
            $explanation .= '<div class="maker-comment">[메이커 코멘트]<br>' . $this->nl2br($makerComment) . '</div>';
        }
        if ($mdComment !== '') {
            $explanation .= '<div class="maker-comment">[MD 코멘트]<br>' . $this->nl2br($mdComment) . '</div>';
        }
        if ($explanation !== '') {
            $html .= '<div class="g2-explanation">' . $explanation . '</div>';
        }

        $points = (isset($content['summary_points']) && is_array($content['summary_points'])) ? $content['summary_points'] : [];
        if ($points !== []) {
            $html .= '<div class="g2-point">';
            $html .= '<ul class="g2-point-title-ul"><div class="g2-point-title">POINT</div></ul>';
            $html .= '<ul class="g2-point-box">';
            foreach ($points as $point) {
                $html .= '<li>' . $this->escapeHtml((string)($point['text'] ?? '')) . '</li>';
            }
            $html .= '</ul></div>';
        }

        $specs = (isset($content['specs']) && is_array($content['specs'])) ? $content['specs'] : [];
        if ($specs !== []) {
            $html .= '<div class="g2-spec">';
            $html .= '<ul class="g2-spec-title-ul"><div class="g2-spec-title">SPEC</div></ul>';
            $html .= '<ul>';
            foreach ($specs as $spec) {
                $specName = trim((string)($spec['name'] ?? ''));
                $html .= '<li>';
                if ($specName !== '') {
                    $html .= '<label>' . $this->escapeHtml($specName) . ' :</label>';
                }
                $html .= ' ' . $this->escapeHtml((string)($spec['value'] ?? ''));
                $html .= '</li>';
            }
            $html .= '</ul></div>';
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * 고도몰에 상품 컨텐츠가 등록됐는지, 배포버전이 무엇인지 확인한다.
     *
     * @return array{
     *   has_godo_code:bool,
     *   godo_code:string,
     *   checked:bool,
     *   found:bool,
     *   registered:bool,
     *   deploy_version:int,
     *   deploy_version_code:string,
     *   matches_local:bool,
     *   error:string
     * }
     */
    private function loadGodoDeployStatus(array $product, array $content): array
    {
        $status = [
            'has_godo_code' => false,
            'godo_code' => '',
            'checked' => false,
            'found' => false,
            'registered' => false,
            'deploy_version' => 0,
            'deploy_version_code' => '',
            'matches_local' => false,
            'error' => '',
        ];

        $godoCode = $this->normalizeGodoCode($product['cd_godo_code'] ?? '');
        if ($godoCode === '') {
            return $status;
        }

        $status['has_godo_code'] = true;
        $status['godo_code'] = $godoCode;

        try {
            $rows = (new GodoApiService())->getGodoGoodsInfoByGoodsNo($godoCode, ['DnfixContent']);
        } catch (Throwable $e) {
            $status['error'] = $e->getMessage();
            return $status;
        }

        $status['checked'] = true;
        $goods = $this->pickGodoGoodsRow(is_array($rows) ? $rows : [], $godoCode);
        if ($goods === []) {
            return $status;
        }

        $status['found'] = true;
        $deploy = $this->extractGodoDeployContent($goods);
        $status['registered'] = !empty($deploy['registered']);
        $status['deploy_version'] = (int)($deploy['deploy_version'] ?? 0);
        $status['deploy_version_code'] = trim((string)($deploy['deploy_version_code'] ?? ''));

        $localVersion = (int)($content['deploy_version'] ?? 0);
        $localCode = trim((string)($content['deploy_version_code'] ?? ''));
        $status['matches_local'] = $status['registered']
            && $localVersion > 0
            && $status['deploy_version'] === $localVersion
            && ($localCode === '' || $status['deploy_version_code'] === '' || hash_equals($localCode, $status['deploy_version_code']));

        return $status;
    }

    /**
     * @param array<int,mixed> $rows
     */
    private function pickGodoGoodsRow(array $rows, string $godoCode): array
    {
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            if (trim((string)($row['goodsNo'] ?? '')) === $godoCode) {
                return $row;
            }
        }
        $first = $rows[0] ?? null;
        return is_array($first) ? $first : [];
    }

    /**
     * @return array{registered:bool,deploy_version:int,deploy_version_code:string}
     */
    private function extractGodoDeployContent(array $goods): array
    {
        $nested = [];
        foreach (['dnfixContent', 'dnfix_content', 'DnfixContent', 'prd_detail_content'] as $key) {
            if (!array_key_exists($key, $goods)) {
                continue;
            }
            $raw = $goods[$key];
            if (is_array($raw)) {
                $nested = $raw;
                break;
            }
            if (is_string($raw) && trim($raw) !== '') {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $nested = $decoded;
                    break;
                }
            }
        }

        $source = $nested !== [] ? $nested : $goods;
        $version = (int)($source['deploy_version'] ?? $source['deployVersion'] ?? $source['version'] ?? 0);
        $code = $this->normalizeDeployVersionCode(
            $source['deploy_version_code'] ?? $source['deployVersionCode'] ?? $source['deploy_code'] ?? $source['version_code'] ?? ''
        );

        $html = trim((string)($source['html'] ?? $goods['goodsDescription'] ?? ''));
        if ($code === '' && $html !== '' && preg_match('/data-pdc-version="([^"]+)"/i', $html, $matches)) {
            $code = $this->normalizeDeployVersionCode($matches[1] ?? '');
        }
        if ($version <= 0 && $code !== '' && preg_match('/-V(\d{4})-/', $code, $matches)) {
            $version = (int)$matches[1];
        }

        return [
            'registered' => $version > 0 || $code !== '' || strpos($html, 'new-goods2-wrap') !== false,
            'deploy_version' => $version,
            'deploy_version_code' => $code,
        ];
    }

    /**
     * @return array{ok:false,stage:string,error_code:string,message:string,debug:array}
     */
    private function deployError(string $stage, string $errorCode, string $message, array $debug): array
    {
        $stageLabels = [
            'validate' => '준비 확인',
            'godo_api' => '고도몰 API 호출',
            'deploy_log' => '배포로그 저장',
            'server' => '서버 처리',
        ];

        $lines = [
            '단계: ' . ($stageLabels[$stage] ?? $stage),
            '코드: ' . $errorCode,
            $message,
        ];
        if (!empty($debug['godo_code'])) {
            $lines[] = 'goodsNo: ' . $debug['godo_code'];
        }
        if (!empty($debug['deploy_version']) || !empty($debug['deploy_version_code'])) {
            $lines[] = '버전: v' . (int)($debug['deploy_version'] ?? 0) . ' / ' . (string)($debug['deploy_version_code'] ?? '');
        }
        if (!empty($debug['godo_http_code'])) {
            $lines[] = '고도몰 HTTP: ' . (int)$debug['godo_http_code'];
        }
        if (!empty($debug['godo_url'])) {
            $lines[] = '고도몰 URL: ' . (string)$debug['godo_url'];
        }
        if (!empty($debug['godo_error_code'])) {
            $lines[] = '고도몰 error_code: ' . (string)$debug['godo_error_code'];
        }
        if (!empty($debug['godo_raw'])) {
            $lines[] = '고도몰 응답: ' . (string)$debug['godo_raw'];
        }
        if (!empty($debug['log_error'])) {
            $lines[] = '배포로그 오류: ' . (string)$debug['log_error'];
        }
        if (!empty($debug['exception'])) {
            $lines[] = '예외: ' . (string)$debug['exception'] . ' @ ' . (string)($debug['file'] ?? '');
        }

        error_log(json_encode([
            'event' => 'prd_detail_content_deploy_failed',
            'stage' => $stage,
            'error_code' => $errorCode,
            'message' => $message,
            'debug' => $debug,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return [
            'ok' => false,
            'stage' => $stage,
            'error_code' => $errorCode,
            'message' => implode("\n", $lines),
            'debug' => $debug,
        ];
    }

    private function writeDeployLog(array $content, array $payload, array $admin, array $godoResult): void
    {
        $success = !empty($godoResult['success']);
        $response = $godoResult['response'] ?? [];
        if (!is_array($response)) {
            $response = ['raw' => (string)($godoResult['raw'] ?? '')];
        }

        ProductDetailContentDeployLogModel::create([
            'prd_pk' => (int)($content['prd_pk'] ?? 0),
            'godo_code' => (string)($payload['godo_code'] ?? ''),
            'content_idx' => (int)($content['idx'] ?? 0) ?: null,
            'deploy_version' => (int)($content['deploy_version'] ?? 0),
            'deploy_version_code' => (string)($content['deploy_version_code'] ?? ''),
            'original_name' => (string)($content['original_name'] ?? ''),
            'korean_name' => (string)($content['korean_name'] ?? ''),
            'title' => (string)($content['title'] ?? ''),
            'maker_comment' => (string)($content['maker_comment'] ?? ''),
            'md_comment' => (string)($content['md_comment'] ?? ''),
            'summary_points' => $payload['summary_points'] ?? [],
            'specs' => $payload['specs'] ?? [],
            'html' => (string)($payload['html'] ?? ''),
            'snapshot' => $payload,
            'deploy_status' => $success ? 'success' : 'fail',
            'godo_http_code' => (int)($godoResult['http_code'] ?? 0) ?: null,
            'godo_response' => $response,
            'error_message' => $success ? null : $this->clip((string)($godoResult['message'] ?? '고도몰 배포 실패'), 500),
            'admin_idx' => (int)($admin['idx'] ?? 0) ?: null,
            'admin_name' => (string)($admin['name'] ?? '') ?: null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function normalizeGodoCode($value): string
    {
        $value = trim((string)$value);
        if ($value === '' || !preg_match('/^[1-9][0-9]*$/', $value)) {
            return '';
        }
        return $value;
    }

    private function normalizeDeployVersionCode($value): string
    {
        $value = strtoupper(trim((string)$value));
        if ($value === '' || !preg_match('/^PDC-[0-9]+-V[0-9]{4}-[0-9]{14}-[A-F0-9]{6}$/', $value)) {
            return '';
        }
        return $value;
    }

    /**
     * @return array{ok:false,status:int,error_code:string,message:string}
     */
    private function godoSyncError(int $status, string $errorCode, string $message): array
    {
        return [
            'ok' => false,
            'status' => $status,
            'error_code' => $errorCode,
            'message' => $message,
        ];
    }

    private function escapeHtml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private function nl2br(string $value): string
    {
        return preg_replace('/\r\n|\r|\n/', '<br>', $this->escapeHtml($value)) ?? $this->escapeHtml($value);
    }
}
