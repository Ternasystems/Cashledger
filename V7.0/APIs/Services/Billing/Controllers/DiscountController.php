<?php

namespace API_Billing_Controller;

use API_Administration_Controller\AbstractController;
use API_Billing_Facade\DiscountFacade;

/**
 * The concrete DiscountController.
 * It extends the AbstractController and is now extremely simple.
 */
class DiscountController extends AbstractController
{
    /**
     * This tells the parent class what the default resource type is
     * if the 'controller' query param is not provided.
     */
    protected string $defaultResourceType = 'Discount';

    /**
     * We inject our specific DiscountFacade for type safety.
     * We then pass it up to the parent constructor.
     */
    public function __construct(DiscountFacade $facade)
    {
        parent::__construct($facade);
    }
}