<?php

namespace API_Teller_Controller;

use API_Administration_Controller\AbstractController;
use API_Teller_Facade\TellerAuditFacade;

/**
 * The concrete TellerAuditController.
 * It extends the AbstractController and is now extremely simple.
 */
class TellerAuditController extends AbstractController
{
    /**
     * This tells the parent class what the default resource type is
     * if the 'controller' query param is not provided.
     */
    protected string $defaultResourceType = 'TellerAudit';

    /**
     * We inject our specific TellerAuditFacade for type safety.
     * We then pass it up to the parent constructor.
     */
    public function __construct(TellerAuditFacade $facade)
    {
        parent::__construct($facade);
    }
}