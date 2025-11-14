<?php
/*
|--------------------------------------------------------------------------
| Cashledger API Bootstrap
|--------------------------------------------------------------------------
|
| This file is the single entry point for all API requests.
| It uses the query parameter routing (e.g., ?controller=...&action=...)
| that the API controllers expect.
|
*/

declare(strict_types=1);

// NO MORE global $ViewData;

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
use TS_Http\Classes\Response;
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

    // 8. === API Query Parameter Routing ===

    // ** We get defaults from the ConfigurationService now, not $ViewData **
    /** @var \TS_Configuration\Classes\ConfigurationService $config */
    $config = $application->get(\TS_Configuration\Classes\ConfigurationService::class);
    $defaultLang = $config->getDefaultLanguage();

    // Get the Query strings
    $lang = $request->getQuery('lang', $defaultLang);
    $app = $request->getQuery('app', 'Profiling'); // Default to an API app
    $ctrl = $request->getQuery('controller', 'Profile');
    $action = $request->getQuery('action', 'index');

    // ... (rest of the API routing logic is the same) ...
    $controllerName = 'API_' . ucfirst($app) . '_Controller\\' . ucfirst($ctrl) . 'Controller';

    if (!class_exists($controllerName)) {
        throw new \TS_Exception\Classes\ControllerException('controller_not_found', [':name' => $controllerName], 404);
    }

    $controller = $application->get($controllerName);

    if (!method_exists($controller, $action)) {
        throw new \TS_Exception\Classes\ControllerException('action_not_found', [':name' => $action], 404);
    }

    $method = new \ReflectionMethod($controller, $action);
    $params = [];
    if (count($method->getParameters()) === 1 && $method->getParameters()[0]->getType()->getName() === Request::class) {
        $params[] = $request;
    }

    /** @var Response $response */
    $response = $controller->{$action}(...$params);

    // 9. Send the response to the client
    $response->send();

} catch (AbstractException $e) {
    // ... (error handling is the same) ...
    http_response_code($e->getCode() ?: 500);
    echo json_encode(['error' => $e->getTranslatedMessage()]);

} catch (Exception $e) {
    // ... (error handling is the same) ...
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}