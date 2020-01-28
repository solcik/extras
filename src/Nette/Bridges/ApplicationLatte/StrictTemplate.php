<?php

declare(strict_types=1);

namespace Solcik\Nette\Bridges\ApplicationLatte;

use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\LatteTemplate;
use Nette\Security\User;
use Nette\SmartObject;

class StrictTemplate extends LatteTemplate
{
    use SmartObject;

    public string $baseUrl;

    public string $basePath;

    public array

 $flashes = [];

    public string $_lang;

    public string $_rmsUrl;

    public User $_user;

    public ?Presenter $presenter;

    public ?Control $control;

    /**
     * @var string[]
     */
    public array

 $_langs = [];

    /**
     * Returns array of all parameters.
     */
    public function getParameters(): array
    {
        return get_object_vars($this);
    }
}
