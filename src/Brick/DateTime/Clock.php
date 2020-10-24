<?php

declare(strict_types=1);

namespace Solcik\Brick\DateTime;

use Brick\DateTime\Clock as BrickClock;
use Brick\DateTime\Instant;
use Brick\DateTime\LocalDate;
use Brick\DateTime\LocalDateTime;
use Brick\DateTime\LocalTime;
use Brick\DateTime\TimeZone;
use Brick\DateTime\Year;
use Brick\DateTime\YearMonth;
use Brick\DateTime\YearWeek;
use Brick\DateTime\ZonedDateTime;

interface Clock
{
    public function getTimeZone(): TimeZone;

    public function getClock(): BrickClock;

    public function createInstant(): Instant;

    public function createZonedDateTime(): ZonedDateTime;

    public function createLocalDateTime(): LocalDateTime;

    public function createYear(): Year;

    public function createYearMonth(): YearMonth;

    public function createYearWeek(): YearWeek;

    public function createLocalDate(): LocalDate;

    public function createLocalTime(): LocalTime;
}
