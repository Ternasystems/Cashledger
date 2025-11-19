<?php

namespace API_Teller_Controller;

use API_Administration_Controller\AbstractController;
use API_Teller_Facade\TellerFacade;

/**
 * The concrete TellerController.
 * It extends the AbstractController and is now extremely simple.
 */
class TellerController extends AbstractController
{
    /**
     * This tells the parent class what the default resource type is
     * if the 'controller' query param is not provided.
     */
    protected string $defaultResourceType = 'Teller';

    /**
     * We inject our specific TellerFacade for type safety.
     * We then pass it up to the parent constructor.
     */
    public function __construct(TellerFacade $facade)
    {
        parent::__construct($facade);
    }
}