<?php

namespace APP_Inventory_Model;

use DateTime;

class InventoryModel
{
    // Inventory data
    public ?string $inventoryid;
    public string $inventorynumber;
    public DateTime $inventorydate;
    public string $warehouseid;

    // Stock data
    public array $stocks; // array($stockid => inventstockmodel)
    public ?string $inventorydesc;
}