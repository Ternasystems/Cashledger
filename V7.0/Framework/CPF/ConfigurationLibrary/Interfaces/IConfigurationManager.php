<?php

declare(strict_types=1);

namespace TS_Configuration\Interfaces;

/**
 * Defines a generic, format-agnostic contract for configuration managers.
 *
 * This interface allows interaction with various configuration file types (XML, JSON, INI, etc.)
 * through a unified set of methods.
 */
interface IConfigurationManager
{
    /**
     * Retrieves the full file path of the configuration source.
     *
     * @return string
     */
    public function getFilename(): string;

    /**
     * Gets a configuration value using a format-agnostic path.
     * For hierarchical formats, "dot" notation is the expected convention (e.g., 'database.user.name').
     * Implementations are responsible for translating this path to their specific query language (e.g., XPath).
     *
     * @param string $path The dot-notation path to the value.
     * @param mixed|null $default The default value to return if the path is not found.
     * @return mixed The found value or the default.
     */
    public function get(string $path, mixed $default = null): mixed;

    /**
     * Sets a configuration value at a specific path.
     * If the path does not exist, the implementation should attempt to create it.
     *
     * @param string $path The dot-notation path for the value.
     * @param mixed $value The value to set. It can be a scalar type or an array.
     * @return bool True on success, false on failure.
     */
    public function set(string $path, mixed $value): bool;

    /**
     * Deletes a configuration key or node at a specific path.
     *
     * @param string $path The dot-notation path of the node to delete.
     * @return bool True on success, false on failure.
     */
    public function delete(string $path): bool;

    /**
     * Persists all changes made to the configuration back to the file.
     *
     * @return bool True if the file was saved successfully, false otherwise.
     */
    public function save(): bool;
}
