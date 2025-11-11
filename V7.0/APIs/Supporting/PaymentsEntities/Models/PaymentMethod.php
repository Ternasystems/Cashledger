<?php

namespace API_PaymentsEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;

class PaymentMethod extends Entity
{
    /**
     * Initializes a new instance of the AppCategory class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_PaymentsRepositories_Model\PaymentMethod $_entity The raw AppCategory DTO.
     */
    public function __construct(\API_PaymentsRepositories_Model\PaymentMethod $_entity)
    {
        parent::__construct($_entity);
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_PaymentsRepositories_Model\PaymentMethod
    {
        $entity = parent::it();
        if (!$entity instanceof \API_PaymentsRepositories_Model\PaymentMethod) {
            throw new EntityException('invalid_entity_name', [':name' => \API_PaymentsRepositories_Model\PaymentMethod::class]);
        }

        return $entity;
    }
}