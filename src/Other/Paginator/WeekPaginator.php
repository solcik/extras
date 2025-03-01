<?php

declare(strict_types=1);

namespace Solcik\Other\Paginator;

use Brick\DateTime\DateTimeException;
use Brick\DateTime\Interval;
use Brick\DateTime\LocalDateRange;
use Brick\DateTime\LocalTime;
use Brick\DateTime\YearWeek;
use Solcik\Brick\DateTime\Clock;

final readonly class WeekPaginator implements DatePaginator
{
    public function __construct(
        private Clock $clockProvider,
        private YearWeek $week,
    ) {
    }

    public static function now(Clock $clockProvider): self
    {
        $current = $clockProvider->createYearWeek();

        return new self($clockProvider, $current);
    }

    /**
     * @throws DateTimeException
     */
    public static function of(Clock $clockProvider, ?int $year = null, ?int $week = null): self
    {
        $year ??= $clockProvider->createYear()->getValue();
        $week ??= $clockProvider->createYearWeek()->getWeek();

        $current = YearWeek::of($year, $week);

        return new self($clockProvider, $current);
    }

    public function getCurrent(): YearWeek
    {
        return $this->week;
    }

    /**
     * @return YearWeek[]
     */
    public function getPrevious(int $count = 3): array
    {
        $weeks = [];
        for ($i = $count; $i >= 1; --$i) {
            $weeks[] = $this->week->minusWeeks($i);
        }

        return $weeks;
    }

    /**
     * @return YearWeek[]
     */
    public function getNext(int $count = 3): array
    {
        $maxFuture = $this->clockProvider->createLocalDate();

        $weeks = [];
        for ($i = 1; $i <= $count; ++$i) {
            $nextWeek = $this->week->plusWeeks($i);
            if ($nextWeek->getFirstDay()->isAfter($maxFuture)) {
                break;
            }
            $weeks[] = $nextWeek;
        }

        return $weeks;
    }

    public function getCurrentLocalDateRange(): LocalDateRange
    {
        $current = $this->getCurrent();
        $start = $current->getFirstDay();
        $end = $current->getLastDay();

        return LocalDateRange::of($start, $end);
    }

    public function getCurrentInterval(): Interval
    {
        $current = $this->getCurrent();
        $start = $current
            ->getFirstDay()
            ->atTime(LocalTime::min())
            ->atTimeZone($this->clockProvider->getTimeZone())
            ->getInstant()
        ;
        $end = $current
            ->getLastDay()
            ->atTime(LocalTime::max())
            ->atTimeZone($this->clockProvider->getTimeZone())
            ->getInstant()
        ;

        return new Interval($start, $end);
    }
}
