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

// Dashboard (default page for this app)
$router->get('/', [HomeController::class, 'index']);
$router->get('/dashboard', [HomeController::class, 'index']);

// Login
$router->get('/login', [HomeController::class, 'login']);
$router->post('/login', [HomeController::class, 'loginSubmit']);

// Logout
$router->get('/logout', [HomeController::class, 'logout']);