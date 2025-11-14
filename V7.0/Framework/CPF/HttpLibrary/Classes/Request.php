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
    private array $segments = [];
    public ?string $content = null {
        get {
            if ($this->content === null) {
                $this->content = file_get_contents('php://input') ?: '';
            }
            return $this->content;
        }
    }

    public function __construct(array $get, array $post, array $server)
    {
        $this->get = $get;
        $this->post = $post;
        $this->server = $server;
        $this->segments = $this->parsePathSegments();
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
        $path = $this->get['uri'] ?? parse_url($this->server['REQUEST_URI'] ?? '', PHP_URL_PATH);
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

        // Remove base path from request path
        if (str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath));
        }

        return '/' . trim($path, '/');
    }

    private function parsePathSegments(): array
    {
        $path = $this->getPath();
        return explode('/', trim($path, '/'));
    }

    /**
     * Gets a specific segment of the URL path by index.
     * /segment0/segment1/segment2
     */
    public function getSegment(int $index): ?string
    {
        return $this->segments[$index] ?? null;
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
     * @param string|null $key The key of the POST variable.
     * @param mixed|null $default The default value to return if the key is not found.
     * @return mixed
     */
    public function getPost(?string $key = null, mixed $default = null): mixed
    {
        return is_null($key) ? $this->post : $this->post[$key] ?? $default;
    }

    /**
     * Gets a value from the GET query string.
     *
     * @param string|null $key The key of the query variable.
     * @param mixed|null $default The default value to return if the key is not found.
     * @return mixed
     */
    public function getQuery(?string $key = null, mixed $default = null): mixed
    {
        return is_null($key) ? $this->get : $this->get[$key] ?? $default;
    }

    /**
     * Gets a server variable.
     *
     * @param string $key The key of the server variable (e.g., 'REMOTE_ADDR').
     * @param mixed|null $default The default value to return if not found.
     * @return mixed
     */
    public function getServer(string $key, mixed $default = null): mixed
    {
        return $this->server[$key] ?? $default;
    }
}