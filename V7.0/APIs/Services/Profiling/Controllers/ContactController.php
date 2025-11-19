<?php

namespace API_Profiling_Controller;

use API_Administration_Controller\AbstractController;

/**
 * The concrete ContactController.
 * It extends the AbstractController and is now extremely simple.
 */
class ContactController extends AbstractController
{
    /**
     * This tells the parent class what the default resource type is
     * if the 'controller' query param is not provided.
     */
    protected string $defaultResourceType = 'Contact';

    /**
     * We inject our specific ContactFacade for type safety.
     * We then pass it up to the parent constructor.
     */
    public function __construct(ContactFacade $facade)
    {
        parent::__construct($facade);
    }
}