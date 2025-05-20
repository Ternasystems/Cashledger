<?php

namespace API_InventoryRepositories_Model;

use API_DTORepositories_Model\DTOBase;
use DateTime;

class TransferNote extends DTOBase
{
    public string $TransferNumber;
    public ?string $Reference;
    public DateTime $TransferDate;
    public DateTime $EditDate;
}