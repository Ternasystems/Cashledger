<?php

namespace API_DTOEntities_Model;

use API_DTOEntities_Contract\IEntity;
use API_DTORepositories_Model\DTOBase;

/**
 * The abstract base class for all rich domain entities.
 * It implements the Decorator pattern, wrapping a base DTO to add functionality.
 *
 * @template T of DTOBase
 */
abstract class Entity implements IEntity
{
    use TLanguageRelation;

    /**
     * The underlying raw DTO from the persistence layer.
     * @var T
     */
    private DTOBase $entity;

    /**
     * @param T $_entity The specific DTOBase instance to be decorated.
     */
    public function __construct(DTOBase $_entity)
    {
        $this->entity = $_entity;
    }

    /**
     * Gets the underlying, raw DTO model.
     * While this base method returns DTOBase, concrete implementations
     * should override it to provide a specific, type-safe return value.
     *
     * @return T The underlying DTO.
     */
    public function it(): DTOBase
    {
        return $this->entity;
    }
}