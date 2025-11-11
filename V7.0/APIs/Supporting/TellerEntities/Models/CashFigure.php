<?php

namespace API_TellerEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;

class CashFigure extends Entity
{
    /**
     * Initializes a new instance of the AppCategory class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_TellerRepositories_Model\CashFigure $_entity The raw AppCategory DTO.
     */
    public function __construct(\API_TellerRepositories_Model\CashFigure $_entity)
    {
        parent::__construct($_entity);
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_TellerRepositories_Model\CashFigure
    {
        $entity = parent::it();
        if (!$entity instanceof \API_TellerRepositories_Model\CashFigure) {
            throw new EntityException('invalid_entity_name', [':name' => \API_TellerRepositories_Model\CashFigure::class]);
        }

        return $entity;
    }
}