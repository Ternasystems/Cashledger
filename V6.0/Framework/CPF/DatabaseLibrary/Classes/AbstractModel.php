<?php

declare(strict_types=1);

namespace TS_Database\Classes;

use PDO;
use RuntimeException;
use TS_Configuration\Classes\AbstractCls;
use TS_Exception\Classes\DBException;

/**
 * The base class for all application models, providing ORM-like functionality.
 */
abstract class AbstractModel extends AbstractCls
{
    /** The shared PDO connection instance for all models. */
    protected static ?PDO $pdo = null;

    /** The database table name for the model. Must be defined in the child class. */
    protected static string $table;

    /** The model's attributes, mapping to database columns. */
    protected array $attributes = [];

    /** Tracks if the model instance is new (for INSERT vs UPDATE). */
    protected bool $isNew = true;

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
        // A model is considered "existing" if it has a primary key.
        $this->isNew = empty($this->attributes['id']);
    }

    /**
     * Injects the shared PDO connection for all models to use.
     * This should be called once when the DBContext is initialized.
     */
    public static function setConnection(PDO $pdo): void
    {
        self::$pdo = $pdo;
    }

    /**
     * Creates a new QueryBuilder instance for this model's table.
     */
    public static function query(): QueryBuilder
    {
        if (static::$pdo === null) {
            // Or throw a specific exception.
            throw new RuntimeException("Database connection has not been set for models.");
        }
        return new QueryBuilder(static::$pdo, static::$table);
    }

    /**
     * Finds a record by its primary key.
     * @throws DBException
     */
    public static function find(int $id): ?static
    {
        $results = self::query()->where('id', '=', $id)->get();
        return $results ? new static($results[0]) : null;
    }

    /**
     * Saves the model to the database (handles both INSERT and UPDATE).
     * @throws DBException
     */
    public function save(): bool
    {
        $query = static::query();

        if ($this->isNew) {
            // INSERT
            $query->insert($this->attributes);
            // After inserting, retrieve the new ID and mark as not new.
            $this->attributes['id'] = (int)static::$pdo->lastInsertId();
            $this->isNew = false;
        } else {
            // UPDATE
            $query->where('id', '=', $this->attributes['id'])->update($this->attributes);
        }

        return true;
    }

    /**
     * Deletes the model's record from the database.
     * @throws DBException
     */
    public function delete(): bool
    {
        if ($this->isNew) {
            return false;
        }

        static::query()->where('id', '=', $this->attributes['id'])->delete();
        $this->isNew = true; // After deletion, it's effectively a new, unsaved model.
        return true;
    }

    public function __get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }
}

