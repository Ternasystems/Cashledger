<?php

declare(strict_types=1);

namespace TS_Controller\Classes;

use Exception;
use ReflectionClass;
use ReflectionException;
use TS_Exception\Classes\ControllerException;
use TS_Http\RedirectResponse;
use TS_Http\Response;
use TS_Utility\Classes\UrlGenerator;
use TS_View\View;

/**
 * A modern, lean base class for all controllers in the application.
 * It provides helper methods for returning common response types like views,
 * redirects, and JSON, abstracting away the direct handling of HTTP responses.
 */
abstract class BaseController
{
    /**
     * The constructor uses dependency injection to receive essential services.
     *
     * @param View $view The service responsible for rendering view templates.
     * @param UrlGenerator $urlGenerator The service for generating application-aware URLs.
     */
    public function __construct(
        protected readonly View $view,
        protected readonly UrlGenerator $urlGenerator
    ) {
    }

    /**
     * Creates an HTML response by rendering a view template.
     *
     * @param string $viewName The path to the view file (relative to the configured view path).
     * @param array<string, mixed> $data Data to be extracted into variables for the view.
     * @param int $statusCode The HTTP status code for the response.
     * @return Response A Response object containing the rendered HTML.
     * @throws ControllerException if the view file cannot be found.
     */
    protected function view(string $viewName, array $data = [], int $statusCode = 200): Response
    {
        try {
            $content = $this->view->render($viewName, $data);
            return new Response($content, $statusCode, ['Content-Type' => 'text/html; charset=utf-8']);
        } catch (Exception $e) {
            // Re-throw a more specific, framework-level exception
            throw new ControllerException('view_not_found', [':path' => $viewName], 404, $e);
        }
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
     * @throws ControllerException if the class cannot be reflected.
     */
    private function getShortClassName(): string
    {
        try {
            $longName = new ReflectionClass($this)->getShortName();
            return str_replace('Controller', '', $longName);
        } catch (ReflectionException $e) {
            throw new ControllerException('reflection_error', [':class' => static::class], 500, $e);
        }
    }
}
