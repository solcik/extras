<?php

declare(strict_types=1);

namespace Solcik\FastRoute;

final class Route
{
    private string $method;

    private string $path;

    private string $handler;


    public function __construct(string $method, string $path, string $handler)
    {
        $this->method = $method;
        $this->path = $path;
        $this->handler = $handler;
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
