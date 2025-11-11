<?php

namespace API_TellerEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;
use API_TellerEntities_Collection\Tellers;

class TellerTransfer extends Entity
{
    private Teller $tellerFrom;
    private Teller $tellerTo;
    private Teller $approbator;
    private TellerTransaction $transaction;

    /**
     * Initializes a new instance of the TellerTransfer class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_TellerRepositories_Model\TellerTransfer $_entity The raw TellerTransfer DTO.
     */
    public function __construct(\API_TellerRepositories_Model\TellerTransfer $_entity, Tellers $_tellers, TellerTransaction $_transaction)
    {
        parent::__construct($_entity);
        $this->tellerFrom = $_tellers->first(fn($n) => $n->Id == $_entity->TellerFrom);
        $this->tellerTo = $_tellers->first(fn($n) => $n->Id == $_entity->TellerTo);
        $this->approbator = $_tellers->first(fn($n) => $n->Id == $_entity->ApprovedBy);
        $this->transaction = $_transaction;
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_TellerRepositories_Model\TellerTransfer
    {
        $entity = parent::it();
        if (!$entity instanceof \API_TellerRepositories_Model\TellerTransfer) {
            throw new EntityException('invalid_entity_name', [':name' => \API_TellerRepositories_Model\TellerTransfer::class]);
        }

        return $entity;
    }

    public function TellerFrom(): Teller
    {
        return $this->tellerFrom;
    }

    public function TellerTo(): Teller
    {
        return $this->tellerTo;
    }

    public function Approbator(): Teller
    {
        return $this->approbator;
    }

    public function Transaction(): TellerTransaction
    {
        return $this->transaction;
    }
}