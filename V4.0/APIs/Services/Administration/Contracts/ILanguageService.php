<?php

namespace API_Administration_Contract;

use API_DTOEntities_Collection\Languages;
use API_DTOEntities_Model\Language;

interface ILanguageService
{
    public function GetLanguages(callable $predicate = null): Language|Languages|null;
}