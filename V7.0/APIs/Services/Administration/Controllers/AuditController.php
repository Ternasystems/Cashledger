<?php

namespace API_Administration_Controller;

use API_Administration_Facade\AuditFacade;

/**
 * The concrete AuditController.
 * It extends the AbstractController and is now extremely simple.
 */
class AuditController extends AbstractController
{
    /**
     * This tells the parent class what the default resource type is
     * if the 'controller' query param is not provided.
     */
    protected string $defaultResourceType = 'Audit';

    /**
     * We inject our specific AuditFacade for type safety.
     * We then pass it up to the parent constructor.
     */
    public function __construct(AuditFacade $facade)
    {
        parent::__construct($facade);
    }
}