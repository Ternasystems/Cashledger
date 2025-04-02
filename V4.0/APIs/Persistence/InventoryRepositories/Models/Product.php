<?php

namespace API_InventoryRepositories_Model;

use API_DTORepositories_Model\DTOBase;
use DateTime;

class Product extends DTOBase
{
    public string $CategoryId;
    public string $UnitId;
    public float $MinStock;
    public float $MaxStock;
    public DateTime $StartDate;
}