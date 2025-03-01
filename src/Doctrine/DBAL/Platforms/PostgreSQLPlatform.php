<?php

declare(strict_types=1);

namespace Solcik\Doctrine\DBAL\Platforms;

class PostgreSQLPlatform extends \Doctrine\DBAL\Platforms\PostgreSQLPlatform
{
    public function getDateTimeFormatString(): string
    {
        return str_replace('s', 's.u', parent::getDateTimeFormatString());
    }

    public function getDateTimeTzFormatString(): string
    {
        return str_replace('s', 's.u', parent::getDateTimeTzFormatString());
    }

    public function getTimeFormatString(): string
    {
        return str_replace('s', 's.u', parent::getTimeFormatString());
    }
}
