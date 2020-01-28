<?php

declare(strict_types=1);

namespace Solcik\FastRoute;

use FastRoute\Dispatcher;
use FastRoute\Dispatcher\GroupCountBased;
use FastRoute\RouteCollector;
use Nette\Caching\Cache;

final class DispatcherFactory
{
    private Cache $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function create(RouteCollector $collector, array $routesData, bool $cache = false): Dispatcher
    {
        $data = null;

        if ($cache) {
            $data = $this->cache->load('routes');
        }

        if ($data === null) {
            foreach ($routesData as [$httpMethod, $route, $handler]) {
                $collector->addRoute($httpMethod, $route, $handler);
            }

            $data = $collector->getData();

            if ($cache) {
                $this->cache->save('routes', $data, [
                    Cache::FILES => [__DIR__ . '/../../../app-api/config/routes.php'],
                ]);
            }
        }

        return new GroupCountBased($data);
    }
}
