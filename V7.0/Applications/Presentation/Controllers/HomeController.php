<?php

namespace App_Presentation_Controller;

use API_Administration_Controller\AppController;
use API_Profiling_Controller\AuthenticationController;
use TS_Controller\Classes\Controller;
use TS_Http\Classes\Response;

class HomeController extends Controller
{
    private AuthenticationController $authApi;
    private AppController $appApi;

    // The dot-notation path to our new layout file
    protected ?string $layout = 'Applications.Assets.Libraries.Applications.Views.Layouts._layout';

    public function __construct(AuthenticationController $authApi, AppController $appApi)
    {
        $this->authApi = $authApi;
        $this->appApi = $appApi;
    }

    /**
     * Renders the main dashboard.
     * This will now be rendered inside our 'Main.php' layout.
     */
    public function index(string $lang): Response
    {
        // $this->shareWithView() is called automatically by the base Controller,
        // so our layout already has $config, $translator, and $lang.

        // We just render the view for this page.
        return $this->view('Applications.Presentation.Views.Templates.Index');
    }

    /**
     * Renders the login page.
     * This will also be rendered inside our 'Main.php' layout.
     */
    public function login(string $lang): Response
    {
        return $this->view('Applications.Presentation.Views.Templates.Login');
    }

    public function loginSubmit(string $lang): Response
    {
        // Handle form submission...
        // On success, redirect to the dashboard
        return $this->redirectToAction('index', null, ['lang' => $lang]);
    }

    public function logout(string $lang): Response
    {
        // Handle logout...
        // On success, redirect to the login page
        return $this->redirectToAction('login', null, ['lang' => $lang]);
    }
}