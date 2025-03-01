<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;

/**
 * https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/master/doc/rules/index.rst
 * https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/master/doc/ruleSets/index.rst
 */

/**
 * https://github.com/slevomat/coding-standard
 */
return ECSConfig
    ::configure()
    ->withPaths(
        [
            __DIR__ . '/src',
            __DIR__ . '/tests',
        ]
    )
    ->withRootFiles()
    ->withParallel()
    ->withSpacing(indentation: Option::INDENTATION_SPACES, lineEnding: PHP_EOL)
    //    ->withCache(
    //        directory: __DIR__ . '/storage/temp/cache/.ecs',
    //        namespace: getcwd() // normalized to directory separator
    //    )
    //    ->withPreparedSets(psr12: true, common: true, symplify: true, cleanCode: true)
    //    ->withPhpCsFixerSets(doctrineAnnotation: true, symfony: true)
    ->withRules(
        [
            //            RequireTrailingCommaInDeclarationSniff::class,
            //            DeclareStrictTypesFixer::class,
        ]
    )
    ->withConfiguredRule(GeneralPhpdocAnnotationRemoveFixer::class, [
        'annotations' => [],
    ])
    //    ->withConfiguredRule(LineLengthFixer::class, [
    //        'line_length' => 110,
    //    ])
    ->withSkip(
        [
            //            MethodChainingNewlineFixer::class,
            //            NotOperatorWithSuccessorSpaceFixer::class,
            //            YodaStyleFixer::class,
        ]
    )
;
