<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace Solcik\Nette\Forms\Controls;

use Nette\Forms\Controls\ChoiceControl;
use Nette\Forms\Form;
use Nette\Forms\Helpers;
use Nette\Forms\Validator;
use Nette\Utils\Arrays;
use Nette\Utils\Html;

/**
 * Select box control that allows single item selection.
 */
final class SelectBox extends ChoiceControl
{
    /**
     * @var string
     */
    public const VALID = ':selectBoxValid';

    /**
     * @var array of option / optgroup
     */
    private array $options = [];

    /**
     * @var string|object|false
     */
    private $prompt = false;

    /**
     * @var array
     */
    private array $optionAttributes = [];

    /**
     * @param string|object $label
     */
    public function __construct($label = null, ?array $items = null)
    {
        parent::__construct($label, $items);

        $this->setOption('type', 'select');
        $this->addCondition(function () {
            return $this->prompt === false
                && $this->options
                && $this->control->size < 2;
        })->addRule(Form::FILLED, Validator::$messages[self::VALID]);

        $this->setHtmlAttribute('size', null);
        $this->setTranslator(null);
    }

    /**
     * Sets first prompt item in select box.
     *
     * @param string|object $prompt
     *
     * @return static
     */
    public function setPrompt($prompt)
    {
        $this->prompt = $prompt;

        return $this;
    }

    /**
     * Returns first prompt item?
     *
     * @return string|object|false
     */
    public function getPrompt()
    {
        return $this->prompt;
    }

    /**
     * Sets options and option groups from which to choose.
     * @return static
     */
    public function setItems(array $items, bool $useKeys = true)
    {
        if (!$useKeys) {
            $res = [];
            foreach ($items as $key => $value) {
                unset($items[$key]);
                if (is_array($value)) {
                    foreach ($value as $val) {
                        $res[$key][(string) $val] = $val;
                    }
                } else {
                    $res[(string) $value] = $value;
                }
            }
            $items = $res;
        }
        $this->options = $items;

        return parent::setItems(Arrays::flatten($items, true));
    }

    /**
     * Generates control's HTML element.
     */
    public function getControl(): Html
    {
        $translator = $this->getForm()->getTranslator();

        $items = $this->prompt === false ? [] : ['' => $translator ? $translator->translate(
            $this->prompt
        ) : $this->prompt];
        foreach ($this->options as $key => $value) {
            $items[is_array($value) ? $this->translate($key) : $key] = $this->translate($value);
        }

        return Helpers::createSelectBox(
            $items,
            [
                'disabled:' => is_array($this->disabled) ? $this->disabled : null,
            ] + $this->optionAttributes,
            $this->value
        )->addAttributes(parent::getControl()->attrs);
    }

    public function addOptionAttributes(array $attributes): self
    {
        $this->optionAttributes = $attributes + $this->optionAttributes;

        return $this;
    }

    public function isOk(): bool
    {
        return $this->isDisabled()
            || $this->prompt !== false
            || $this->getValue() !== null
            || !$this->options
            || $this->control->size > 1;
    }

    public function getOptionAttributes(): array
    {
        return $this->optionAttributes;
    }
}
