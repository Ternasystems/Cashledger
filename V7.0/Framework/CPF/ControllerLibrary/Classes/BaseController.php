<?php

declare(strict_types=1);

namespace TS_Controller\Classes;


use TS_Configuration\Classes\AbstractCls;
use TS_Http\Classes\RedirectResponse;
use TS_Http\Classes\Response;

/**
 * The lean, foundational base class for ALL controllers (both API and Application).
 * It provides universal helper methods for creating common response types.
 * This class has no dependencies.
 */
abstract class BaseController extends AbstractCls
{
    /**
     * Creates a response that redirects the user to a specific URL.
     */
    protected function redirect(string $url): RedirectResponse
    {
        return new RedirectResponse($url);
    }

    /**
     * Creates a JSON response.
     */
    protected function json(mixed $data, int $statusCode = 200): Response
    {
        $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        return new Response($content, $statusCode, ['Content-Type' => 'application/json']);
    }
}

