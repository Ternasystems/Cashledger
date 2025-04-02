<?php

namespace API_ProfilingEntities_Model;

use API_DTOEntities_Model\Entity;
use API_ProfilingEntities_Collection\Permissions;
use UnexpectedValueException;

class Token extends Entity
{
    private Permissions $permissions;

    public function __construct(\API_ProfilingRepositories_Model\Token $_entity, Permissions $_permissions)
    {
        parent::__construct($_entity, null);
        $_tokens = explode('-', $_entity->Permissions);
        $this->permissions = $_permissions->Where(fn($n) => in_array($n->It()->Code, $_tokens));
    }

    public function It(): \API_ProfilingRepositories_Model\Token
    {
        $entity = parent::It();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Token)
            throw new UnexpectedValueException('Object must be an instance of '.\API_ProfilingRepositories_Model\Token::class);

        return $entity;
    }

    public function Permissions(): Permissions
    {
        return $this->permissions;
    }
}