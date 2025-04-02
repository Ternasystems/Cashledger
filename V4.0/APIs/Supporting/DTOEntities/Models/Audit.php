<?php

namespace API_DTOEntities_Model;

use UnexpectedValueException;

class Audit extends Entity
{
    public function __construct(\API_DTORepositories_Model\Audit $_entity)
    {
        parent::__construct($_entity, null);
    }

    public function It(): \API_DTORepositories_Model\Audit
    {
        $entity = parent::It();
        if (!$entity instanceof \API_DTORepositories_Model\Audit)
            throw new UnexpectedValueException('Object must be an instance of '.\API_DTORepositories_Model\Audit::class);

        return $entity;
    }
}