<?php

namespace API_Teller_Controller;

use API_Administration_Controller\AbstractController;
use API_Teller_Facade\CashFigureFacade;

/**
 * The concrete CashFigureController.
 * It extends the AbstractController and is now extremely simple.
 */
class CashFigureController extends AbstractController
{
    /**
     * This tells the parent class what the default resource type is
     * if the 'controller' query param is not provided.
     */
    protected string $defaultResourceType = 'CashFigure';

    /**
     * We inject our specific CashFigureFacade for type safety.
     * We then pass it up to the parent constructor.
     */
    public function __construct(CashFigureFacade $facade)
    {
        parent::__construct($facade);
    }
}