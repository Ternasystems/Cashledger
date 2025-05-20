<?php

namespace API_RelationRepositories_Model;

use API_DTORepositories_Model\DTOBase;

class TransferRelation extends DTOBase
{
    public string $TransferId;
    public string $StockId;
}