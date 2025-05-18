<?php

namespace API_RelationRepositories_Model;

use API_DTORepositories_Model\DTOBase;

class InventRelation extends DTOBase
{
    public string $InventId;
    public string $StockId;
}