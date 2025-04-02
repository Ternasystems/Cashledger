<?php

namespace API_Administration_Controller;

use API_Administration_Contract\IParameterService;
use TS_Controller\Classes\BaseController;

class ParameterController extends BaseController
{
    private IParameterService $service;

    public function __construct(IParameterService $_service)
    {
        $this->service = $_service;
    }

    public function GetByName(string $name): string
    {
        $data = $this->service->GetParameter($name);
        return is_null($data[2]) ? $data[2] : $data[3];
    }

    public function GetFrom(string $predicate, ?array $args = null): string|float|null
    {
        return $this->service->GetFrom($predicate, $args);
    }

    public function CheckParameter(string $predicate, ?array $args = null): bool
    {
        return $this->service->CheckParameter($predicate, $args);
    }

    public function UpdateParameter(string $paramName, string $paramValue, bool $encrypted): void
    {
        $this->service->SetParameter([$paramName, $paramValue, $encrypted]);
    }
}