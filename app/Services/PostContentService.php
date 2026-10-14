<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;
use Illuminate\Support\Str;

class PostContentService
{
    /**
     * Allowed HTML tags in post content.
     *
     * @var list<string>
     */
    protected static array $allowedTags = [
        'p', 'h2', 'h3', 'h4', 'strong', 'b', 'em', 'i', 'u', 's',
        'blockquote', 'ul', 'ol', 'li', 'a', 'img', 'pre', 'code', 'hr', 'br',
        'span', 'div', 'figure', 'figcaption',
    ];

    /**
     * Check if a text string contains HTML tags.
     */
    public static function isHtml(?string $content): bool
    {
        if (empty($content)) {
            return false;
        }

        return (bool) preg_match('/<\s*(p|h[1-6]|ul|ol|li|blockquote|div|table|img|a|strong|em|u)\b/i', $content);
    }

    /**
     * Convert legacy Markdown or plain text content to editor-ready HTML.
     */
    public static function toEditorHtml(?string $body): string
    {
        if (empty($body)) {
            return '';
        }

        // If it's already HTML from the WYSIWYG editor, sanitize and return
        if (static::isHtml($body)) {
            return static::cleanHtml($body);
        }

        // Convert legacy Markdown / newlines to clean HTML
        $html = Str::markdown($body, [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]);

        return static::cleanHtml($html);
    }

    /**
     * Render post body for public display (sanitized HTML).
     */
    public static function renderHtml(?string $body): string
    {
        if (empty($body)) {
            return '';
        }

        if (static::isHtml($body)) {
            return static::cleanHtml($body);
        }

        // Legacy format: convert Markdown to HTML safely
        $html = Str::markdown($body, [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]);

        return static::cleanHtml($html);
    }

    /**
     * Sanitize and format HTML content using DOMDocument.
     * Strips dangerous tags, event handlers, and enforces safe URL protocols.
     */
    public static function cleanHtml(string $html): string
    {
        $trimmed = trim($html);
        if ($trimmed === '') {
            return '';
        }

        $doc = new DOMDocument();
        $previousErrors = libxml_use_internal_errors(true);

        // Prepend utf-8 encoding hint to prevent UTF-8 encoding distortion
        $loaded = $doc->loadHTML('<?xml encoding="utf-8" ?><body>'.$trimmed.'</body>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrors);

        if (! $loaded) {
            return e($trimmed);
        }

        $bodyNode = $doc->getElementsByTagName('body')->item(0);
        if (! $bodyNode) {
            return e($trimmed);
        }

        static::sanitizeNode($bodyNode);

        // Extract inner HTML of body
        $cleanHtml = '';
        foreach ($bodyNode->childNodes as $child) {
            $cleanHtml .= $doc->saveHTML($child);
        }

        return trim($cleanHtml);
    }

    /**
     * Recursively sanitize a DOMNode according to whitelist rules.
     */
    protected static function sanitizeNode(DOMNode $node): void
    {
        if ($node->nodeType === XML_ELEMENT_NODE && $node instanceof DOMElement) {
            $tagName = strtolower($node->nodeName);

            // Container tag - do not remove body, just process its children
            if ($tagName === 'body') {
                for ($i = $node->childNodes->length - 1; $i >= 0; $i--) {
                    $child = $node->childNodes->item($i);
                    if ($child) {
                        static::sanitizeNode($child);
                    }
                }
                return;
            }

            // If not in allowed tags, remove node entirely
            if (! in_array($tagName, static::$allowedTags, true)) {
                $parent = $node->parentNode;
                if ($parent) {
                    $parent->removeChild($node);
                }

                return;
            }

            // Clean attributes
            if ($node->hasAttributes()) {
                for ($i = $node->attributes->length - 1; $i >= 0; $i--) {
                    $attr = $node->attributes->item($i);
                    $attrName = strtolower($attr->name);

                    // 1. Strip all JavaScript event handlers (onclick, onerror, onload, etc.)
                    if (str_starts_with($attrName, 'on')) {
                        $node->removeAttribute($attrName);

                        continue;
                    }

                    // 2. Tag-specific attribute rules
                    if ($tagName === 'a') {
                        if ($attrName === 'href') {
                            $href = trim($attr->value);
                            // Only allow safe protocols: http, https, mailto, or relative path/anchor
                            if (! preg_match('/^(https?:\/\/|\/|#|mailto:)/i', $href)) {
                                $node->removeAttribute('href');
                            }
                        } elseif (! in_array($attrName, ['target', 'rel', 'title', 'class'], true)) {
                            $node->removeAttribute($attrName);
                        }
                    } elseif ($tagName === 'img') {
                        if ($attrName === 'src') {
                            $src = trim($attr->value);
                            // Only allow http(s) or local storage URLs
                            if (! preg_match('/^(https?:\/\/|\/storage\/|\/images\/)/i', $src)) {
                                $node->removeAttribute('src');
                            }
                        } elseif (! in_array($attrName, ['alt', 'title', 'class', 'loading', 'width', 'height'], true)) {
                            $node->removeAttribute($attrName);
                        }
                    } elseif (! in_array($attrName, ['class', 'title', 'id'], true)) {
                        $node->removeAttribute($attrName);
                    }
                }
            }

            // Post-attribute normalization
            if ($tagName === 'a') {
                $href = $node->getAttribute('href');
                if (empty($href)) {
                    // Remove useless <a> without href by replacing with text content
                    $parent = $node->parentNode;
                    if ($parent) {
                        $textNode = $node->ownerDocument->createTextNode($node->textContent);
                        $parent->replaceChild($textNode, $node);

                        return;
                    }
                } else {
                    $node->setAttribute('rel', 'noopener noreferrer');
                    if (! $node->hasAttribute('target')) {
                        $node->setAttribute('target', '_blank');
                    }
                    $existingClass = $node->getAttribute('class');
                    if (! str_contains($existingClass, 'editor-content-link')) {
                        $node->setAttribute('class', trim($existingClass.' editor-content-link text-decoration-underline'));
                    }
                }
            } elseif ($tagName === 'img') {
                $src = $node->getAttribute('src');
                if (empty($src)) {
                    $parent = $node->parentNode;
                    if ($parent) {
                        $parent->removeChild($node);
                    }

                    return;
                }

                $existingClass = $node->getAttribute('class');
                if (! str_contains($existingClass, 'editor-content-image')) {
                    $node->setAttribute('class', trim($existingClass.' editor-content-image'));
                }
                $node->setAttribute('loading', 'lazy');
            }
        }

        // Process children backwards so removals don't skip indices
        for ($i = $node->childNodes->length - 1; $i >= 0; $i--) {
            $child = $node->childNodes->item($i);
            if ($child) {
                static::sanitizeNode($child);
            }
        }
    }
}
