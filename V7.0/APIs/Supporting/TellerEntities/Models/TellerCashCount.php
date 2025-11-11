<?php

namespace API_TellerEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;
use API_RelationRepositories_Collection\CashRelations;
use API_TellerEntities_Collection\Tellers;

class TellerCashCount extends Entity
{
    private Teller $teller;
    private Teller $approbator;
    private CashRelations $cashRelations;

    /**
     * Initializes a new instance of the TellerCashCount class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_TellerRepositories_Model\TellerCashCount $_entity The raw TellerCashCount DTO.
     */
    public function __construct(\API_TellerRepositories_Model\TellerCashCount $_entity, Tellers $_tellers, CashRelations $cashRelations)
    {
        parent::__construct($_entity);
        $this->teller = $_tellers->first(fn($n) => $n->Id == $_entity->TellerId);
        $this->approbator = $_tellers->first(fn($n) => $n->Id == $_entity->ApprovedBy);
        $this->cashRelations = $cashRelations;
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_TellerRepositories_Model\TellerCashCount
    {
        $entity = parent::it();
        if (!$entity instanceof \API_TellerRepositories_Model\TellerCashCount) {
            throw new EntityException('invalid_entity_name', [':name' => \API_TellerRepositories_Model\TellerCashCount::class]);
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

    public function cashRelations(): CashRelations
    {
        return $this->cashRelations;
    }
}