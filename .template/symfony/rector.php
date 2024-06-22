<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\CodeQuality\Rector\If_\SimplifyIfReturnBoolRector;
use Rector\Config\RectorConfig;
use Rector\Symfony\Symfony53\Rector\StaticPropertyFetch\KernelTestCaseContainerPropertyDeprecationRector;
use Rector\Symfony\Symfony62\Rector\MethodCall\SimplifyFormRenderingRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictNativeCallRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/.castor',
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withImportNames(
        removeUnusedImports: true,
    )
    // uncomment to reach your current PHP version
    ->withPhpSets(
        php83: true
    )
    ->withRules(rules: [
        AddVoidReturnTypeWhereNoReturnRector::class,
        InlineConstructorDefaultToPropertyRector::class,
        ReturnTypeFromStrictNativeCallRector::class,
        TypedPropertyFromStrictConstructorRector::class,
        KernelTestCaseContainerPropertyDeprecationRector::class,
        SimplifyFormRenderingRector::class,
        Rector\DeadCode\Rector\Return_\RemoveDeadConditionAboveReturnRector::class,
        Rector\DeadCode\Rector\For_\RemoveDeadIfForeachForRector::class,
        Rector\Php84\Rector\Param\ExplicitNullableParamTypeRector::class,
        Rector\Privatization\Rector\Class_\FinalizeTestCaseClassRector::class,
    ])
    ->withSkip([
        SimplifyIfReturnBoolRector::class,
    ])
    ->withAttributesSets(symfony: true, doctrine: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        instanceOf: true,
        earlyReturn: true,
        strictBooleans: true,
    );
