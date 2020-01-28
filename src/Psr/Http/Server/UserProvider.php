<?php

declare(strict_types=1);

namespace Solcik\Psr\Http\Server;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;
use Solcik\Domain\Acl\GetUserById;
use Solcik\Exception\Runtime\Acl\UserNotFound;

final class UserProvider implements MiddlewareInterface
{
    /**
     * @var string
     */
    public const ATTR_USER = 'user';

    private GetUserById $getUserById;

    public function __construct(GetUserById $getUserById)
    {
        $this->getUserById = $getUserById;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $jwt = $request->getAttribute('jwt');

        if ($jwt !== null) {
            $id = $jwt['id'];
            try {
                $user = ($this->getUserById)(Uuid::fromString($id));
                $request = $request->withAttribute(self::ATTR_USER, $user);
            } catch (UserNotFound $e) {
                return new JsonResponse([
                    'error' => [
                        'message' => $e->getMessage(),
                    ],
                ], 401);
            }
        }

        return $handler->handle($request);
    }
}
