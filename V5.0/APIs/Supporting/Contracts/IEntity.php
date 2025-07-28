<?php

namespace API_DTOEntities_Contract;

use API_DTORepositories_Model\DTOBase;
use API_RelationRepositories_Collection\LanguageRelations;

interface IEntity
{
    public function It(): DTOBase;
    public function LanguageRelations() : ?LanguageRelations;
}