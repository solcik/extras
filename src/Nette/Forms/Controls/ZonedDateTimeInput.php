<?php

declare(strict_types=1);

namespace Solcik\Nette\Forms\Controls;

use Brick\DateTime\LocalDateTime;
use Brick\DateTime\TimeZone;
use Brick\DateTime\ZonedDateTime;

class ZonedDateTimeInput extends LocalDateTimeInput
{
    public static string $timezone = 'Europe/Prague';

    #[\Override]
    public function getValue(): mixed
    {
        $val = parent::getValue();
        if (!$this->isValidated) {
            return $val;
        }

        if ($val === null || $val === '') {
            return null;
        }

        assert($val instanceof LocalDateTime);

        return $val->atTimeZone(TimeZone::parse(self::$timezone));
    }

    #[\Override]
    public function setValue($value)
    {
        if ($value === null) {
            parent::setValue(null);
        } elseif ($value instanceof ZonedDateTime) {
            parent::setValue($value->getDateTime()->__toString());

            $this->validate();
        } else {
            parent::setValue($value);
        }

        return $this;
    }
}
