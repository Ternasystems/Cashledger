<?php

declare(strict_types=1);

namespace TS_Http;

final class RedirectResponse extends Response
{
    public function __construct(string $url, int $statusCode = 302)
    {
        parent::__construct('', $statusCode, ['Location' => $url]);
    }
}