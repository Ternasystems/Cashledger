<?php

declare(strict_types=1);

namespace TS_Configuration\Classes;

use TS_Configuration\Interfaces\IConfigurationManager;
use TS_Exception\Classes\JsonException; // Assuming a JsonException class exists
use TS_Utility\Helpers\ArrayHelper;

/**
 * Manages configuration data stored in JSON files.
 */
class JsonManager extends AbstractCls implements IConfigurationManager
{
    /** The file path to the JSON document. */
    protected readonly string $file;

    /** The entire JSON data structure, held as a PHP array. */
    protected array $data;

    /**
     * Constructs the JsonManager, loading an existing JSON file or creating a new one.
     *
     * @param string $filepath The path to the JSON file.
     * @param array<mixed> $defaultData Default data for file creation.
     * @throws JsonException if the file is invalid or cannot be created/read.
     */
    public function __construct(string $filepath, array $defaultData = [])
    {
        if (file_exists($filepath) && strtolower(pathinfo($filepath, PATHINFO_EXTENSION)) !== 'json') {
            throw new JsonException('file_not_json', [':path' => $filepath]);
        }

        $this->file = $filepath;

        if (!file_exists($this->file)) {
            $this->data = $defaultData;
            if (!$this->save()) {
                throw new JsonException('file_creation_failed', [':path' => $this->file]);
            }
        } else {
            $jsonContent = file_get_contents($this->file);
            if ($jsonContent === false) {
                throw new JsonException('file_read_failed', [':path' => $this->file]);
            }
            $this->data = json_decode($jsonContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new JsonException('parse_failed', [':reason' => json_last_error_msg()]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFilename(): string
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     * This implementation uses dot notation for nested access (e.g., 'database.mysql.host').
     */
    public function get(string $path, mixed $default = null): mixed
    {
        return ArrayHelper::get($this->data, $path, $default);
    }

    /**
     * {@inheritdoc}
     * This operation modifies the data in memory. Call save() to persist changes.
     */
    public function set(string $path, mixed $value): bool
    {
        ArrayHelper::set($this->data, $path, $value);
        return true; // Operation is in-memory, save() will handle persistence.
    }

    /**
     * {@inheritdoc}
     * This operation modifies the data in memory. Call save() to persist changes.
     */
    public function delete(string $path): bool
    {
        ArrayHelper::delete($this->data, $path);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function save(): bool
    {
        $jsonString = json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($jsonString === false) {
            return false;
        }
        return file_put_contents($this->file, $jsonString) !== false;
    }
}
