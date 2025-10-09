<?php

declare(strict_types=1);

namespace TS_Database\Classes;

use TS_Exception\Classes\DBException;

/**
 * Represents a "belongs to" relationship, returning a single model reference.
 */

class Reference extends AbstractRelation
{
    public function __construct(QueryBuilder $query, AbstractModel $parent, private readonly string $foreignKey, private readonly string $ownerKey)
    {
        parent::__construct($query, $parent);
    }

    /**
     * Executes the query for a "belongs to" relationship.
     *
     * @return AbstractModel|null The related model.
     * @throws DBException
     */
    public function getResults(): ?AbstractModel
    {
        $foreignKeyValue = $this->parent->{$this->foreignKey};
        $result = $this->query->where($this->ownerKey, '=', $foreignKeyValue)->get();

        if (empty($result)) {
            return null;
        }

        $relatedClass = $this->query->modelClass;
        return new $relatedClass($result[0]);
    }
}