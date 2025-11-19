<?php
/*
|--------------------------------------------------------------------------
| Administration Application Routes
|--------------------------------------------------------------------------
|
| All routes defined here are automatically prefixed by:
| '/{lang}/Administration'
|
*/

use APP_Administration_Controller\HomeController;
use TS_Http\Classes\Router;

/** @var Router $router */
// $router is the router instance, passed from the group closure

// Dashboard (default page for this app)
$router->get('/', [HomeController::class, 'AppVersion']);