<?php

namespace API_TellerEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;
use API_TellerEntities_Collection\Tellers;

class TellerTransaction extends Entity
{
    private Teller $teller;
    private Teller $approbator;

    public function __construct(\API_TellerRepositories_Model\TellerTransaction $_entity, Tellers $_tellers)
    {
        parent::__construct($_entity);
        $this->teller = $_tellers->first(fn($n) => $_entity->CreatedBy == $n->it()->Id);
        $this->approbator = $_tellers->first(fn($n) => $_entity->ApprovedBy == $n->it()->Id);
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_TellerRepositories_Model\TellerTransaction
    {
        $entity = parent::it();
        if (!$entity instanceof \API_TellerRepositories_Model\TellerTransaction)
            throw new EntityException('invalid_entity_name', [':name' => \API_TellerRepositories_Model\TellerTransaction::class]);

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
}