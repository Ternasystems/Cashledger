<?php

namespace API_InventoryRepositories_Model;

use API_DTORepositories_Model\DTOBase;
use DateTime;

class DeliveryNote extends DTOBase
{
    public string $DeliveryNumber;
    public ?string $Reference;
    public DateTime $DeliveryDate;
}