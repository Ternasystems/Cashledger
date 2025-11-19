<?php

namespace API_Taxes_Controller;

use API_Administration_Controller\AbstractController;
use API_Taxes_Facade\TaxFacade;

/**
 * The concrete TaxController.
 * It extends the AbstractController and is now extremely simple.
 */
class TaxController extends AbstractController
{
    /**
     * This tells the parent class what the default resource type is
     * if the 'controller' query param is not provided.
     */
    protected string $defaultResourceType = 'Tax';

    /**
     * We inject our specific TaxFacade for type safety.
     * We then pass it up to the parent constructor.
     */
    public function __construct(TaxFacade $facade)
    {
        parent::__construct($facade);
    }
}