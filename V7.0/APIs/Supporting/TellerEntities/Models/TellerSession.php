<?php

namespace API_TellerEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;
use API_TellerRepositories_Collection\TellerTransactions;

class TellerSession extends Entity
{
    private Teller $teller;
    private TellerTransactions $transactions;

    /**
     * Initializes a new instance of the TellerSession class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_TellerRepositories_Model\TellerSession $_entity The raw TellerSession DTO.
     */
    public function __construct(\API_TellerRepositories_Model\TellerSession $_entity, Teller $_teller, TellerTransactions $_transactions)
    {
        parent::__construct($_entity);
        $this->teller = $_teller;
        $this->transactions = $_transactions->where(fn($n) => $n->SessionId == $_entity->Id);
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_TellerRepositories_Model\TellerSession
    {
        $entity = parent::it();
        if (!$entity instanceof \API_TellerRepositories_Model\TellerSession) {
            throw new EntityException('invalid_entity_name', [':name' => \API_TellerRepositories_Model\TellerSession::class]);
        }

        return $entity;
    }

    public function Teller(): Teller
    {
        return $this->teller;
    }

    /**
     * @throws EntityException
     */
    public function SessionId(): string
    {
        return $this->teller->it()->SessionId;
    }

    public function Transactions(): TellerTransactions
    {
        return $this->transactions;
    }
}