<?php

namespace API_Administration_Contract;

use API_Administration_Service\ReloadMode;
use API_DTOEntities_Model\Parameter;

interface IParameterService
{
    public function GetParameter(string $parameter, ReloadMode $reloadMode = ReloadMode::NO): ?Parameter;
    public function GetFrom(string $predicate, ?array $args = null): string|float|null;
    public function CheckParameter(string $predicate, ?array $args = null): bool;
    public function SetParameter(string $paramName, string $paramValue, bool $encrypted): void;
}