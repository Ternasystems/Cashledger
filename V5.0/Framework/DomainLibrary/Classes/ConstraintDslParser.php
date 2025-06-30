<?php

declare(strict_types=1);

namespace TS_Domain\Classes;

use TS_Database\Enums\DBDriver;
use TS_Exception\Classes\DomainException;

/**
 * A static helper class to parse a Domain-Specific Language (DSL) for generating
 * and reverse-engineering SQL CHECK constraints in a database-agnostic way.
 * This class cannot be instantiated.
 */
final class ConstraintDslParser
{
    private const DSL_KEYWORDS = ['from', 'range', 'list', '<', '=', '>', '!', '<=', '>=', '!='];
    private const VALUE_PATTERN = '(?:-?\d+(?:\.\d+)?|\d{4}-\d{2}-\d{2}(?:\s+\d{2}:\d{2}:\d{2})?|\d{2}:\d{2}:\d{2})';
    private const QUOTED_VALUE_PATTERN = '(?:\d+\.?\d*|\'[^\']*\'|"[^"]*"|\d{4}-\d{2}-\d{2}(?:\s+\d{2}:\d{2}:\d{2})?|\d{2}:\d{2}:\d{2})';

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
     * @throws DomainException if the DSL is invalid.
     */
    public static function toSql(string $dsl, DBDriver $driver, string $tablePrefix = 'cl_'): string
    {
        $dsl = trim($dsl);
        $keyword = strtolower(explode('(', explode('[', explode(' ', $dsl)[0])[0])[0]);

        if (!in_array($keyword, self::DSL_KEYWORDS)) {
            throw new DomainException('invalid_dsl_keyword', [':keyword' => $keyword]);
        }

        return match ($keyword) {
            'from' => self::parseFromDsl($dsl, $driver, $tablePrefix),
            'range' => self::parseRangeDsl($dsl),
            'list' => self::parseListDsl($dsl),
            '<', '=', '>', '!', '<=', '>=', '!=' => self::parseOperatorDsl($dsl),
            default => throw new DomainException('unsupported_dsl_keyword', [':keyword' => $keyword])
        };
    }

    // ... fromSql methods would also need to be made database-aware ...

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

    private static function parseFromDsl(string $dsl, DBDriver $driver, string $tablePrefix): string
    {
        if (preg_match('/^FROM\s+([a-zA-Z_]\w*)\.([a-zA-Z_]\w*)$/i', $dsl, $matches) !== 1) {
            throw new DomainException('invalid_from_dsl');
        }
        $tableName = $matches[1];
        $columnName = $matches[2];

        $quotedTable = self::getQuotedIdentifier($tablePrefix . $tableName, $driver);
        $quotedColumn = self::getQuotedIdentifier($columnName, $driver);

        return sprintf('VALUE IN (SELECT %s FROM %s)', $quotedColumn, $quotedTable);
    }

    private static function parseRangeDsl(string $dsl): string
    {
        $pattern = '/^RANGE\[\s*(?<min>' . self::VALUE_PATTERN . ')\s*,\s*(?<max>' . self::VALUE_PATTERN . ')\s*\]$/i';
        if (preg_match($pattern, $dsl, $matches) !== 1) {
            throw new DomainException('invalid_range_dsl');
        }
        $min = $matches['min'];
        $max = $matches['max'];
        if ($min > $max) {
            throw new DomainException('invalid_range_values');
        }
        $minFormatted = preg_match('/^\d{4}-\d{2}-\d{2}/', $min) ? "'$min'" : $min;
        $maxFormatted = preg_match('/^\d{4}-\d{2}-\d{2}/', $max) ? "'$max'" : $max;
        return sprintf('VALUE BETWEEN %s AND %s', $minFormatted, $maxFormatted);
    }

    private static function parseListDsl(string $dsl): string
    {
        if (preg_match('/^LIST\(\s*(\'.*?\')\s*(?:,\s*\'.*?\'\s*)*\)$/i', $dsl) !== 1) {
            throw new DomainException('invalid_list_dsl');
        }
        return preg_replace('/^list/i', 'VALUE IN', $dsl);
    }

    private static function parseOperatorDsl(string $dsl): string
    {
        $pattern = '/^(?<operator><=|>=|!=|=|<|>)\s*(?<value>' . self::QUOTED_VALUE_PATTERN . ')$/i';
        if (preg_match($pattern, $dsl) !== 1) {
            throw new DomainException('invalid_operator_dsl');
        }
        return 'VALUE ' . $dsl;
    }
}
