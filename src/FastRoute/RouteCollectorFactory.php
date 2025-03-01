<?php

declare(strict_types=1);

namespace Solcik\FastRoute;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;

final readonly class RouteCollectorFactory
{
    public function __construct(
        private Std $routeParser,
        private GroupCountBased $dataGenerator,
    ) {
    }

    public function create(): RouteCollector
    {
        return new RouteCollector($this->routeParser, $this->dataGenerator);
    }
}
