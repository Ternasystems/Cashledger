<?php

declare(strict_types=1);

namespace TS_Controller\Classes;

use ReflectionClass;
use TS_Exception\Classes\ControllerException;
use TS_Http\RedirectResponse;
use TS_Http\Response;
use TS_Utility\Classes\UrlGenerator;

/**
 * A modern, lean base class for all controllers in the application.
 * It provides helper methods for returning common response types like views,
 * redirects, and JSON, abstracting away the direct handling of HTTP responses.
 */
abstract class BaseController
{
    /**
     * The constructor is now parameterless to simplify inheritance.
     */
    public function __construct(protected readonly UrlGenerator $urlGenerator)
    {
    }

    /**
     * Creates a response that redirects the user to a specific URL.
     *
     * @param string $url The absolute URL to redirect to.
     * @return RedirectResponse A specialized Response object with the Location header.
     */
    protected function redirect(string $url): RedirectResponse
    {
        return new RedirectResponse($url);
    }

    /**
     * Creates a response that redirects to another controller action within the application.
     *
     * @param string $action The name of the action method.
     * @param string|null $controller The short name of the controller (e.g., 'Home'). Defaults to the current controller.
     * @param array<string, string> $params Additional parameters for the URL.
     * @return RedirectResponse
     * @throws ControllerException
     */
    protected function redirectToAction(string $action, ?string $controller = null, array $params = []): RedirectResponse
    {
        if (!$this->urlGenerator)
            throw new ControllerException('url_generator_not_set', [], 500);

        $controllerName = $controller ?? $this->getShortClassName();
        $urlParams = array_merge($params, ['action' => $action, 'controller' => $controllerName]);

        $url = $this->urlGenerator->generate($urlParams);
        return $this->redirect($url);
    }

    /**
     * Creates a JSON response.
     *
     * @param mixed $data The data to be JSON-encoded.
     * @param int $statusCode The HTTP status code.
     * @return Response A Response object with the application/json content type.
     */
    protected function json(mixed $data, int $statusCode = 200): Response
    {
        $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        return new Response($content, $statusCode, ['Content-Type' => 'application/json']);
    }

    /**
     * A utility method to get the short name of the current controller class.
     * For example, 'PresentationHomeController' becomes 'PresentationHome'.
     *
     * @return string The short name of the class.
     */
    private function getShortClassName(): string
    {
        $longName = new ReflectionClass($this)->getShortName();
        return str_replace('Controller', '', $longName);
    }
}
