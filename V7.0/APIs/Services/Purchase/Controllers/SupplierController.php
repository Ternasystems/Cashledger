<?php

namespace API_Purchase_Controller;

use API_Administration_Controller\AbstractController;
use API_Purchase_Facade\SupplierFacade;

/**
 * The concrete SupplierController.
 * It extends the AbstractController and is now extremely simple.
 */
class SupplierController extends AbstractController
{
    /**
     * This tells the parent class what the default resource type is
     * if the 'controller' query param is not provided.
     */
    protected string $defaultResourceType = 'Supplier';

    /**
     * We inject our specific SupplierFacade for type safety.
     * We then pass it up to the parent constructor.
     */
    public function __construct(SupplierFacade $facade)
    {
        parent::__construct($facade);
    }
}