<?php

declare(strict_types=1);

namespace Solcik\Nette\Forms\Controls;

use Brick\DateTime\LocalDate;
use Brick\DateTime\Parser\DateTimeParseException;
use Brick\DateTime\Parser\DateTimeParser;
use Brick\DateTime\Parser\IsoParsers;
use Nette\Forms\Controls\TextInput;
use Nette\Utils\Html;

class LocalDateInput extends TextInput
{
    /**
     * @var string[]
     */
    public static array $additionalHtmlClasses = [];

    /**
     * This errorMessage is added for invalid format
     */
    public string $invalidFormatMessage = 'Date format is invalid/incorrect.';

    private bool $isValidated = false;

    private DateTimeParser $parser;

    public function __construct(?string $label = null, ?DateTimeParser $parser = null)
    {
        parent::__construct($label, null);

        $this->parser = $parser ?? IsoParsers::localDate();

        $this->addRule(
            function (self $input): bool {
                try {
                    $this->parser->parse($input->value);

                    return true;
                } catch (DateTimeParseException $e) {
                    return false;
                }
            },
            $this->invalidFormatMessage
        );

        $this->setHtmlType('date');
    }

    /**
     * @return static
     */
    public function setInvalidFormatMessage(string $invalidFormatMessage)
    {
        $this->invalidFormatMessage = $invalidFormatMessage;

        return $this;
    }

    public function cleanErrors(): void
    {
        $this->isValidated = false;
    }

    public function getValue()
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

    public function getControl(): Html
    {
        $control = parent::getControl();
        $control->class[] = implode(' ', static::$additionalHtmlClasses);

        return $control;
    }

    public function validate(): void
    {
        parent::validate();

        $this->isValidated = true;
    }
}
