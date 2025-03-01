<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Set\ValueObject\LevelSetList;

/*
 * https://getrector.com/find-rule
 */

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withRootFiles()
    ->withParallel()
    // ->withSymfonyContainerXml(__DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml')
    ->withIndent(indentChar: ' ', indentSize: 4)
    ->withImportNames(
        importNames: true,
        importDocBlockNames: true,
        importShortClasses: true,
        removeUnusedImports: true,
    )
    ->withPhpSets(php84: true)
    ->withAttributesSets(symfony: true, doctrine: true, phpunit: true)
    ->withPreparedSets(
        //        deadCode:         false,
        //        codeQuality:      false,
        //        codingStyle:      false,
        //        typeDeclarations: true,
        //        privatization:    false,
        //        naming:           false,
        //        instanceOf:       false,
        //        earlyReturn:      false,
        //        strictBooleans:   false,
    )
    ->withComposerBased(doctrine: true, phpunit: true)
    ->withRules(
        [
            //            AddVoidReturnTypeWhereNoReturnRector::class,
            //            StaticDataProviderClassMethodRector::class,
            //            ExplicitNullableParamTypeRector::class,
            //            StringExtensionToConfigBuilderRector::class,
        ]
    )
    ->withSkip(
        [
            //            AddOverrideAttributeToOverriddenMethodsRector::class,
            //            ClassPropertyAssignToConstructorPromotionRector::class,
            //            ClosureToArrowFunctionRector::class,
            //            StringableForToStringRector::class,
            //            NullToStrictStringFuncCallArgRector::class,
            //            ReadOnlyPropertyRector::class => [__DIR__ . '/src/Entity/V1/Embeddable/NbnkMoney.php'],
        ]
    )
    ->withSets(
        [
            //            SymfonySetList::SYMFONY_72,
            //            SymfonySetList::SYMFONY_CODE_QUALITY,
            //            SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
            DoctrineSetList::GEDMO_ANNOTATIONS_TO_ATTRIBUTES,
            DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
            LevelSetList::UP_TO_PHP_84,
        ]
    )
    ->withTypeCoverageLevel(100)
    //    ->withDeadCodeLevel(0)
    ->withCodeQualityLevel(100)
;
