<?php

namespace API_InventoryRepositories_Model;

use API_DTORepositories_Model\DTOBase;

class ProductAttribute extends DTOBase
{
    public string $AttributeType;
    public ?string $AttributeConstraint;
    public ?string $AttributeTable;
}