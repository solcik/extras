<?php

declare(strict_types=1);

namespace Solcik\Exception\Runtime\Authentication;

use Solcik\Exception\Runtime\RuntimeException;

final class InvalidPasswordException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Invalid password.');
    }
}
