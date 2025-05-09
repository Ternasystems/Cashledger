<?php

namespace APP_Inventory_Model;

use DateTime;

class StockItemModel
{
    public string $stockid;
    public string $dispatchnumber;
    public string $dispatchreference;
    public DateTime $dispatchdate;
    public string $customerid;
    public string $productid;
    public float $stockquantity;
    public string $batchnumber;
    public string $unitid;
    public string $warehouseid;
    public string $packagingid;
    public float $unitcost;
    public string $json;
    public StockInventModel $stockinvent;
    public bool $state;
}