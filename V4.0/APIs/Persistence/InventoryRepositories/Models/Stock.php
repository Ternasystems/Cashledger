<?php

namespace API_InventoryRepositories_Model;

use API_DTORepositories_Model\DTOBase;
use DateTime;

class Stock extends DTOBase
{
    public string $ProductId;
    public string $UnitId;
    public string $WarehouseId;
    public DateTime $StockDate;
    public string $BatchNumber;
    public DateTime $LastChecked;
    public float $Quantity;
    public float $UnitCost;
    public float $UnitPrice;
}