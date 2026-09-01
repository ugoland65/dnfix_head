<?php
namespace App\Services;

class ProductSpecService
{
    public function getSpecType(string $categoryCode): string
    {
        $categoryCode = trim($categoryCode);
        if (preg_match('/^0201\d{4}$/', $categoryCode)) {
            return '02010000';
        }
        if (preg_match('/^01\d{6}$/', $categoryCode)) {
            return '01000000';
        }
        return $categoryCode;
    }

    public function getSchema(string $categoryCode): array
    {
        $schemas = $this->getSchemas();

        return $schemas[$this->getSpecType($categoryCode)] ?? [];
    }

    public function getSchemas(): array
    {
        return [
            '01000000' => [
                'label' => '오나홀',
                'fields' => [
                    'weight' => ['상품중량', 'g', true],
                    'inner_length' => ['내부길이', 'cm', true],
                ],
                'options' => [],
            ],
            '02010000' => [
                'label' => '토르소형',
                'fields' => [
                    'body_height' => ['신체높이', 'cm', true],
                    'overall_width' => ['전체 너비', 'cm', false, 'Width'],
                    'overall_depth' => ['전체 깊이', 'cm', false, 'Depth, Long'], 
                    'weight' => ['무게(체중)', 'kg', true],
                    'shoulder_width' => ['어깨 너비', 'cm', false, 'Shoulder Width'], 
                    'chest_circumference' => ['가슴둘레 (B)', 'cm', true, 'Upper bust'],
                    'chest_width' => ['가슴 너비', 'cm'],
                    'underbust_circumference' => ['밑가슴 둘레', 'cm', false, 'Under bust'], 
                    'waist_circumference' => ['허리둘레 (W)', 'cm', true, 'Waistline'],
                    'waist_width' => ['허리 너비', 'cm'],
                    'hip_circumference' => ['엉덩이 둘레 (H)', 'cm', true, 'Hipline'], 
                    'hip_width' => ['엉덩이 너비', 'cm'],
                    'hip_depth' => ['엉덩이 두께', 'cm'],
                    'thigh_circumference' => ['허벅지 둘레', 'cm', false, 'Thigh Circumference'], 
                    'leg_length' => ['다리길이', 'cm', false, 'Leg length'],
                    'inner_length_vagina' => ['내부길이 (질)', 'cm', true], 
                    'inner_length_anal' => ['내부길이 (애널)', 'cm'],
                    'material' => ['소재', '', false, 'TPE, 플래티넘 실리콘(백금)', true],
                ],
                'options' => [],
            ],
            '02020000' => [
                'label' => '가슴장난감',
                'fields' => [
                    'length' => ['가로', 'cm'], 'height' => ['세로', 'cm'], 'width' => ['두께', 'cm'],
                    'shoulder_width' => ['어깨너비', 'cm'], 'chest_circumference' => ['가슴둘레', 'cm', true],
                    'underbust_circumference' => ['밑가슴둘레', 'cm'], 'weight' => ['무게', 'kg'],
                    'material' => ['소재', ''], 'inner_length_1' => ['내부길이 1', 'cm'],
                    'inner_length_2' => ['내부길이 2', 'cm'],
                ],
                'options' => ['insertion_function_yn' => ['삽입기능 여부', ['N' => '없음', 'Y' => '있음']]],
            ],
            '02060000' => [
                'label' => '하반신형',
                'fields' => [
                    'overall_length' => ['전체 길이', 'cm', true],
                    'overall_width' => ['전체 너비', 'cm', false, 'Width'],
                    'overall_depth' => ['전체 깊이', 'cm', false, 'Depth, Long'], 
                    'weight' => ['무게', 'kg', true],
                    'waist_circumference' => ['허리둘레', 'cm', true],
                    'waist_width' => ['허리 너비', 'cm'],
                    'hip_circumference' => ['엉덩이둘레', 'cm', true],
                    'hip_width' => ['엉덩이 너비', 'cm'],
                    'hip_depth' => ['엉덩이 두께', 'cm'],
                    'thigh_circumference' => ['허벅지 둘레', 'cm'],
                    'calf_circumference' => ['종아리 둘레', 'cm'],
                    'foot_length' => ['다리 길이', 'cm'],
                    'shoe_size_cn' => ['신발 사이즈', 'mm'],
                    'inner_length_vagina' => ['내부길이 (질)', 'cm', true],
                    'inner_length_anal' => ['내부길이 (애널)', 'cm'],
                    'material' => ['소재', '', false, 'TPE, 플래티넘 실리콘(백금)', true],
                ],
                'options' => [],
            ],
            '02050000' => [
                'label' => '리얼돌/전신형',
                'fields' => [
                    'height' => ['신장', 'cm'], 'weight' => ['무게', 'kg'], 'head_length' => ['머리길이', 'cm'],
                    'chest_circumference' => ['가슴둘레', 'cm', true], 'shoulder_width' => ['어깨너비', 'cm'],
                    'waist_circumference' => ['허리둘레', 'cm', true], 'hip_circumference' => ['엉덩이둘레', 'cm', true],
                    'arm_length' => ['팔길이', 'cm'], 'leg_length' => ['다리길이', 'cm'],
                    'foot_length' => ['발길이', 'cm'], 'inner_length_vagina' => ['내부길이 (질)', 'cm'],
                    'inner_length_anal' => ['내부길이 (애널)', 'cm'], 'material' => ['소재', ''],
                ],
                'options' => [],
            ],
        ];
    }

    public function build(string $categoryCode, array $postData, array $categoryNameByCode = []): array
    {
        $schema = $this->getSchema($categoryCode);
        if ($schema === []) {
            return [];
        }

        $vendor = $this->normalizeValues($postData['spec_vendor'] ?? $postData['cd_spec_vendor'] ?? [], $schema['fields']);
        $measured = $this->normalizeValues($postData['spec_measured'] ?? $postData['cd_spec_measured'] ?? [], $schema['fields']);
        $options = $this->normalizeOptions($postData['spec_option'] ?? $postData['cd_spec_option'] ?? [], $schema['options']);

        return [
            'category_code' => trim($categoryCode),
            'category_name' => trim((string)($categoryNameByCode[$categoryCode] ?? '')),
            'vendor_size' => $vendor,
            'measured_size' => $measured,
            'options' => $options,
        ];
    }

    private function normalizeValues($raw, array $fields): array
    {
        $raw = is_array($raw) ? $raw : [];
        $result = [];
        foreach ($fields as $key => $_field) {
            $value = trim((string)($raw[$key] ?? ''));
            if ($value !== '') {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    private function normalizeOptions($raw, array $options): array
    {
        $raw = is_array($raw) ? $raw : [];
        $result = [];
        foreach ($options as $key => $_option) {
            $value = trim((string)($raw[$key] ?? ''));
            if ($value !== '') {
                $result[$key] = $value;
            }
        }
        return $result;
    }
}
