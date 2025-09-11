<?php

declare(strict_types=1);

namespace TS_Http\Classes;

use TS_Configuration\Classes\AbstractCls;

/**
 * Represents an incoming HTTP request, providing an object-oriented
 * way to access superglobals like $_SERVER, $_GET, and $_POST.
 */
final class Request extends AbstractCls
{
    private array $get;
    private array $post;
    private array $server;

    public function __construct(array $get, array $post, array $server)
    {
        $this->get = $get;
        $this->post = $post;
        $this->server = $server;
    }

    /**
     * Creates a new Request instance from the current PHP superglobals.
     */
    public static function createFromGlobals(): self
    {
        return new self($_GET, $_POST, $_SERVER);
    }

    /**
     * Gets the request path (e.g., '/users/show/123').
     */
    public function getPath(): string
    {
        return strtok($this->server['REQUEST_URI'] ?? '/', '?');
    }

    /**
     * Gets the HTTP method (e.g., 'GET', 'POST').
     */
    public function getMethod(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Gets a value from the POST data.
     *
     * @param string $key The key of the POST variable.
     * @param mixed|null $default The default value to return if the key is not found.
     */
    public function getPost(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    /**
     * Gets a value from the GET query string.
     *
     * @param string $key The key of the query variable.
     * @param mixed|null $default The default value to return if the key is not found.
     */
    public function getQuery(string $key, mixed $default = null): mixed
    {
        return $this->get[$key] ?? $default;
    }
}