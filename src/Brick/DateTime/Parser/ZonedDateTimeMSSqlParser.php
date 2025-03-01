<?php

declare(strict_types=1);

namespace Solcik\Brick\DateTime\Parser;

use Brick\DateTime\Parser\IsoParsers;
use Brick\DateTime\Parser\PatternParser;
use Brick\DateTime\Parser\PatternParserBuilder;
use Nette\StaticClass;

final class ZonedDateTimeMSSqlParser
{
    use StaticClass;

    /**
     * Returns a parser for a date-time with offset such as `2019-10-17 11:52:32.040948 +00:00`.
     */
    public static function zonedDateTime(): PatternParser
    {
        static $parser;

        if ($parser instanceof PatternParser) {
            return $parser;
        }

        return $parser = (new PatternParserBuilder())
            ->append(IsoParsers::localDate())
            ->appendLiteral(' ')
            ->append(IsoParsers::localTime())
            ->appendLiteral(' ')
            ->append(IsoParsers::timeZoneOffset())
            ->startOptional()
            ->appendLiteral('[')
            ->append(IsoParsers::timeZoneRegion())
            ->appendLiteral(']')
            ->endOptional()
            ->toParser();
    }
}
