<?php

declare(strict_types=1);

namespace TS_Controller\Classes;

use ReflectionClass;
use TS_Exception\Classes\ViewException;
use TS_Http\Classes\FlashMessageService;
use TS_Http\Classes\RedirectResponse;
use TS_Http\Classes\Response;
use TS_Utility\Classes\UrlGenerator;
use TS_View\Classes\View;

/**
 * The base class for all Application (UI) controllers.
 * It inherits universal helpers from BaseController and adds dependencies
 * and methods specific to rendering HTML views and handling application-level redirects.
 */
class Controller extends BaseController
{
    protected readonly View $view;
    protected readonly FlashMessageService $flash;
    protected readonly UrlGenerator $urlGenerator;

    /**
     * The framework's DI container will automatically inject these common UI services.
     */
    public function __construct(View $view, FlashMessageService $flash, UrlGenerator $urlGenerator)
    {
        $this->view = $view;
        $this->flash = $flash;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Renders a main view template and wraps it in an HTML response.
     *
     * @param string $viewName Path to the view, e.g., "Home/Templates/Index"
     * @param array<string, mixed> $data Data to make available to the view.
     * @throws ViewException
     */
    protected function view(string $viewName, array $data = [], int $statusCode = 200): Response
    {
        $content = $this->view->render($viewName, $data);
        return new Response($content, $statusCode, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    /**
     * Renders a partial view component and wraps it in an HTML response.
     *
     * @param string $componentName Path to the component, e.g., "Shared/Components/Header"
     * @param array<string, mixed> $data Data to make available to the component.
     * @throws ViewException
     */
    protected function viewComponent(string $componentName, array $data = [], int $statusCode = 200): Response
    {
        $content = $this->view->render($componentName, $data);
        return new Response($content, $statusCode, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    /**
     * Creates a response that redirects to another controller action within the application.
     *
     * @param string $action The name of the action method.
     * @param string|null $controller The short name of the controller (e.g., 'Home'). Defaults to the current controller.
     * @param array<string, string> $params Additional parameters for the URL.
     * @return RedirectResponse
     */
    protected function redirectToAction(string $action, ?string $controller = null, array $params = []): RedirectResponse
    {
        $controllerName = $controller ?? $this->getShortClassName();

        // Ensure controller and action are part of the main parameters
        $urlParams = array_merge($params, [
            'controller' => $controllerName,
            'action' => $action
        ]);

        $url = $this->urlGenerator->generate($urlParams);
        return $this->redirect($url);
    }

    /**
     * A utility method to get the short name of the current controller class.
     * For example, 'HomeController' becomes 'Home'.
     */
    private function getShortClassName(): string
    {
        $longName = new ReflectionClass($this)->getShortName();
        return str_replace('Controller', '', $longName);
    }
}

