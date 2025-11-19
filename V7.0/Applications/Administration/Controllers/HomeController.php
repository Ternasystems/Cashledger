<?php

namespace App_Administration_Controller;

use API_Administration_Contract\IParameterService;
use API_Administration_Service\ReloadMode;
use Exception;
use TS_Controller\Classes\Controller;

class HomeController extends Controller
{
    function __construct(private IParameterService $parameterService){}

    /* Actions */
    public function AppVersion(): string
    {
        try{
            $param = $this->parameterService->getParameter('AppVersion', ReloadMode::YES);
            if ($param){
                return $param->it()->ParamUValue ?? $param->it()->ParamValue ?? '';
            }
            return '';

        } catch (Exception $e){
            return $e->getMessage();
        }
    }
}