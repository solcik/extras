<?php

declare(strict_types=1);

namespace Solcik\Sentry;

use Sentry\ClientInterface;
use Sentry\SentrySdk;
use Sentry\State\Hub;
use Sentry\State\HubInterface;

final class HubFactory
{
    public static function create(ClientInterface $client): HubInterface
    {
        $hub = new Hub($client);

        SentrySdk::setCurrentHub($hub);

        return $hub;
    }
}
