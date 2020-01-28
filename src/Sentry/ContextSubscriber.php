<?php

declare(strict_types=1);

namespace Solcik\Sentry;

use Contributte\Events\Extra\Event\Application\StartupEvent;
use Nette\Security\IUserStorage;
use Sentry\State\HubInterface;
use Sentry\State\Scope;
use Solcik\Nette\Security\Identity;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ContextSubscriber implements EventSubscriberInterface
{
    private IUserStorage $storage;

    private HubInterface $hub;

    public function __construct(IUserStorage $storage, HubInterface $hub)
    {
        $this->storage = $storage;
        $this->hub = $hub;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [StartupEvent::class => 'onStartup'];
    }

    public function onStartup(StartupEvent $event): void
    {
        $identity = $this->storage->getIdentity();

        if ($identity !== null) {
            assert($identity instanceof Identity);

            $this->hub->configureScope(static function (Scope $scope) use ($identity): void {
                $scope->setUser(['email' => $identity->getEmail()]);
            });
        }
    }
}
