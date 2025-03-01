<?php

declare(strict_types=1);

namespace Solcik\Nette\Application\UI;

use Nette\Application\UI\Form as NetteForm;
use Nette\Forms\Controls\TextInput;

final class Form extends NetteForm
{
    #[\Override]
    public function addFloat(string $name, ?string $label = null): TextInput
    {
        $input = $this->addText($name, $label);
        $input->addCondition(self::FILLED)
            ->addRule(self::MAX_LENGTH, null, 255)
            ->addRule(self::FLOAT);

        return $input;
    }

    public function addNumeric(string $name, ?string $label = null): TextInput
    {
        $input = $this->addText($name, $label);
        $input->addCondition(self::FILLED)
            ->addRule(self::MAX_LENGTH, null, 255)
            ->addRule(self::NUMERIC);

        return $input;
    }
}
