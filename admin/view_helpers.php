<?php

if (!function_exists('admin_in_sale_icon')) {
    /**
     * 할인 아이콘 HTML을 생성한다.
     *
     * @param mixed $psInSaleS 할인 시작일시(Unix timestamp 또는 날짜 문자열)
     * @param mixed $psInSaleE 할인 종료일시(Unix timestamp 또는 날짜 문자열)
     * @param mixed $psInSaleData 할인 데이터 JSON 문자열 또는 배열
     * @return string
     */
    function admin_in_sale_icon($psInSaleS, $psInSaleE, $psInSaleData): string
    {
        $toTimestamp = static function ($value): int {
            if ($value === null || $value === '') {
                return 0;
            }
            if (is_numeric($value)) {
                return (int)$value;
            }
            $ts = strtotime((string)$value);
            return $ts !== false ? $ts : 0;
        };

        $startTs = $toTimestamp($psInSaleS);
        $endTs = $toTimestamp($psInSaleE);
        $nowTs = time();

        if ($startTs <= 0 || $endTs <= 0 || $nowTs < $startTs || $nowTs > $endTs) {
            return '';
        }

        $decoded = is_array($psInSaleData) ? $psInSaleData : json_decode((string)$psInSaleData, true);
        if (!is_array($decoded)) {
            return '';
        }

        $saleMode = (string)($decoded['sale_mode'] ?? '');
        $salePer = (int)($decoded['sale_per'] ?? 0);
        if ($saleMode === '' || $salePer <= 0) {
            return '';
        }

        $saleName = $saleMode === 'period' ? '기간할인중 ' : '일일할인중 ';
        $safeSaleMode = htmlspecialchars($saleMode, ENT_QUOTES, 'UTF-8');

        return sprintf(
            "<div class='in-sale-icon-wrap'>
                <span class='isi %s'>%s <b>%d</b>%%</span>
                <span class='isi-date'>%s ~ %s</span>
            </div>",
            $safeSaleMode,
            $saleName,
            $salePer,
            date('y.m.d H:i', $startTs),
            date('y.m.d H:i', $endTs)
        );
    }
}
