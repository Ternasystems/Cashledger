<?php

namespace API_InventoryRepositories_Model;

use API_DTORepositories_Model\DTOBase;
use DateTime;

class Inventory extends DTOBase
{
    public string $StockId;
    public string $UnitId;
    public string $PartnerId;
    public string $InventoryType;
    public float $Quantity;
    public DateTime $InventDate;
}