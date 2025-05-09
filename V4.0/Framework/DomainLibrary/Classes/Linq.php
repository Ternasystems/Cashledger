<?php
/*
 * This website is powered by Ternary Data Systems.
 * Ternary Data Systems is the technology department of the Jéoline company
 * This website is based on the Hypergates framework.
 */

declare(strict_types=1);

namespace TS_Domain\Classes;

header('Content-Type: text/html; charset=utf-8');

use TS_Configuration\Classes\AbstractCls;
use TS_Exception\Classes\UtilsException;

class Linq extends AbstractCls
{
    private string $prefix = 'public."cl_';
    private array $classes = ['from', 'range', 'list', '<', '=', '>', '!', '<=', '>=', '!='];

    /* Inherited protected methods */

    // Method to set the exception property
    protected function setException(): void
    {
        $this->exception = new UtilsException();
    }

    // Method to get the exception property
    public function getException(): void
    {
        throw $this->exception;
    }

    /* Public methods */

    public function constraint(string $constraintType, ?string $constraint): ?string
    {
        if (is_null($constraint))
            return null;

        $constraint = trim($constraint);

        if (!in_array($constraintType, $this->classes))
            return null;

        if (!str_starts_with(strtolower($constraint), $constraintType))
            return null;

        switch ($constraintType){
            case 'from':
            {
                // Check Constraint expression
                if (preg_match('/^FROM\s+([a-zA-Z_]\w*)\.([a-zA-Z_]\w*)$/i', $constraint) != 1)
                    return null;

                $arr = explode('.', $constraint);
                $tablename = preg_replace('/^FROM\s+/i', '', $arr[0]);
                $str = sprintf('VALUE IN (SELECT "%s" FROM %s%s")', $arr[1], $this->prefix, $tablename);
            }
            break;
            case 'range':
            {
                // Define the value pattern as a constant for reusability
                define('VALUE_PATTERN', '(?:-?\d+(?:\.\d+)?|\d{4}-\d{2}-\d{2}(?:\s+\d{2}:\d{2}:\d{2})?|\d{2}:\d{2}:\d{2})');

                // Main pattern with named capture groups
                $pattern = '/^RANGE\[\s*(?<min>'.VALUE_PATTERN.')\s*,\s*(?<max>'.VALUE_PATTERN.')\s*\]$/i';

                if (preg_match($pattern, $constraint, $matches) !== 1)
                    return null;

                // Extract values using named captures
                $min = $matches['min'];
                $max = $matches['max'];

                // Validate min <= max
                if ($min > $max)
                    return null;

                $min = preg_match('/^\d{4}-\d{2}-\d{2}(?: \d{2}:\d{2}:\d{2})?$|^\d{2}:\d{2}:\d{2}$/', $min) ? "'$min'" : $min;
                $max = preg_match('/^\d{4}-\d{2}-\d{2}(?: \d{2}:\d{2}:\d{2})?$|^\d{2}:\d{2}:\d{2}$/', $max) ? "'$max'" : $max;

                // Format the output string
                $str = sprintf('VALUE BETWEEN %s AND %s', $min, $max);
            }
            break;
            case 'list':
            {
                if (preg_match('/^LIST\(\s*(\'.*?\')\s*(?:,\s*\'.*?\'\s*)*\)$/i', $constraint) != 1)
                    return null;

                $str = preg_replace('/\blist\b/i', 'VALUE IN ', $constraint);
            }
            break;
            case '<':
            case '=':
            case '>':
            case '!':
            case '<=':
            case '>=':
            case '!=':
            {
                // Define the value pattern as a constant
                define('VALUE_PATTERN', '(?:\d+\.?\d*|\'[^\']*\'|\"[^\"]*\"|\d{4}-\d{2}-\d{2}(?:\s+\d{2}:\d{2}:\d{2})?|\d{2}:\d{2}:\d{2})');

                // Main pattern with named capture groups
                $pattern = '/^(?<operator><=|>=|!=|=|<|>)\s*(?<value>'.VALUE_PATTERN.')$/i';

                if (preg_match($pattern, $constraint) !== 1)
                    return null;

// Format the output string using named captures
                $str = sprintf('VALUE %s', $constraint);
            }
            break;
            default:
                return null;
        }

        return $str;
    }

    public function constraintType(?string $constraint): string
    {
        if (is_null($constraint))
            return 'none';

        foreach ($this->classes as $class){
            $str = $class == 'range' ? 'between' : ($class == 'list' ? 'in' : $class);
            if (preg_match('/'.$str.'/mi', $constraint) == 1)
                return $class;
        }
        return 'none';
    }

    public function constraintTable(?string $constraint): ?string
    {
        if (is_null($constraint))
            return null;

        if (preg_match('/from/i', $constraint) != 1)
            return null;

        if (preg_match('/value/i', $constraint) != 1){
            $arr = explode('.', $constraint);
            return 'cl_'.preg_replace('/^FROM\s+/i', '', $arr[0]);
        }
        else{
            preg_match('/VALUE\s+IN\s+\(SELECT\s+"(?<column>\w+)"\s+FROM\s+(?<schema>\w+)\."(?<table>\w+)"\)/mi', $constraint, $matches);
            return $matches['table'];
        }
    }

    public function linq(string $constraintType, string $constraint): ?string
    {
        $constraint = trim($constraint);
        $constraintType = strtolower($constraintType);

        if (!in_array($constraintType, $this->classes))
            return null;

        switch ($constraintType){
            case 'from':
                {
                    // Check Constraint expression
                    if (preg_match('/VALUE\s+IN\s+\(SELECT\s+"(?<column>\w+)"\s+FROM\s+(?<schema>\w+)\."(?<table>\w+)"\)/mi', $constraint, $matches) != 1)
                        return null;

                    $str = sprintf('FROM %s.%s', substr($matches['table'], 3), $matches['column']);
                }
                break;
            case 'range':
                {
                    if (preg_match('/^VALUE\s+BETWEEN\s+(?<min>\'?[\d:\-\s\.]+\'?)\s+AND\s+(?<max>\'?[\d:\-\s\.]+\'?)/i', $constraint, $matches) !== 1)
                        return null;

                    // Trim surrounding quotes if present
                    $min = trim($matches['min'], " \t\n\r\0\x0B'\"");
                    $max = trim($matches['max'], " \t\n\r\0\x0B'\"");

                    $str = sprintf('RANGE[%s,%s]', $min, $max);

                }
                break;
            case 'list':
                {
                    if (preg_match('/^VALUE\s+IN\s+\((?<items>.+)\)$/mi', $constraint, $matches) != 1)
                        return null;

                    $str = sprintf('LIST(%s)', $matches['items']);
                }
                break;
            case '<':
            case '=':
            case '>':
            case '!':
            case '<=':
            case '>=':
            case '!=':
                {
                    if (preg_match('/^VALUE\s*(=|!=|<>|<=|>=|<|>)\s*(.+)$/mi', $constraint, $matches) != 1)
                        return null;

                    $value = trim($matches[2], " '\"");
                    $str = sprintf('%s %s', $matches[1], $value);
                }
                break;
            default:
                return null;
        }

        return $str;
    }
}