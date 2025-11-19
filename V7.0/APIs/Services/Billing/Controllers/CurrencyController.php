<?php

namespace API_Billing_Controller;



use API_Administration_Controller\AbstractController;

/**
 * The concrete CurrencyController.
 * It extends the AbstractController and is now extremely simple.
 */
class CurrencyController extends AbstractController
{
    /**
     * This tells the parent class what the default resource type is
     * if the 'controller' query param is not provided.
     */
    protected string $defaultResourceType = 'Currency';

    /**
     * We inject our specific CurrencyFacade for type safety.
     * We then pass it up to the parent constructor.
     */
    public function __construct(CurrencyFacade $facade)
    {
        parent::__construct($facade);
    }
}