<?php

namespace API_RelationRepositories_Model;

use API_DTORepositories_Model\DTOBase;

/**
 * Represents a record from the 'cl_CashRelations' table.
 */
class CashRelation extends DTOBase
{
    /**
     * The database table name for this model.
     * @var string
     */
    protected static string $table = 'cl_CashRelations';
}