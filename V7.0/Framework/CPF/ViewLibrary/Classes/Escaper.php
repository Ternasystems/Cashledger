<?php

declare(strict_types=1);

namespace TS_View\Classes;

use TS_Configuration\Classes\AbstractCls;

/**
 * A service for context-aware output escaping to prevent XSS attacks.
 */
class Escaper extends AbstractCls
{
    /**
     * Escapes a string for safe use inside an HTML body.
     *
     * @param string|null $string The string to escape.
     * @return string The escaped string.
     */
    public function html(?string $string): string
    {
        if ($string === null) {
            return '';
        }
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Escapes a string for safe use inside an HTML attribute value.
     *
     * @param string|null $string The string to escape.
     * @return string The escaped string.
     */
    public function attr(?string $string): string
    {
        // For attributes, htmlspecialchars is also the correct function.
        return $this->html($string);
    }

    /**
     * Escapes a string for safe embedding within a JavaScript string literal.
     *
     * @param string|null $string The string to escape.
     * @return string The JSON-encoded and escaped string.
     */
    public function js(?string $string): string
    {
        if ($string === null) {
            return '""';
        }
        // json_encode is the safest way to pass a string to JavaScript.
        // It handles quotes, backslashes, and other control characters.
        return json_encode($string, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    }

    /**
     * Escapes a string for safe use as a component in a URL.
     *
     * @param string|null $string The string to escape.
     * @return string The URL-encoded string.
     */
    public function url(?string $string): string
    {
        if ($string === null) {
            return '';
        }
        return rawurlencode($string);
    }
}