<?php

declare(strict_types=1);

namespace TS_View;

use TS_Exception\Classes\ViewException;

/**
 * A simple View renderer.
 */
final class View
{
    private string $viewPath;

    public function __construct(string $baseViewPath)
    {
        // Example: /path/to/your/project/Applications/
        $this->viewPath = rtrim($baseViewPath, DIRECTORY_SEPARATOR);
    }

    /**
     * Renders a view template with the given data.
     *
     * @param string $templatePath Path to the template relative to the base view path (e.g., "Inventory/Views/Templates/Home").
     * @param array $data Data to make available to the view.
     * @return string The rendered content.
     * @throws ViewException
     */
    public function render(string $templatePath, array $data = []): string
    {
        $fullPath = $this->viewPath . DIRECTORY_SEPARATOR . $templatePath . '.php';

        if (!file_exists($fullPath)) {
            // In a real app, throw a more specific ViewNotFoundException
            throw new ViewException('view_not_found', [':path' => $fullPath]);
        }

        // Extracts the data array into variables like $title, $products, etc.
        extract($data);

        // Start output buffering
        ob_start();

        require $fullPath;

        // Get the content of the buffer and clean it.
        return ob_get_clean() ?: '';
    }
}