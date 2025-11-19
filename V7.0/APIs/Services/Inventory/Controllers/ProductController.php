<?php

namespace API_Inventory_Controller;

use API_Administration_Controller\AbstractController;
use API_Inventory_Facade\ProductFacade;

/**
 * The concrete ProductController.
 * It extends the AbstractController and is now extremely simple.
 */
class ProductController extends AbstractController
{
    /**
     * This tells the parent class what the default resource type is
     * if the 'controller' query param is not provided.
     */
    protected string $defaultResourceType = 'Product';

    /**
     * We inject our specific ProductFacade for type safety.
     * We then pass it up to the parent constructor.
     */
    public function __construct(ProductFacade $facade)
    {
        parent::__construct($facade);
    }
}