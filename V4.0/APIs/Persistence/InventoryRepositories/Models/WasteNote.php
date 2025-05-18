<?php

namespace API_InventoryRepositories_Model;

use API_DTORepositories_Model\DTOBase;
use DateTime;

class WasteNote extends DTOBase
{
    public string $WasteNumber;
    public ?string $Reference;
    public DateTime $WasteDate;
    public DateTime $EditDate;
}