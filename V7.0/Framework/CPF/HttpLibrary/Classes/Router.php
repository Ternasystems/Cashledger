<?php

declare(strict_types=1);

namespace TS_Http\Classes;

use Closure;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use TS_Configuration\Classes\AbstractCls;
use TS_Controller\Classes\ActionFilterExecutor;
use TS_DependencyInjection\Classes\Application;
use TS_Exception\Classes\ControllerException;
use TS_Exception\Classes\DIException;
use TS_Exception\Classes\HttpException;

/**
 * A decentralized, powerful router.
 * It matches request URIs to controller actions defined in app-specific route files.
 */
final class Router extends AbstractCls
{
    private array $routes = [];
    private array $groupStack = [];
    private readonly Application $container;
    private readonly ActionFilterExecutor $filterExecutor;

    public function __construct(Application $container, ActionFilterExecutor $filterExecutor)
    {
        $this->container = $container;
        $this->filterExecutor = $filterExecutor;
    }

    // --- Route Registration ---

    public function get(string $path, array|Closure $handler, array $options = []): void
    {
        $this->addRoute('GET', $path, $handler, $options);
    }

    public function post(string $path, array|Closure $handler, array $options = []): void
    {
        $this->addRoute('POST', $path, $handler, $options);
    }

    public function put(string $path, array|Closure $handler, array $options = []): void
    {
        $this->addRoute('PUT', $path, $handler, $options);
    }

    public function patch(string $path, array|Closure $handler, array $options = []): void
    {
        $this->addRoute('PATCH', $path, $handler, $options);
    }

    public function delete(string $path, array|Closure $handler, array $options = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $options);
    }

    public function group(string $prefix, Closure $callback): void
    {
        // Add the prefix to the stack
        $this->groupStack[] = $prefix;

        // Call the user's closure, which will register routes
        // The $router instance ($this) is passed, and it will
        // automatically use the new prefix
        $callback($this);

        // Remove the prefix from the stack
        array_pop($this->groupStack);
    }

    /**
     * Alias for the 'group' method.
     */
    public function prefix(string $prefix): self
    {
        $this->groupStack[] = $prefix;
        return $this; // Allows for chaining if we refactor group
    }

    /**
     * Registers a full set of CRUD routes for a resource.
     *
     * @param string $path The base path for the resource (e.g., /users)
     * @param string $controller The controller class name
     * @param array $options An array for 'defaults'
     */
    public function resource(string $path, string $controller, array $options = []): void
    {
        $path = trim($path, '/');

        // GET /path
        $this->addRoute('GET', $path, [$controller, 'index'], $options);

        // POST /path
        $this->addRoute('POST', $path, [$controller, 'store'], $options);

        // GET /path/{id}
        $this->addRoute('GET', $path . '/{id}', [$controller, 'show'], $options);

        // PUT /path/{id}
        $this->addRoute('PUT', $path . '/{id}', [$controller, 'update'], $options);

        // DELETE /path/{id}
        $this->addRoute('DELETE', $path . '/{id}', [$controller, 'destroy'], $options);

        // PUT /path/{id}/disable
        $this->addRoute('PUT', $path . '/{id}/disable', [$controller, 'disable'], $options);
    }

    private function addRoute(string $method, string $path, array|Closure $handler, array $options = []): void
    {
        // Apply all stacked group prefixes to the path
        $prefix = implode('', $this->groupStack);
        $path = '/' . trim($prefix . '/' . trim($path, '/'), '/');

        // Convert the path to a regex
        // /users/{id} -> #^/users/([\w-]+)$#
        $pathRegex = preg_replace('/\{([\w-]+)\}/', '([\w-]+)', $path);
        $pathRegex = '#^' . $pathRegex . '$#';

        // Get the names of the parameters
        preg_match_all('/\{([\w-]+)\}/', $path, $matches);
        $paramNames = $matches[1];

        $this->routes[$method][] = [
            'path' => $path,
            'regex' => $pathRegex,
            'handler' => $handler,
            'params' => $paramNames,
            'options' => $options // Store options (like 'defaults')
        ];
    }

    // --- Route Dispatching ---

    /**
     * @throws HttpException|ControllerException|DIException|ReflectionException
     */
    public function dispatch(Request $request): Response
    {
        $path = $request->getPath();
        $method = $request->getMethod();

        // Find a matching route
        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['regex'], $path, $matches)) {
                // Remove the full match from the array
                array_shift($matches);

                // Combine param names with matched values
                $urlParams = array_combine($route['params'], $matches);

                // Get defaults from the route registration
                $routeDefaults = $route['options']['defaults'] ?? [];

                // Merge defaults and URL params. URL params override defaults.
                $allRouteParams = array_merge($routeDefaults, $urlParams);

                // ** CRITICAL STEP **
                // Store these parameters on the request object so that
                // controllers (like AbstractController) can access them.
                // We must assume Request.php has this method.
                if (method_exists($request, 'setRouteParams')) {
                    $request->setRouteParams($allRouteParams);
                }

                // We found our route, now execute it
                return $this->executeHandler($route['handler'], $allRouteParams, $request);
            }
        }

        // No route matched
        throw new HttpException('route_not_found', [':path' => $path, ':method' => $method], 404);
    }

    /**
     * @throws DIException|ReflectionException|ControllerException
     */
    private function executeHandler(array|Closure $handler, array $routeParams, Request $request): Response
    {
        // Handle simple closure routes
        if ($handler instanceof Closure) {
            $params = $this->resolveActionParameters($handler, $routeParams, $request);
            // Call the closure with resolved parameters
            return $handler(...$params);
        }

        // Handle [Controller, 'action'] routes
        [$controllerClass, $actionName] = $handler;

        if (!class_exists($controllerClass)) {
            throw new ControllerException('controller_not_found', [':name' => $controllerClass], 500);
        }

        // Get the controller from the container
        $controller = $this->container->get($controllerClass);

        if (!method_exists($controller, $actionName)) {
            throw new ControllerException('action_not_found', [':name' => $actionName], 500);
        }

        // Resolve parameters for the action method
        $actionParams = $this->resolveActionParameters([$controller, $actionName], $routeParams, $request);

        // Delegate to the ActionFilterExecutor
        return $this->filterExecutor->execute($controller, $actionName, $actionParams, $request);
    }

    /**
     * @throws ReflectionException|DIException
     */
    private function resolveActionParameters(string|array|Closure $handler, array $routeParams, Request $request): array
    {
        $reflector = ($handler instanceof Closure) ? new \ReflectionFunction($handler) : new ReflectionMethod($handler[0], $handler[1]);
        $resolvedParams = [];

        foreach ($reflector->getParameters() as $param) {
            $paramName = $param->getName();
            $type = $param->getType();

            if (array_key_exists($paramName, $routeParams)) {
                // 1. Check route parameters (e.g., {id} from URL)
                $resolvedParams[] = $routeParams[$paramName];
            } elseif ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                // 2. Check for injectable classes (Services, Request object, etc.)
                $typeName = $type->getName();
                if ($typeName === Request::class) {
                    $resolvedParams[] = $request;
                } elseif ($this->container->has($typeName)) {
                    $resolvedParams[] = $this->container->get($typeName);
                } else {
                    // 3. (Optional) Not a service, maybe a "Form Model"?
                    // Try to auto-bind from POST data (if we add this feature)
                    $resolvedParams[] = null; // Or throw error
                }
            } elseif ($request->getPost($paramName) !== null) {
                // 4. Check POST data
                $resolvedParams[] = $request->getPost($paramName);
            } elseif ($request->getQuery($paramName) !== null) {
                // 5. Check GET data
                $resolvedParams[] = $request->getQuery($paramName);
            } elseif ($param->isDefaultValueAvailable()) {
                // 6. Use default value
                $resolvedParams[] = $param->getDefaultValue();
            } else {
                throw new DIException('unresolvable_parameter', [':param' => $paramName, ':class' => 'route']);
            }
        }
        return $resolvedParams;
    }
}

