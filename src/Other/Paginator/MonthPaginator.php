<?php

declare(strict_types=1);

namespace Solcik\Other\Paginator;

use Brick\DateTime\DateTimeException;
use Brick\DateTime\Interval;
use Brick\DateTime\LocalDateRange;
use Brick\DateTime\LocalTime;
use Brick\DateTime\YearMonth;
use Solcik\Brick\DateTime\Clock;

final class MonthPaginator implements DatePaginator
{
    private Clock $clockProvider;

    private YearMonth $yearMonth;

    public function __construct(Clock $clockProvider, YearMonth $yearMonth)
    {
        $this->clockProvider = $clockProvider;
        $this->yearMonth = $yearMonth;
    }

    public static function now(Clock $clockProvider): self
    {
        $yearMonth = $clockProvider->createYearMonth();

        return new self($clockProvider, $yearMonth);
    }

    /**
     * @throws DateTimeException
     */
    public static function of(Clock $clockProvider, ?int $year = null, ?int $month = null): self
    {
        $year ??= $clockProvider->createYear()->getValue();
        $month ??= $clockProvider->createYearMonth()->getMonth();

        $yearMonth = YearMonth::of($year, $month);

        return new self($clockProvider, $yearMonth);
    }

    public function getCurrent(): YearMonth
    {
        return $this->yearMonth;
    }

    /**
     * @return YearMonth[]
     */
    public function getPrevious(int $count = 3): array
    {
        $months = [];
        for ($i = $count; $i >= 1; $i--) {
            $months[] = $this->yearMonth->minusMonths($i);
        }

        return $months;
    }

    /**
     * @return YearMonth[]
     */
    public function getNext(int $count = 3): array
    {
        $maxFuture = $this->clockProvider->createLocalDate();

        $months = [];
        for ($i = 1; $i <= $count; $i++) {
            $nextMonth = $this->yearMonth->plusMonths($i);
            if ($nextMonth->getFirstDay()->isAfter($maxFuture)) {
                break;
            }
            $months[] = $nextMonth;
        }

        return $months;
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
