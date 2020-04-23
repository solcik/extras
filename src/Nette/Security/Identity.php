<?php

declare(strict_types=1);

namespace Solcik\Nette\Security;

use Nette\Security\IIdentity;
use Solcik\Model\Entity\Acl\User;

final class Identity implements IIdentity
{
    private string $id;
    private string $email;
    private array $roles;


    private function __construct(User $user)
    {
        $this->id = $user->getId()->toString();
        $this->roles = $user->getRoles();
        $this->email = $user->getEmail();
    }


    public static function create(User $user): IIdentity
    {
        return new self($user);
    }


    public function getId(): string
    {
        return $this->id;
    }


    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }


    public function getEmail(): string
    {
        return $this->email;
    }
}
