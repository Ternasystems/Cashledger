<?php

namespace APP_Inventory_Model;

use DateTime;

class StockDeliveryModel
{
    // Delivery note data
    public ?string $deliveryid;
    public string $deliverynumber;
    public ?string $deliveryreference;
    public DateTime $deliverydate;

    // Stock data
    public array $warehouses; // array(warehouses => array(products => packaging, unit, quantity, unitcost, unitprice)
    public string $supplierid;
    public ?string $deliverydesc;
}