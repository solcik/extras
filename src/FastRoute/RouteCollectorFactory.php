<?php

declare(strict_types=1);

namespace Solcik\FastRoute;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;

final class RouteCollectorFactory
{
    private Std $routeParser;

    private GroupCountBased $dataGenerator;


    public function __construct(Std $routeParser, GroupCountBased $dataGenerator)
    {
        $this->routeParser = $routeParser;
        $this->dataGenerator = $dataGenerator;
    }


    public function create(): RouteCollector
    {
        return new RouteCollector($this->routeParser, $this->dataGenerator);
    }
}
