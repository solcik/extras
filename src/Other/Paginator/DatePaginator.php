<?php

declare(strict_types=1);

namespace Solcik\Other\Paginator;

use Brick\DateTime\Interval;
use Brick\DateTime\LocalDateRange;
use Brick\DateTime\YearMonth;
use Brick\DateTime\YearWeek;

interface DatePaginator
{
    /**
     * @return YearMonth|YearWeek
     */
    public function getCurrent();

    /**
     * @return YearMonth[]|YearWeek[]
     */
    public function getPrevious(int $count = 3): array;

    /**
     * @return YearMonth[]|YearWeek[]
     */
    public function getNext(int $count = 3): array;

    public function getCurrentLocalDateRange(): LocalDateRange;

    public function getCurrentInterval(): Interval;
}
