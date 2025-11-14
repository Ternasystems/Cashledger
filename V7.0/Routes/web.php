<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you load all of the route files from your
| applications. The 'Router' service is already available via the
| ServiceLocator.
|
*/

use TS_DependencyInjection\Classes\ServiceLocator;
use TS_Exception\Classes\DIException;
use TS_Http\Classes\RedirectResponse;
use TS_Http\Classes\Router;
use TS_Utility\Classes\UrlGenerator;

/** @var Router $router */
try {
    $router = ServiceLocator::get(Router::class);
} catch (DIException $e) {}

// --- Global/Root Routes ---
// Redirect the root to the default language and app dashboard
$router->get('/', function() {
    global $ViewData;
    $defaultLang = $ViewData['DefaultLanguage'] ?? 'en-US';
    // This uses the UrlGenerator service to build a safe URL
    $urlGen = ServiceLocator::get(UrlGenerator::class);
    return new RedirectResponse(
        $urlGen->generate(['lang' => $defaultLang])
    );
});


// --- Application Route Groups ---
// All routes loaded from these files will be automatically
// prefixed with the group's path.

$router->group('/{lang}', function($router) {
    // Routes for the main Presentation App
    $router->group('/Presentation', function($router) {
        require_once __DIR__ . '/../Applications/Presentation/routes.php';
    });

    // Routes for the Profiling App
    $router->group('/Profiling', function($router) {
        // require_once __DIR__ . '/../Applications/Profiling/routes.php';
    });

    // Routes for the Teller App
    $router->group('/Teller', function($router) {
        // require_once __DIR__ . '/../Applications/Teller/routes.php';
    });

    // ... add all other application route files here
});