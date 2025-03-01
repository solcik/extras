<?php

declare(strict_types=1);

namespace Solcik\Brick\DateTime;

use Brick\DateTime\Clock as BrickClock;
use Brick\DateTime\DefaultClock;
use Brick\DateTime\Instant;
use Brick\DateTime\LocalDate;
use Brick\DateTime\LocalDateTime;
use Brick\DateTime\LocalTime;
use Brick\DateTime\TimeZone;
use Brick\DateTime\TimeZoneRegion;
use Brick\DateTime\Year;
use Brick\DateTime\YearMonth;
use Brick\DateTime\YearWeek;
use Brick\DateTime\ZonedDateTime;

final readonly class SystemClock implements Clock
{
    public const string ZONE = 'Europe/Prague';

    private TimeZone $timeZone;

    private BrickClock $clock;

    public function __construct(string $timeZone = self::ZONE)
    {
        $this->timeZone = TimeZoneRegion::parse($timeZone);
        $this->clock = DefaultClock::get();
    }

    public function getTimeZone(): TimeZone
    {
        return $this->timeZone;
    }

    public function getClock(): BrickClock
    {
        return $this->clock;
    }

    public function createInstant(): Instant
    {
        return Instant::now($this->getClock());
    }

    public function createZonedDateTime(): ZonedDateTime
    {
        return ZonedDateTime::now($this->getTimeZone(), $this->getClock());
    }

    public function createLocalDateTime(): LocalDateTime
    {
        return LocalDateTime::now($this->getTimeZone(), $this->getClock());
    }

    public function createYear(): Year
    {
        return Year::now($this->getTimeZone(), $this->getClock());
    }

    public function createYearMonth(): YearMonth
    {
        return YearMonth::now($this->getTimeZone(), $this->getClock());
    }

    public function createYearWeek(): YearWeek
    {
        return YearWeek::now($this->getTimeZone(), $this->getClock());
    }

    public function createLocalDate(): LocalDate
    {
        return LocalDate::now($this->getTimeZone(), $this->getClock());
    }

    public function createLocalTime(): LocalTime
    {
        return LocalTime::now($this->getTimeZone(), $this->getClock());
    }
}
