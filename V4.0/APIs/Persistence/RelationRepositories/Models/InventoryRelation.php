<?php

namespace API_RelationRepositories_Model;

use API_DTORepositories_Model\DTOBase;

class InventoryRelation extends DTOBase
{
    public string $InventoryId;
    public string $CredentialId;
}