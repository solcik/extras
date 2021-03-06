<?php

declare(strict_types=1);

namespace Solcik\Exception\Runtime\Acl;

use Solcik\Exception\Runtime\EntityNotFoundException;
use function Safe\sprintf;

final class UserNotFoundByEmail extends EntityNotFoundException
{
    private string $email;

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
