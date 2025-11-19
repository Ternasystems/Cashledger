<?php

namespace API_Teller_Controller;

use API_Administration_Controller\AbstractController;
use API_Teller_Facade\TellerSessionFacade;

/**
 * The concrete TellerSessionController.
 * It extends the AbstractController and is now extremely simple.
 */
class TellerSessionController extends AbstractController
{
    /**
     * This tells the parent class what the default resource type is
     * if the 'controller' query param is not provided.
     */
    protected string $defaultResourceType = 'TellerSession';

    /**
     * We inject our specific TellerSessionFacade for type safety.
     * We then pass it up to the parent constructor.
     */
    public function __construct(TellerSessionFacade $facade)
    {
        parent::__construct($facade);
    }
}