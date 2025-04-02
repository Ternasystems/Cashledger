<?php

namespace API_DTORepositories_Context;

use PDO;
use TS_Database\Classes\DBContext;
use TS_Database\Traits\TraitContext;

class Context extends DBContext
{
    protected PDO $pdo;

    public function __construct(array $_connectionString){
        $this->pdo = DBContext::GetConnection($_connectionString);
    }

    use TraitContext;
}