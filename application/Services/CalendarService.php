<?php

namespace App\Services;

use App\Core\AuthAdmin;
use App\Models\CalendarModel;
use App\Models\ScheduleSttafModel;
use App\Models\AdminModel;

class CalendarService
{
    /**
     * 메인 달력 뷰 데이터 생성
     *
     * @param array $requestData
     * @return array
     */
    public function buildCalendarViewData(array $requestData): array
    {
        $todayYear = (int)date('Y');
        $todayMonth = (int)date('n');
        $todayDay = (int)date('j');

        $year = (int)($requestData['y'] ?? $todayYear);
        $month = (int)($requestData['m'] ?? $todayMonth);
        $day = (int)($requestData['day'] ?? $todayDay);

        if ($year < 2000 || $year > 2100) {
            $year = $todayYear;
        }
        if ($month < 1 || $month > 12) {
            $month = $todayMonth;
        }

        $calendarViewInput = $requestData['calendar_view'] ?? [];
        if (!is_array($calendarViewInput)) {
            $calendarViewInput = is_string($calendarViewInput) ? [$calendarViewInput] : [];
        }
        $calendarViewInput = array_values(array_filter(array_map('trim', array_map('strval', $calendarViewInput))));

        $defaultViewKeys = [
            'delivery',
            'tax',
            'staff_meeting',
            'meeting',
            'schedule',
            'check',
            'point',
            'event',
            'individual',
            'holiday',
        ];

        $calendarViewMap = [];
        if (empty($calendarViewInput)) {
            foreach ($defaultViewKeys as $key) {
                $calendarViewMap[$key] = 'show';
            }
        } else {
            foreach ($calendarViewInput as $key) {
                $calendarViewMap[$key] = 'show';
            }
        }

        $prevMonth = $month - 1;
        $nextMonth = $month + 1;
        $prevYear = $year;
        $nextYear = $year;

        if ($month === 1) {
            $prevMonth = 12;
            $prevYear = $year - 1;
        } elseif ($month === 12) {
            $nextMonth = 1;
            $nextYear = $year + 1;
        }

        $maxDay = (int)date('t', mktime(0, 0, 0, $month, 1, $year));
        $startWeek = (int)date('w', mktime(0, 0, 0, $month, 1, $year));
        $totalWeek = (int)ceil(($maxDay + $startWeek) / 7);
        $lastWeek = (int)date('w', mktime(0, 0, 0, $month, $maxDay, $year));

        $beforeMonthMaxDay = (int)date('t', mktime(0, 0, 0, $month - 1, 1, $year));
        $beforeMonthStartDay = ($beforeMonthMaxDay - $startWeek) + 1;
        $afterMonthEndDay = 6 - $lastWeek;

        $firstDayOfMonth = new \DateTime(sprintf('%04d-%02d-01', $year, $month));
        $startDateObj = clone $firstDayOfMonth;
        if ($startWeek > 0) {
            $startDateObj->modify('-' . $startWeek . ' days');
        }
        $endDateObj = clone $startDateObj;
        $endDateObj->modify('+' . (($totalWeek * 7) - 1) . ' days');

        $startDate = $startDateObj->format('Y-m-d');
        $endDate = $endDateObj->format('Y-m-d');

        $adIdx = (int)(AuthAdmin::getSession('sess_idx') ?? 0);

        $staffHolidayByDay = [];
        if (($calendarViewMap['holiday'] ?? '') === 'show') {
            $holidayRows = ScheduleSttafModel::query()
                ->where('date_s', '>=', $startDate . ' 00:00:00')
                ->where('date_e', '<=', $endDate . ' 23:59:59')
                ->orderBy('idx', 'desc')
                ->get()
                ->toArray();

            foreach ($holidayRows as $row) {
                $meta = json_decode($row['data'] ?? '{}', true);
                if (!is_array($meta)) {
                    $meta = [];
                }

                $dayCode = date('Ymd', strtotime((string)($row['date_s'] ?? '')));
                if ($dayCode === '19700101') {
                    continue;
                }

                $staffHolidayByDay[$dayCode][] = [
                    'idx' => $row['idx'] ?? '',
                    'mode' => $row['mode'] ?? '',
                    'target_name' => $meta['target']['name'] ?? '',
                ];
            }
        }

        $calendarQuery = CalendarModel::query()
            ->where('date_s', '>=', $startDate . ' 00:00:00')
            ->where('date_e', '<=', $endDate . ' 23:59:59');

        if (($calendarViewMap['delivery'] ?? '') !== 'show') {
            $calendarQuery->where('kind', '!=', '배송비');
        }
        if (($calendarViewMap['tax'] ?? '') !== 'show') {
            $calendarQuery->where('kind', '!=', '관/부가세');
        }
        if (($calendarViewMap['staff_meeting'] ?? '') !== 'show') {
            $calendarQuery->where('kind', '!=', '회의');
        }
        if (($calendarViewMap['meeting'] ?? '') !== 'show') {
            $calendarQuery->where('kind', '!=', '방문미팅');
            $calendarQuery->where('kind', '!=', '외부미팅');
        }
        if (($calendarViewMap['schedule'] ?? '') !== 'show') {
            $calendarQuery->where('kind', '!=', '일정');
        }
        if (($calendarViewMap['check'] ?? '') !== 'show') {
            $calendarQuery->where('kind', '!=', '체크');
        }
        if (($calendarViewMap['point'] ?? '') !== 'show') {
            $calendarQuery->where('kind', '!=', '중요');
        }
        if (($calendarViewMap['event'] ?? '') !== 'show') {
            $calendarQuery->where('kind', '!=', '행사');
        }

        if (($calendarViewMap['individual'] ?? '') === 'show') {
            $calendarQuery->whereIn('target_idx', [0, $adIdx]);
        } else {
            $calendarQuery->where('target_idx', '=', 0);
        }

        $calendarRows = $calendarQuery
            ->orderBy('idx', 'desc')
            ->get()
            ->toArray();

        $commentDayMap = [];
        $calendarByDay = [];
        foreach ($calendarRows as $row) {
            $dayCode = date('Ymd', strtotime((string)($row['date_s'] ?? '')));
            if ($dayCode === '19700101') {
                continue;
            }

            if (($row['mode'] ?? '') === 'comment') {
                $commentDayMap[$dayCode] = [
                    'idx' => $row['idx'] ?? '',
                    'comment_count' => (int)($row['comment_count'] ?? 0),
                ];
            } else {
                $calendarByDay[$dayCode][] = [
                    'idx' => $row['idx'] ?? '',
                    'mode' => $row['mode'] ?? '',
                    'kind' => $row['kind'] ?? '',
                    'state' => $row['state'] ?? '',
                    'subject' => $row['subject'] ?? '',
                    'data' => $row['data'] ?? '',
                ];
            }
        }

        return [
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'todayYear' => $todayYear,
            'todayMonth' => $todayMonth,
            'todayDay' => $todayDay,
            'prevYear' => $prevYear,
            'prevMonth' => $prevMonth,
            'nextYear' => $nextYear,
            'nextMonth' => $nextMonth,
            'maxDay' => $maxDay,
            'startWeek' => $startWeek,
            'totalWeek' => $totalWeek,
            'lastWeek' => $lastWeek,
            'beforeMonthStartDay' => $beforeMonthStartDay,
            'afterMonthEndDay' => $afterMonthEndDay,
            'staffHolidayByDay' => $staffHolidayByDay,
            'calendarByDay' => $calendarByDay,
            'commentDayMap' => $commentDayMap,
        ];
    }

    /**
     * 캘린더 등록/상세 폼 데이터
     *
     * @param array $requestData
     * @return array
     */
    public function getCalendarRegFormData(array $requestData): array
    {
        $idx = (int)($requestData['idx'] ?? 0);
        $year = (int)($requestData['y'] ?? date('Y'));
        $month = (int)($requestData['m'] ?? date('n'));
        $day = (int)($requestData['d'] ?? date('j'));

        $calendarData = [];
        if ($idx > 0) {
            $row = CalendarModel::find($idx);
            $calendarData = $row ? $row->toArray() : [];
        }

        if ($idx > 0 && !empty($calendarData['date_s'])) {
            $date = date('Y-m-d', strtotime((string)$calendarData['date_s']));
            $dateS = date('Y-m-d\TH:i', strtotime((string)$calendarData['date_s']));
            $dateE = date('Y-m-d\TH:i', strtotime((string)$calendarData['date_e']));
        } else {
            $date = date('Y-m-d', strtotime(sprintf('%04d-%02d-%02d 00:00:00', $year, $month, $day)));
            $dateS = date('Y-m-d\TH:i', strtotime(sprintf('%04d-%02d-%02d 00:00:00', $year, $month, $day)));
            $dateE = $dateS;
        }

        $mentionTarget = AdminModel::query()
            ->select(['idx', 'ad_nick', 'ad_name', 'ad_image'])
            ->where('is_mention', 'Y')
            ->orderBy('idx', 'desc')
            ->get()
            ->toArray();

        $selectedTargetIds = [];
        $targetMbRaw = (string)($calendarData['target_mb'] ?? '');
        if ($targetMbRaw !== '') {
            $selectedTargetIds = array_values(array_filter(explode('@', ltrim($targetMbRaw, '@')), static function ($v) {
                return trim((string)$v) !== '';
            }));
        }

        return [
            'idx' => $idx,
            'calendarData' => $calendarData,
            'date' => $date,
            'date_s' => $dateS,
            'date_e' => $dateE,
            'calendarKinds' => ["회의", "방문미팅", "외부미팅", "일정", "체크", "중요", "행사", "개인"],
            'mentionTarget' => $mentionTarget,
            'selectedTargetIds' => $selectedTargetIds,
        ];
    }

    /**
     * 캘린더 신규 등록
     *
     * @param array $requestData
     * @return array
     */
    public function createCalendar(array $requestData): array
    {
        $subject = trim((string)($requestData['subject'] ?? ''));
        $open = trim((string)($requestData['open'] ?? '전체공개'));
        $kind = trim((string)($requestData['kind'] ?? '일정'));
        $state = trim((string)($requestData['state'] ?? 'I'));
        $mode = trim((string)($requestData['mode'] ?? '일반'));
        $dateS = trim((string)($requestData['date_s'] ?? ''));
        $dateE = trim((string)($requestData['date_e'] ?? ''));
        $memo = trim((string)($requestData['memo'] ?? ''));

        if ($subject === '') {
            throw new \InvalidArgumentException('제목을 입력해주세요.');
        }
        if ($dateS === '' || $dateE === '') {
            throw new \InvalidArgumentException('일시를 입력해주세요.');
        }

        $adIdx = (int)(AuthAdmin::getSession('sess_idx') ?? 0);
        $targetIdx = ($open === '개인') ? $adIdx : 0;
        if ($open === '개인') {
            $kind = '개인';
        }

        $regInfo = AuthAdmin::getConnectionInfo();
        $reg = json_encode(['reg' => $regInfo], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $insertId = CalendarModel::query()->insert([
            'subject' => $subject,
            'open' => $open,
            'target_idx' => $targetIdx,
            'kind' => $kind,
            'mode' => $mode,
            'state' => $state,
            'date_s' => $this->normalizeDateTime($dateS),
            'date_e' => $this->normalizeDateTime($dateE),
            'memo' => $memo,
            'comment_count' => 0,
            'reg' => $reg,
        ]);

        return [
            'success' => true,
            'msg' => '완료',
            'key' => $insertId,
        ];
    }

    /**
     * 캘린더 수정
     *
     * @param array $requestData
     * @return array
     */
    public function saveCalendar(array $requestData): array
    {
        $idx = (int)($requestData['idx'] ?? 0);
        if ($idx <= 0) {
            throw new \InvalidArgumentException('수정할 일정이 없습니다.');
        }

        $row = CalendarModel::find($idx);
        $calendarData = $row ? $row->toArray() : null;
        if (empty($calendarData)) {
            throw new \RuntimeException('일정 정보를 찾을 수 없습니다.');
        }

        $subject = trim((string)($requestData['subject'] ?? ''));
        $open = trim((string)($requestData['open'] ?? '전체공개'));
        $kind = trim((string)($requestData['kind'] ?? '일정'));
        $state = trim((string)($requestData['state'] ?? 'I'));
        $mode = trim((string)($requestData['mode'] ?? ($calendarData['mode'] ?? '일반')));
        $dateS = trim((string)($requestData['date_s'] ?? ''));
        $dateE = trim((string)($requestData['date_e'] ?? ''));
        $memo = trim((string)($requestData['memo'] ?? ''));

        if ($subject === '') {
            throw new \InvalidArgumentException('제목을 입력해주세요.');
        }
        if ($dateS === '' || $dateE === '') {
            throw new \InvalidArgumentException('일시를 입력해주세요.');
        }

        $adIdx = (int)(AuthAdmin::getSession('sess_idx') ?? 0);
        $targetIdx = ($open === '개인') ? $adIdx : 0;
        if ($open === '개인') {
            $kind = '개인';
        }

        $targetMbIds = $requestData['target_mb_id'] ?? [];
        if (!is_array($targetMbIds)) {
            $targetMbIds = [];
        }
        $targetMbIds = array_values(array_filter(array_map('strval', $targetMbIds), static function ($v) {
            return trim($v) !== '';
        }));
        $targetMb = '';
        foreach ($targetMbIds as $mbId) {
            $targetMb .= '@' . $mbId;
        }

        $regJson = json_decode((string)($calendarData['reg'] ?? '{}'), true);
        if (!is_array($regJson)) {
            $regJson = [];
        }
        if (!isset($regJson['mod']) || !is_array($regJson['mod'])) {
            $regJson['mod'] = [];
        }
        $regJson['mod'][] = AuthAdmin::getConnectionInfo();
        $reg = json_encode($regJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        CalendarModel::where('idx', $idx)->update([
            'subject' => $subject,
            'open' => $open,
            'target_idx' => $targetIdx,
            'kind' => $kind,
            'state' => $state,
            'mode' => $mode,
            'date_s' => $this->normalizeDateTime($dateS),
            'date_e' => $this->normalizeDateTime($dateE),
            'target_mb' => $targetMb,
            'memo' => $memo,
            'reg' => $reg,
        ]);

        return [
            'success' => true,
            'msg' => '완료',
            'key' => $idx,
        ];
    }

    /**
     * 캘린더 삭제
     *
     * @param array $requestData
     * @return array
     */
    public function deleteCalendar(array $requestData): array
    {
        $idx = (int)($requestData['idx'] ?? 0);
        if ($idx <= 0) {
            throw new \InvalidArgumentException('삭제할 일정이 없습니다.');
        }

        $row = CalendarModel::find($idx);
        $calendarData = $row ? $row->toArray() : null;
        if (empty($calendarData)) {
            throw new \RuntimeException('일정 정보를 찾을 수 없습니다.');
        }

        CalendarModel::where('idx', $idx)->delete();

        return [
            'success' => true,
            'msg' => '삭제되었습니다.',
            'key' => $idx,
        ];
    }

    private function normalizeDateTime(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return date('Y-m-d H:i:s');
        }

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return date('Y-m-d H:i:s');
        }

        return date('Y-m-d H:i:s', $timestamp);
    }
}
