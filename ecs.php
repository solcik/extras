<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer;
use SlevomatCodingStandard\Sniffs\Commenting\DisallowCommentAfterCodeSniff;
use SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff;
use SlevomatCodingStandard\Sniffs\PHP\DisallowDirectMagicInvokeCallSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\Naming\StandardizeHereNowDocKeywordFixer;
use Symplify\CodingStandard\Fixer\Spacing\MethodChainingNewlineFixer;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $c): void {
    $c->import(__DIR__ . '/vendor/symplify/coding-standard/config/symplify.php');
    $c->import(__DIR__ . '/vendor/symplify/easy-coding-standard/config/set/clean-code.php');
    $c->import(__DIR__ . '/vendor/symplify/easy-coding-standard/config/set/common/*.php');
    $c->import(__DIR__ . '/vendor/symplify/easy-coding-standard/config/set/dead-code.php');
    $c->import(__DIR__ . '/vendor/symplify/easy-coding-standard/config/set/php70.php');
    $c->import(__DIR__ . '/vendor/symplify/easy-coding-standard/config/set/php71.php');
    $c->import(
        __DIR__ . '/vendor/symplify/easy-coding-standard/config/set/php_cs_fixer/php-cs-fixer-psr2.php'
    );
    $c->import(__DIR__ . '/vendor/symplify/easy-coding-standard/config/set/psr12.php');

    $parameters = $c->parameters();

    $parameters->set(
        Option::PATHS,
        [
            __DIR__ . '/src',
            __DIR__ . '/tests',
        ]
    );
    $parameters->set('indentation', '    ');
    $parameters->set('line_length', 140);
    $parameters->set('exclude_files', []);

    $parameters->set(
        Option::SKIP,
        [
            // ToDo
            UnusedParameterSniff::class . '.UnusedParameter' => null,
            // ----------------------------------------------------------------
            ParameterTypeHintSniff::class . '.MissingAnyTypeHint' => null,
            ParameterTypeHintSniff::class . '.MissingAnyTypeHint' => null,
            PropertyTypeHintSniff::class . '.MissingAnyTypeHint' => null,
            ReturnTypeHintSniff::class . '.MissingAnyTypeHint' => null,
            // ----------------------------------------------------------------
            // TMP skip: not working correctly
            MethodChainingIndentationFixer::class => null,
            MethodChainingNewlineFixer::class => null,
            StandardizeHereNowDocKeywordFixer::class => null,
            ParameterTypeHintSniff::class . '.MissingNativeTypeHint' => null,
            PropertyTypeHintSniff::class . '.MissingNativeTypeHint' => null,
            ReturnTypeHintSniff::class . '.MissingNativeTypeHint' => null,
            // ----------------------------------------------------------------
            ClassAttributesSeparationFixer::class => null,
            NotOperatorWithSuccessorSpaceFixer::class => null,
            DisallowCommentAfterCodeSniff::class . '.DisallowedCommentAfterCode' => null,
            DisallowDirectMagicInvokeCallSniff::class . '.DisallowDirectMagicInvokeCall' => null,
            ParameterTypeHintSniff::class . '.MissingTraversableTypeHintSpecification' => null,
            PropertyTypeHintSniff::class . '.MissingTraversableTypeHintSpecification' => null,
            ReturnTypeHintSniff::class . '.MissingTraversableTypeHintSpecification' => null,
        ]
    );
};
