<?php

declare(strict_types=1);

namespace Solcik\FastRoute;

final readonly class Route
{
    public function __construct(
        private string $method,
        private string $path,
        private string $handler,
    ) {
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getHandler(): string
    {
        return $this->handler;
    }
}
