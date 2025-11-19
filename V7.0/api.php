<?php
/*
|--------------------------------------------------------------------------
| Cashledger API Bootstrap
|--------------------------------------------------------------------------
|
| This file is the single entry point for all API requests.
| It has been refactored to use the central Router, just like index.php.
|
*/

declare(strict_types=1);

// 1. Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Register the autoloader
require_once __DIR__ . '/autoload.php';

use TS_DependencyInjection\Classes\ServiceLocator;
use TS_Exception\Classes\AbstractException;
use TS_Http\Classes\Request;
use TS_Http\Classes\Router; // <-- We will use the Router
use TS_Locale\Classes\Translator;

try {
    // 4. Register all application services (DI container)
    $application = require_once __DIR__ . '/services.php';

    // 5. Set the global ServiceLocator
    ServiceLocator::setApplication($application);

    // 6. (Optional) Set the global translator for exceptions
    if ($application->has(Translator::class)) {
        AbstractException::setTranslator($application->get(Translator::class));
    }

    // 7. Create the request
    $request = Request::createFromGlobals();

    // 8. === Load API Routes ===
    // We load our new API-specific routes.
    // The .htaccess file ensures that all requests starting with /api/
    // are sent to this file. The Router will handle the rest.
    require_once __DIR__ . '/routes/api.php';

    // 9. === Dispatch the Router ===
    // All the complex "?app=" and "?controller=" logic is gone.
    // The Router now handles everything, just like in index.php.
    /** @var Router $router */
    $router = ServiceLocator::get(Router::class);
    $response = $router->dispatch($request);

    // 10. Send the response to the client
    $response->send();

} catch (AbstractException $e) {
    // Handle framework-specific exceptions (e.g., 404, 500)
    http_response_code($e->getCode() ?: 500);
    echo json_encode(['error' => $e->getTranslatedMessage()]);

} catch (Exception $e) {
    // Handle all other unexpected errors
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}