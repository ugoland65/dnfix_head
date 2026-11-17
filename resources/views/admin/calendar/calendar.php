<?php
$staffHolidayByDay = is_array($staffHolidayByDay ?? null) ? $staffHolidayByDay : [];
$calendarByDay = is_array($calendarByDay ?? null) ? $calendarByDay : [];
$calendarWeekBars = is_array($calendarWeekBars ?? null) ? $calendarWeekBars : [];
$commentDayMap = is_array($commentDayMap ?? null) ? $commentDayMap : [];

$renderCalendarContents = function ($dayCode) use ($staffHolidayByDay, $calendarByDay) {
    $html = '';

    $html .= "<div class='m-t-10'>";
    $staffHolidayList = $staffHolidayByDay[$dayCode] ?? [];
    foreach ($staffHolidayList as $item) {
        $idx = (int)($item['idx'] ?? 0);
        $mode = htmlspecialchars((string)($item['mode'] ?? ''), ENT_QUOTES, 'UTF-8');
        $targetName = htmlspecialchars((string)($item['target_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $html .= "
            <ul class='calendar-unit-ul'>
                <span style='cursor:pointer;' onclick='onlyAD.staffHolidayView({$idx})'>
                    <i class='fas fa-user-alt-slash' style=' font-size:10px !important;'></i> {$mode} - {$targetName}
                </span>
            </ul>";
    }
    $html .= "</div>";

    $html .= "<div>";
    $calendarList = $calendarByDay[$dayCode] ?? [];
    foreach ($calendarList as $val) {
        $mode = (string)($val['mode'] ?? '');
        $kind = (string)($val['kind'] ?? '');
        $state = (string)($val['state'] ?? '');
        $subject = (string)($val['subject'] ?? '');
        $calendarIdx = (int)($val['idx'] ?? 0);
        $unitClass = 'calendar-unit-ul';

        if ($mode === '결제기한' && ($kind === '배송비' || $kind === '관/부가세')) {
            $icon = '';
            if ($kind === '배송비') {
                $icon = '<i class="fas fa-truck" style="font-size:10px !important;" ></i>(배) ';
            } elseif ($kind === '관/부가세') {
                $icon = '<i class="fas fa-receipt"></i>(관) ';
            }

            $thisData = json_decode((string)($val['data'] ?? '{}'), true);
            if (!is_array($thisData)) {
                $thisData = [];
            }
            $ooIdx = (string)($thisData['oo_idx'] ?? '');
            $priceText = number_format((float)($thisData['price'] ?? 0));
            $thisSubject = $icon . ' ' . $priceText;
            if ($state === 'E') {
                $thisSubject = "<s> <font class='calendar-approval-state-end'>{$icon} {$priceText}</font> </s>";
            }

            $onclick = "onlyAD.orderSheetView('{$ooIdx}', 'global');";
            $title = $priceText;
        } elseif ($mode === '주문서예상') {
            $icon = $kind === '입고예상'
                ? '<i class="fas fa-box" style="font-size:10px !important;"></i> '
                : '<i class="fas fa-clipboard-list" style="font-size:10px !important;"></i> ';
            $thisData = json_decode((string)($val['data'] ?? '{}'), true);
            if (!is_array($thisData)) {
                $thisData = [];
            }
            $ooIdx = (int)($thisData['oo_idx'] ?? 0);
            $safeKind = htmlspecialchars($kind, ENT_QUOTES, 'UTF-8');
            $safeSubject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
            $thisSubject = $icon . $safeKind . ' ' . $safeSubject;
            if ($state === 'E') {
                $thisSubject = "<font class='calendar-approval-state-end'>{$thisSubject}</font>";
            }
            $onclick = "onlyAD.orderSheetView('{$ooIdx}', 'global');";
            $title = htmlspecialchars(trim($kind . ' ' . $subject), ENT_QUOTES, 'UTF-8');
            $unitClass .= $kind === '입고예상' ? ' calendar-unit-inbound' : ' calendar-unit-order';
        } else {
            $icon = '';
            switch ($kind) {
                case '회의':
                    $icon = '<i class="far fa-comment-dots"></i> ';
                    break;
                case '방문미팅':
                case '외부미팅':
                    $icon = '<i class="far fa-handshake"></i> ';
                    break;
                case '일정':
                    $icon = '<i class="fas fa-hiking"></i> ';
                    break;
                case '체크':
                    $icon = '<i class="fas fa-calendar-check"></i> ';
                    break;
                case '중요':
                    $icon = '<i class="fas fa-star"></i> ';
                    break;
                case '행사':
                    $icon = '<i class="fas fa-democrat"></i> ';
                    break;
                case '개인':
                    $icon = '<i class="fas fa-tag"></i> ';
                    break;
            }

            if ($state === 'C') {
                $thisSubject = '<s>[' . htmlspecialchars($kind, ENT_QUOTES, 'UTF-8') . '] ' . htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') . '</s>';
            } elseif ($state === 'E') {
                $thisSubject = "<font class='calendar-approval-state-end'>{$icon} " . htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') . '</font>';
            } else {
                $thisSubject = $icon . ' ' . htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
            }

            $onclick = "calendar.detail(this, '{$calendarIdx}');";
            $title = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
        }

        $html .= '<ul class="' . $unitClass . '" title="' . $title . '"><span style="cursor:pointer;" onclick="' . $onclick . '">' . $thisSubject . '</span></ul>';
    }

    $html .= "</div>";
    return $html;
};

$renderSpanBars = function (array $bars): string {
    $kindClassMap = [
        '회의' => 'is-staff-meeting',
        '방문미팅' => 'is-meeting',
        '외부미팅' => 'is-meeting',
        '일정' => 'is-schedule',
        '체크' => 'is-check',
        '중요' => 'is-point',
        '행사' => 'is-event',
        '개인' => 'is-individual',
        '배송비' => 'is-delivery',
        '관/부가세' => 'is-tax',
    ];
    $html = '';
    foreach ($bars as $bar) {
        if (!is_array($bar)) {
            continue;
        }
        $mode = (string)($bar['mode'] ?? '');
        $kind = (string)($bar['kind'] ?? '');
        $state = (string)($bar['state'] ?? '');
        $subject = (string)($bar['subject'] ?? '');
        $calendarIdx = (int)($bar['idx'] ?? 0);
        $startCol = (int)($bar['startCol'] ?? 0);
        $span = max(1, (int)($bar['span'] ?? 1));
        $lane = (int)($bar['lane'] ?? 0);
        $continuesBefore = !empty($bar['continuesBefore']);
        $continuesAfter = !empty($bar['continuesAfter']);

        if ($mode === '결제기한' && ($kind === '배송비' || $kind === '관/부가세')) {
            $icon = $kind === '배송비'
                ? '<i class="fas fa-truck"></i>'
                : '<i class="fas fa-receipt"></i>';
            $thisData = json_decode((string)($bar['data'] ?? '{}'), true);
            if (!is_array($thisData)) {
                $thisData = [];
            }
            $priceText = number_format((float)($thisData['price'] ?? 0));
            $labelText = trim($kind . ' ' . $priceText);
            $labelHtml = $icon . ' ' . htmlspecialchars($labelText, ENT_QUOTES, 'UTF-8');
            $onclick = "onlyAD.orderSheetView('" . htmlspecialchars((string)($thisData['oo_idx'] ?? ''), ENT_QUOTES, 'UTF-8') . "', 'global');";
        } else {
            $icon = '';
            switch ($kind) {
                case '회의':
                    $icon = '<i class="far fa-comment-dots"></i>';
                    break;
                case '방문미팅':
                case '외부미팅':
                    $icon = '<i class="far fa-handshake"></i>';
                    break;
                case '일정':
                    $icon = '<i class="fas fa-hiking"></i>';
                    break;
                case '체크':
                    $icon = '<i class="fas fa-calendar-check"></i>';
                    break;
                case '중요':
                    $icon = '<i class="fas fa-star"></i>';
                    break;
                case '행사':
                    $icon = '<i class="fas fa-democrat"></i>';
                    break;
                case '개인':
                    $icon = '<i class="fas fa-tag"></i>';
                    break;
            }
            $labelText = trim($kind . ' ' . $subject);
            $labelHtml = $icon . ($icon !== '' ? ' ' : '') . htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
            $onclick = "calendar.detail(this, '{$calendarIdx}');";
        }

        if ($state === 'C') {
            $labelHtml = '<s>' . $labelHtml . '</s>';
        }

        $startText = substr((string)($bar['start_date'] ?? ''), 5);
        $endText = substr((string)($bar['end_date'] ?? ''), 5);
        $title = $labelText;
        if ($startText !== '' && $endText !== '') {
            $title .= ' (' . str_replace('-', '.', $startText) . ' ~ ' . str_replace('-', '.', $endText) . ')';
        }

        $classes = ['calendar-span-bar', $kindClassMap[$kind] ?? 'is-default'];
        if ($continuesBefore) {
            $classes[] = 'is-continued-before';
        }
        if ($continuesAfter) {
            $classes[] = 'is-continued-after';
        }
        if ($state === 'E') {
            $classes[] = 'is-end';
        }
        if ($state === 'C') {
            $classes[] = 'is-cancel';
        }

        $style = 'grid-column:' . ($startCol + 1) . ' / span ' . $span . ';grid-row:' . ($lane + 1) . ';';
        $html .= '<div class="' . implode(' ', $classes) . '" style="' . $style . '" title="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '" onclick="' . htmlspecialchars($onclick, ENT_QUOTES, 'UTF-8') . '"><span>' . $labelHtml . '</span></div>';
    }

    return $html;
};
?>

<style type="text/css">
.calendal{ width:100%; margin:0 auto; font-size:0; vertical-align:top; overflow:hidden; }
.week_name{ vertical-align:top; height:30px; background-color:#fff; text-align:center;  line-height:30px; font-size: 14px; display:inline-block; box-sizing:border-box; border-bottom:1px solid #444; border-right:1px solid #444; }
.calendal > div.box{ vertical-align:top; min-height:130px; display:inline-block; box-sizing:border-box; border-top:1px solid #444; border-right:1px solid #444; padding:5px 0 0 5px;  }
.holiday-ok{ width:12.5%; }
.holiday-no{ width:15%; }
.day.holiday-no{ background-color:#fff; }
.calendal-title{ text-align:center; }
.calendal-title .ym{ font-size:17px; font-weight:600;  }
.calendal-table{ width:100%; border-spacing:0; border-collapse:collapse; padding:0; margin:0; border:none; table-layout:fixed; box-sizing:border-box;  }
.calendal-table tr.info th{ height:28px; border:1px solid #666; line-height:30px;  text-align:center; }
.calendal-table tbody tr td { height:115px; border:1px solid #666; padding:5px; vertical-align:top; box-sizing:border-box;  }
.calendal-table tbody tr td.black { background-color:#fff; }
.calendal-table tbody tr td.today { background-color:#ffffd9; }
.calendal-table tbody tr td span{ font-size:12px; }
.calendal-table .day{ display:inline-block; width:25px; height:25px; margin:-3px 0 0 -3px; line-height:25px; text-align:center; font-size:13px; font-weight:600; cursor:pointer; border-radius:50%; }
.calendal-table .day-before { width:25px; height:25px; margin:-3px 0 0 -3px; line-height:25px; text-align:center; color:#888; font-size:13px; font-weight:500; }
.calendal-table .day.holy{ color:#ff407a; }
.calendal-table .day.blue{ color:#216eec; }
.calendal-table .day:hover{ background-color:#1b56ff; color:#fff; }
.calendar-approval-state-end{ color:#aaa; }
.calendar-unit-ul { padding:2px; margin-bottom:3px; background-color:#f5f5f5; border:1px solid #eeee; border-radius:4px; }
.calendar-unit-ul span{ font-size:12px !important; display:block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.calendar-unit-ul span i{ font-size:12px !important; width:16px; text-align:center; }
.calendar-unit-ul:hover{ background-color:#fff; border:1px solid #ff0000; }
.calendar-unit-ul:hover span{ color:#ff0000; }
.calendar-unit-order{ background:#e8f1ff; border-color:#c9ddff; }
.calendar-unit-inbound{ background:#e9f8ef; border-color:#b7ebc6; }
.calendal-table tbody tr td.calendar-week-cell{ height:auto; padding:0; border:0; background:transparent; }
.calendar-week-wrap{ position:relative; }
.calendar-week{ display:grid; grid-template-columns:repeat(7, minmax(0, 1fr)); }
.calendar-day{ min-height:115px; box-sizing:border-box; padding:4px 4px 6px; border-right:1px solid #666; border-bottom:1px solid #666; background:#fff; }
.calendar-day:first-child{ border-left:1px solid #666; }
.calendar-day.today{ background:#ffffd9; }
.calendar-day-head{ display:flex; align-items:center; justify-content:space-between; gap:4px; height:28px; margin-bottom:4px; }
.calendar-day-bar-space{ height:calc(var(--calendar-lanes, 0) * 24px); }
.calendar-span-layer{
    position:absolute;
    left:0;
    right:0;
    top:36px;
    display:grid;
    grid-template-columns:repeat(7, minmax(0, 1fr));
    grid-auto-rows:24px;
    pointer-events:none;
    z-index:2;
}
.calendar-span-bar{
    pointer-events:auto;
    margin:2px 4px;
    height:20px;
    padding:0 6px;
    border-radius:4px;
    box-sizing:border-box;
    font-size:12px;
    line-height:20px;
    color:#1d2939;
    background:#e8eefc;
    cursor:pointer;
    overflow:hidden;
}
.calendar-span-bar span{ display:block; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; }
.calendar-span-bar span i{ font-size:11px !important; width:14px; text-align:center; }
.calendar-span-bar.is-continued-before{ margin-left:0; border-top-left-radius:0; border-bottom-left-radius:0; }
.calendar-span-bar.is-continued-after{ margin-right:0; border-top-right-radius:0; border-bottom-right-radius:0; }
.calendar-span-bar.is-end,
.calendar-span-bar.is-cancel{ color:#98a2b3; background:#f2f4f7; }
.calendar-span-bar.is-cancel span{ text-decoration:line-through; }
.calendar-span-bar.is-staff-meeting{ background:#dbeafe; }
.calendar-span-bar.is-meeting{ background:#fce7f3; }
.calendar-span-bar.is-schedule{ background:#dcfce7; }
.calendar-span-bar.is-check{ background:#fef3c7; }
.calendar-span-bar.is-point{ background:#fee2e2; }
.calendar-span-bar.is-event{ background:#ede9fe; }
.calendar-span-bar.is-individual{ background:#e5e7eb; }
.calendar-span-bar.is-delivery{ background:#fff4e5; }
.calendar-span-bar.is-tax{ background:#e0f2fe; }
.calendar-span-bar:hover{ outline:1px solid #ff0000; }
</style>

<div>
    <div class="calendal-title">
        <button type="button" class="btnstyle1 btnstyle1-sm" onclick="calendar.view('<?= $prevYear ?>', '<?= $prevMonth ?>')"><i class="fas fa-chevron-circle-left"></i></button>
        <span class="ym"><?= $year ?>년 <?= $month ?>월</span>
        <button type="button" class="btnstyle1 btnstyle1-sm" onclick="calendar.view('<?= $nextYear ?>', '<?= $nextMonth ?>')"><i class="fas fa-chevron-circle-right"></i></button>
    </div>

    <table class="calendal-table m-t-15">
        <tr class="info">
            <th><span style="color:#ff407a;">일</span></th>
            <th>월</th>
            <th>화</th>
            <th>수</th>
            <th>목</th>
            <th>금</th>
            <th><span style="color:#216eec;">토</span></th>
        </tr>
        <tbody>
            <?php
            $dayCursor = 1;
            $beforeMonthCursor = (int)$beforeMonthStartDay;
            $afterMonthCursor = 1;

            for ($i = 1; $i <= (int)$totalWeek; $i++) {
                $weekBars = $calendarWeekBars[$i] ?? [];
                if (!is_array($weekBars)) {
                    $weekBars = [];
                }
                $laneCount = 0;
                foreach ($weekBars as $weekBar) {
                    if (is_array($weekBar)) {
                        $laneCount = max($laneCount, (int)($weekBar['lane'] ?? 0) + 1);
                    }
                }
                echo '<tr><td colspan="7" class="calendar-week-cell"><div class="calendar-week-wrap" style="--calendar-lanes:' . $laneCount . '"><div class="calendar-week">';
                for ($j = 0; $j < 7; $j++) {
                    $isCurrentMonthDay = !(($i === 1 && $j < (int)$startWeek) || ($i === (int)$totalWeek && $j > (int)$lastWeek));

                    if ($isCurrentMonthDay) {
                        if ($j === 0) {
                            $holyStyle = 'holy';
                        } elseif ($j === 6) {
                            $holyStyle = 'blue';
                        } else {
                            $holyStyle = 'black';
                        }

                        $todayStyle = ($year == $todayYear && $month == $todayMonth && $dayCursor == $todayDay) ? 'today' : '';
                        $dayCode = date('Ymd', mktime(0, 0, 0, $month, $dayCursor, $year));
                        $dayCode2 = date('Y-m-d', mktime(0, 0, 0, $month, $dayCursor, $year));

                        $commentInfo = $commentDayMap[$dayCode] ?? [];
                        $calendarIdx = (int)($commentInfo['idx'] ?? 0);
                        $commentCount = (int)($commentInfo['comment_count'] ?? 0);
                        ?>
                        <div class="calendar-day <?= $todayStyle ?>">
                            <div class="calendar-day-head">
                                <div class="day <?= $holyStyle ?>" onclick="calendar.reg('<?= $year ?>','<?= $month ?>','<?= $dayCursor ?>')"><?= $dayCursor ?></div>
                                <button type="button" id="show_type_all" class="btnstyle1 btnstyle1-xs" onclick="footerGlobal.comment('calendar','<?= $calendarIdx ?>','<?= $dayCode2 ?>')">
                                    댓글
                                    <?php if ($commentCount > 0) { ?> : <b><?= $commentCount ?></b><?php } ?>
                                </button>
                            </div>
                            <?php if ($laneCount > 0) { ?>
                                <div class="calendar-day-bar-space"></div>
                            <?php } ?>
                            <?= $renderCalendarContents($dayCode) ?>
                        </div>
                        <?php
                        $dayCursor++;
                    } else {
                        if ($i === 1) {
                            $thisDay = $beforeMonthCursor;
                            $dayCode = date('Ymd', mktime(0, 0, 0, $prevMonth, $thisDay, $prevYear));
                            $beforeMonthCursor++;
                        } else {
                            $thisDay = $afterMonthCursor;
                            $dayCode = date('Ymd', mktime(0, 0, 0, $nextMonth, $thisDay, $nextYear));
                            $afterMonthCursor++;
                        }
                        ?>
                        <div class="calendar-day">
                            <div class="calendar-day-head">
                                <div class="day-before"><?= $thisDay ?></div>
                            </div>
                            <?php if ($laneCount > 0) { ?>
                                <div class="calendar-day-bar-space"></div>
                            <?php } ?>
                            <?= $renderCalendarContents($dayCode) ?>
                        </div>
                        <?php
                    }
                }
                echo '</div>';
                if ($weekBars !== []) {
                    echo '<div class="calendar-span-layer">' . $renderSpanBars($weekBars) . '</div>';
                }
                echo '</div></td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $(".calendar-unit-ul, .calendar-span-bar").mouseover(function() {
        $(this).tooltip();
    });
});
</script>
