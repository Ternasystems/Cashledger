<?php

namespace API_RelationRepositories_Model;

use API_DTORepositories_Model\DTOBase;

class RoleRelation extends DTOBase
{
    public string $CredentialId;
    public string $RoleId;
}