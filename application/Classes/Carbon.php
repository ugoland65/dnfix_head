<?php

namespace App\Classes;

use DateTime;
use DateTimeZone;
use Exception;

/**
 * Carbon - Laravel과 유사한 날짜/시간 처리 클래스
 */
class Carbon extends DateTime
{
    /**
     * 기본 날짜 형식
     */
    const DEFAULT_TO_STRING_FORMAT = 'Y-m-d H:i:s';
    
    /**
     * 생성자
     * 
     * @param string|null $time
     * @param DateTimeZone|string|null $timezone
     */
    public function __construct($time = null, $timezone = null)
    {
        if ($timezone && is_string($timezone)) {
            $timezone = new DateTimeZone($timezone);
        }
        
        parent::__construct($time ?: 'now', $timezone);
    }
    
    /**
     * 현재 시간으로 Carbon 인스턴스 생성
     * 
     * @param DateTimeZone|string|null $timezone
     * @return static
     */
    public static function now($timezone = null)
    {
        return new static(null, $timezone);
    }
    
    /**
     * 오늘 날짜로 Carbon 인스턴스 생성 (시간은 00:00:00)
     * 
     * @param DateTimeZone|string|null $timezone
     * @return static
     */
    public static function today($timezone = null)
    {
        return static::now($timezone)->startOfDay();
    }
    
    /**
     * 어제 날짜로 Carbon 인스턴스 생성
     * 
     * @param DateTimeZone|string|null $timezone
     * @return static
     */
    public static function yesterday($timezone = null)
    {
        return static::today($timezone)->subDay();
    }
    
    /**
     * 내일 날짜로 Carbon 인스턴스 생성
     * 
     * @param DateTimeZone|string|null $timezone
     * @return static
     */
    public static function tomorrow($timezone = null)
    {
        return static::today($timezone)->addDay();
    }
    
    /**
     * 문자열을 파싱하여 Carbon 인스턴스 생성
     * 
     * @param string $time
     * @param DateTimeZone|string|null $timezone
     * @return static
     */
    public static function parse($time, $timezone = null)
    {
        return new static($time, $timezone);
    }
    
    /**
     * 특정 형식으로 파싱하여 Carbon 인스턴스 생성
     * 
     * @param string $format
     * @param string $time
     * @param DateTimeZone|string|null $timezone
     * @return static
     */
    public static function createFromFormat($format, $time, $timezone = null)
    {
        if ($timezone && is_string($timezone)) {
            $timezone = new DateTimeZone($timezone);
        }
        
        $datetime = parent::createFromFormat($format, $time, $timezone);
        
        if (!$datetime) {
            throw new Exception("Could not parse '{$time}' with format '{$format}'");
        }
        
        return static::parse($datetime->format(self::DEFAULT_TO_STRING_FORMAT), $timezone);
    }
    
    /**
     * 날짜 생성
     * 
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @param int $second
     * @param DateTimeZone|string|null $timezone
     * @return static
     */
    public static function create($year = null, $month = null, $day = null, $hour = 0, $minute = 0, $second = 0, $timezone = null)
    {
        $now = static::now($timezone);
        
        $year = $year ?: $now->year;
        $month = $month ?: $now->month;
        $day = $day ?: $now->day;
        
        $dateString = sprintf('%04d-%02d-%02d %02d:%02d:%02d', $year, $month, $day, $hour, $minute, $second);
        
        return new static($dateString, $timezone);
    }
    
    /**
     * 하루의 시작 (00:00:00)
     * 
     * @return static
     */
    public function startOfDay()
    {
        return $this->setTime(0, 0, 0);
    }
    
    /**
     * 하루의 끝 (23:59:59)
     * 
     * @return static
     */
    public function endOfDay()
    {
        return $this->setTime(23, 59, 59);
    }
    
    /**
     * 일 추가
     * 
     * @param int $days
     * @return static
     */
    public function addDays($days)
    {
        return $this->modify("+{$days} days");
    }
    
    /**
     * 일 빼기
     * 
     * @param int $days
     * @return static
     */
    public function subDays($days)
    {
        return $this->modify("-{$days} days");
    }
    
    /**
     * 하루 추가
     * 
     * @return static
     */
    public function addDay()
    {
        return $this->addDays(1);
    }
    
    /**
     * 하루 빼기
     * 
     * @return static
     */
    public function subDay()
    {
        return $this->subDays(1);
    }
    
    /**
     * 월 추가
     * 
     * @param int $months
     * @return static
     */
    public function addMonths($months)
    {
        return $this->modify("+{$months} months");
    }
    
    /**
     * 월 빼기
     * 
     * @param int $months
     * @return static
     */
    public function subMonths($months)
    {
        return $this->modify("-{$months} months");
    }
    
    /**
     * 년 추가
     * 
     * @param int $years
     * @return static
     */
    public function addYears($years)
    {
        return $this->modify("+{$years} years");
    }
    
    /**
     * 년 빼기
     * 
     * @param int $years
     * @return static
     */
    public function subYears($years)
    {
        return $this->modify("-{$years} years");
    }
    
    /**
     * 시간 추가
     * 
     * @param int $hours
     * @return static
     */
    public function addHours($hours)
    {
        return $this->modify("+{$hours} hours");
    }
    
    /**
     * 시간 빼기
     * 
     * @param int $hours
     * @return static
     */
    public function subHours($hours)
    {
        return $this->modify("-{$hours} hours");
    }
    
    /**
     * 분 추가
     * 
     * @param int $minutes
     * @return static
     */
    public function addMinutes($minutes)
    {
        return $this->modify("+{$minutes} minutes");
    }
    
    /**
     * 분 빼기
     * 
     * @param int $minutes
     * @return static
     */
    public function subMinutes($minutes)
    {
        return $this->modify("-{$minutes} minutes");
    }
    
    /**
     * 주 추가
     * 
     * @param int $weeks
     * @return static
     */
    public function addWeeks($weeks)
    {
        return $this->modify("+{$weeks} weeks");
    }
    
    /**
     * 주 빼기
     * 
     * @param int $weeks
     * @return static
     */
    public function subWeeks($weeks)
    {
        return $this->modify("-{$weeks} weeks");
    }
    
    /**
     * 일주일 추가
     * 
     * @return static
     */
    public function addWeek()
    {
        return $this->addWeeks(1);
    }
    
    /**
     * 일주일 빼기
     * 
     * @return static
     */
    public function subWeek()
    {
        return $this->subWeeks(1);
    }
    
    /**
     * 한 달 추가
     * 
     * @return static
     */
    public function addMonth()
    {
        return $this->addMonths(1);
    }
    
    /**
     * 한 달 빼기
     * 
     * @return static
     */
    public function subMonth()
    {
        return $this->subMonths(1);
    }
    
    /**
     * 한 해 추가
     * 
     * @return static
     */
    public function addYear()
    {
        return $this->addYears(1);
    }
    
    /**
     * 한 해 빼기
     * 
     * @return static
     */
    public function subYear()
    {
        return $this->subYears(1);
    }
    
    /**
     * 특정 형식으로 포맷
     * 
     * @param string $format
     * @return string
     */
    public function format($format)
    {
        return parent::format($format);
    }
    
    /**
     * Y-m-d 형식으로 반환
     * 
     * @return string
     */
    public function toDateString()
    {
        return $this->format('Y-m-d');
    }
    
    /**
     * H:i:s 형식으로 반환
     * 
     * @return string
     */
    public function toTimeString()
    {
        return $this->format('H:i:s');
    }
    
    /**
     * Y-m-d H:i:s 형식으로 반환
     * 
     * @return string
     */
    public function toDateTimeString()
    {
        return $this->format(self::DEFAULT_TO_STRING_FORMAT);
    }
    
    /**
     * ISO 8601 형식으로 반환
     * 
     * @return string
     */
    public function toISOString()
    {
        return $this->format('c');
    }
    
    /**
     * 문자열로 변환 시 기본 형식
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->format(self::DEFAULT_TO_STRING_FORMAT);
    }
    
    /**
     * 다른 날짜와 비교 (같은지)
     * 
     * @param Carbon|DateTime|string $date
     * @return bool
     */
    public function equalTo($date)
    {
        if (!$date instanceof DateTime) {
            $date = static::parse($date);
        }
        
        return $this->format('Y-m-d H:i:s') === $date->format('Y-m-d H:i:s');
    }
    
    /**
     * 다른 날짜보다 이후인지
     * 
     * @param Carbon|DateTime|string $date
     * @return bool
     */
    public function greaterThan($date)
    {
        if (!$date instanceof DateTime) {
            $date = static::parse($date);
        }
        
        return $this > $date;
    }
    
    /**
     * 다른 날짜보다 이전인지
     * 
     * @param Carbon|DateTime|string $date
     * @return bool
     */
    public function lessThan($date)
    {
        if (!$date instanceof DateTime) {
            $date = static::parse($date);
        }
        
        return $this < $date;
    }
    
    /**
     * 오늘인지 확인
     * 
     * @return bool
     */
    public function isToday()
    {
        return $this->toDateString() === static::today()->toDateString();
    }
    
    /**
     * 어제인지 확인
     * 
     * @return bool
     */
    public function isYesterday()
    {
        return $this->toDateString() === static::yesterday()->toDateString();
    }
    
    /**
     * 내일인지 확인
     * 
     * @return bool
     */
    public function isTomorrow()
    {
        return $this->toDateString() === static::tomorrow()->toDateString();
    }
    
    /**
     * 주말인지 확인
     * 
     * @return bool
     */
    public function isWeekend()
    {
        return in_array($this->format('w'), [0, 6]); // 0 = Sunday, 6 = Saturday
    }
    
    /**
     * 평일인지 확인
     * 
     * @return bool
     */
    public function isWeekday()
    {
        return !$this->isWeekend();
    }
    
    /**
     * 과거인지 확인
     * 
     * @return bool
     */
    public function isPast()
    {
        return $this < static::now();
    }
    
    /**
     * 미래인지 확인
     * 
     * @return bool
     */
    public function isFuture()
    {
        return $this > static::now();
    }
    
    /**
     * 두 날짜 사이의 차이 (일 단위)
     * 
     * @param Carbon|DateTime|string $date
     * @param bool $absolute
     * @return int
     */
    public function diffInDays($date, $absolute = true)
    {
        if (!$date instanceof DateTime) {
            $date = static::parse($date);
        }
        
        $diff = $this->diff($date);
        $days = $diff->days;
        
        return $absolute ? abs($days) : ($this > $date ? $days : -$days);
    }
    
    /**
     * 두 날짜 사이의 차이 (시간 단위)
     * 
     * @param Carbon|DateTime|string $date
     * @param bool $absolute
     * @return int
     */
    public function diffInHours($date, $absolute = true)
    {
        if (!$date instanceof DateTime) {
            $date = static::parse($date);
        }
        
        $diff = ($this->getTimestamp() - $date->getTimestamp()) / 3600;
        
        return $absolute ? abs($diff) : $diff;
    }
    
    /**
     * 월의 시작
     * 
     * @return static
     */
    public function startOfMonth()
    {
        return static::create($this->year, $this->month, 1, 0, 0, 0, $this->getTimezone());
    }
    
    /**
     * 월의 끝
     * 
     * @return static
     */
    public function endOfMonth()
    {
        return $this->startOfMonth()->addMonth()->subDay()->endOfDay();
    }
    
    /**
     * 년의 시작
     * 
     * @return static
     */
    public function startOfYear()
    {
        return static::create($this->year, 1, 1, 0, 0, 0, $this->getTimezone());
    }
    
    /**
     * 년의 끝
     * 
     * @return static
     */
    public function endOfYear()
    {
        return static::create($this->year, 12, 31, 23, 59, 59, $this->getTimezone());
    }
    
    /**
     * 복사본 생성
     * 
     * @return static
     */
    public function copy()
    {
        return clone $this;
    }
    
    /**
     * 타임스탬프 반환
     * 
     * @return int
     */
    public function timestamp()
    {
        return $this->getTimestamp();
    }
    
    /**
     * 나이 계산 (년 단위)
     * 
     * @param Carbon|DateTime|string|null $date
     * @return int
     */
    public function age($date = null)
    {
        $date = $date ? static::parse($date) : static::now();
        
        return $date->year - $this->year - (($date->format('md') < $this->format('md')) ? 1 : 0);
    }
}
