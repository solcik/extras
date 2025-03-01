<?php

declare(strict_types=1);

namespace Solcik\Doctrine\DBAL\Platforms;

class PostgreSQLPlatform extends \Doctrine\DBAL\Platforms\PostgreSQLPlatform
{
    #[\Override]
    public function getDateTimeFormatString(): string
    {
        return str_replace('s', 's.u', parent::getDateTimeFormatString());
    }

    #[\Override]
    public function getDateTimeTzFormatString(): string
    {
        return str_replace('s', 's.u', parent::getDateTimeTzFormatString());
    }

    #[\Override]
    public function getTimeFormatString(): string
    {
        return str_replace('s', 's.u', parent::getTimeFormatString());
    }
}
