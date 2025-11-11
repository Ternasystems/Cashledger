<?php

namespace API_InventoryEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;

class Packaging extends Entity
{
    /**
     * Initializes a new instance of the Packaging class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_InventoryRepositories_Model\Packaging $_entity The raw Packaging DTO.
     */
    public function __construct(\API_InventoryRepositories_Model\Packaging $_entity)
    {
        parent::__construct($_entity);
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_InventoryRepositories_Model\Packaging
    {
        $entity = parent::it();
        if (!$entity instanceof \API_InventoryRepositories_Model\Packaging) {
            throw new EntityException('invalid_entity_name', [':name' => \API_InventoryRepositories_Model\Packaging::class]);
        }

        return $entity;
    }
}