<?php

declare(strict_types=1);

namespace Solcik\WebpackLoader;

use Solcik\WebpackLoader\Controls\CssControl;
use Solcik\WebpackLoader\Controls\JavaScriptControl;

final class ControlFactory
{
    private string $wwwDir;

    private array $javascripts = [];

    private array $styles = [];

    public function __construct(string $config, string $wwwDir)
    {
        $string = file_get_contents($config);
        $webpackJson = json_decode($string, true);

        $this->javascripts = $webpackJson;
        $this->styles = $webpackJson;
        $this->wwwDir = $wwwDir;
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
