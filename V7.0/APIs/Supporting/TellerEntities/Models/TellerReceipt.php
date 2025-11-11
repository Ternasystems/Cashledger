<?php

namespace API_TellerEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;

class TellerReceipt extends Entity
{
    private Teller $teller;
    private TellerTransaction $transaction;

    /**
     * Initializes a new instance of the TellerReceipt class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_TellerRepositories_Model\TellerReceipt $_entity The raw TellerReceipt DTO.
     */
    public function __construct(\API_TellerRepositories_Model\TellerReceipt $_entity, Teller $_teller, TellerTransaction $_transaction)
    {
        parent::__construct($_entity);
        $this->teller = $_teller;
        $this->transaction = $_transaction;
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_TellerRepositories_Model\TellerReceipt
    {
        $entity = parent::it();
        if (!$entity instanceof \API_TellerRepositories_Model\TellerReceipt) {
            throw new EntityException('invalid_entity_name', [':name' => \API_TellerRepositories_Model\TellerReceipt::class]);
        }

        return $entity;
    }

    public function Teller(): Teller
    {
        return $this->teller;
    }

    public function Transaction(): TellerTransaction
    {
        return $this->transaction;
    }
}