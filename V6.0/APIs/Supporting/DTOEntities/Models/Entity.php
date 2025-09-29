<?php

namespace API_DTOEntities_Model;

use API_DTOEntities_Contract\IEntity;
use API_DTORepositories_Model\DTOBase;

abstract class Entity implements IEntity
{
    use TLanguageRelation;
    private DTOBase $entity;

    public function __construct(DTOBase $_entity)
    {
        $this->entity = $_entity;
    }

    /**
     * @inheritDoc
     */
    public function it(): DTOBase
    {
        return $this->entity;
    }
}