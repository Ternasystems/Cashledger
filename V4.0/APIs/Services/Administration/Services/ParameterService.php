<?php

namespace API_Administration_Service;

use API_Administration_Contract\IParameterService;
use API_DTORepositories_Context\Context;

class ParameterService implements IParameterService
{
    protected Context $context;

    public function __construct(Context $_context)
    {
        $this->context = $_context;
    }

    public function GetParameter(string $paramName): string
    {
        $data = $this->context->ExecuteQuery(sprintf('SELECT * FROM "cl_Parameters" WHERE "ParamName" = %s', $paramName));
        return $data[2] ?? $data[3];
    }

    public function GetFrom(string $predicate, ?array $args = null): string|float|null
    {
        $str = is_null($args) ? null : implode(', ', array_fill(0, count($args), '?'));
        return $this->context->ExecuteQuery(sprintf('SELECT * FROM "%s"(%s)', $predicate, $str))[0][$predicate];
    }

    public function CheckParameter(string $predicate, ?array $args = null): bool
    {
        $str = is_null($args) ? null : implode(', ', array_fill(0, count($args), '?'));
        return $this->context->ExecuteQuery(sprintf('SELECT * FROM "%s"(\'%s\')', $predicate, $str))[0][$predicate];
    }

    public function SetParameter(array $parameters): void
    {
        $sql = 'CALL "p_UpdateParameter"(:paramName, :paramValue, :encrypted)';
        $args = [
            ':paramName' => $parameters[0],
            ':paramValue' => $parameters[1],
            ':encrypted' => $parameters[2]
        ];
        $this->context->ExecuteQuery($sql, $args);
    }
}