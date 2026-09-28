<?php

namespace App\Services;

use Exception;
use Throwable;
use App\Models\ProductModel;
use App\Models\ProductCategoryMappingModel;
use App\Models\ProductDetailContentModel;
use App\Models\ProductDetailContentDeployLogModel;
use App\Services\ProductImageLibraryService;
use App\Classes\Database;

class ProductDetailContentService
{
    private const THROUGH_FEATURE_CODE = 'ONAHOLE_FEATURE_THROUGH';

    /**
     * 상품 컨텐츠 관리 페이지 데이터
     */
    public function getPageData(int $prdPk): array
    {
        $this->ensureContentColumns();
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

        $godoBottomContent = $this->loadGodoBottomDeployStatus($product, $content);
        $canDeployBottom = !empty($godoBottomContent['has_godo_code'])
            && (int)($content['bottom_deploy_version'] ?? 0) > 0
            && trim((string)($content['bottom_deploy_version_code'] ?? '')) !== ''
            && empty($godoBottomContent['matches_local']);

        $imageStoragePath = trim((string)($product['CD_IMAGE_STORAGE_PATH'] ?? ''));
        $imageLibrary = [];
        try {
            $imageLibrary = (new ProductImageLibraryService())->getLibrary($prdPk);
        } catch (Throwable $e) {
            $imageLibrary = [];
        }

        return [
            'prd_pk' => $prdPk,
            'product_name' => (string)($product['CD_NAME'] ?? ''),
            'product_image' => $this->buildProductImagePath($product),
            'content' => $content,
            'godo_content' => $godoContent,
            'can_deploy' => $canDeploy,
            'godo_bottom_content' => $godoBottomContent,
            'can_deploy_bottom' => $canDeployBottom,
            'can_recommend_specs' => !empty($recommended['supported']),
            'image_storage_path' => $imageStoragePath,
            'image_library' => $imageLibrary,
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
        $keepVersion = (($data['save_mode'] ?? '') === 'draft') || !empty($data['keep_version']);
        $currentVersion = (int)($currentData['deploy_version'] ?? 0);
        $currentCode = trim((string)($currentData['deploy_version_code'] ?? ''));
        if ($keepVersion) {
            $nextDeployVersion = $currentVersion;
            $deployVersionCode = $currentCode;
        } else {
            $nextDeployVersion = $currentVersion + 1;
            $deployVersionCode = $this->makeDeployVersionCode($prdPk, $nextDeployVersion);
        }

        $now = date('Y-m-d H:i:s');
        $saved = ProductDetailContentModel::updateOrCreate(
            ['prd_pk' => $prdPk],
            [
                'original_name' => $this->clip((string)($data['original_name'] ?? ''), 255),
                'korean_name' => $this->clip((string)($data['korean_name'] ?? ''), 255),
                'list_summary' => $this->clip((string)($data['list_summary'] ?? ''), 255),
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
     * 하단 컨텐츠 저장 (상단 배포버전과 별개)
     */
    public function saveBottom(int $prdPk, array $data, array $admin = []): array
    {
        $this->ensureContentColumns();
        $product = $this->requireProduct($prdPk);
        $current = ProductDetailContentModel::where('prd_pk', $prdPk)->first();
        $currentData = $this->rowToArray($current);
        $keepVersion = (($data['save_mode'] ?? '') === 'draft') || !empty($data['keep_version']);
        $currentVersion = (int)($currentData['bottom_deploy_version'] ?? 0);
        $currentCode = trim((string)($currentData['bottom_deploy_version_code'] ?? ''));
        if ($keepVersion) {
            $nextDeployVersion = $currentVersion;
            $deployVersionCode = $currentCode;
        } else {
            $nextDeployVersion = $currentVersion + 1;
            $deployVersionCode = $this->makeDeployVersionCode($prdPk, $nextDeployVersion, 'PDB');
        }

        $now = date('Y-m-d H:i:s');
        $values = [
            'bottom_items' => $this->normalizeBottomItems($data['bottom_items'] ?? []),
            'bottom_position' => $this->normalizeBottomPosition($data['bottom_position'] ?? ''),
            'bottom_deploy_version' => $nextDeployVersion,
            'bottom_deploy_version_code' => $deployVersionCode,
            'bottom_admin_idx' => (int)($admin['idx'] ?? 0) ?: null,
            'bottom_admin_name' => (string)($admin['name'] ?? '') ?: null,
            'bottom_updated_at' => $now,
        ];
        if ((int)($currentData['idx'] ?? 0) <= 0) {
            $values['original_name'] = $this->clip((string)($product['CD_NAME_OG'] ?? ''), 255);
            $values['korean_name'] = $this->clip((string)($product['CD_NAME'] ?? ''), 255);
            $values['created_at'] = $now;
            $values['updated_at'] = $now;
        }
        $saved = ProductDetailContentModel::updateOrCreate(
            ['prd_pk' => $prdPk],
            $values
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
                (string)$content['deploy_version_code'],
                (string)($payload['list_summary'] ?? '')
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
     * 하단 컨텐츠를 고도몰에 실배포한다.
     *
     * @return array{ok:bool,stage:string,error_code?:string,message:string,debug?:array,content?:array,godo_code?:int,deploy_version?:int,deploy_version_code?:string}
     */
    public function deployBottomToGodo(int $prdPk, array $admin = []): array
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
            'target' => 'bottom',
        ];

        try {
            $this->ensureContentColumns();
            $product = $this->requireProduct($prdPk);
            $godoCode = $this->normalizeGodoCode($product['cd_godo_code'] ?? '');
            $debug['godo_code'] = $godoCode;
            if ($godoCode === '') {
                return $this->deployError('validate', 'NO_GODO_CODE', '고도몰 상품번호가 없습니다.', $debug);
            }

            $row = ProductDetailContentModel::where('prd_pk', $prdPk)->first();
            $content = $this->normalizeRow($row, $prdPk, $product);
            $debug['deploy_version'] = (int)($content['bottom_deploy_version'] ?? 0);
            $debug['deploy_version_code'] = trim((string)($content['bottom_deploy_version_code'] ?? ''));
            if ((int)($content['idx'] ?? 0) <= 0 || (int)($content['bottom_deploy_version'] ?? 0) <= 0) {
                return $this->deployError('validate', 'CONTENT_NOT_SAVED', '배포할 하단 컨텐츠가 없습니다. 먼저 저장해 주세요.', $debug);
            }
            if ($debug['deploy_version_code'] === '') {
                return $this->deployError('validate', 'NO_VERSION_CODE', '하단 배포코드가 없습니다. 다시 저장해 주세요.', $debug);
            }

            $payload = $this->buildGodoBottomSyncData($content, $godoCode);
            $godoResult = (new GodoApiService())->deployPrdDetailBottomContent(
                $godoCode,
                (int)$content['bottom_deploy_version'],
                (string)$content['bottom_deploy_version_code'],
                $payload
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
                    (string)($godoResult['message'] ?? '고도몰 하단 배포에 실패했습니다.'),
                    $debug
                );
            }

            return [
                'ok' => true,
                'stage' => 'done',
                'message' => (string)($godoResult['message'] ?? '고도몰에 하단 컨텐츠를 배포했습니다.'),
                'content' => $content,
                'godo_code' => (int)$godoCode,
                'deploy_version' => (int)$content['bottom_deploy_version'],
                'deploy_version_code' => (string)$content['bottom_deploy_version_code'],
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
        $rawDeployVersionCode = trim((string)($criteria['deploy_version_code'] ?? ''));
        $target = strtolower(trim((string)($criteria['target'] ?? '')));
        if (in_array($target, ['bottom', 'pdb', 'goods2', 'goods2-info'], true) || stripos($rawDeployVersionCode, 'PDB-') === 0) {
            return $this->getGodoBottomSyncPayload($criteria);
        }

        $godoCode = $this->normalizeGodoCode($criteria['godo_code'] ?? '');
        $deployVersion = (int)($criteria['deploy_version'] ?? 0);
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

    /**
     * 고도몰이 하단 컨텐츠를 받아 DB에 저장할 페이로드
     *
     * @param array{godo_code?:mixed,deploy_version?:mixed,deploy_version_code?:mixed} $criteria
     * @return array{ok:bool,status:int,error_code?:string,message:string,data?:array,current?:array}
     */
    public function getGodoBottomSyncPayload(array $criteria): array
    {
        $this->ensureContentColumns();
        $godoCode = $this->normalizeGodoCode($criteria['godo_code'] ?? '');
        $deployVersion = (int)($criteria['deploy_version'] ?? 0);
        $rawDeployVersionCode = trim((string)($criteria['deploy_version_code'] ?? ''));
        $deployVersionCode = $this->normalizeDeployVersionCode($rawDeployVersionCode, 'PDB');

        if ($godoCode === '') {
            return $this->godoSyncError(400, 'INVALID_REQUEST', '고도몰 상품번호가 필요합니다.');
        }
        if ($deployVersion <= 0 || $rawDeployVersionCode === '') {
            return $this->godoSyncError(400, 'INVALID_REQUEST', '하단 배포버전과 배포코드가 필요합니다.');
        }
        if ($deployVersionCode === '') {
            return $this->godoSyncError(400, 'INVALID_REQUEST', '하단 배포코드 형식이 올바르지 않습니다.');
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
            return $this->godoSyncError(404, 'CONTENT_NOT_FOUND', '하단 컨텐츠가 아직 저장되지 않았습니다.');
        }

        $content = $this->normalizeRow($row, $prdPk, $product);
        if ((int)($content['idx'] ?? 0) <= 0 || (int)($content['bottom_deploy_version'] ?? 0) <= 0) {
            return $this->godoSyncError(404, 'CONTENT_NOT_FOUND', '하단 컨텐츠가 아직 저장되지 않았습니다.');
        }

        $currentVersion = (int)$content['bottom_deploy_version'];
        $currentVersionCode = trim((string)$content['bottom_deploy_version_code']);
        $versionMatched = $currentVersion === $deployVersion
            && $currentVersionCode !== ''
            && hash_equals($currentVersionCode, $deployVersionCode);

        if (!$versionMatched) {
            return [
                'ok' => false,
                'status' => 409,
                'error_code' => 'VERSION_MISMATCH',
                'message' => '요청한 하단 배포버전과 현재 저장된 배포버전이 다릅니다.',
                'current' => [
                    'godo_code' => (int)$godoCode,
                    'prd_pk' => $prdPk,
                    'deploy_version' => $currentVersion,
                    'deploy_version_code' => $currentVersionCode,
                    'target' => 'bottom',
                ],
            ];
        }

        return [
            'ok' => true,
            'status' => 200,
            'message' => '하단 컨텐츠를 조회했습니다.',
            'data' => $this->buildGodoBottomSyncData($content, $godoCode),
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
                'A.cd_accessories',
                'A.CD_IMG',
                'A.img_mode',
                'A.CD_IMAGE_STORAGE_PATH',
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
        $product['cd_accessories'] = $this->decodeJsonList($product['cd_accessories'] ?? []);
        $product['cd_sub_category_codes'] = $this->getSubCategoryCodes((int)($product['CD_IDX'] ?? 0));

        return $product;
    }

    private function buildProductImagePath(array $product): string
    {
        $image = trim((string)($product['CD_IMG'] ?? ''));
        if ($image === '') {
            return '';
        }
        if (trim((string)($product['img_mode'] ?? '')) === 'out') {
            return $image;
        }
        return '/data/comparion/' . $image;
    }

    /**
     * @return array{supported:bool,kind_code:string,message:string,specs:array<int,array{code:string,name:string,value:string}>}
     */
    private function buildRecommendedSpecs(array $product): array
    {
        $kindCode = trim((string)($product['CD_KIND_CODE'] ?? ''));
        $categoryCode = trim((string)($product['CD_CATEGORY_CODE'] ?? ''));
        $isOnahole = $this->isOnaholeProduct($kindCode, $categoryCode);
        $isTorsoFamily = $this->isTorsoFamilyProduct($kindCode, $categoryCode);
        if (!$isOnahole && !$isTorsoFamily) {
            return [
                'supported' => false,
                'kind_code' => $kindCode,
                'message' => '오나홀, 토르소 상품만 추천값을 생성할 수 있습니다.',
                'specs' => [],
            ];
        }

        $spec = (isset($product['cd_spec']) && is_array($product['cd_spec'])) ? $product['cd_spec'] : [];
        $vendor = (isset($spec['vendor_size']) && is_array($spec['vendor_size'])) ? $spec['vendor_size'] : [];
        $measured = (isset($spec['measured_size']) && is_array($spec['measured_size'])) ? $spec['measured_size'] : [];
        $specType = $this->resolveRecommendedSpecType($kindCode, $categoryCode, $isOnahole);
        $sizeSpecs = $this->usesOnaholeSizeLayout($specType)
            ? $this->buildOnaholeSizeSpecs($vendor, $measured, (string)($product['CD_SIZE2'] ?? ''))
            : $this->buildTorsoFamilySizeSpecs($specType, $vendor, $measured, (string)($product['CD_SIZE2'] ?? ''));
        $weightUnit = $this->recommendedWeightUnit($specType);

        $specs = [
            $this->specItem('brand', '브랜드', $this->buildBrandValue($product)),
            $this->specItem('type', '유형', $isOnahole ? $this->buildOnaholeTypeValue($product) : $this->buildTorsoTypeValue($product)),
            $this->specItem('color', '색상', $this->specFieldValue($vendor, $measured, 'color')),
            $this->specItem('material', '소재', $this->specFieldValue($vendor, $measured, 'material')),
        ];
        foreach ($sizeSpecs as $sizeSpec) {
            $specs[] = $sizeSpec;
        }
        $specs[] = $this->specItem('weight', '중량', $this->buildWeightValue($product, $vendor, $measured, $weightUnit));
        $accessorySpec = $this->buildAccessorySpec($product);
        if ($accessorySpec !== null) {
            $specs[] = $accessorySpec;
        }

        return [
            'supported' => true,
            'kind_code' => $kindCode,
            'message' => '추천값을 생성했습니다.',
            'specs' => $specs,
        ];
    }

    private function isOnaholeProduct(string $kindCode, string $categoryCode): bool
    {
        return $kindCode === 'ONAHOLE' || (bool)preg_match('/^01\d{6}$/', $categoryCode);
    }

    private function isTorsoFamilyProduct(string $kindCode, string $categoryCode): bool
    {
        $torsoKinds = ['TORSO', 'BREAST', 'BUTT', 'LEG', 'BODY_PART', 'REALDOLL', 'HEAD', 'FURRY'];
        return in_array($kindCode, $torsoKinds, true) || (bool)preg_match('/^02\d{6}$/', $categoryCode);
    }

    private function resolveRecommendedSpecType(string $kindCode, string $categoryCode, bool $isOnahole): string
    {
        $lookupCode = $categoryCode !== '' ? $categoryCode : $kindCode;
        $specService = new ProductSpecService();
        $specType = $specService->getSpecType($lookupCode);
        if ($specType !== '' && $specService->getSchema($specType) !== []) {
            return $specType;
        }
        if ($isOnahole) {
            return '01000000';
        }
        if ($kindCode === 'TORSO' || preg_match('/^0201\d{4}$/', $categoryCode)) {
            return '02010000';
        }
        return $specType !== '' ? $specType : $lookupCode;
    }

    private function usesOnaholeSizeLayout(string $specType): bool
    {
        return in_array($specType, ['01000000', '02020000', '02080000'], true);
    }

    private function recommendedWeightUnit(string $specType): string
    {
        $schema = (new ProductSpecService())->getSchema($specType);
        $unit = trim((string)($schema['fields']['weight'][1] ?? ''));
        return $unit !== '' ? $unit : ($this->usesOnaholeSizeLayout($specType) ? 'g' : 'kg');
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

    private function buildTorsoTypeValue(array $product): string
    {
        $categoryCode = trim((string)($product['CD_CATEGORY_CODE'] ?? ''));
        $path = $this->getCategoryNamePath($categoryCode);
        if ($path !== [] && in_array($path[0], ['리얼/토르소', '토르소'], true)) {
            array_shift($path);
        }

        if ($path === []) {
            $kindCode = trim((string)($product['CD_KIND_CODE'] ?? ''));
            $kindNames = config('admin.product')['prd_kind_name'] ?? [];
            $kindName = trim((string)($kindNames[$kindCode] ?? ''));
            if ($kindName !== '') {
                $path[] = $kindName;
            }
        }

        return implode(' / ', array_map(static function ($name): string {
            return trim((string)$name);
        }, $path));
    }

    /**
     * @return array<int,string>
     */
    private function getCategoryNamePath(string $categoryCode): array
    {
        $categoryCode = trim($categoryCode);
        if ($categoryCode === '') {
            return [];
        }

        $categories = config('admin.product')['categories'] ?? [];
        if (!is_array($categories)) {
            return [];
        }

        $found = [];
        $walk = function (array $nodes, array $trail) use (&$walk, &$found, $categoryCode): bool {
            foreach ($nodes as $node) {
                if (!is_array($node)) {
                    continue;
                }
                $code = trim((string)($node['code'] ?? ''));
                $name = trim((string)($node['name'] ?? ''));
                $next = $trail;
                if ($name !== '') {
                    $next[] = $name;
                }
                if ($code === $categoryCode) {
                    $found = $next;
                    return true;
                }
                $children = $node['children'] ?? [];
                if (is_array($children) && $children !== [] && $walk($children, $next)) {
                    return true;
                }
            }
            return false;
        };
        $walk($categories, []);

        return $found;
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
            $outerParts[] = '가로(W) ' . $length;
        }
        if ($height !== '') {
            $outerParts[] = '세로(H) ' . $height;
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
                    '내부길이(질) : ' . $this->formatApproxInnerLength($vaginaLength) . ' / 내부길이(애널) : ' . $this->formatApproxInnerLength($analLength)
                ),
            ];
        }

        $sizeValue = $outerSize;
        $singleInner = $hasVagina ? $vaginaLength : $analLength;
        if ($singleInner !== '') {
            $innerText = '내부길이 : ' . $this->formatApproxInnerLength($singleInner);
            $sizeValue = $sizeValue !== '' ? ($sizeValue . ' / ' . $innerText) : $innerText;
        }

        return [
            $this->specItem('size', '사이즈', $sizeValue),
        ];
    }

    /**
     * @return array<int,array{code:string,name:string,value:string}>
     */
    private function buildTorsoFamilySizeSpecs(string $specType, array $vendor, array $measured, string $legacyInnerLength): array
    {
        $outerKeys = $this->torsoFamilyOuterSizeKeys($specType);
        $measureKeys = $this->torsoFamilyMeasureKeys($specType);

        $outerParts = [];
        foreach ($outerKeys as $key => $label) {
            $value = $this->specFieldValue($vendor, $measured, $key);
            if ($value !== '') {
                $outerParts[] = $label . ' ' . $this->withCm($value);
            }
        }
        $outerSize = implode(' x ', $outerParts);

        $measureParts = [];
        foreach ($measureKeys as $key => $label) {
            $value = $this->specFieldValue($vendor, $measured, $key);
            if ($value !== '') {
                $measureParts[] = $label . $this->withCm($value);
            }
        }
        if ($measureParts !== []) {
            $measureText = implode(' / ', $measureParts);
            $outerSize = $outerSize !== '' ? ($outerSize . ' / ' . $measureText) : $measureText;
        }

        return $this->appendInnerLengthSpecs($outerSize, $vendor, $measured, $legacyInnerLength);
    }

    /**
     * @return array<string,string>
     */
    private function torsoFamilyOuterSizeKeys(string $specType): array
    {
        if ($specType === '02060000') {
            return [
                'overall_length' => '전체 길이',
                'overall_width' => '전체 너비',
                'overall_depth' => '전체 깊이',
            ];
        }
        if ($specType === '02050000') {
            return [
                'height' => '신장',
            ];
        }

        return [
            'body_height' => '신체높이',
            'overall_width' => '전체 너비',
            'overall_depth' => '전체 깊이',
        ];
    }

    /**
     * @return array<string,string>
     */
    private function torsoFamilyMeasureKeys(string $specType): array
    {
        if ($specType === '02060000') {
            return [
                'waist_circumference' => 'W',
                'hip_circumference' => 'H',
            ];
        }

        return [
            'chest_circumference' => 'B',
            'waist_circumference' => 'W',
            'hip_circumference' => 'H',
        ];
    }

    /**
     * @return array<int,array{code:string,name:string,value:string}>
     */
    private function appendInnerLengthSpecs(string $outerSize, array $vendor, array $measured, string $legacyInnerLength): array
    {
        $vaginaLength = $this->specFieldValue($vendor, $measured, 'inner_length_vagina');
        if ($vaginaLength === '') {
            $vaginaLength = trim($legacyInnerLength);
        }
        $analLength = $this->specFieldValue($vendor, $measured, 'inner_length_anal');
        $hasVagina = $vaginaLength !== '';
        $hasAnal = $analLength !== '';
        if ($hasVagina && $hasAnal) {
            return [
                $this->specItem('size', '사이즈', $outerSize),
                $this->specItem(
                    'inner_length',
                    '내부길이',
                    '내부길이(질) : ' . $this->formatApproxInnerLength($vaginaLength) . ' / 내부길이(애널) : ' . $this->formatApproxInnerLength($analLength)
                ),
            ];
        }

        $sizeValue = $outerSize;
        $singleInner = $hasVagina ? $vaginaLength : $analLength;
        if ($singleInner !== '') {
            $innerText = '내부길이 : ' . $this->formatApproxInnerLength($singleInner);
            $sizeValue = $sizeValue !== '' ? ($sizeValue . ' / ' . $innerText) : $innerText;
        }

        return [
            $this->specItem('size', '사이즈', $sizeValue),
        ];
    }

    private function buildWeightValue(array $product, array $vendor = [], array $measured = [], string $unit = 'g'): string
    {
        $specWeight = $this->specFieldValue($vendor, $measured, 'weight');
        if ($specWeight !== '') {
            return $this->withApprox($this->withUnit($specWeight, $unit));
        }

        $weightFn = (isset($product['cd_weight_fn']) && is_array($product['cd_weight_fn'])) ? $product['cd_weight_fn'] : [];
        $weight = trim((string)($weightFn['1'] ?? ''));
        if ($weight === '') {
            return '';
        }
        return $this->withApprox($this->withUnit($weight, 'g'));
    }

    /**
     * @return array{code:string,name:string,value:string}|null
     */
    private function buildAccessorySpec(array $product): ?array
    {
        $texts = [];
        foreach ((isset($product['cd_accessories']) && is_array($product['cd_accessories'])) ? $product['cd_accessories'] : [] as $accessory) {
            if (is_string($accessory) || is_numeric($accessory)) {
                $text = trim((string)$accessory);
            } elseif (is_array($accessory)) {
                $text = trim((string)($accessory['text'] ?? ''));
            } else {
                continue;
            }
            if ($text !== '') {
                $texts[] = $text;
            }
        }

        if ($texts === []) {
            return null;
        }
        if (count($texts) === 1) {
            return $this->specItem('accessory', '부속품', $texts[0]);
        }

        return $this->specItem('components', '구성품', '본품, ' . implode(', ', $texts));
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

    private function formatApproxInnerLength(string $value): string
    {
        return $this->withApprox($this->withCm($value));
    }

    private function withCm(string $value): string
    {
        return $this->withUnit($value, 'cm');
    }

    private function withApprox(string $value): string
    {
        $value = trim($value);
        if ($value === '' || preg_match('/^약\s*/u', $value)) {
            return $value;
        }
        return '약 ' . $value;
    }

    private function withUnit(string $value, string $unit): string
    {
        $value = trim($value);
        $unit = trim($unit);
        if ($value === '' || $unit === '') {
            return $value;
        }
        if (preg_match('/' . preg_quote($unit, '/') . '$/i', $value)) {
            return $value;
        }
        return $value . $unit;
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
            'list_summary' => (string)($data['list_summary'] ?? ''),
            'title' => (string)($data['title'] ?? ''),
            'maker_comment' => (string)($data['maker_comment'] ?? ''),
            'md_comment' => (string)($data['md_comment'] ?? ''),
            'summary_points' => $this->normalizeSummaryPoints($data['summary_points'] ?? []),
            'specs' => $this->normalizeSpecs($data['specs'] ?? []),
            'bottom_items' => $this->normalizeBottomItems($data['bottom_items'] ?? []),
            'bottom_position' => $this->normalizeBottomPosition($data['bottom_position'] ?? ''),
            'deploy_version' => (int)($data['deploy_version'] ?? 0),
            'deploy_version_code' => trim((string)($data['deploy_version_code'] ?? '')),
            'bottom_deploy_version' => (int)($data['bottom_deploy_version'] ?? 0),
            'bottom_deploy_version_code' => trim((string)($data['bottom_deploy_version_code'] ?? '')),
            'admin_name' => (string)($data['admin_name'] ?? ''),
            'updated_at' => (string)($data['updated_at'] ?? ''),
            'bottom_admin_name' => (string)($data['bottom_admin_name'] ?? ''),
            'bottom_updated_at' => (string)($data['bottom_updated_at'] ?? ''),
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

    private function normalizeBottomItems($raw): array
    {
        $rows = $this->decodeJsonList($raw);
        $items = [];
        $seen = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $url = trim((string)($row['hosting_url'] ?? $row['url'] ?? ''));
            $filename = trim((string)($row['filename'] ?? ''));
            if ($url === '') {
                continue;
            }
            if ($filename === '') {
                $filename = basename(parse_url($url, PHP_URL_PATH) ?: $url);
            }
            $key = strtolower($url);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $items[] = [
                'library_idx' => (int)($row['library_idx'] ?? $row['idx'] ?? 0),
                'filename' => $filename,
                'hosting_url' => $url,
                'comment' => trim((string)($row['comment'] ?? '')),
                'width' => (int)($row['width'] ?? 0),
                'height' => (int)($row['height'] ?? 0),
            ];
        }
        return $items;
    }

    private function normalizeBottomPosition($value): string
    {
        $raw = trim((string)$value);
        $key = strtolower($raw);
        $aliases = [
            'top' => 'top',
            'header' => 'top',
            'above' => 'top',
            'body_top' => 'top',
            '본문상단' => 'top',
            '상단' => 'top',
            'bottom' => 'bottom',
            'footer' => 'bottom',
            'below' => 'bottom',
            'body_bottom' => 'bottom',
            '본문하단' => 'bottom',
            '하단' => 'bottom',
        ];
        if (isset($aliases[$key])) {
            return $aliases[$key];
        }
        if (isset($aliases[$raw])) {
            return $aliases[$raw];
        }
        return 'bottom';
    }

    private function bottomPositionLabel(string $position): string
    {
        return $position === 'top' ? '본문상단' : '본문하단';
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

    private function makeDeployVersionCode(int $prdPk, int $version, string $prefix = 'PDC'): string
    {
        $prefix = strtoupper($prefix) === 'PDB' ? 'PDB' : 'PDC';
        $column = $prefix === 'PDB' ? 'bottom_deploy_version_code' : 'deploy_version_code';
        for ($i = 0; $i < 8; $i++) {
            $candidate = $prefix . '-' . $prdPk
                . '-V' . str_pad((string)$version, 4, '0', STR_PAD_LEFT)
                . '-' . date('YmdHis')
                . '-' . strtoupper(bin2hex(random_bytes(3)));
            $exists = ProductDetailContentModel::where($column, $candidate)->exists();
            if (!$exists) {
                return $candidate;
            }
            usleep(1000);
        }

        return $prefix . '-' . $prdPk . '-V' . str_pad((string)$version, 4, '0', STR_PAD_LEFT) . '-' . date('YmdHis') . '-FFFFFF';
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
     *   list_summary:string,
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
            'list_summary' => (string)($content['list_summary'] ?? ''),
            'listSummary' => (string)($content['list_summary'] ?? ''),
            'title' => (string)($content['title'] ?? ''),
            'maker_comment' => (string)($content['maker_comment'] ?? ''),
            'md_comment' => (string)($content['md_comment'] ?? ''),
            'summary_points' => $summaryPoints,
            'specs' => $specs,
            'updated_at' => (string)($content['updated_at'] ?? ''),
        ];
        $payload['html'] = $this->buildDeployHtml($payload);
        if ((int)($content['bottom_deploy_version'] ?? 0) > 0) {
            $bottom = $this->buildGodoBottomSyncData($content, $godoCode);
            $payload['bottom'] = $bottom;
            $payload['dnfixBottomContent'] = $bottom;
            $payload['bottom_items'] = $bottom['bottom_items'];
            $payload['bottom_html'] = $bottom['bottom_html'];
            $payload['bottomHtml'] = $bottom['bottom_html'];
            $payload['bottom_deploy_version'] = $bottom['bottom_deploy_version'];
            $payload['bottom_deploy_version_code'] = $bottom['bottom_deploy_version_code'];
            $payload['bottom_updated_at'] = $bottom['bottom_updated_at'];
            $payload['bottom_position'] = $bottom['bottom_position'];
            $payload['bottomPosition'] = $bottom['bottom_position'];
            $payload['bottom_position_label'] = $bottom['bottom_position_label'];
            $payload['goodsBottomDescription'] = $bottom['html'];
        }

        return $payload;
    }

    private function buildDeployHtml(array $content): string
    {
        $versionCode = trim((string)($content['deploy_version_code'] ?? ''));
        $html = '<article class="dnfix-goods-contents"'
            . ($versionCode !== '' ? ' data-pdc-version="' . $this->escapeHtml($versionCode) . '"' : '')
            . '>';

        if ($versionCode !== '') {
            $html .= '<div class="g3-pdc-version" data-pdc-version="' . $this->escapeHtml($versionCode) . '" hidden></div>';
        }

        $originalName = trim((string)($content['original_name'] ?? ''));
        $koreanName = trim((string)($content['korean_name'] ?? ''));
        if ($originalName !== '' || $koreanName !== '') {
            $html .= '<header class="g3-header">';
            if ($originalName !== '') {
                $html .= '<p class="g3-name-en">' . $this->escapeHtml($originalName) . '</p>';
            }
            if ($koreanName !== '') {
                $html .= '<h2 class="g3-name">' . $this->escapeHtml($koreanName) . '</h2>';
            }
            $html .= '</header>';
        }

        $explanation = '';
        $title = trim((string)($content['title'] ?? ''));
        $makerComment = trim((string)($content['maker_comment'] ?? ''));
        $mdComment = trim((string)($content['md_comment'] ?? ''));
        if ($title !== '') {
            $explanation .= '<p class="highlight">' . $this->escapeHtml($title) . '</p>';
        }
        if ($makerComment !== '') {
            $explanation .= '<div class="maker-comment"><h4 class="maker-comment-title">[메이커 코멘트]</h4>' . $this->nl2br($makerComment) . '</div>';
        }
        if ($mdComment !== '') {
            $explanation .= '<div class="md-comment"><h4 class="md-comment-title">[MD 코멘트]</h4>' . $this->nl2br($mdComment) . '</div>';
        }
        if ($explanation !== '') {
            $html .= '<section class="g3-explanation">' . $explanation . '</section>';
        }

        $points = (isset($content['summary_points']) && is_array($content['summary_points'])) ? $content['summary_points'] : [];
        if ($points !== []) {
            $html .= '<section class="g3-point">';
            $html .= '<h4 class="g3-point-title">POINT</h4>';
            $html .= '<ul class="g3-point-list">';
            foreach ($points as $point) {
                $html .= '<li>' . $this->escapeHtml((string)($point['text'] ?? '')) . '</li>';
            }
            $html .= '</ul></section>';
        }

        $specs = (isset($content['specs']) && is_array($content['specs'])) ? $content['specs'] : [];
        if ($specs !== []) {
            $html .= '<section class="g3-spec">';
            $html .= '<h4 class="g3-spec-title">SPEC</h4>';
            $html .= '<dl class="g3-spec-list">';
            foreach ($specs as $spec) {
                $html .= '<div class="g3-spec-row">';
                $html .= '<dt>' . $this->escapeHtml((string)($spec['name'] ?? '')) . ' :</dt>';
                $html .= '<dd>' . $this->escapeHtml((string)($spec['value'] ?? '')) . '</dd>';
                $html .= '</div>';
            }
            $html .= '</dl>';
            $html .= '<p class="g3-spec-note">※ 사이즈, 중량정보는 브랜드(메이커)에서 제공하는 정보를 기준으로 합니다. 개체별, 측정기구에 따라 차이가 있을 수 있습니다.</p>';
            $html .= '</section>';
        }

        $html .= '</article>';
        return $html;
    }

    /**
     * 고도몰 dnfix_goods_contents 하단 컬럼에 바로 넣을 수 있는 페이로드
     *
     * @return array{
     *   godo_code:int,
     *   goodsNo:int,
     *   prd_pk:int,
     *   target:string,
     *   table:string,
     *   deploy_version:int,
     *   deploy_version_code:string,
     *   bottom_deploy_version:int,
     *   bottom_deploy_version_code:string,
     *   bottom_items:array<int,array{sort:int,library_idx:int,filename:string,hosting_url:string,comment:string,width:int,height:int}>,
     *   html:string,
     *   bottom_html:string,
     *   goodsBottomDescription:string,
     *   updated_at:string,
     *   bottom_updated_at:string
     * }
     */
    private function buildGodoBottomSyncData(array $content, string $godoCode): array
    {
        $items = [];
        foreach ((isset($content['bottom_items']) && is_array($content['bottom_items'])) ? $content['bottom_items'] : [] as $item) {
            if (!is_array($item)) {
                continue;
            }
            $url = trim((string)($item['hosting_url'] ?? ''));
            if ($url === '') {
                continue;
            }
            $items[] = [
                'sort' => count($items) + 1,
                'library_idx' => (int)($item['library_idx'] ?? 0),
                'filename' => (string)($item['filename'] ?? ''),
                'hosting_url' => $url,
                'comment' => (string)($item['comment'] ?? ''),
                'width' => (int)($item['width'] ?? 0),
                'height' => (int)($item['height'] ?? 0),
            ];
        }

        $version = (int)($content['bottom_deploy_version'] ?? 0);
        $versionCode = trim((string)($content['bottom_deploy_version_code'] ?? ''));
        $updatedAt = (string)($content['bottom_updated_at'] ?? '');
        $position = $this->normalizeBottomPosition($content['bottom_position'] ?? 'bottom');
        $positionLabel = $this->bottomPositionLabel($position);
        $html = $this->buildBottomDeployHtml([
            'deploy_version_code' => $versionCode,
            'bottom_items' => $items,
            'bottom_position' => $position,
        ]);

        return [
            'godo_code' => (int)$godoCode,
            'goodsNo' => (int)$godoCode,
            'prd_pk' => (int)($content['prd_pk'] ?? 0),
            'target' => 'bottom',
            'table' => 'dnfix_goods_contents',
            'deploy_version' => $version,
            'deploy_version_code' => $versionCode,
            'bottom_deploy_version' => $version,
            'bottom_deploy_version_code' => $versionCode,
            'bottomDeployVersion' => $version,
            'bottomDeployVersionCode' => $versionCode,
            'bottom_position' => $position,
            'bottomPosition' => $position,
            'bottom_position_label' => $positionLabel,
            'bottomPositionLabel' => $positionLabel,
            'bottom_items' => $items,
            'bottomItems' => $items,
            'bottom_items_json' => json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]',
            'html' => $html,
            'bottom_html' => $html,
            'bottomHtml' => $html,
            'goodsBottomDescription' => $html,
            'updated_at' => $updatedAt,
            'bottom_updated_at' => $updatedAt,
        ];
    }

    private function buildBottomDeployHtml(array $content): string
    {
        $versionCode = trim((string)($content['deploy_version_code'] ?? ''));
        $position = $this->normalizeBottomPosition($content['bottom_position'] ?? 'bottom');
        $html = '<div class="dnfix-goods-bottom" data-pdb-position="' . $this->escapeHtml($position) . '">';
        if ($versionCode !== '') {
            $html .= '<div class="g3-pdb-version" data-pdb-version="' . $this->escapeHtml($versionCode) . '" data-pdb-position="' . $this->escapeHtml($position) . '" hidden></div>';
        }
        $html .= '<section class="dnfix-goods-bottom-list" aria-label="상품 상세 설명">';

        $items = (isset($content['bottom_items']) && is_array($content['bottom_items'])) ? $content['bottom_items'] : [];
        foreach ($items as $item) {
            $url = trim((string)($item['hosting_url'] ?? ''));
            if ($url === '') {
                continue;
            }
            $comment = trim((string)($item['comment'] ?? ''));
            $html .= '<figure class="dnfix-goods-bottom-item">';
            $html .= '<img class="dnfix-goods-bottom-img" alt="" src="' . $this->escapeHtml($url) . '">';
            if ($comment !== '') {
                $html .= '<figcaption class="dnfix-goods-bottom-info">' . $this->nl2br($comment) . '</figcaption>';
            }
            $html .= '</figure>';
        }

        $html .= '</section></div>';
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
            'registered' => $version > 0 || $code !== '' || strpos($html, 'dnfix-goods-contents') !== false || strpos($html, 'new-goods2-wrap') !== false,
            'deploy_version' => $version,
            'deploy_version_code' => $code,
        ];
    }

    private function loadGodoBottomDeployStatus(array $product, array $content): array
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
            $rows = (new GodoApiService())->getGodoGoodsInfoByGoodsNo($godoCode, ['DnfixContent', 'DnfixBottomContent']);
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
        $deploy = $this->extractGodoBottomDeployContent($goods);
        $status['registered'] = !empty($deploy['registered']);
        $status['deploy_version'] = (int)($deploy['deploy_version'] ?? 0);
        $status['deploy_version_code'] = trim((string)($deploy['deploy_version_code'] ?? ''));

        $localVersion = (int)($content['bottom_deploy_version'] ?? 0);
        $localCode = trim((string)($content['bottom_deploy_version_code'] ?? ''));
        $status['matches_local'] = $status['registered']
            && $localVersion > 0
            && $status['deploy_version'] === $localVersion
            && ($localCode === '' || $status['deploy_version_code'] === '' || hash_equals($localCode, $status['deploy_version_code']));

        return $status;
    }

    /**
     * @return array{registered:bool,deploy_version:int,deploy_version_code:string}
     */
    private function extractGodoBottomDeployContent(array $goods): array
    {
        $nested = [];
        $fromBottomObject = false;
        foreach (['dnfixBottomContent', 'dnfix_bottom_content', 'DnfixBottomContent'] as $key) {
            if (!array_key_exists($key, $goods)) {
                continue;
            }
            $raw = $goods[$key];
            if (is_array($raw)) {
                $nested = $raw;
                $fromBottomObject = true;
                break;
            }
            if (is_string($raw) && trim($raw) !== '') {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $nested = $decoded;
                    $fromBottomObject = true;
                    break;
                }
            }
        }
        if ($nested === []) {
            foreach (['dnfixContent', 'dnfix_content', 'DnfixContent'] as $key) {
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
        }

        $source = $nested !== [] ? $nested : $goods;
        $version = (int)($source['bottom_deploy_version'] ?? $source['bottomDeployVersion'] ?? 0);
        if ($version <= 0 && $fromBottomObject) {
            $version = (int)($source['deploy_version'] ?? $source['deployVersion'] ?? 0);
        }
        $code = $this->normalizeDeployVersionCode(
            $source['bottom_deploy_version_code'] ?? $source['bottomDeployVersionCode'] ?? '',
            'PDB'
        );
        if ($code === '' && $fromBottomObject) {
            $code = $this->normalizeDeployVersionCode(
                $source['deploy_version_code'] ?? $source['deployVersionCode'] ?? '',
                'PDB'
            );
        }

        $html = trim((string)($source['bottom_html'] ?? $source['html'] ?? $goods['goodsBottomDescription'] ?? ''));
        if ($code === '' && $html !== '' && preg_match('/data-pdb-version="([^"]+)"/i', $html, $matches)) {
            $code = $this->normalizeDeployVersionCode($matches[1] ?? '', 'PDB');
        }
        if ($version <= 0 && $code !== '' && preg_match('/-V(\d{4})-/', $code, $matches)) {
            $version = (int)$matches[1];
        }

        return [
            'registered' => $version > 0 || $code !== '' || strpos($html, 'dnfix-goods-bottom') !== false || strpos($html, 'dnfix-goods-bottom-list') !== false || strpos($html, 'goods2-info-block') !== false,
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
            'deploy_version' => (int)($payload['deploy_version'] ?? $content['deploy_version'] ?? 0),
            'deploy_version_code' => (string)($payload['deploy_version_code'] ?? $content['deploy_version_code'] ?? ''),
            'original_name' => (string)($content['original_name'] ?? ''),
            'korean_name' => (string)($content['korean_name'] ?? ''),
            'list_summary' => (string)($content['list_summary'] ?? ''),
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

    private function normalizeDeployVersionCode($value, string $prefix = 'PDC'): string
    {
        $value = strtoupper(trim((string)$value));
        $prefix = strtoupper($prefix) === 'PDB' ? 'PDB' : 'PDC';
        if ($value === '' || !preg_match('/^' . $prefix . '-[0-9]+-V[0-9]{4}-[0-9]{14}-[A-F0-9]{6}$/', $value)) {
            return '';
        }
        return $value;
    }

    private function ensureContentColumns(): void
    {
        static $ensured = false;
        if ($ensured) {
            return;
        }
        $ensured = true;
        try {
            $columns = Database::getInstance()->fetchAll('SHOW COLUMNS FROM `prd_detail_content`');
        } catch (Throwable $e) {
            return;
        }
        $names = [];
        foreach ($columns as $column) {
            $name = (string)($column['Field'] ?? '');
            if ($name !== '') {
                $names[$name] = true;
            }
        }
        $alters = [
            'bottom_items' => 'ALTER TABLE `prd_detail_content` ADD COLUMN `bottom_items` longtext NULL AFTER `specs`',
            'bottom_position' => 'ALTER TABLE `prd_detail_content` ADD COLUMN `bottom_position` varchar(20) NOT NULL DEFAULT \'bottom\' AFTER `bottom_items`',
            'bottom_deploy_version' => 'ALTER TABLE `prd_detail_content` ADD COLUMN `bottom_deploy_version` int unsigned NOT NULL DEFAULT 0 AFTER `deploy_version_code`',
            'bottom_deploy_version_code' => 'ALTER TABLE `prd_detail_content` ADD COLUMN `bottom_deploy_version_code` varchar(80) NOT NULL DEFAULT \'\' AFTER `bottom_deploy_version`',
            'bottom_admin_idx' => 'ALTER TABLE `prd_detail_content` ADD COLUMN `bottom_admin_idx` int unsigned NULL AFTER `admin_name`',
            'bottom_admin_name' => 'ALTER TABLE `prd_detail_content` ADD COLUMN `bottom_admin_name` varchar(100) NULL AFTER `bottom_admin_idx`',
            'bottom_updated_at' => 'ALTER TABLE `prd_detail_content` ADD COLUMN `bottom_updated_at` datetime NULL AFTER `bottom_admin_name`',
        ];
        foreach ($alters as $name => $sql) {
            if (empty($names[$name])) {
                Database::getInstance()->execute($sql);
            }
        }
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
