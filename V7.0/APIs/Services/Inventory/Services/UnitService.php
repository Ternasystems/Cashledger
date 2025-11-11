<?php

namespace API_Inventory_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\InventoryException;
use API_DTOEntities_Factory\CollectableFactory;
use API_Inventory_Contract\IUnitService;
use API_InventoryEntities_Collection\Units;
use API_InventoryEntities_Model\Unit;
use API_InventoryRepositories\UnitRepository;
use API_RelationRepositories\LanguageRelationRepository;
use ReflectionException;
use Throwable;
use TS_Exception\Classes\DomainException;

class UnitService implements IUnitService
{
    protected UnitRepository $unitRepository;
    protected CollectableFactory $factory;
    protected Units $units;
    protected LanguageRelationRepository $relationRepository;

    /**
     * @throws ReflectionException
     */
    function __construct(UnitRepository $_unitRepository, LanguageRelationRepository $_relationRepository)
    {
        $this->unitRepository = $_unitRepository;
        $this->relationRepository = $_relationRepository;

        $this->factory = new CollectableFactory($this->unitRepository, $this->relationRepository);
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getUnits(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Unit|Units|null
    {
        if (!isset($this->units) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->factory->filter($filter, $pageSize, $offset);
            $this->factory->Create();
            $this->units = $this->factory->collectable();
        }

        if (count($this->units) === 0)
            return null;

        return $this->units->count() > 1 ? $this->units : $this->units->first();
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws InventoryException
     * @throws Throwable
     */
    public function SetUnit(array $data): Unit
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main unit DTO
            $unit = new \API_InventoryRepositories_Model\Unit($data['unitData']);
            $this->factory->repository()->add($unit);

            // 2. Get the newly created unit
            $unit = $this->factory->repository()->first([['Name', '=', $data['unitData']['Name']]]);
            if (!$unit)
                throw new InventoryException('unit_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getUnits([['Id', '=', $unit->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws InventoryException
     * @throws Throwable
     */
    public function PutUnit(string $id, array $data): ?Unit
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            $unit = $this->getUnits([['Id', '=', $id]])?->first();
            if (!$unit)
                throw new InventoryException('unit_not_found', ["Id" => $id]);

            // 1. Update the main unit record
            foreach ($data as $field => $value)
                $unit->it()->{$field} = $value ?? $unit->it()->{$field};

            $this->factory->repository()->update($unit->it());
            $context->commit();

            return $this->getUnits([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws InventoryException
     * @throws Throwable
     */
    public function DeleteUnit(string $id): bool
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            $unit = $this->getUnits([['Id', '=', $id]])?->first();
            if (!$unit){
                $context->commit();
                return true;
            }

            $this->factory->repository()->remove($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}