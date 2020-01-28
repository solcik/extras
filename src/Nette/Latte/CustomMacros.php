<?php

declare(strict_types=1);

namespace Solcik\Nette\Latte;

use Latte\CompileException;
use Latte\Compiler;
use Latte\Helpers;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;

final class CustomMacros extends MacroSet
{
    public static function install(Compiler $compiler): void
    {
        $me = new static($compiler);
        $me->addMacro('varType', [$me, 'macroVarType'], null, null, self::ALLOWED_IN_HEAD);
        $me->addMacro('templateType', [$me, 'macroTemplateType'], null, null, self::ALLOWED_IN_HEAD);
    }

    /**
     * {varType type $var}
     */
    public function macroVarType(MacroNode $node, PhpWriter $writer): void
    {
        if ($node->modifiers) {
            throw new CompileException('Modifiers are not allowed in ' . $node->getNotation());
        }
        $type = $node->tokenizer->fetchWord();
        $variable = $node->tokenizer->fetchWord();
        if (!$type || !$variable || !Helpers::startsWith($variable, '$')) {
            throw new CompileException('Unexpected content, expecting {varType type $var}.');
        }
    }

    /**
     * {templateType ClassName}
     */
    public function macroTemplateType(MacroNode $node, PhpWriter $writer): void
    {
        $type = $node->tokenizer->fetchWord();
        if (!$this->getCompiler()->isInHead()) {
            throw new CompileException($node->getNotation() . ' is allowed only in template header.');
        } elseif ($node->modifiers) {
            throw new CompileException('Modifiers are not allowed in ' . $node->getNotation());
        } elseif (!$type) {
            throw new CompileException('Missing class name in {templateType} macro.');
        } elseif (!class_exists($type)) {
            throw new CompileException("Class '${type}' not found.");
        }
    }
}
