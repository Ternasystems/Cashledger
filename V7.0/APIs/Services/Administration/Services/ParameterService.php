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
    protected Parameter $parameter;
    protected Parameters $parameters;

    function __construct(ParameterFactory $_factory)
    {
        $this->factory = $_factory;
    }

    public function getParameters(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Parameter|Parameters|null
    {
        if (!isset($this->parameters) || $reloadMode == ReloadMode::YES)
        {
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->factory->filter($filter, $pageSize, $offset);
            $this->factory->Create();
            $this->parameters = $this->factory->collectable();
        }

        if ($this->parameters->count() === 0)
            return null;

        return $this->parameters->count() > 1 ? $this->parameters : $this->parameters->first();
    }

    /**
     * @throws DomainException
     */
    public function getParameter(string $parameter, ReloadMode $reloadMode = ReloadMode::NO): ?Parameter
    {
        if (!isset($this->parameters) || $reloadMode == ReloadMode::YES)
        {
            $this->factory->Create();
            $this->parameters = $this->factory->collectable();
        }

        if ($this->parameters->count() === 0)
            return null;

        {
            $param = $this->factory->repository()->getParameter($parameter);
            $content = trim($param->parameter, '()');
            $id = str_getcsv($content, ',', '"', '\\')[0];
        }

        $item = $this->parameters->first(fn($n) => $n->it()->Id == $id);

        return $this->parameters->first(fn($n) => $n->it()->Id == $item->it()->Id);
    }

    /**
     * @throws DomainException
     */
    public function getFrom(string $predicate, ?array $args = null): string|float|null
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
    public function checkParameter(string $predicate, ?array $args = null): bool
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
    public function setParameter(string $paramName, string $paramValue, bool $encrypted): void
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