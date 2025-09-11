<?php

namespace TS_Controller\Classes;

use Exception;
use TS_Exception\Classes\ControllerException;
use TS_Http\Response;
use TS_Utility\Classes\UrlGenerator;
use TS_View\View;

class Controller extends BaseController
{
    /**
     * The constructor is now parameterless to simplify inheritance.
     */
    public function __construct(protected readonly View $view, UrlGenerator $urlGenerator)
    {
        parent::__construct($urlGenerator);
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
}