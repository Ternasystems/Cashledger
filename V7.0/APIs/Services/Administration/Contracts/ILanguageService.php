<?php

namespace API_Administration_Contract;

use API_Administration_Service\ReloadMode;
use API_DTOEntities_Collection\Languages;
use API_DTOEntities_Model\Language;

interface ILanguageService
{
    public function GetLanguages(?array $filter = null, ?int $page = 1, ?int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Language|Languages|null;
}