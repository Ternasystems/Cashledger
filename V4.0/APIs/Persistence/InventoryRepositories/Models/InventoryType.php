<?php

namespace API_InventoryRepositories_Model;

enum InventoryType
{
    case IN;
    case OUT;
    case RETURN;
    case WASTE;
    case INVENT;
    case TRANSFER;
}
