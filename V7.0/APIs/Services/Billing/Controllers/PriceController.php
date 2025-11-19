<?php

namespace API_Billing_Controller;

use API_Administration_Controller\AbstractController;
use API_Billing_Facade\PriceFacade;

/**
 * The concrete PriceController.
 * It extends the AbstractController and is now extremely simple.
 */
class PriceController extends AbstractController
{
    /**
     * This tells the parent class what the default resource type is
     * if the 'controller' query param is not provided.
     */
    protected string $defaultResourceType = 'Price';

    /**
     * We inject our specific PriceFacade for type safety.
     * We then pass it up to the parent constructor.
     */
    public function __construct(PriceFacade $facade)
    {
        parent::__construct($facade);
    }
}