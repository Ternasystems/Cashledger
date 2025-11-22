<?php

namespace App_Presentation_Controller;

use API_Administration_Contract\IParameterService;
use API_Administration_Service\ReloadMode;
use Exception;
use TS_Controller\Classes\Controller;
use TS_Http\Classes\Response;

class HomeController extends Controller
{
    public function __construct(private IParameterService $parameterService){}

    /* Actions */

    // Home page
    public function index(): Response
    {
        try {
            // 1. Fetch AppVersion using the Service
            // This logic is shared, safe, and fast (cached).
            $param = $this->parameterService->getParameter('AppVersion', ReloadMode::YES);

            $version = '';
            if ($param)
                $version = $param->it()->ParamUValue ?? $param->it()->ParamValue ?? '1.0.0';

            // 2. Render the View
            // For now, wrap the string in a Response object
            return new Response("Welcome to Cashledger (Version: $version)", 200);

        } catch (Exception $e) {
            // Log error here if needed
            return new Response("Error: " . $e->getMessage(), 500);
        }
    }
}