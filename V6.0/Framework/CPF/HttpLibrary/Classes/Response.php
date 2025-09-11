<?php

declare(strict_types=1);

namespace TS_Http\Classes;

use TS_Configuration\Classes\AbstractCls;

/**
 * Represents an HTTP response.
 */
class Response extends AbstractCls
{
    public function __construct(
        public readonly string $content = '',
        public readonly int $statusCode = 200,
        public readonly array $headers = []
    ) {
    }

    /**
     * Sends the HTTP response to the browser.
     * This should be called once at the end of the request lifecycle.
     */
    public function send(): void
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        echo $this->content;
    }
}