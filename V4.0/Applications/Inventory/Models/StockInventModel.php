<?php

namespace APP_Inventory_Model;

class StockInventModel
{
    public ?string $inventid;
    public ?string $noteid;
    public ?string $stockid;
    public string $unitid;
    public string $partnerid;
    public string $inventorytype;
    public float $quantity;
    public float $unitcost;
    public string $credentialid;
}