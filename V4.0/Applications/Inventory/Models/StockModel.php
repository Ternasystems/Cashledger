<?php

namespace APP_Inventory_Model;

use DateTime;

class StockModel
{
    // Delivery note data
    public ?string $deliveryid;
    public string $deliverynumber;
    public ?string $deliveryreference;
    public DateTime $deliverydate;


    // Stock data
    public ?string $stockid;
    public string $batchnumber;
    public string $productid;
    public string $unitid;
    public string $warehouseid;
    public string $packagingid;
    public float $stockquantity;
    public float $unitcost;
    public StockInventModel $stockinvent;
    public ?array $attributes;
    public bool $state;
}