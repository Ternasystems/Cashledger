<?php

namespace API_Administration_Controller;

use API_Administration_Contract\ILanguageService;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class LanguageController extends BaseController
{
    protected ILanguageService $service;

    public function __construct(ILanguageService $service)
    {
        $this->service = $service;
    }

    /**
     * Gets a paginated list of languages.
     * Responds to URLs like: /index.php?controller=Language&action=index
     */
    public function index(Request $request): Response
    {
        $result = $this->service->GetLanguages(null, null, null);

        return $this->json($result);
    }
}