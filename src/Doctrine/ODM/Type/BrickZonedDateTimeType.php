<?php

declare(strict_types=1);

namespace Solcik\Doctrine\ODM\Type;

use Brick\DateTime\ZonedDateTime;
use Doctrine\ODM\MongoDB\Types\Type;
use MongoDB\BSON\UTCDateTime;

class BrickZonedDateTimeType extends Type
{
    /**
     * This function is only called when your custom type is used
     * as an identifier. For other cases, closureToPHP() will be called.
     */
    public function convertToPHPValue(UTCDateTime $value): ZonedDateTime
    {
        return ZonedDateTime::fromDateTime($value->toDateTime());
    }


    /**
     * Return the string body of a PHP closure that will receive $value
     * and store the result of a conversion in a $return variable
     */
    public function closureToPHP(): string
    {
        return '$return = \Brick\DateTime\ZonedDateTime::fromDateTime($value->toDatetime());';
    }


    /**
     * This is called to convert a PHP value to its Mongo equivalent
     */
    public function convertToDatabaseValue(ZonedDateTime $value): UTCDateTime
    {
        return new UTCDateTime($value->getInstant()->getEpochSecond() * 1000);
    }
}
