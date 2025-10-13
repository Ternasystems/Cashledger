<?php

namespace API_DTOEntities_Contract;

use API_DTORepositories_Model\DTOBase;
use API_RelationRepositories_Collection\LanguageRelations;

/**
 * Defines the contract for a rich domain entity.
 * An entity acts as a decorator, wrapping a base DTO and enriching it with related data.
 */
interface IEntity
{
    /**
     * Gets the underlying, raw DTO model from the persistence layer.
     *
     * @return DTOBase The simple data transfer object.
     */
    public function it(): DTOBase;

    /**
     * Gets the language-specific translations related to this entity.
     *
     * @return LanguageRelations|null
     */
    public function languageRelations(): ?LanguageRelations;
}