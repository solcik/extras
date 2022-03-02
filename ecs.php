<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\CommentedOutCodeSniff;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer;
use SlevomatCodingStandard\Sniffs\Commenting\DisallowCommentAfterCodeSniff;
use SlevomatCodingStandard\Sniffs\Functions\RequireTrailingCommaInDeclarationSniff;
use SlevomatCodingStandard\Sniffs\PHP\DisallowDirectMagicInvokeCallSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\Naming\StandardizeHereNowDocKeywordFixer;
use Symplify\CodingStandard\Fixer\Spacing\MethodChainingNewlineFixer;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $c): void {
    $c->import(SetList::SYMPLIFY);
    $c->import(SetList::CLEAN_CODE);
    $c->import(SetList::COMMON);
    $c->import(SetList::PSR_12);
    $c->import(SetList::PHPUNIT);
    // $c->import(SetList::DOCTRINE_ANNOTATIONS);
    // $c->import(SetList::PHP_CS_FIXER);
    // $c->import(SetList::PHP_CS_FIXER_RISKY);

    $parameters = $c->parameters();

    $parameters->set(
        Option::PATHS,
        [
            __DIR__ . '/src',
            __DIR__ . '/tests',
        ]
    );

    $parameters->set(Option::INDENTATION, '    ');
    $parameters->set(Option::PARALLEL, true);

    $parameters->set(
        Option::SKIP,
        [
            CommentedOutCodeSniff::class => null,
            DisallowCommentAfterCodeSniff::class => null,
            DisallowDirectMagicInvokeCallSniff::class => null,
            NotOperatorWithSuccessorSpaceFixer::class => null,
            MethodChainingNewlineFixer::class => null,
            StandardizeHereNowDocKeywordFixer::class => null,
        ]
    );

    $services = $c->services();
    $services->set(RequireTrailingCommaInDeclarationSniff::class);
    $services->set(GeneralPhpdocAnnotationRemoveFixer::class)->call(
        'configure',
        [
            [
                'annotations' => [
                ],
            ],
        ]
    )
    ;
};
