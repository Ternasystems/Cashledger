<?php

namespace APP_Inventory_Model;

use DateTime;

class StockDispatchModel
{
    // Dispatch data
    public ?string $dispatchid;
    public string $dispatchnumber;
    public string $dispatchreference;
    public DateTime $dispatchdate;

    // Stock data
    public array $stocks; // array($stockid => stockitemmodel)
    public ?string $dispatchdesc;
}