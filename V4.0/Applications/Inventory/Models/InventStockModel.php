<?php

namespace APP_Inventory_Model;

use DateTime;

class InventStockModel
{
    public string $stockid;
    public string $inventorynumber;
    public DateTime $inventorydate;
    public string $productid;
    public float $stockquantity;
    public float $stockavailable;
    public string $warehouseid;
    public string $json; // Stock
    public StockInventModel $stockinvent;
    public bool $state;
}