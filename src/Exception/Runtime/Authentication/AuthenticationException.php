<?php

declare(strict_types=1);

namespace Solcik\Exception\Runtime\Authentication;

use Nette\Security\AuthenticationException as NetteAuthenticationException;

final class AuthenticationException extends NetteAuthenticationException
{
}
