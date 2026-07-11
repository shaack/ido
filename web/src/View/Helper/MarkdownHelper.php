<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Parsedown;

/**
 * Markdown helper
 *
 * Renders user supplied Markdown (customer notes, project descriptions) as HTML.
 *
 * Parsedown runs in safe mode, so raw HTML in the source is escaped instead of
 * passed through. Never call Parsedown directly from a template.
 *
 * Es gab hier einmal ein toHtmlWithHashtags(), das #hashtags einfärbte. Es
 * wurde nur von den Task-Notizen benutzt, und die sind aus der Oberfläche
 * entfernt. Die Fassung steht in der Git-Historie, falls sie je zurück soll.
 */
class MarkdownHelper extends Helper
{
    /**
     * @var \Parsedown
     */
    protected Parsedown $parsedown;

    /**
     * @param array $config The helper configuration.
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->parsedown = new Parsedown();
        $this->parsedown->setSafeMode(true);
    }

    /**
     * Render Markdown as HTML.
     *
     * @param string|null $markdown The Markdown source.
     * @return string
     */
    public function toHtml(?string $markdown): string
    {
        if ($markdown === null || trim($markdown) === '') {
            return '';
        }

        return $this->parsedown->text($markdown);
    }
}
