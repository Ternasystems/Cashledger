<?php

declare(strict_types=1);

namespace TS_View\Classes;

use ReflectionClass;
use ReflectionException;
use TS_Configuration\Classes\AbstractCls;
use TS_DependencyInjection\Classes\Application;
use TS_Exception\Classes\DIException;
use TS_Exception\Classes\ViewException;

/**
 * Discovers, instantiates, and renders View Components.
 * This service should be registered as a singleton in the DI container.
 */
class ComponentService extends AbstractCls
{
    public function __construct(
        private readonly Application $container,
        private readonly string $componentNamespace
    ) {
    }

    /**
     * Renders a view component with the given arguments.
     *
     * @param string $name The short name of the component (e.g., 'Card', 'Modal').
     * @param array $args Arguments to pass to the component's render method.
     * @return string The rendered HTML output of the component.
     * @throws ViewException|ReflectionException|DIException
     */
    public function render(string $name, array $args = []): string
    {
        $className = rtrim($this->componentNamespace, '\\') . '\\' . ucfirst($name);

        if (!class_exists($className)) {
            throw new ViewException('component_not_found', [':name' => $className]);
        }

        // Use the DI container to build the component, injecting any services it needs.
        $componentInstance = $this->container->get($className);

        if (!($componentInstance instanceof ViewComponent)) {
            throw new ViewException('invalid_component_class', [':name' => $className]);
        }

        // Use reflection to resolve dependencies for the render() method itself.
        $reflector = new ReflectionClass($componentInstance);
        $renderMethod = $reflector->getMethod('render');
        $resolvedArgs = [];

        foreach ($renderMethod->getParameters() as $param) {
            $paramName = $param->getName();
            if (array_key_exists($paramName, $args)) {
                $resolvedArgs[] = $args[$paramName];
            } elseif ($param->isDefaultValueAvailable()) {
                $resolvedArgs[] = $param->getDefaultValue();
            } else {
                throw new ViewException('missing_component_argument', [':param' => $paramName, ':component' => $name]);
            }
        }

        // Call the render method to get the configured View object.
        $view = $renderMethod->invokeArgs($componentInstance, $resolvedArgs);

        if (!($view instanceof View)) {
            throw new ViewException('component_render_must_return_view', [':name' => $name]);
        }

        // The render method of a component should not have a layout.
        // It returns its partial content directly.
        return $view->renderPartial($view->templatePath, $view->data);
    }
}