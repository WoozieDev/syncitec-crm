<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use Illuminate\Support\Str;

class RichTextSanitizer
{
    /**
     * @var array<int, string>
     */
    private const ALLOWED_TAGS = [
        'p',
        'br',
        'strong',
        'b',
        'em',
        'i',
        'ul',
        'ol',
        'li',
    ];

    /**
     * @var array<int, string>
     */
    private const BLOCKED_TAGS = [
        'script',
        'style',
        'iframe',
        'object',
        'embed',
    ];

    public static function sanitize(mixed $value): ?string
    {
        $html = trim((string) $value);

        if ($html === '') {
            return null;
        }

        $previousState = libxml_use_internal_errors(true);

        $document = new DOMDocument('1.0', 'UTF-8');
        $document->loadHTML(
            '<?xml encoding="utf-8" ?><div>'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
        );

        self::sanitizeNode($document->documentElement);

        $sanitized = '';

        foreach ($document->documentElement?->childNodes ?? [] as $childNode) {
            $sanitized .= $document->saveHTML($childNode);
        }

        libxml_clear_errors();
        libxml_use_internal_errors($previousState);

        $sanitized = trim($sanitized);

        return self::containsVisibleContent($sanitized) ? $sanitized : null;
    }

    private static function sanitizeNode(?DOMNode $node): void
    {
        if (! $node instanceof DOMNode) {
            return;
        }

        if ($node instanceof DOMElement) {
            if (in_array($node->tagName, self::BLOCKED_TAGS, true)) {
                $node->parentNode?->removeChild($node);

                return;
            }

            if (! in_array($node->tagName, self::ALLOWED_TAGS, true)) {
                self::unwrapNode($node);

                return;
            }

            while ($node->attributes->length > 0) {
                $attribute = $node->attributes->item(0);

                if ($attribute !== null) {
                    $node->removeAttributeNode($attribute);
                }
            }
        }

        $children = [];

        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            self::sanitizeNode($child);
        }
    }

    private static function unwrapNode(DOMNode $node): void
    {
        $parent = $node->parentNode;

        if ($parent === null) {
            return;
        }

        while ($node->firstChild !== null) {
            $parent->insertBefore($node->firstChild, $node);
        }

        $parent->removeChild($node);
    }

    private static function containsVisibleContent(string $html): bool
    {
        $text = trim(Str::of(html_entity_decode(strip_tags($html)))->squish()->toString());

        return $text !== '' || str_contains($html, '<br') || str_contains($html, '<li');
    }
}
