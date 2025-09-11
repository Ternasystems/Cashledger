<?php

declare(strict_types=1);

namespace TS_View\Classes;

use TS_Configuration\Classes\AbstractCls;
use TS_Exception\Classes\ViewException;

/**
 * A simple View renderer that supports shared data.
 */
final class View extends AbstractCls
{
    private string $viewPath;
    private array $sharedData = [];

    public function __construct(string $baseViewPath)
    {
        $this->viewPath = rtrim($baseViewPath, DIRECTORY_SEPARATOR);
    }

    /**
     * Shares a piece of data across all views rendered by this instance.
     */
    public function share(string $key, mixed $value): void
    {
        $this->sharedData[$key] = $value;
    }

    /**
     * Renders a view template with the given data.
     * @throws ViewException
     */
    public function render(string $templatePath, array $data = []): string
    {
        $fullPath = $this->viewPath . DIRECTORY_SEPARATOR . $templatePath . '.php';

        if (!file_exists($fullPath)) {
            throw new ViewException('view_not_found', [':path' => $fullPath]);
        }

        // Merge shared data with view-specific data. View-specific data wins.
        $finalData = array_merge($this->sharedData, $data);
        extract($finalData);

        ob_start();
        require $fullPath;
        return ob_get_clean() ?: '';
    }
}
