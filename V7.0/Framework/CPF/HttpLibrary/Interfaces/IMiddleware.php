<?php

declare(strict_types=1);

namespace TS_Http\Interfaces;

use Closure;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

/**
 * Defines the contract for a middleware component.
 * Each middleware must have a `handle` method to process the request.
 */
interface IMiddleware
{
    /**
     * Handles an incoming request.
     *
     * @param Request $request The incoming HTTP request.
     * @param Closure $next A closure that calls the next middleware in the pipeline.
     * @return Response The outgoing HTTP response.
     */
    public function handle(Request $request, Closure $next): Response;
}