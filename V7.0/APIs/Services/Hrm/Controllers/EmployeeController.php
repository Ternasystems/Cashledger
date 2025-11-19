<?php

namespace API_Hrm_Controller;

use API_Administration_Controller\AbstractController;
use API_Hrm_Facade\EmployeeFacade;

/**
 * The concrete EmployeeController.
 * It extends the AbstractController and is now extremely simple.
 */
class EmployeeController extends AbstractController
{
    /**
     * This tells the parent class what the default resource type is
     * if the 'controller' query param is not provided.
     */
    protected string $defaultResourceType = 'Employee';

    /**
     * We inject our specific EmployeeFacade for type safety.
     * We then pass it up to the parent constructor.
     */
    public function __construct(EmployeeFacade $facade)
    {
        parent::__construct($facade);
    }
}