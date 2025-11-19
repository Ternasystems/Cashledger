<?php

namespace App_Presentation_Controller;

use API_Profiling_Contract\IAuthenticationService;
use TS_Controller\Classes\Controller;
use TS_Http\Classes\Response;

class HomeController extends Controller
{
    public function __construct(private IAuthenticationService $authenticationService){}

    /* Actions */

    // Home page
    public function index(): Response{}
}