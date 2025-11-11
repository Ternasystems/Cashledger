<?php

namespace API_RelationRepositories_Model;

use API_DTORepositories_Model\DTOBase;

/**
 * Represents a record from the 'cl_PriceRelations' table.
 */
class PriceRelation extends DTOBase
{
    /**
     * The database table name for this model.
     * @var string
     */
    protected static string $table = 'cl_PriceRelations';
}