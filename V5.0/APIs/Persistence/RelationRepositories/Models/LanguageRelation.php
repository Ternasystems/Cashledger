<?php

namespace API_RelationRepositories_Model;

use API_DTORepositories_Model\DTOBase;

class LanguageRelation extends DTOBase
{
    public string $Label;
    public string $LangId;
    public string $ReferenceId;
}