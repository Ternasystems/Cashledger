<?php

namespace API_ProfilingEntities_Model;

use API_DTOEntities_Model\Entity;
use API_RelationRepositories_Collection\LanguageRelations;
use http\Exception\UnexpectedValueException;

class Role extends Entity
{
    private Token $permissions;

    public function __construct(\API_ProfilingRepositories_Model\Role $_entity, Token $_permissions, ?LanguageRelations $_languageRelations)
    {
        parent::__construct($_entity, $_languageRelations);
        $this->permissions = $_permissions;
    }

    public function It(): \API_ProfilingRepositories_Model\Role
    {
        $entity = parent::It();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Role)
            throw new UnexpectedValueException('Object must be an instance of '.\API_ProfilingRepositories_Model\Role::class);

        return $entity;
    }

    public function Token(): Token
    {
        return $this->permissions;
    }
}