<?php

declare(strict_types=1);

namespace Solcik\Nette\Security;

use Nette\Application\UI\Presenter;
use Nette\ComponentModel\IComponent;
use Nette\Security\User;
use Nette\Utils\Strings;

final class AclHelper
{
    /**
     * @var int
     */
    public const INACTIVITY = 0b0001;

    /**
     * @var int
     */
    public const LOGIN_REQUIRED = 0b0010;

    /**
     * @var int
     */
    public const NOT_ALLOWED = 0b0100;

    /**
     * @var string
     */
    public const ALL_COMPONENTS = '*';

    /**
     * @var string
     */
    public const DYNAMIC_NAME_PATTERN = '#^[1-9][0-9]*\z#';

    private User $user;


    public function __construct(User $user)
    {
        $this->user = $user;
    }


    public function checkPrivileges(Presenter $presenter): int
    {
        $flag = 0;

        $resource = get_class($presenter);
        $action = $presenter->getAction();
        $signal = $presenter->getSignal();

        // just inform the user about the logout
        if (!$this->user->isLoggedIn()
            && $this->user->getLogoutReason() === User::INACTIVITY
            && $action !== 'logout'
        ) {
            $flag |= static::INACTIVITY;
        }

        if (!$this->user->isAllowed($resource, $action) || !$this->checkSignalPrivilege($resource, $signal)) {
            if (!$this->user->isLoggedIn()) {
                $flag |= self::LOGIN_REQUIRED;
            } else {
                $flag |= self::NOT_ALLOWED;
            }
        }

        return $flag;
    }


    private function checkSignalPrivilege(string $resource, ?array $signal): bool
    {
        if ($signal === null) {
            return true;
        }

        foreach (self::signalToActions($signal) as $action) {
            if ($this->user->isAllowed($resource, $action)) {
                return true;
            }
        }

        return false;
    }


    /**
     * @return string[]
     */
    private static function signalToActions(array $signal): array
    {
        $actions = [];
        $dynamics = [];
        $parts = explode(
            IComponent::NAME_SEPARATOR,
            ltrim(implode(IComponent::NAME_SEPARATOR, $signal), IComponent::NAME_SEPARATOR)
        );

        foreach ($parts as $key => $part) {
            if (Strings::match($part, self::DYNAMIC_NAME_PATTERN) !== null) {
                $dynamics[] = $key;
            }
        }

        // generate all action combinations
        $count = count($dynamics);
        for ($i = 0; $i < (1 << $count); $i++) {
            $tmp = $parts;

            for ($j = 0; $j < $count; $j++) {
                $decider = ($i >> ($count - $j - 1) & 1 << $j) > 0;
                $tmp[$dynamics[$j]] = $decider ? self::ALL_COMPONENTS : $parts[$dynamics[$j]];
            }

            $actions[] = implode(IComponent::NAME_SEPARATOR, $tmp) . '!';
        }

        return $actions;
    }
}
