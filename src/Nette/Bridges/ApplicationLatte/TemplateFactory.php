<?php

declare(strict_types=1);

namespace Solcik\Nette\Bridges\ApplicationLatte;

use Nette\Application\UI\Control;
use Nette\Application\UI\ITemplate;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Bridges\ApplicationLatte\TemplateFactory as NetteTemplateFactory;
use Nette\Caching\IStorage;
use Nette\Http\IRequest;
use Nette\Localization\ITranslator;
use Nette\Security\User;
use Symfony\Component\Translation\Translator;

final class TemplateFactory extends NetteTemplateFactory
{
    private string $rmsUrl;

    private ITranslator $translator;

    /**
     * @var string[]
     */
    private array $languages = [];


    /**
     * @param string[] $languages
     */
    public function __construct(
        ILatteFactory $latteFactory,
        IRequest $httpRequest,
        User $user,
        IStorage $cacheStorage,
        ITranslator $translator,
        array $languages,
        string $rmsUrl
    ) {
        parent::__construct(
            $latteFactory,
            $httpRequest,
            $user,
            $cacheStorage,
        );

        $this->translator = $translator;
        $this->languages = $languages;
        $this->rmsUrl = $rmsUrl;
    }


    public function createTemplate(?Control $control = null, ?string $class = null): ITemplate
    {
        /** @var Template $template */
        $template = parent::createTemplate($control);

        assert($this->translator instanceof Translator);

        $template->_user = $template->user;

        unset($template->user);

        $template->_lang = $this->translator->getLocale();
        $template->_langs = $this->languages;
        $template->_rmsUrl = $this->rmsUrl;

        return $template;
    }
}
