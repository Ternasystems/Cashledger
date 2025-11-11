<?php

namespace API_Administration_Service;

use API_Administration_Contract\IParameterService;
use API_Assets\Classes\AdministrationException;
use API_DTOEntities_Collection\Parameters;
use API_DTOEntities_Factory\ParameterFactory;
use API_DTOEntities_Model\Parameter;
use TS_Exception\Classes\DomainException;

class ParameterService implements IParameterService
{
    protected ParameterFactory $factory;
    protected Parameters $parameters;

    function __construct(ParameterFactory $_factory)
    {
        $this->factory = $_factory;
    }

    /**
     * @throws DomainException
     */
    public function GetParameter(string $parameter, ReloadMode $reloadMode = ReloadMode::NO): ?Parameter
    {
        if (!isset($this->parameters) || $reloadMode == ReloadMode::YES)
        {
            $this->factory->Create();
            $this->parameters = $this->factory->collectable();
        }

        if ($this->parameters->count() === 0)
            return null;

        return $this->parameters->first(fn($n) => $n->it()->ParamName == $parameter);
    }

    /**
     * @throws DomainException
     */
    public function GetFrom(string $predicate, ?array $args = null): string|float|null
    {
        if (!isset($this->parameters))
        {
            $this->factory->Create();
            $this->parameters = $this->factory->collectable();
        }

        return $this->factory->repository()->getParameterFrom($predicate, $args);
    }

    /**
     * @throws DomainException
     */
    public function CheckParameter(string $predicate, ?array $args = null): bool
    {
        if (!isset($this->parameters))
        {
            $this->factory->Create();
            $this->parameters = $this->factory->collectable();
        }

        return $this->factory->repository()->checkParameter($predicate, $args);
    }

    /**
     * @throws DomainException
     * @throws AdministrationException
     */
    public function SetParameter(string $paramName, string $paramValue, bool $encrypted): void
    {
        if (!isset($this->parameters))
        {
            $this->factory->Create();
            $this->parameters = $this->factory->collectable();
        }

        $item = $this->parameters->select(fn($n) => $n->it()->ParamName == $paramName)?->first();

        if ($item === null)
            throw new AdministrationException("item_not_found", [$paramName]);

        if ($encrypted)
            $item['ParamValue'] = hash('256', $paramValue);
        else
            $item['ParamUValue'] = $paramValue;

        $this->factory->repository()->updateParameter($paramName, $paramValue, $encrypted);
    }
}