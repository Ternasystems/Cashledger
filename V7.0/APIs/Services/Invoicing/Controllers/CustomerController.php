<?php

namespace API_Invoicing_Controller;

use API_Administration_Controller\AbstractController;
use API_Invoicing_Facade\CustomerFacade;

/**
 * The concrete CustomerController.
 * It extends the AbstractController and is now extremely simple.
 */
class CustomerController extends AbstractController
{
    /**
     * This tells the parent class what the default resource type is
     * if the 'controller' query param is not provided.
     */
    protected string $defaultResourceType = 'Customer';

    /**
     * We inject our specific CustomerFacade for type safety.
     * We then pass it up to the parent constructor.
     */
    public function __construct(CustomerFacade $facade)
    {
        parent::__construct($facade);
    }
}