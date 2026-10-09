<?php

namespace App\Services;

use Exception;
use Throwable;

/**
 * 입고예상일 기준 잔여수량과 발주 필요수량.
 *
 * 잔여수량 = 현재고 - (적용 일판매 × 입고까지 일수)
 * 필요수량 = 적용 일판매 × (입고까지 일수 + 커버기간) - 현재고
 * 추천안전재고 = 적용 일판매 × 7일. 필요수량과 따로 표시한다.
 * 일판매는 선택한 기간의 정상월 월평균이고, 급판매이면 최근 28일 일판매를 쓴다.
 */
class ExpectedInboundOrderService
{
    private const DEFAULT_LOOKBACK_MONTHS = 6;
    private const SURGE_RATIO = 1.5;
    private const DEFAULT_COVER_DAYS = 30;
    private const SAFETY_DAYS = 7;

    /**
     * @param int $psIdx
     * @param string $expectedDate Y-m-d
     * @param mixed $coverDays
     * @param mixed $lookbackMonths 3, 6, 12
     * @return array
     */
    public function calculate(int $psIdx, string $expectedDate, $coverDays = self::DEFAULT_COVER_DAYS, $lookbackMonths = self::DEFAULT_LOOKBACK_MONTHS): array
    {
        $lookbackMonths = $this->normalizeLookbackMonths($lookbackMonths);
        $basis = (new ProductStockService())->getExpectedInboundDemandBasis(
            $psIdx,
            $lookbackMonths + 1
        );

        return $this->calculateFromBasis($basis, $expectedDate, date('Y-m-d'), $coverDays);
    }

    /**
     * @param array $basis
     * @param string $expectedDate
     * @param string $today
     * @param mixed $coverDays
     * @return array
     */
    public function calculateFromBasis(array $basis, string $expectedDate, string $today, $coverDays = self::DEFAULT_COVER_DAYS): array
    {
        $expectedDate = $this->normalizeDate($expectedDate, '입고예상일');
        $today = $this->normalizeDate($today, '기준일');
        if ($expectedDate < $today) {
            throw new Exception('입고예상일은 오늘 이후 날짜여야 합니다.');
        }

        $currentStock = (int)($basis['current_stock'] ?? 0);
        $monthlyAvg = (float)($basis['monthly_avg'] ?? 0);
        $dailyMonth = $monthlyAvg > 0 ? round($monthlyAvg / 30, 2) : 0.0;
        $dailyRecent = (float)($basis['daily_recent'] ?? 0);
        $recentDays = (int)($basis['recent_days'] ?? 28);
        $sampleMonths = (int)($basis['sample_months'] ?? 0);
        $daysUntil = $this->diffDays($today, $expectedDate);
        $coverDays = $this->normalizeCoverDays($coverDays);
        $horizonDays = $daysUntil + $coverDays;

        $isSurge = $currentStock > 1 && $dailyMonth > 0 && $dailyRecent >= ($dailyMonth * self::SURGE_RATIO);
        $useDaily = ($isSurge && $dailyRecent > 0) ? $dailyRecent : $dailyMonth;

        $demandMonthUntil = $this->demandQty($dailyMonth, $daysUntil);
        $demandSurgeUntil = $this->demandQty($dailyRecent, $daysUntil);
        $demandUseUntil = $this->demandQty($useDaily, $daysUntil);
        $demandMonth = $this->demandQty($dailyMonth, $horizonDays);
        $demandSurge = $this->demandQty($dailyRecent, $horizonDays);
        $demandUse = $this->demandQty($useDaily, $horizonDays);
        $safetyMonth = $this->demandQty($dailyMonth, self::SAFETY_DAYS);
        $safetySurge = $this->demandQty($dailyRecent, self::SAFETY_DAYS);
        $safetyQty = $this->demandQty($useDaily, self::SAFETY_DAYS);

        $remainMonth = $currentStock - $demandMonthUntil;
        $remainSurge = $currentStock - $demandSurgeUntil;
        $remainQty = $currentStock - $demandUseUntil;

        $orderMonth = max(0, $demandMonth - $currentStock);
        $orderSurge = max(0, $demandSurge - $currentStock);
        $orderQty = max(0, $demandUse - $currentStock);

        return [
            'expected_date' => $expectedDate,
            'today' => $today,
            'days_until' => $daysUntil,
            'cover_days' => $coverDays,
            'horizon_days' => $horizonDays,
            'current_stock' => $currentStock,
            'sample_months' => $sampleMonths,
            'completed_month_window' => (int)($basis['completed_month_window'] ?? self::DEFAULT_LOOKBACK_MONTHS),
            'monthly_avg' => round($monthlyAvg, 1),
            'daily_month' => $dailyMonth,
            'recent_days' => $recentDays,
            'daily_recent' => $dailyRecent,
            'is_surge' => $isSurge,
            'use_daily' => $useDaily,
            'applied_basis' => $isSurge ? 'surge' : 'month',
            'demand_until' => $demandUseUntil,
            'demand_month_until' => $demandMonthUntil,
            'demand_surge_until' => $demandSurgeUntil,
            'demand_month' => $demandMonth,
            'demand_surge' => $demandSurge,
            'demand_qty' => $demandUse,
            'safety_days' => self::SAFETY_DAYS,
            'safety_qty' => $safetyQty,
            'safety_qty_month' => $safetyMonth,
            'safety_qty_surge' => $safetySurge,
            'remain_qty' => $remainQty,
            'remain_qty_month' => $remainMonth,
            'remain_qty_surge' => $remainSurge,
            'order_qty' => $orderQty,
            'order_qty_month' => $orderMonth,
            'order_qty_surge' => $orderSurge,
            'summary' => $this->buildSummary(
                $expectedDate,
                $daysUntil,
                $coverDays,
                $currentStock,
                (int)($basis['completed_month_window'] ?? self::DEFAULT_LOOKBACK_MONTHS),
                $sampleMonths,
                $monthlyAvg,
                $dailyMonth,
                $recentDays,
                $dailyRecent,
                $isSurge,
                $remainQty,
                $safetyQty,
                $orderQty
            ),
        ];
    }

    /**
     * 주문서 상품 목록처럼 여러 재고를 같은 입고예상일 기준으로 계산한다.
     *
     * @param array $psIdxList
     * @param string $expectedDate
     * @param mixed $coverDays
     * @param mixed $lookbackMonths
     * @return array
     */
    public function calculateMany(array $psIdxList, string $expectedDate, $coverDays = self::DEFAULT_COVER_DAYS, $lookbackMonths = self::DEFAULT_LOOKBACK_MONTHS): array
    {
        $lookbackMonths = $this->normalizeLookbackMonths($lookbackMonths);
        $coverDays = $this->normalizeCoverDays($coverDays);
        $expectedDate = $this->normalizeDate($expectedDate, '입고예상일');
        if ($expectedDate < date('Y-m-d')) {
            throw new Exception('입고예상일은 오늘 이후 날짜여야 합니다.');
        }

        $results = [];
        foreach ($psIdxList as $psIdx) {
            $psIdx = (int)$psIdx;
            if ($psIdx <= 0 || isset($results[(string)$psIdx])) {
                continue;
            }

            $key = (string)$psIdx;
            try {
                $row = $this->calculate($psIdx, $expectedDate, $coverDays, $lookbackMonths);
                $orderQty = (int)($row['order_qty'] ?? 0);
                $safetyQty = (int)($row['safety_qty'] ?? 0);
                $results[$key] = [
                    'ps_idx' => $psIdx,
                    'order_qty' => $orderQty,
                    'safety_qty' => $safetyQty,
                    'total_qty' => $orderQty + $safetyQty,
                ];
            } catch (Throwable $e) {
                $results[$key] = [
                    'ps_idx' => $psIdx,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * @param float $dailySale
     * @param int $days
     * @return int
     */
    private function demandQty(float $dailySale, int $days): int
    {
        if ($dailySale <= 0 || $days <= 0) {
            return 0;
        }

        return (int)ceil($dailySale * $days);
    }

    /**
     * @param mixed $value
     * @return int
     */
    private function normalizeCoverDays($value): int
    {
        if ($value === null || trim((string)$value) === '') {
            return self::DEFAULT_COVER_DAYS;
        }

        $text = trim((string)$value);
        if (!preg_match('/^\d+$/', $text)) {
            throw new Exception('커버기간은 0 이상의 정수여야 합니다.');
        }

        $days = (int)$text;
        if ($days > 365) {
            throw new Exception('커버기간은 365일 이하로 입력해 주세요.');
        }

        return $days;
    }

    /**
     * @param mixed $value
     * @return int
     */
    private function normalizeLookbackMonths($value): int
    {
        if ($value === null || trim((string)$value) === '') {
            return self::DEFAULT_LOOKBACK_MONTHS;
        }

        $months = (int)trim((string)$value);
        if (!in_array($months, [3, 6, 12], true)) {
            throw new Exception('계산개월은 최근 3개월, 6개월, 1년 중에서 선택해 주세요.');
        }

        return $months;
    }

    /**
     * @param string $value
     * @param string $label
     * @return string
     */
    private function normalizeDate(string $value, string $label): string
    {
        $value = trim($value);
        $date = \DateTime::createFromFormat('Y-m-d', $value);
        if (!$date || $date->format('Y-m-d') !== $value) {
            throw new Exception($label . ' 형식이 올바르지 않습니다.');
        }

        return $value;
    }

    /**
     * @param string $startDay
     * @param string $endDay
     * @return int
     */
    private function diffDays(string $startDay, string $endDay): int
    {
        $from = new \DateTime($startDay);
        $to = new \DateTime($endDay);

        return (int)$from->diff($to)->days;
    }

    /**
     * @param string $expectedDate
     * @param int $daysUntil
     * @param int $coverDays
     * @param int $currentStock
     * @param int $lookbackMonths
     * @param int $sampleMonths
     * @param float $monthlyAvg
     * @param float $dailyMonth
     * @param int $recentDays
     * @param float $dailyRecent
     * @param bool $isSurge
     * @param int $remainQty
     * @param int $safetyQty
     * @param int $orderQty
     * @return string
     */
    private function buildSummary(
        string $expectedDate,
        int $daysUntil,
        int $coverDays,
        int $currentStock,
        int $lookbackMonths,
        int $sampleMonths,
        float $monthlyAvg,
        float $dailyMonth,
        int $recentDays,
        float $dailyRecent,
        bool $isSurge,
        int $remainQty,
        int $safetyQty,
        int $orderQty
    ): string {
        $basisText = $isSurge
            ? '최근 ' . $recentDays . '일 일판매 ' . $dailyRecent . '개(급판매)'
            : '월평균 일판매 ' . $dailyMonth . '개';

        $monthText = '최근 ' . $lookbackMonths . '개월';
        if ($sampleMonths !== $lookbackMonths) {
            $monthText .= ' 중 정상월 ' . $sampleMonths . '개월';
        }

        return '입고예상일 ' . $expectedDate . '까지 ' . $daysUntil . '일, 커버 ' . $coverDays . '일. '
            . $monthText . ' 월평균 ' . round($monthlyAvg, 1) . '건. '
            . $basisText . ' 기준 현재고 ' . $currentStock . '개의 입고일 잔여는 ' . $remainQty . '개, 필요수량은 ' . $orderQty . '개 + 안전재고 ' . $safetyQty . '개입니다.';
    }
}
