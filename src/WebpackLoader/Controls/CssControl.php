<?php

declare(strict_types=1);

namespace Solcik\WebpackLoader\Controls;

use Nette\Utils\Html;

final class CssControl extends BaseControl
{
    private string $media;

    private string $title;

    private string $type = 'text/css';

    private bool $alternate = false;

    public function getMedia(): string
    {
        return $this->media;
    }

    /**
     * Set media.
     *
     * @return $this
     */
    public function setMedia(string $media)
    {
        $this->media = $media;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set type.
     *
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set title.
     *
     * @return $this
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Is alternate ?
     */
    public function isAlternate(): bool
    {
        return $this->alternate;
    }

    /**
     * Set alternate.
     *
     * @return $this
     */
    public function setAlternate(bool $alternate)
    {
        $this->alternate = $alternate;

        return $this;
    }

    /**
     * Get link element.
     */
    public function getElement(string $source, bool $inline = false): ?Html
    {
        if (array_key_exists($source, $this->webpackStats)) {
            $url = '/dist/' . $this->webpackStats[$source];

            if ($inline) {
                $url = $this->wwwDir . $url;
                // return Html::el('style')->setHtml(file_get_contents($url));
            }

            return Html::el('link')
                ->setAttribute('rel', 'stylesheet' . ($this->alternate ? ' alternate' : ''))
                ->setAttribute('type', $this->type)
                ->setAttribute('media', $this->media)
                ->setAttribute('title', $this->title)
                ->href($url);
        }

        return null;
    }
}
