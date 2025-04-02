<?php

namespace TS_Database\Classes;

use PDO;

class DBQuery extends DBPDO
{
    // Method to parse, prepare and execute the statement
    public function QueryPDO(PDO $_pdo, ?array $_arg = null, int $_fetchMode = 0, ?array $_options = null) : array
    {
        return $this->DBReturn(true, $_pdo, $_arg, $_fetchMode, $_options);
    }
}