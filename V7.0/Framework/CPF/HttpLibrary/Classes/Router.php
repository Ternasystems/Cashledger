<?php

declare(strict_types=1);

namespace TS_Http\Classes;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use TS_Configuration\Classes\AbstractCls;
use TS_Controller\Classes\ActionFilterExecutor;
use TS_DependencyInjection\Classes\Application;
use TS_Exception\Classes\ControllerException;
use TS_Exception\Classes\DIException;

/**
 * The central dispatcher. It resolves the controller and delegates execution
 * to the ActionFilterExecutor to handle attributes and filters.
 */
final class Router extends AbstractCls
{
    public function __construct(private readonly Application $container, private readonly ActionFilterExecutor $filterExecutor)
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
        $controller = $this->container->get($controllerName);

        if (!method_exists($controller, $actionName)) {
            throw new ControllerException('action_not_found', [':name' => $actionName], 404);
        }

        // 3. Resolve dependencies for the action method.
        $actionParams = $this->resolveActionParameters($controller, $actionName, $request);

        // 4. Delegate execution to the filter executor.
        return $this->filterExecutor->execute($controller, $actionName, $actionParams, $request);
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
                    $params[] = $this->container->get($typeName);
                } else {
                    $modelInstance = new $typeName();
                    $modelReflector = new ReflectionClass($modelInstance);
                    foreach ($modelReflector->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
                        $propName = $property->getName();
                        if ($request->getPost($propName) !== null) {
                            $modelInstance->{$propName} = $request->getPost($propName);
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

