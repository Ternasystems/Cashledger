<?php

namespace API_TellerEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;
use API_TellerEntities_Collection\Tellers;

class TellerReversal extends Entity
{
    private Teller $teller;
    private Teller $approbator;
    private TellerTransaction $transaction;

    /**
     * Initializes a new instance of the TellerReversal class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_TellerRepositories_Model\TellerReversal $_entity The raw TellerReversal DTO.
     */
    public function __construct(\API_TellerRepositories_Model\TellerReversal $_entity, Tellers $_tellers, TellerTransaction $_transaction)
    {
        parent::__construct($_entity);
        $this->teller = $_tellers->first(fn($n) => $n->Id == $_entity->ReversedBy);
        $this->approbator = $_tellers->first(fn($n) => $n->Id == $_entity->ApprovedBy);
        $this->transaction = $_transaction;
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_TellerRepositories_Model\TellerReversal
    {
        $entity = parent::it();
        if (!$entity instanceof \API_TellerRepositories_Model\TellerReversal) {
            throw new EntityException('invalid_entity_name', [':name' => \API_TellerRepositories_Model\TellerReversal::class]);
        }

        return $entity;
    }

    public function Teller(): Teller
    {
        return $this->teller;
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