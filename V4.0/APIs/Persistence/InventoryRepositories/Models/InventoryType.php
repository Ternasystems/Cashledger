<?php

namespace API_InventoryRepositories_Model;

enum InventoryType: string
{
    case IN = 'IN';
    case OUT = 'OUT';
    case RETURN = 'RETURN';
    case WASTE = 'WASTE';
    case INVENT = 'INVENT';
    case TRANSFER = 'TRANSFER';
}
