<?php

declare(strict_types=1);

namespace TS_Controller\Classes;

use ReflectionClass;
use TS_Configuration\Classes\ConfigurationService;
use TS_DependencyInjection\Classes\ServiceLocator;
use TS_Exception\Classes\DIException;
use TS_Exception\Classes\ViewException;
use TS_Http\Classes\FlashMessageService;
use TS_Http\Classes\RedirectResponse;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;
use TS_Locale\Classes\Translator;
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
    protected readonly ConfigurationService $config;
    protected readonly Request $request;
    protected readonly Translator $translator;
    protected readonly string $lang;

    /**
     * Defines the default layout file for all views rendered by this controller.
     * Can be overridden by child controllers. Set to null to render without a layout.
     * The path is relative to the base view path, using dot notation (e.g., 'Layouts.Admin').
     */
    protected ?string $layout = null;

    /**
     * @throws DIException
     */
    public function __construct()
    {
        $this->view = ServiceLocator::get(View::class);
        $this->flash = ServiceLocator::get(FlashMessageService::class);
        $this->urlGenerator = ServiceLocator::get(UrlGenerator::class);
        $this->config = ServiceLocator::get(ConfigurationService::class);
        $this->request = ServiceLocator::get(Request::class); // <-- ADDED
        $this->translator = ServiceLocator::get(Translator::class); // <-- ADDED

        // Determine the current language from the URL (segment 1)
        // Fall back to the default language from config
        $this->lang = $this->request->getSegment(1) ?? $this->config->getDefaultLanguage();

        // *** NEW ***
        // Automatically share config data (like image paths) with all views
        $this->shareViewData();
    }

    /**
     * Shares global data from the config service with the View.
     * This replaces the old global $ViewData array.
     */
    private function shareViewData(): void
    {
        // Pass the translator and lang to the view
        $this->view->setTranslator($this->translator); // <-- ADDED
        $this->view->share('lang', $this->lang); // <-- ADDED

        // Share all image paths under an 'images' variable
        $this->view->share('images', $this->config->getImages());
        $this->view->share('companyName', $this->config->getCompanyName());
        $this->view->share('languages', $this->config->getLanguages());
        $this->view->share('defaultLang', $this->config->getDefaultLanguage());
        $this->view->share('defaultApp', $this->config->getDefaultApp());
        $this->view->share('paths', $this->config->getPaths());
        $this->view->share('ip', $this->config->getIP());
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

        // *** UPDATED ***
        // Automatically add the current language to all redirects.
        $defaultParams = ['lang' => $this->lang];

        // This assumes your UrlGenerator can handle a flat array of parameters.
        $urlParams = array_merge($defaultParams, $params, [
            'controller' => $controllerName,
            'action' => $action
        ]);

        $url = $this->urlGenerator->generate($urlParams);
        return $this->redirect($url);
    }

    private function getShortClassName(): string
    {
        return str_replace('Controller', '', new ReflectionClass($this)->getShortName());
    }
}

