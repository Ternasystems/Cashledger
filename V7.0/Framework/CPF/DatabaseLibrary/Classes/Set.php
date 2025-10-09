<?php

declare(strict_types=1);

namespace TS_Database\Classes;

use TS_Exception\Classes\DBException;

/**
 * Represents a "has many" relationship, returning a set of models.
 */
class Set extends AbstractRelation
{
    public function __construct(QueryBuilder $query, AbstractModel $parent, private readonly string $foreignKey, private readonly string $localKey)
    {
        parent::__construct($query, $parent);
    }

    /**
     * Executes the query for a "has many" relationship.
     *
     * @return array<AbstractModel> An array of related models.
     * @throws DBException
     */
    public function getResults(): array
    {
        $localKeyValue = $this->parent->{$this->localKey};
        $results = $this->query->where($this->foreignKey, '=', $localKeyValue)->get();

        $relatedClass = $this->query->modelClass;
        $models = [];
        foreach ($results as $row) {
            $models[] = new $relatedClass($row);
        }
        return $models;
    }
}