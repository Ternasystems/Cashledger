<?php

declare(strict_types=1);

namespace TS_Controller\Classes;

use ReflectionClass;
use ReflectionException;
use TS_Exception\Classes\ViewException;
use TS_Http\Classes\FlashMessageService;
use TS_Http\Classes\RedirectResponse;
use TS_Http\Classes\Response;
use TS_Utility\Classes\UrlGenerator;
use TS_View\Classes\View;

/**
 * The base class for all Application (UI) controllers.
 * It is now "layout-aware" to seamlessly integrate with the advanced View system.
 */
class Controller extends BaseController
{
    protected readonly View $view;
    protected readonly FlashMessageService $flash;
    protected readonly UrlGenerator $urlGenerator;

    /**
     * Defines the default layout file for all views rendered by this controller.
     * Can be overridden by child controllers. Set to null to render without a layout.
     * The path is relative to the base view path, using dot notation (e.g., 'Layouts.Admin').
     */
    protected ?string $layout = null;

    public function __construct(View $view, FlashMessageService $flash, UrlGenerator $urlGenerator)
    {
        $this->view = $view;
        $this->flash = $flash;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Renders a view template, automatically applying the controller's layout,
     * and wraps it in an HTML response.
     *
     * @param string $viewName Path to the view, e.g., "Home.Templates.Index"
     * @param array<string, mixed> $data Data to make available to the view.
     * @throws ViewException
     */
    protected function view(string $viewName, array $data = [], int $statusCode = 200): Response
    {
        // If a layout is defined in the controller, apply it.
        if ($this->layout !== null) {
            $this->view->layout($this->layout);
        }

        $content = $this->view->render($viewName, $data);

        return new Response($content, $statusCode, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    /**
     * Renders a view WITHOUT a layout. Useful for fetching HTML fragments for AJAX requests.
     * @throws ViewException
     */
    protected function partial(string $viewName, array $data = [], int $statusCode = 200): Response
    {
        $content = $this->view->renderPartial($viewName, $data);
        return new Response($content, $statusCode, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    /**
     * Creates a response that redirects to another controller action within the application.
     *
     * @param string $action The name of the action method.
     * @param string|null $controller The short name of the controller (e.g., 'Home'). Defaults to the current controller.
     * @param array<string, mixed> $params Additional parameters for the URL.
     * @return RedirectResponse
     */
    protected function redirectToAction(string $action, ?string $controller = null, array $params = []): RedirectResponse
    {
        $controllerName = $controller ?? $this->getShortClassName();

        // This assumes your UrlGenerator can handle a flat array of parameters.
        $urlParams = array_merge($params, [
            'controller' => $controllerName,
            'action' => $action
        ]);

        $url = $this->urlGenerator->generate($urlParams);
        return $this->redirect($url);
    }

    private function getShortClassName(): string
    {
        try {
            return str_replace('Controller', '', new ReflectionClass($this)->getShortName());
        } catch (ReflectionException) {
            return '';
        }
    }
}

