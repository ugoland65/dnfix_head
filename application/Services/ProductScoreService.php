<?php

namespace App\Services;

use App\Models\ProductScoreModel;

class ProductScoreService
{

    /**
     * 상품 평점 조회
     * 
     * @param int $pd_idx 상품 인덱스
     * @return array 상품 평점 데이터
     */
    public function getProductScoreByPdIdx($pd_idx)
    {

        $query = ProductScoreModel::where('ps_pd_idx', $pd_idx)
            ->where('ps_mode', 'total')
            ->first();

        if (!empty($query)) {
            $result = $query->toArray();
        }

        if (!empty($result)) {
            if (!empty($result['ps_score'])) {
                $result['ps_score'] = json_decode($result['ps_score'], true);
            }
        }

        return $result ?? [];

    }


    /**
     * 상품 평점 갱신
     * 
     * @param int $productId 상품 인덱스
     * @param float $grade 개인평점
     * @param array $scores 스코어 배열
     * @param string $mode 모드 (total / month)
     * @return array
     */
    public function updateProductScore($productId, $grade,  $scores, $mode)
    {

        $ym = date('Y-m');

        // 전체(total) / 이번달(month) 데이터 가져오기
        $total = ProductScoreModel::where('ps_pd_idx', $productId)
            ->where('ps_mode', 'total')
            ->first();

        $month = ProductScoreModel::where('ps_pd_idx', $productId)
            ->where('ps_mode', 'month')
            ->where('ps_ym', $ym)
            ->first();

        // 없으면 새로 생성
        if (!$total) {
            $total = ProductScoreModel::create([
                'ps_pd_idx' => $productId,
                'ps_mode' => 'total',
                'ps_score' => json_encode(['score' => [], 'total' => 0], JSON_UNESCAPED_UNICODE),
                'ps_grade' => 0,
                'ps_grade_count' => 0,
                'ps_grade_total' => 0,
                'ps_score_total' => 0,
                'ps_grade_data' => '',
                'ps_count' => 0
            ]);
        }

        if (!$month) {
            $month = ProductScoreModel::create([
                'ps_pd_idx' => $productId,
                'ps_mode' => 'month',
                'ps_ym' => $ym,
                'ps_score' => json_encode(['score' => [], 'total' => 0], JSON_UNESCAPED_UNICODE),
                'ps_grade' => 0,
                'ps_grade_count' => 0,
                'ps_grade_total' => 0,
                'ps_score_total' => 0,
                'ps_grade_data' => '',
                'ps_count' => 0
            ]);
        }

        // 평균 계산
        $totalCount = ( $total->ps_grade_count ?? 0 ) + 1;
        $monthCount = ( $month->ps_grade_count ?? 0 ) + 1;

        $totalSum = ( $total->ps_grade_total ?? 0 ) + $grade;
        $monthSum = ( $month->ps_grade_total ?? 0 ) + $grade;

        $avgTotal = round($totalSum / $totalCount, 1);
        $avgMonth = round($monthSum / $monthCount, 1);

        // 개별 점수 항목 계산
        $scoreAvg = [];
        $sumTotalAvg = 0;
        foreach ($scores as $i => $value) {
            $sumTotalAvg += $value;
            $scoreAvg[$i] = round($value, 1);
        }

        ProductScoreModel::update(
            ['ps_idx' => $total->ps_idx],
            [
                'ps_score' => json_encode(['score' => $scoreAvg, 'total' => $avgTotal], JSON_UNESCAPED_UNICODE),
                'ps_grade' => $avgTotal,
                'ps_grade_count' => $totalCount,
                'ps_grade_total' => $totalSum,
                'ps_count' => $total->ps_count + 1,
            ]
        );

        ProductScoreModel::update(
            ['ps_idx' => $month->ps_idx],
            [
                'ps_score' => json_encode(['score' => $scoreAvg, 'total' => $avgMonth], JSON_UNESCAPED_UNICODE),
                'ps_grade' => $avgMonth,
                'ps_grade_count' => $monthCount,
                'ps_grade_total' => $monthSum,
                'ps_count' => $month->ps_count + 1,
            ]
        );

        return [
            'average_scores' => $scoreAvg,
            'user_score' => [
                'score' => $scores,
                'score_sum' => array_sum($scores ?? []),
                'score_avg' => count($scores ?? []) > 0 ? round(array_sum($scores) / count($scores), 1) : 0
            ]
        ];
    }

}