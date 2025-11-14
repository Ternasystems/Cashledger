<?php
/*
|--------------------------------------------------------------------------
| Cashledger ERP Bootstrap
|--------------------------------------------------------------------------
|
| This file is the single entry point for all HTTP requests into the
| application. It bootstraps the environment, registers services,
| and dispatches the router to handle the incoming request.
|
*/

declare(strict_types=1);

// 1. Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Register the autoloader
require_once __DIR__ . '/autoload.php';

// 3. --- DELETED StaticData.php include ---
//    (The ConfigurationService now handles this.)

use TS_DependencyInjection\Classes\ServiceLocator;
use TS_Exception\Classes\AbstractException;
use TS_Http\Classes\Request;
use TS_Http\Classes\Router;
use TS_Locale\Classes\Translator;

try {
    // 4. Register all application services (DI container)
    $application = require_once __DIR__ . '/services.php';

    // 5. Set the global ServiceLocator
    ServiceLocator::setApplication($application);

    // 6. Set the global translator for exceptions
    if ($application->has(Translator::class)) {
        AbstractException::setTranslator($application->get(Translator::class));
    }

    // 7. Create the request
    $request = Request::createFromGlobals();

    // 8. Load all application routes
    require_once __DIR__ . '/routes/web.php';

    // 9. Get the router and dispatch the request
    /** @var Router $router */
    $router = ServiceLocator::get(Router::class);
    $response = $router->dispatch($request);

    // 10. Send the response to the client
    $response->send();

} catch (AbstractException $e) {
    // Handle framework-specific exceptions (e.g., 404, 500)
    http_response_code($e->getCode() ?: 500);
    // In production, you'd render a pretty error view here
    echo "<h1>Error " . $e->getCode() . "</h1><p>" . $e->getTranslatedMessage() . "</p>";
    if (getenv('APP_ENV') !== 'production') {
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }

} catch (Exception $e) {
    // Handle all other unexpected errors
    http_response_code(500);
    echo "<h1>An unexpected error occurred</h1><p>" . $e->getMessage() . "</p>";
    if (getenv('APP_ENV') !== 'production') {
        echo "<pre>"."File: ".$e->getFile()."\nLine: ".$e->getLine()."\n\n".$e->getTraceAsString() . "</pre>";
    }
}