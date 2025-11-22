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

use TS_Configuration\Classes\ConfigurationService;
use TS_DependencyInjection\Classes\ServiceLocator;
use TS_Http\Classes\RedirectResponse;
use TS_Http\Classes\Router;
use TS_Utility\Classes\UrlGenerator;

/** @var Router $router */
$router = ServiceLocator::get(Router::class);

// --- Global/Root Routes ---
$router->get('/', function() {
    $config = ServiceLocator::get(ConfigurationService::class);
    $defaultLang = $config->getDefaultLanguage() ?? 'en-US';

    $urlGen = ServiceLocator::get(UrlGenerator::class);

    // This generates: /cashledger/fr-FR/Presentation/Home/index
    return new RedirectResponse(
        $urlGen->generate([
            'lang' => $defaultLang,
            'application' => 'Presentation',
            'controller' => 'Home',
            'action' => 'index'
        ])
    );
});

// --- Application Route Groups ---
$router->group('/{lang}', function($router) {

    // Presentation App
    $router->group('/Presentation', function($router) {
        require_once __DIR__ . '/../Applications/Presentation/routes.php';
    });
});