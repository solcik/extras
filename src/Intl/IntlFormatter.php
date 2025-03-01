<?php

declare(strict_types=1);

namespace Solcik\Intl;

use Brick\Math\BigNumber;
use Locale;
use Nette\Localization\Translator as NetteTranslator;
use NumberFormatter;
use RuntimeException;
use Symfony\Component\Translation\Translator;

use function sprintf;

final readonly class IntlFormatter
{
    private string $locale;

    public function __construct(NetteTranslator $translator)
    {
        assert($translator instanceof Translator);
        Locale::setDefault($translator->getLocale());
        $this->locale = Locale::getDefault();
    }

    public function percentage(BigNumber $number, int $scale = 0): string
    {
        $fmt = new NumberFormatter($this->locale, NumberFormatter::PERCENT);
        $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, $scale);

        $str = $fmt->format($number->toFloat());
        if ($str === false) {
            throw new RuntimeException(sprintf(
                'NumberFormatter error for value: \'%s\'',
                $number->__toString()
            ));
        }

        return $str;
    }

    /**
     * @param BigNumber|int|float $value
     */
    public function number($value, int $scale = 0, string $grouping = ' '): string
    {
        if ($value instanceof BigNumber) {
            $value = $value->toFloat();
        }

        $fmt = new NumberFormatter($this->locale, NumberFormatter::DECIMAL);
        $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, $scale);
        $fmt->setAttribute(NumberFormatter::GROUPING_USED, 1);
        $fmt->setSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL, $grouping);

        $str = $fmt->format($value);
        if ($str === false) {
            throw new RuntimeException(sprintf('NumberFormatter error for value: \'%s\'', $value));
        }

        return $str;
    }
}
