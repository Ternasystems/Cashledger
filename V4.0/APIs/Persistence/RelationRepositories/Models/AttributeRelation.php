<?php

namespace API_RelationRepositories_Model;

use API_DTORepositories_Model\DTOBase;

class AttributeRelation extends DTOBase
{
    public string $AttributeId;
    public string $ProductId;
    public string $Value;
}