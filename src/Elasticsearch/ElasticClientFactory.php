<?php

declare(strict_types=1);

namespace Solcik\Elasticsearch;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

final class ElasticClientFactory
{
    /**
     * @param string[] $hosts
     */
    public static function create(array $hosts): Client
    {
        $builder = ClientBuilder::create();
        $builder->setHosts($hosts);

        return $builder->build();
    }
}
