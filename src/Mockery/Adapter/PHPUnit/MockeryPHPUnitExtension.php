<?php

declare(strict_types=1);

namespace Solcik\Mockery\Adapter\PHPUnit;

use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

final readonly class MockeryPHPUnitExtension implements Extension
{
    public function bootstrap(
        Configuration $configuration,
        Facade $facade,
        ParameterCollection $parameters,
    ): void {
        $facade->registerSubscriber(new WarnIfExpectationsHaveNotBeenVerifiedSubscriber());
    }
}
