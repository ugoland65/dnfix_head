<?php
$staffHolidayByDay = is_array($staffHolidayByDay ?? null) ? $staffHolidayByDay : [];
$calendarByDay = is_array($calendarByDay ?? null) ? $calendarByDay : [];
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

        $html .= '<ul class="calendar-unit-ul" title="' . $title . '"><span style="cursor:pointer;" onclick="' . $onclick . '">' . $thisSubject . '</span></ul>';
    }

    $html .= "</div>";
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
                echo '<tr>';
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
                        <td class="<?= $holyStyle ?> <?= $todayStyle ?>">
                            <div>
                                <div class="day <?= $holyStyle ?>" onclick="calendar.reg('<?= $year ?>','<?= $month ?>','<?= $dayCursor ?>')"><?= $dayCursor ?></div>
                                <button type="button" id="show_type_all" class="btnstyle1 btnstyle1-xs" onclick="footerGlobal.comment('calendar','<?= $calendarIdx ?>','<?= $dayCode2 ?>')">
                                    댓글
                                    <?php if ($commentCount > 0) { ?> : <b><?= $commentCount ?></b><?php } ?>
                                </button>
                            </div>
                            <?= $renderCalendarContents($dayCode) ?>
                        </td>
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
                        <td>
                            <div class="day-before"><?= $thisDay ?></div>
                            <?= $renderCalendarContents($dayCode) ?>
                        </td>
                        <?php
                    }
                }
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $(".calendar-unit-ul").mouseover(function() {
        $(this).tooltip();
    });
});
</script>
