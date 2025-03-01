<?php

declare(strict_types=1);

namespace Solcik\WebpackLoader\Controls;

use Nette\Application\UI\Control;
use Nette\Utils\Html;

abstract class BaseControl extends Control
{
    protected string $wwwDir;

    protected array $webpackStats = [];

    public function setWebpackStats(array $webpackStats): self
    {
        $this->webpackStats = $webpackStats;

        return $this;
    }

    public function setWWWDir(string $wwwDir): self
    {
        $this->wwwDir = $wwwDir;

        return $this;
    }

    public function render(string $name, bool $inline = false): void
    {
        echo $this->getElement($name, $inline), PHP_EOL;
    }

    /**
     * Get html element including generated content.
     */
    abstract public function getElement(string $source, bool $inline = false): ?Html;
}
