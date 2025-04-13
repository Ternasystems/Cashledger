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

    public function constraint(string $constraintType, string $constraint): ?string
    {
        $constraint = trim($constraint);

        if (!in_array($constraintType, $this->classes))
            return null;

        if (!str_starts_with(strtolower($constraint), $constraintType))
            return null;

        switch ($constraintType){
            case 'from':
            {
                // Check Constraint expression
                if (preg_match('/^FROM\s+([a-zA-Z_]\w*)\.([a-zA-Z_]\w*)$/mi', $constraint) != 1)
                    return null;

                $arr = explode('.', $constraint);
                $tablename = preg_replace('/^FROM\s+/i', '', $arr[0]);
                $str = sprintf('VALUE IN (SELECT "%s" FROM %s%s")', $arr[1], $this->prefix, $tablename);
            }
            break;
            case 'range':
            {
                if (preg_match('/^RANGE\[\s*(-?\d+(\.\d+)?)\s*,\s*(-?\d+(\.\d+)?)\s*]$/mi', $constraint) != 1)
                    return null;

                sscanf($constraint, 'RANGE[%d,%d]', $min, $max);
                $str = sprintf('VALUE BETWEEN %s AND %s', $min, $max);
            }
            break;
            case 'list':
            {
                if (preg_match('/^LIST\(\s*([-\w.]+)(\s*,\s*[-\w.]+)*\s*\)$/mi', $constraint) != 1)
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
                if (preg_match('/^(<=|>=|!=|=|<|>|!)\s*([\w.]+)/mi', $constraint) != 1)
                    return null;

                $str = sprintf('VALUE %s', $constraint);
            }
            break;
            default:
                return null;
        }

        return $str;
    }

    public function constraintType(?string $constraint): ?string
    {
        if (is_null($constraint))
            return 'none';

        foreach ($this->classes as $class){
            $str = $class == 'range' ? 'between' : ($class == 'list' ? 'in' : $class);
            if (preg_match('/'.$str.'/mi', $constraint) == 1)
                return $class;
        }
        return null;
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
                    if (preg_match('/^VALUE\s+BETWEEN\s+(?<min>[\d.-]+)\s+AND\s+(?<max>[\d.-]+)/mi', $constraint, $matches) != 1)
                        return null;

                    $str = sprintf('RANGE[%s,%s]', $matches['min'], $matches['max']);
                }
                break;
            case 'list':
                {
                    if (preg_match('/^VALUE\s+IN\s+\((?<items>.+)\)$/mi', $constraint, $matches) != 1)
                        return null;

                    $str = sprintf('LIST(%s)', implode(',', $matches['items']));
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