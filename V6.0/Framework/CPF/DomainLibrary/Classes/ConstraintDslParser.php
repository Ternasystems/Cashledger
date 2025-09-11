<?php

declare(strict_types=1);

namespace TS_Domain\Classes;

use TS_Configuration\Classes\AbstractCls;
use TS_Database\Enums\DBDriver;
use TS_Exception\Classes\DSLException;

/**
 * A static helper class to parse a Domain-Specific Language (DSL) for generating
 * and reverse-engineering SQL CHECK constraints in a database-agnostic way.
 * This class cannot be instantiated.
 */
final class ConstraintDslParser extends AbstractCls
{
    private const array DSL_KEYWORDS = ['from', 'range', 'list', '<', '=', '>', '!', '<=', '>=', '!='];
    private const string VALUE_PATTERN = '(?:-?\d+(?:\.\d+)?|\d{4}-\d{2}-\d{2}(?:\s+\d{2}:\d{2}:\d{2})?|\d{2}:\d{2}:\d{2})';
    private const string QUOTED_VALUE_PATTERN = '(?:\d+\.?\d*|\'[^\']*\'|"[^"]*"|\d{4}-\d{2}-\d{2}(?:\s+\d{2}:\d{2}:\d{2})?|\d{2}:\d{2}:\d{2})';

    /**
     * Private constructor to prevent instantiation of this static helper class.
     */
    private function __construct()
    {
    }

    /**
     * Converts a DSL string into an SQL CHECK constraint clause.
     *
     * @param string $dsl The DSL string (e.g., "FROM table.column").
     * @param DBDriver $driver The current database driver, used for correct SQL syntax generation.
     * @param string $tablePrefix A prefix to add to table names, like 'cl_'.
     * @return string The generated SQL clause.
     * @throws DSLException if the DSL is invalid.
     */
    public static function toSql(string $dsl, DBDriver $driver, string $tablePrefix = 'cl_'): string
    {
        $dsl = trim($dsl);
        $keyword = strtolower(explode('(', explode('[', explode(' ', $dsl)[0])[0])[0]);

        if (!in_array($keyword, self::DSL_KEYWORDS)) {
            throw new DSLException('invalid_dsl_keyword', [':keyword' => $keyword]);
        }

        return match ($keyword) {
            'from' => self::parseFromDsl($dsl, $driver, $tablePrefix),
            'range' => self::parseRangeDsl($dsl),
            'list' => self::parseListDsl($dsl),
            '<', '=', '>', '!', '<=', '>=', '!=' => self::parseOperatorDsl($dsl),
            default => throw new DSLException('unsupported_dsl_keyword', [':keyword' => $keyword])
        };
    }

    /**
     * Converts an SQL CHECK constraint clause back into a DSL string.
     *
     * @param string $sql The raw SQL from the CHECK constraint.
     * @param string $tablePrefix The prefix to remove from table names.
     * @return string The generated DSL string.
     * @throws DSLException if the SQL cannot be parsed.
     */
    public static function fromSql(string $sql, string $tablePrefix = 'cl_'): string
    {
        $sql = trim($sql);

        // Attempt to match each known SQL pattern in order of complexity.
        if (preg_match('/VALUE\s+BETWEEN\s+(.+)\s+AND\s+(.+)/i', $sql, $matches)) {
            return sprintf('RANGE[%s, %s]', trim($matches[1], "'"), trim($matches[2], "'"));
        }

        if (preg_match('/VALUE\s+IN\s*\((.+)\)/i', $sql, $matches)) {
            // Check if it's a subquery (FROM clause)
            if (preg_match('/SELECT\s+.+\s+FROM\s+[`"\[](.+)[`"\]]/i', $matches[1], $subQueryMatches)) {
                $fullTableName = $subQueryMatches[1];
                $tableName = str_starts_with($fullTableName, $tablePrefix) ? substr($fullTableName, strlen($tablePrefix)) : $fullTableName;
                // Note: We can't know the column name from the check constraint alone. We assume 'ID' as a default.
                return "FROM {$tableName}.ID";
            }
            // Otherwise, it's a LIST
            return 'LIST(' . $matches[1] . ')';
        }

        if (preg_match('/VALUE\s*(<=|>=|!=|=|<|>)\s*(.+)/i', $sql, $matches)) {
            return trim("{$matches[1]} {$matches[2]}");
        }

        throw new DSLException('unparsable_sql_constraint', [':sql' => $sql]);
    }

    // --- Private Helper to Quote Identifiers Correctly ---

    private static function getQuotedIdentifier(string $identifier, DBDriver $driver): string
    {
        return match ($driver) {
            DBDriver::Mysql => "`{$identifier}`",
            DBDriver::Pgsql, DBDriver::Oracle => "\"{$identifier}\"",
            DBDriver::Sqlsrv => "[{$identifier}]",
        };
    }

    // --- Private Parsers: DSL to SQL ---

    /**
     * @throws DSLException
     */
    private static function parseFromDsl(string $dsl, DBDriver $driver, string $tablePrefix): string
    {
        if (preg_match('/^FROM\s+([a-zA-Z_]\w*)\.([a-zA-Z_]\w*)$/i', $dsl, $matches) !== 1) {
            throw new DSLException('invalid_from_dsl');
        }
        $tableName = $matches[1];
        $columnName = $matches[2];

        $quotedTable = self::getQuotedIdentifier($tablePrefix . $tableName, $driver);
        $quotedColumn = self::getQuotedIdentifier($columnName, $driver);

        return sprintf('VALUE IN (SELECT %s FROM %s)', $quotedColumn, $quotedTable);
    }

    /**
     * @throws DSLException
     */
    private static function parseRangeDsl(string $dsl): string
    {
        $pattern = '/^RANGE\[\s*(?<min>' . self::VALUE_PATTERN . ')\s*,\s*(?<max>' . self::VALUE_PATTERN . ')\s*\]$/i';
        if (preg_match($pattern, $dsl, $matches) !== 1) {
            throw new DSLException('invalid_range_dsl');
        }
        $min = $matches['min'];
        $max = $matches['max'];
        if ($min > $max) {
            throw new DSLException('invalid_range_values');
        }
        $minFormatted = preg_match('/^\d{4}-\d{2}-\d{2}/', $min) ? "'$min'" : $min;
        $maxFormatted = preg_match('/^\d{4}-\d{2}-\d{2}/', $max) ? "'$max'" : $max;
        return sprintf('VALUE BETWEEN %s AND %s', $minFormatted, $maxFormatted);
    }

    /**
     * @throws DSLException
     */
    private static function parseListDsl(string $dsl): string
    {
        if (preg_match('/^LIST\(\s*(\'.*?\')\s*(?:,\s*\'.*?\'\s*)*\)$/i', $dsl) !== 1) {
            throw new DSLException('invalid_list_dsl');
        }
        return preg_replace('/^list/i', 'VALUE IN', $dsl);
    }

    /**
     * @throws DSLException
     */
    private static function parseOperatorDsl(string $dsl): string
    {
        $pattern = '/^(?<operator><=|>=|!=|=|<|>)\s*(?<value>' . self::QUOTED_VALUE_PATTERN . ')$/i';
        if (preg_match($pattern, $dsl) !== 1) {
            throw new DSLException('invalid_operator_dsl');
        }
        return 'VALUE ' . $dsl;
    }
}
