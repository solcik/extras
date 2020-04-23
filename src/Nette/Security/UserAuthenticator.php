<?php

declare(strict_types=1);

namespace Solcik\Nette\Security;

use Nette\Security\IAuthenticator;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Solcik\Domain\Acl\GetUserByEmail;
use Solcik\Domain\Acl\UserLoggedIn;
use Solcik\Exception\Runtime\Acl\UserNotFoundByEmail;
use Solcik\Exception\Runtime\Authentication\AuthenticationException;
use Solcik\Exception\Runtime\Authentication\InvalidPasswordException;
use Solcik\Exception\Runtime\Authentication\UserNotActiveException;

final class UserAuthenticator implements IAuthenticator
{
    private Passwords $passwords;

    private GetUserByEmail $getUserByEmail;

    private UserLoggedIn $userLoggedIn;


    public function __construct(Passwords $passwords, GetUserByEmail $getUserByEmail, UserLoggedIn $userLoggedIn)
    {
        $this->passwords = $passwords;
        $this->getUserByEmail = $getUserByEmail;
        $this->userLoggedIn = $userLoggedIn;
    }


    /**
     * @throws AuthenticationException
     */
    public function authenticate(array $credentials): IIdentity
    {
        [$username, $password] = $credentials;

        try {
            $user = ($this->getUserByEmail)($username);
        } catch (UserNotFoundByEmail $e) {
            throw new AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
        }

        try {
            $user->logIn($this->passwords, $password, $this->userLoggedIn);
        } catch (InvalidPasswordException $e) {
            throw new AuthenticationException($e->getMessage(), self::INVALID_CREDENTIAL);
        } catch (UserNotActiveException $e) {
            throw new AuthenticationException($e->getMessage(), self::NOT_APPROVED);
        }

        return Identity::create($user);
    }
}
