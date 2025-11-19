<?php

namespace API_Administration_Contract;

use API_Administration_Service\ReloadMode;
use API_DTOEntities_Model\Parameter;

interface IParameterService
{
    public function getParameter(string $parameter, ReloadMode $reloadMode = ReloadMode::NO): ?Parameter;
    public function getFrom(string $predicate, ?array $args = null): string|float|null;
    public function checkParameter(string $predicate, ?array $args = null): bool;
    public function setParameter(string $paramName, string $paramValue, bool $encrypted): void;
}