<?php

namespace API_DTOEntities_Model;

use API_DTOEntities_Contract\IEntity;
use API_DTORepositories_Model\DTOBase;
use Exception;
use API_RelationRepositories_Collection\LanguageRelations;

class Entity implements IEntity
{
    private DTOBase $entity;
    private ?LanguageRelations $languageRelations;

    public function __construct(DTOBase $_entity, ?LanguageRelations $_languageRelations)
    {
        $this->entity = $_entity;
        $this->languageRelations = $_languageRelations?->Where(fn($n) => $n->ReferenceId == $_entity->Id);
    }

    public function It(): DTOBase
    {
        return $this->entity;
    }

    /**
     * @throws Exception
     */
    public function LanguageRelations(): ?LanguageRelations
    {
        return $this->languageRelations;
    }
}