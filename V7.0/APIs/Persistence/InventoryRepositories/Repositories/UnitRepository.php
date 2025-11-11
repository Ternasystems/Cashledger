<?php

namespace API_InventoryRepositories;

use API_DTORepositories\Repository;
use API_InventoryRepositories_Collection\Units;
use API_InventoryRepositories_Context\InventoryContext;
use API_InventoryRepositories_Model\Unit;

/**
 * @extends Repository<Unit, Units>
 */
class UnitRepository extends Repository
{
    public function __construct(InventoryContext $context)
    {
        parent::__construct($context, Unit::class, Units::class);
    }
}