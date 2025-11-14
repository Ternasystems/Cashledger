<?php

namespace App_Administration_Controller;

use API_Administration_Controller\ParameterController;
use TS_Controller\Classes\Controller;

class AdministrationController extends Controller
{
    function __construct(private ParameterController $parameterController){}

    /* Actions */
    public function AppVersion(): string
    {
    }
}