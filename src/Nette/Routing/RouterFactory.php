<?php

declare(strict_types=1);

namespace Solcik\Nette\Routing;

use Nette\Application\Routers\RouteList;
use Nette\Routing\Router;
use Nette\StaticClass;

final class RouterFactory
{
    use StaticClass;

    /**
     * @param string[] $languages
     */
    public static function createRouter(array $languages): Router
    {
        $defaultLang = reset($languages);

        $router = new RouteList();

        $languagePattern = '[<locale=' . $defaultLang . ' ' . implode('|', $languages) . '>/]';

        $router->addRoute($languagePattern . '<presenter>[/<action>][/<id>]', [
            'presenter' => 'Homepage',
            'action' => 'default',
        ]);

        return $router;
    }
}
