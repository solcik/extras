<?php

declare(strict_types=1);

namespace Solcik\Firebase\JWT;

use Firebase\JWT\JWT;
use Solcik\Model\Entity\Acl\User;

final class JWTService
{
    private string $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    public function sign(User $user): string
    {
        return JWT::encode([
            'id' => $user->getId()->toString(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
        ], $this->secret);
    }
}
