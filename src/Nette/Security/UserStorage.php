<?php

declare(strict_types=1);

namespace Solcik\Nette\Security;

use Doctrine\ORM\NoResultException;
use Nette\Http\Session;
use Nette\Http\UserStorage as NetteUserStorage;
use Nette\Security\IIdentity;
use Ramsey\Uuid\Uuid;
use Solcik\Domain\Acl\GetUserById;
use Solcik\Model\Entity\Acl\User;

final class UserStorage extends NetteUserStorage
{
    private GetUserById $getUserById;

    private ?User $user = null;


    public function __construct(Session $sessionHandler, GetUserById $getUserById)
    {
        parent::__construct($sessionHandler);

        $this->getUserById = $getUserById;
    }


    public function getIdentity(): ?IIdentity
    {
        $identity = parent::getIdentity();

        if ($identity !== null) {
            try {
                if ($this->user === null) {
                    $this->user = ($this->getUserById)(Uuid::fromString($identity->getId()));
                }

                $identity = Identity::create($this->user);
            } catch (NoResultException $e) {
                $identity = null;
            }
        }

        $this->setIdentity($identity);

        return $identity;
    }
}
