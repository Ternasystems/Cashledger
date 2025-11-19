<?php

namespace API_Administration_Controller;

use API_Administration_Facade\LanguageFacade;

/**
 * The concrete LanguageController.
 * It extends the AbstractController and is now extremely simple.
 */
class LanguageController extends AbstractController
{
    /**
     * This tells the parent class what the default resource type is
     * if the 'controller' query param is not provided.
     */
    protected string $defaultResourceType = 'Language';

    /**
     * We inject our specific LanguageFacade for type safety.
     * We then pass it up to the parent constructor.
     */
    public function __construct(LanguageFacade $facade)
    {
        parent::__construct($facade);
    }
}