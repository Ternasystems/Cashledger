<?php

namespace API_InventoryRepositories_Model;

use API_DTORepositories_Model\DTOBase;
use DateTime;

class InventNote extends DTOBase
{
    public string $InventNumber;
    public ?string $Reference;
    public DateTime $InventDate;
    public DateTime $EditDate;
}