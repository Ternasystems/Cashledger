<?php

namespace APP_Administration_Controller;

use API_Administration_Controller\LanguageController;
use Exception;
use TS_Controller\Classes\BaseController;
use TS_DependencyInjection\Classes\ServiceLocator;
use TS_Utility\Classes\UrlGenerator;

class Controller extends BaseController
{
    protected LanguageController $languageController;

    /**
     * @throws Exception
     */
    public function __construct(UrlGenerator $_urlGenerator)
    {
        parent::__construct($_urlGenerator);

        // Set the Exception property
        $this->SetException();
        $this->languageController = ServiceLocator::GetController('API_Administration_Controller\LanguageController');
    }
}