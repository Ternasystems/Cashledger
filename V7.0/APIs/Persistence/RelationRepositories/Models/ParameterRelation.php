<?php

namespace API_RelationRepositories_Model;

/**
 * Represents a record from the 'cl_ParameterRelations' table.
 */
use API_DTORepositories_Model\DTOBase;

class ParameterRelation extends DTOBase
{
    /**
     * The database table name for this model.
     * @var string
     */
    protected static string $table = 'cl_ParameterRelations';
}