<?php
/*
|--------------------------------------------------------------------------
| Presentation Application Routes
|--------------------------------------------------------------------------
|
| All routes defined here are automatically prefixed by:
| '/{lang}/Presentation'
|
*/

use App_Presentation_Controller\HomeController;
use TS_Http\Classes\Router;

/** @var Router $router */
// $router is the router instance, passed from the group closure

// This matches /cashledger/en-US/Presentation/Home/index
$router->get('/', [HomeController::class, 'index']);
$router->get('/Home/index', [HomeController::class, 'index']);