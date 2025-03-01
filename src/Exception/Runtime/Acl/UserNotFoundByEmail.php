<?php

declare(strict_types=1);

namespace Solcik\Exception\Runtime\Acl;

use Solcik\Exception\Runtime\EntityNotFoundException;

final class UserNotFoundByEmail extends EntityNotFoundException
{
    private readonly string $email;

    public function __construct(string $email)
    {
        parent::__construct(sprintf('User was not found for email: %s', $email));

        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
