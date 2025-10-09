<?php

declare(strict_types=1);

namespace TS_Database\Classes;

use TS_Configuration\Classes\AbstractCls;

/**
 * Base class for all relationship types.
 */
abstract class AbstractRelation extends AbstractCls
{
    public function __construct(protected QueryBuilder $query, protected AbstractModel $parent)
    {
    }

    /**
     * Executes the relationship query and returns the results.
     * This method must be implemented by each specific relationship type.
     */
    abstract public function getResults();
}