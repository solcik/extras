<?php

declare(strict_types=1);

namespace Solcik\WebpackLoader\Controls;

use Nette\Utils\Html;

final class JavaScriptControl extends BaseControl
{
    /**
     * Get script element
     */
    public function getElement(string $source, bool $inline = false): ?Html
    {
        if (array_key_exists($source, $this->webpackStats)) {
            $url = $this->webpackStats[$source];

            if ($inline) {
                $url = $this->wwwDir . $url;
            }

            return Html::el('script')
                ->setAttribute('type', 'text/javascript')
                ->setAttribute('src', $url)
                ->setAttribute('defer', true);
        }

        return null;
    }
}
