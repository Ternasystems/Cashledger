<?php

namespace API_InventoryEntities_Model;

use API_DTOEntities_Model\Entity;
use API_InventoryEntities_Collection\Stocks;
use UnexpectedValueException;

class DispatchNote extends Entity
{
    private Stocks $stocks;

    public function __construct(\API_InventoryRepositories_Model\DispatchNote $_entity, Stocks $_stocks)
    {
        parent::__construct($_entity, null);
        $this->stocks = $_stocks;
    }

    public function It(): \API_InventoryRepositories_Model\DispatchNote
    {
        $entity = parent::It();
        if (!$entity instanceof \API_InventoryRepositories_Model\DispatchNote)
            throw new UnexpectedValueException('Object must be an instance of '.\API_InventoryRepositories_Model\DispatchNote::class);

        return $entity;
    }

    public function Stocks(): Stocks
    {
        return $this->stocks;
    }
}