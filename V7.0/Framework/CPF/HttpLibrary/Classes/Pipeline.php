<?php

declare(strict_types=1);

namespace TS_Http\Classes;

use Closure;
use TS_Configuration\Classes\AbstractCls;
use TS_DependencyInjection\Classes\Application;
use TS_Http\Interfaces\IMiddleware;

/**
 * Manages the middleware pipeline, passing the request through each layer.
 */
class Pipeline extends AbstractCls
{
    /**
     * The array of middleware classes to process.
     * @var array<class-string<IMiddleware>>
     */
    private array $middleware = [];
    private readonly Application $container;

    public function __construct(Application $container)
    {
        $this->container = $container;
    }

    /**
     * Sets the middleware classes for the pipeline.
     * @param array<class-string<IMiddleware>> $middleware
     */
    public function through(array $middleware): self
    {
        $this->middleware = $middleware;
        return $this;
    }

    /**
     * Runs the request through the middleware pipeline, ending with a final destination.
     *
     * @param Request $request The incoming request.
     * @param Closure $destination The final closure to execute if the pipeline completes.
     * @return Response
     */
    public function then(Request $request, Closure $destination): Response
    {
        // The core of the pipeline is a reversed chain of closures.
        $pipeline = array_reduce(
            array_reverse($this->middleware),
            $this->carry(),
            $destination
        );

        return $pipeline($request);
    }

    /**
     * Creates the closure that represents a single layer of the "onion".
     */
    private function carry(): Closure
    {
        return function (Closure $stack, string $middlewareClass) {
            return function (Request $request) use ($stack, $middlewareClass) {
                // Resolve the middleware from the DI container so it can have dependencies.
                $middleware = $this->container->get($middlewareClass);
                return $middleware->handle($request, $stack);
            };
        };
    }
}