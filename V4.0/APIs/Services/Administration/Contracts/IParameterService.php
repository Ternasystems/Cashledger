<?php

namespace API_Administration_Contract;

interface IParameterService
{
    public function GetParameter(string $parameter): string;
    public function GetFrom(string $predicate, ?array $args = null): string|float|null;
    public function CheckParameter(string $predicate, ?array $args = null): bool;
    public function SetParameter(array $parameters): void;
}