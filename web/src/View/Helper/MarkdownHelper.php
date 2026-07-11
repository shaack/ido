<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Parsedown;

/**
 * Markdown helper
 *
 * Renders user supplied Markdown (notes, descriptions) as HTML.
 *
 * Parsedown runs in safe mode, so raw HTML in the source is escaped instead of
 * passed through. Never call Parsedown directly from a template.
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

    /**
     * Render Markdown as HTML and highlight #hashtags.
     *
     * @param string|null $markdown The Markdown source.
     * @return string
     */
    public function toHtmlWithHashtags(?string $markdown): string
    {
        if ($markdown === null || trim($markdown) === '') {
            return '';
        }

        // Escape the '#' of a hashtag before parsing, otherwise Parsedown reads
        // a hashtag at the start of a line as a heading. A real heading is
        // '# Titel' with a space and stays untouched.
        $escaped = preg_replace('/(^|\s)#(\w+)/', '$1\\\\#$2', $markdown) ?? $markdown;
        $html = $this->parsedown->text($escaped);

        // Wrap after parsing, because safe mode would escape the markup if it
        // were injected into the Markdown source. The lookahead keeps the match
        // out of tags, so a '#' inside href="#anchor" is left alone.
        $highlighted = preg_replace(
            '/(^|[\s>])#(\w+)(?![^<]*>)/',
            '$1<span class="text-info">#$2</span>',
            $html
        );

        return $highlighted ?? $html;
    }
}
