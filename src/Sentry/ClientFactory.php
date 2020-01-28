<?php

declare(strict_types=1);

namespace Solcik\Sentry;

use Jean85\PrettyVersions;
use Sentry\ClientBuilder;
use Sentry\ClientInterface;
use Sentry\Transport\TransportFactoryInterface;

final class ClientFactory
{
    /**
     * @param string[] $options
     */
    public function create(TransportFactoryInterface $transportFactory, array $options, string $name): ClientInterface
    {
        $version = PrettyVersions::getVersion($name);

        $options = array_merge($options, [
            'release' => $name . '@' . $version->getShortVersion(),
        ]);

        $builder = ClientBuilder::create($options);
        $builder->setTransportFactory($transportFactory);

        return $builder->getClient();
    }
}
