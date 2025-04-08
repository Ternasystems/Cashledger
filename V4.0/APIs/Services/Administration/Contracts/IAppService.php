<?php

namespace API_Administration_Contract;

use API_DTOEntities_Collection\AppCategories;
use API_DTOEntities_Collection\Apps;
use API_DTOEntities_Model\App;
use API_DTOEntities_Model\AppCategory;

interface IAppService
{
    public function GetApps(callable $predicate = null): App|Apps|null;
    public function GetCategories(callable $predicate = null): AppCategory|AppCategories|null;
}