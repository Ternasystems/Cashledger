<?php

declare(strict_types=1);

namespace TS_Database\Traits;

use TS_Database\Classes\Reference;
use TS_Database\Classes\Set;

/**
 * A trait to be used in AbstractModel to enable ORM relationships.
 */
trait TRelations
{
    /**
     * Stores the loaded relationship results to prevent redundant queries.
     *
     * @var array<string, mixed>
     */
    protected array $relations = [];

    /**
     * Defines a one-to-many relationship.
     * Example: A User has a set of Posts.
     *
     * @param class-string $related The fully qualified class name of the related model.
     * @param string|null $foreignKey The foreign key on the related table. If null, it's inferred.
     * @param string|null $localKey The primary key on the current model's table. If null, it's inferred.
     * @return Set
     */
    protected function hasMany(string $related, ?string $foreignKey = null, ?string $localKey = null): Set
    {
        // Infer foreign key if not provided (e.g., 'user_id')
        $foreignKey = $foreignKey ?? strtolower(basename(str_replace('\\', '/', static::class))) . '_id';

        // Infer local key if not provided (usually 'id')
        $localKey = $localKey ?? 'id';

        return new Set($related::query(), $this, $foreignKey, $localKey);
    }

    /**
     * Defines an inverse one-to-one or one-to-many relationship.
     * Example: A Post has a reference to a User.
     *
     * @param class-string $related The fully qualified class name of the related model.
     * @param string $foreignKey The foreign key on the current model's table.
     * @param string|null $ownerKey The primary key on the related model's table. If null, it's inferred.
     * @return Reference
     */
    protected function belongsTo(string $related, string $foreignKey, ?string $ownerKey = null): Reference
    {
        // Infer owner key if not provided (usually 'id')
        $ownerKey = $ownerKey ?? 'id';

        return new Reference($related::query(), $this, $foreignKey, $ownerKey);
    }
}