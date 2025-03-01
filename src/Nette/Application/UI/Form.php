<?php

declare(strict_types=1);

namespace Solcik\Nette\Application\UI;

use Nette\Application\UI\Form as NetteForm;
use Nette\Forms\Controls\TextInput;
use Override;
use Stringable;

final class Form extends NetteForm
{
    #[Override]
    public function addFloat(string $name, string|Stringable|null $label = null): TextInput
    {
        $input = $this->addText($name, $label);
        $input->addCondition(self::Filled)
            ->addRule(self::MaxLength, null, 255)
            ->addRule(self::Float);

        return $input;
    }

    public function addNumeric(string $name, ?string $label = null): TextInput
    {
        $input = $this->addText($name, $label);
        $input->addCondition(self::Filled)
            ->addRule(self::MaxLength, null, 255)
            ->addRule(self::Numeric);

        return $input;
    }
}
