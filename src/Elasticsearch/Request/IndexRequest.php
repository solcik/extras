<?php

declare(strict_types=1);

namespace Solcik\Elasticsearch\Request;

abstract class IndexRequest
{
    public function exportIndexRequest(): array
    {
        $array = [];
        $array['id'] = $this->getId();
        $array['index'] = $this->getIndexName();
        $array['body'] = $this->exportBody();

        return $array;
    }

    abstract public function getId(): int;

    abstract public function getIndexName(): string;

    abstract protected function exportBody(): array;
}
