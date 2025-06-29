<?php

declare(strict_types=1);

namespace TS_Controller\Classes;

use TS_Http\RedirectResponse;
use TS_Http\Response;
use TS_Utility\Classes\UrlGenerator;
use TS_View\View;

/**
 * A modern, lean base class for all controllers in the application.
 */
abstract class BaseController
{
    public function __construct(
        protected readonly View $view,
        protected readonly UrlGenerator $urlGenerator
        // We could also inject a Session manager here
    ) {
    }

    /**
     * Creates an HTML response by rendering a view.
     *
     * @param string $viewName The path to the view file.
     * @param array<string, mixed> $data Data to pass to the view.
     * @param int $statusCode The HTTP status code.
     * @return Response
     */
    protected function view(string $viewName, array $data = [], int $statusCode = 200): Response
    {
        $content = $this->view->render($viewName, $data);
        return new Response($content, $statusCode, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    /**
     * Creates a response that redirects the user to a specific URL.
     *
     * @param string $url The URL to redirect to.
     * @return RedirectResponse
     */
    protected function redirect(string $url): RedirectResponse
    {
        return new RedirectResponse($url);
    }

    /**
     * Creates a response that redirects to another controller action.
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
     * @return Response
     */
    protected function json(mixed $data, int $statusCode = 200): Response
    {
        $content = json_encode($data, JSON_PRETTY_PRINT);
        return new Response($content, $statusCode, ['Content-Type' => 'application/json']);
    }

    private function getShortClassName(): string
    {
        try {
            $longName = (new \ReflectionClass($this))->getShortName();
            return str_replace('Controller', '', $longName);
        } catch (\ReflectionException) {
            return '';
        }
    }
}