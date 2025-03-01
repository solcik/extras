<?php

declare(strict_types=1);

namespace Solcik\Sentry;

use Jean85\PrettyVersions;
use Sentry\ClientInterface;
use Sentry\SentrySdk;

use function Sentry\init;

final class ClientFactory
{
    /**
     * @param string[] $options
     */
    public function create(array $options, string $name): ClientInterface
    {
        $version = PrettyVersions::getVersion($name);

        $options = array_merge($options, [
            'release' => $name . '@' . $version->getShortVersion(),
        ]);

        init($options);

        $hub = SentrySdk::getCurrentHub();

        return $hub->getClient();
    }
}
