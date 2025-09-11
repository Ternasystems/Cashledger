<?php

declare(strict_types=1);

namespace TS_Http\Classes;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use TS_Configuration\Classes\AbstractCls;
use TS_DependencyInjection\Classes\Application;
use TS_Exception\Classes\ControllerException;
use TS_Exception\Classes\DIException;

/**
 * The central dispatcher. It inspects the request, resolves the controller,
 * and invokes the action method with all dependencies injected.
 */
final class Router extends AbstractCls
{
    public function __construct(private readonly Application $container)
    {
    }

    /**
     * @throws ControllerException
     * @throws ReflectionException
     * @throws DIException
     */
    public function dispatch(Request $request): Response
    {
        // 1. Determine controller and action from the request
        $controllerName = 'App\\Controllers\\' . ($request->getQuery('controller', 'Home')) . 'Controller';
        $actionName = $request->getQuery('action', 'index');

        if (!class_exists($controllerName)) {
            throw new ControllerException('controller_not_found', [':name' => $controllerName], 404);
        }

        // 2. Use the DI container to build the controller.
        // It automatically handles injecting View and FlashMessageService into the BaseController.
        $controller = $this->container->get($controllerName);

        if (!method_exists($controller, $actionName)) {
            throw new ControllerException('action_not_found', [':name' => $actionName], 404);
        }

        // 3. Resolve dependencies for the specific ACTION METHOD (Model Binding).
        $actionParams = $this->resolveActionParameters($controller, $actionName, $request);

        // 4. Call the action and return the resulting Response object.
        return $controller->$actionName(...$actionParams);
    }

    /**
     * @throws ReflectionException
     * @throws DIException
     */
    private function resolveActionParameters(object $controller, string $methodName, Request $request): array
    {
        $method = new ReflectionMethod($controller, $methodName);
        $params = [];

        foreach ($method->getParameters() as $param) {
            $type = $param->getType();
            if ($type && !$type->isBuiltin()) {
                $typeName = $type->getName();

                if ($this->container->has($typeName)) {
                    // It's a registered service, resolve it.
                    $params[] = $this->container->get($typeName);
                } else {
                    // Assume it's a Model for binding from POST data.
                    $modelInstance = new $typeName();
                    $modelReflector = new ReflectionClass($modelInstance);

                    // Iterate over the model's public properties and populate them from the request.
                    foreach ($modelReflector->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
                        $propName = $property->getName();
                        $postValue = $request->getPost($propName);
                        if ($postValue !== null) {
                            // A more robust implementation would handle type casting here.
                            $modelInstance->{$propName} = $postValue;
                        }
                    }
                    $params[] = $modelInstance;
                }
            } elseif ($request->getQuery($param->getName())) {
                $params[] = $request->getQuery($param->getName());
            } elseif ($param->isDefaultValueAvailable()) {
                $params[] = $param->getDefaultValue();
            }
        }
        return $params;
    }
}

