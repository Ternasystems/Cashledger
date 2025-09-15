<?php

namespace API_ProfilingRepositories_Model;

use API_DTORepositories_Model\DTOBase;

/**
 * Represents authentication or session tokens.
 */
class Token extends DTOBase
{
    /**
     * @param string $tableName
     * @return void
     */
    public static function setTableName(string $tableName): void
    {
        self::$table = $tableName;
    }

    public static function getTableName(): string
    {
        return self::$table;
    }

    public static function hasTableName(): bool
    {
        return isset(self::$table);
    }
}