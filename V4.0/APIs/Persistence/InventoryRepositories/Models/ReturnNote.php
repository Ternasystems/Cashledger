<?php

namespace API_InventoryRepositories_Model;

use API_DTORepositories_Model\DTOBase;
use DateTime;

class ReturnNote extends DTOBase
{
    public string $ReturnNumber;
    public ?string $Reference;
    public DateTime $ReturnDate;
    public DateTime $EditDate;
}