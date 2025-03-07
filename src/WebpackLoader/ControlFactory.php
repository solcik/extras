<?php

declare(strict_types=1);

namespace Solcik\WebpackLoader;

use Solcik\WebpackLoader\Controls\CssControl;
use Solcik\WebpackLoader\Controls\JavaScriptControl;

final class ControlFactory
{
    /**
     * @var array<string, string>
     */
    private array $javascripts = [];

    /**
     * @var array<string, string>
     */
    private array $styles = [];

    public function __construct(
        string $config,
        private readonly string $wwwDir,
    ) {
        $string = file_get_contents($config);
        /** @var array<string, string> $webpackJson */
        $webpackJson = json_decode($string, true);

        $this->javascripts = $webpackJson;
        $this->styles = $webpackJson;
    }

    public function createCssControl(): CssControl
    {
        $control = new CssControl();
        $control->setWebpackStats($this->styles);
        $control->setWWWDir($this->wwwDir);

        return $control;
    }

    public function createJavaScriptControl(): JavaScriptControl
    {
        $control = new JavaScriptControl();
        $control->setWebpackStats($this->javascripts);
        $control->setWWWDir($this->wwwDir);

        return $control;
    }

    public function getCss(string $key, bool $withFullPath = false): ?string
    {
        if (array_key_exists($key, $this->styles)) {
            $url = $this->styles[$key];

            if ($withFullPath) {
                $url = $this->wwwDir . $url;
            }

            return $url;
        }

        return null;
    }

    public function getJs(string $key, bool $withFullPath = false): ?string
    {
        if (array_key_exists($key, $this->javascripts)) {
            $url = $this->javascripts[$key];

            if ($withFullPath) {
                $url = $this->wwwDir . $url;
            }

            return $url;
        }

        return null;
    }
}
