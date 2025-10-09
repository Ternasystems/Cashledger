<?php

declare(strict_types=1);

namespace TS_Controller\Classes;

use Closure;
use ReflectionClass;
use ReflectionException;
use TS_Controller\Interfaces\IActionFilter;
use TS_DependencyInjection\Classes\Application;
use TS_Exception\Classes\DIException;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

/**
 * Discovers and executes action filters (attributes) for a controller action.
 */
final class ActionFilterExecutor
{
    public function __construct(
        private readonly Application $container,
        private readonly FilterRegistry $registry
    ) {
    }

    /**
     * Executes the pipeline of filters and the final controller action.
     * @throws ReflectionException|DIException
     */
    public function execute(object $controller, string $actionName, array $actionParams, Request $request): Response
    {
        $reflector = new ReflectionClass($controller);
        $method = $reflector->getMethod($actionName);

        // 1. Discover all relevant attributes
        $classAttributes = $reflector->getAttributes();
        $methodAttributes = $method->getAttributes();
        $allAttributes = array_merge($classAttributes, $methodAttributes);

        // 2. Map attributes to filter instances using the registry
        $filters = [];
        $attributeInstances = [];
        foreach ($allAttributes as $attribute) {
            $filterClass = $this->registry->getFilterClass($attribute->getName());
            if ($filterClass && $this->container->has($filterClass)) {
                $filters[] = $this->container->get($filterClass);
                $attributeInstances[] = $attribute;
            }
        }
        $filters = array_unique($filters, SORT_REGULAR); // Ensure each filter runs only once

        // 3. Build the "onion" pipeline
        $action = fn() => $controller->$actionName(...$actionParams);

        $pipeline = array_reduce(
            array_reverse($filters),
            function (Closure $next, IActionFilter $filter) use ($request, $attributeInstances) {
                return function () use ($filter, $next, $request, $attributeInstances) {
                    $response = $filter->onActionExecuting($request, $attributeInstances);
                    if ($response instanceof Response) {
                        return $response; // Short-circuit
                    }

                    $response = $next();

                    return $filter->onActionExecuted($request, $response);
                };
            },
            $action
        );

        // 4. Execute the pipeline
        return $pipeline();
    }
}