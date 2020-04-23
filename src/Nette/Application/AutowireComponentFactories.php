<?php

declare(strict_types=1);

namespace Solcik\Nette\Application;

use Nette\Application\UI\Presenter;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\ComponentModel\IComponent;
use Nette\DI\Container;
use Nette\DI\MissingServiceException;
use Nette\DI\Resolver;
use Nette\MemberAccessException;
use Nette\Reflection\ClassType;
use Nette\Reflection\Method;
use Nette\UnexpectedValueException;
use Nette\Utils\Strings;

/**
 * @method Presenter getPresenter()
 */
trait AutowireComponentFactories
{
    private Container $autowireComponentFactoriesLocator;


    /**
     * @throws MemberAccessException
     * @internal
     */
    public function injectComponentFactories(Container $dic): void
    {
        if (!$this instanceof Presenter) {
            throw new MemberAccessException('Trait ' . __TRAIT__ . ' can be used only in descendants of Presenter.');
        }

        $this->autowireComponentFactoriesLocator = $dic;

        $storage = $dic->hasService('autowired.cacheStorage')
            ? $dic->getService('autowired.cacheStorage')
            : $dic->getByType(IStorage::class);
        $cache = new Cache($storage, 'Kdyby.Autowired.AutowireComponentFactories');

        $presenterClass = static::class;
        if ($cache->load($presenterClass) !== null) {
            return;
        }

        $ignore = class_parents(Presenter::class) + ['ui' => Presenter::class];
        $rc = new ClassType($this);
        foreach ($rc->getMethods() as $method) {
            if (in_array(
                $method->getDeclaringClass()->getName(),
                $ignore,
                true
            ) || !Strings::startsWith($method->getName(), 'createComponent')) {
                continue;
            }

            foreach ($method->getParameters() as $parameter) {
                $class = $parameter->getClassName();
                if (!$class) { // has object type hint
                    continue;
                }

                if (!$this->findByTypeForFactory($class) && !$parameter->allowsNull()) {
                    throw new MissingServiceException(
                        "No service of type {$class} found. Make sure the type hint in ${method} is written correctly and service of this type is registered."
                    );
                }
            }
        }

        $files = array_map(
            function ($class) {
                return ClassType::from($class)->getFileName();
            },
            array_diff(array_values(class_parents($presenterClass) + ['me' => $presenterClass]), $ignore)
        );

        $files[] = ClassType::from($this->autowireComponentFactoriesLocator)->getFileName();

        $cache->save($presenterClass, true, [
            $cache::FILES => $files,
        ]);
    }


    /**
     * @throws UnexpectedValueException
     */
    protected function createComponent(string $name): ?IComponent
    {
        $sl = $this->getComponentFactoriesLocator();

        $ucName = ucfirst($name);
        $method = 'createComponent' . $ucName;
        if ($ucName !== $name && method_exists($this, $method)) {
            $methodReflection = new Method($this, $method);
            if ($methodReflection->getName() !== $method) {
                return null;
            }
            $parameters = $methodReflection->getParameters();

            $args = [];
            $first = reset($parameters);
            if ($first && !$first->className) {
                $args[] = $name;
            }

            $getter = static function (string $type, bool $single) use ($sl) {
                return $single
                    ? $sl->getByType($type)
                    : array_map([$sl, 'getService'], $sl->findAutowired($type));
            };

            $args = Resolver::autowireArguments($methodReflection, $args, $getter);

            return call_user_func_array([$this, $method], $args);
        }

        return null;
    }


    protected function getComponentFactoriesLocator(): Container
    {
        if ($this->autowireComponentFactoriesLocator === null) {
            $this->injectComponentFactories($this->getPresenter()->getContext());
        }

        return $this->autowireComponentFactoriesLocator;
    }


    /**
     * @return string|bool
     */
    private function findByTypeForFactory(string $type)
    {
        $found = $this->autowireComponentFactoriesLocator->findByType($type);

        return reset($found);
    }
}
