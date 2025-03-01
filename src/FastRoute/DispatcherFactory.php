<?php

declare(strict_types=1);

namespace Solcik\FastRoute;

use FastRoute\Dispatcher;
use FastRoute\Dispatcher\GroupCountBased;
use FastRoute\RouteCollector;
use Nette\Caching\Cache;
use Nette\Caching\Storage;

final readonly class DispatcherFactory
{
    private Cache $cache;

    public function __construct(Storage $storage)
    {
        $this->cache = new Cache($storage, 'http.api.dispatcher');
    }

    /**
     * @param Route[] $routes
     */
    public function create(RouteCollector $collector, array $routes, bool $cache = false): Dispatcher
    {
        $data = null;

        if ($cache) {
            $data = $this->cache->load('routes');
        }

        if ($data === null) {
            foreach ($routes as $route) {
                $collector->addRoute($route->getMethod(), $route->getPath(), $route->getHandler());
            }

            $data = $collector->getData();

            if ($cache) {
                $this->cache->save(
                    'routes',
                    $data,
                    [
                        Cache::Files => [__DIR__ . '/../../../config/api/config/routes.php'],
                    ]
                );
            }
        }

        return new GroupCountBased($data);
    }
}
