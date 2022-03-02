<?php

declare(strict_types=1);

namespace Solcik\Http\Middleware;

use Brick\DateTime\TimeZone;
use DateTimeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Solcik\Brick\DateTime\Clock;

final class Expires implements MiddlewareInterface
{
    public const HEADER = 'Expires';

    private Clock $clock;

    private int $minutes;

    public function __construct(Clock $clock, int $minutes)
    {
        $this->clock = $clock;
        $this->minutes = $minutes;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $timeZone = TimeZone::parse('GMT');
        $zdt = $this->clock->createZonedDateTime()->withTimeZoneSameInstant($timeZone);
        $zdt = $zdt->plusMinutes($this->minutes);
        $expires = $zdt->toDateTimeImmutable()->format(DateTimeInterface::RFC7231);

        return $response->withHeader(self::HEADER, $expires);
    }
}
