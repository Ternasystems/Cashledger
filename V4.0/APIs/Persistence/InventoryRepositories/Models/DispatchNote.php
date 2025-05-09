<?php

namespace API_InventoryRepositories_Model;

use API_DTORepositories_Model\DTOBase;
use DateTime;

class DispatchNote extends DTOBase
{
    public string $DispatchNumber;
    public ?string $Reference;
    public DateTime $DispatchDate;
    public DateTime $EditDate;
}