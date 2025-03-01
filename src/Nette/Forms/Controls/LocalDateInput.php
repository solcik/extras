<?php

declare(strict_types=1);

namespace Solcik\Nette\Forms\Controls;

use Brick\DateTime\LocalDate;
use Brick\DateTime\Parser\DateTimeParseException;
use Brick\DateTime\Parser\DateTimeParser;
use Brick\DateTime\Parser\IsoParsers;
use Nette\Forms\Controls\TextInput;
use Nette\Utils\Html;
use Override;

use function implode;

class LocalDateInput extends TextInput
{
    /**
     * @var string[]
     */
    public static array $additionalHtmlClasses = [];

    /**
     * This errorMessage is added for invalid format.
     */
    public string $invalidFormatMessage = 'Date format is invalid/incorrect.';

    private bool $isValidated = false;

    private readonly DateTimeParser $parser;

    public function __construct(?string $label = null, ?DateTimeParser $parser = null)
    {
        parent::__construct($label, null);

        $this->parser = $parser ?? IsoParsers::localDate();

        $this->addRule(
            function (self $input): bool {
                try {
                    $this->parser->parse($input->value);

                    return true;
                } catch (DateTimeParseException) {
                    return false;
                }
            },
            $this->invalidFormatMessage
        );

        $this->setHtmlType('date');
    }

    public function setInvalidFormatMessage(string $invalidFormatMessage): static
    {
        $this->invalidFormatMessage = $invalidFormatMessage;

        return $this;
    }

    #[Override]
    public function cleanErrors(): void
    {
        $this->isValidated = false;
    }

    #[Override]
    public function getValue(): mixed
    {
        $val = parent::getValue();
        if (!$this->isValidated) {
            return $val;
        }

        if ($val === null || $val === '') {
            return null;
        }

        return LocalDate::parse($val, $this->parser);
    }

    #[Override]
    public function setValue($value)
    {
        if ($value === null) {
            parent::setValue(null);
        } elseif ($value instanceof LocalDate) {
            parent::setValue($value->__toString());

            $this->validate();
        } else {
            parent::setValue($value);
        }

        return $this;
    }

    #[Override]
    public function getControl(): Html
    {
        $control = parent::getControl();
        $control->class .= ' ' . implode(' ', static::$additionalHtmlClasses);

        return $control;
    }

    #[Override]
    public function validate(): void
    {
        parent::validate();

        $this->isValidated = true;
    }
}
