<?php

namespace API_Inventory_Service;

use API_DTOEntities_Factory\CollectableFactory;
use API_Inventory_Contract\IUnitService;
use API_InventoryEntities_Collection\Units;
use API_InventoryEntities_Model\Unit;
use API_InventoryRepositories\UnitRepository;
use API_RelationRepositories\LanguageRelationRepository;
use ReflectionException;

class UnitService implements IUnitService
{
    protected ?Units $units;
    protected UnitRepository $unitRepository;
    protected LanguageRelationRepository $relationRepository;

    /**
     * @throws ReflectionException
     */
    public function __construct(UnitRepository $_units, LanguageRelationRepository $_relationRepository)
    {
        $factory = new CollectableFactory($_units, $_relationRepository);
        $factory->Create();
        $this->units = $factory->Collectable();
        $this->unitRepository = $_units;
        $this->relationRepository = $_relationRepository;
    }

    public function GetUnits(callable $predicate = null): Unit|Units|null
    {
        if (is_null($predicate))
            return $this->units;

        $collection = $this->units->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    /**
     * @throws ReflectionException
     */
    public function SetUnit(object $model): void
    {
        $this->unitRepository->Add(\API_InventoryRepositories_Model\Unit::class, array($model->unitname, $model->unitlabel));
        $factory = new CollectableFactory($this->unitRepository, $this->relationRepository);
        $factory->Create();
        $this->units = $factory->Collectable();
    }

    /**
     * @throws ReflectionException
     */
    public function PutUnit(object $model): void
    {
        $this->unitRepository->Update(\API_InventoryRepositories_Model\Unit::class, array($model->Id, $model->unitname, $model->unitlabel));
        $factory = new CollectableFactory($this->unitRepository, $this->relationRepository);
        $factory->Create();
        $this->units = $factory->Collectable();
    }

    /**
     * @throws ReflectionException
     */
    public function DeleteUnit(string $id): void
    {
        $this->unitRepository->Remove(\API_InventoryRepositories_Model\Unit::class, array($id));
        $factory = new CollectableFactory($this->unitRepository, $this->relationRepository);
        $factory->Create();
        $this->units = $factory->Collectable();
    }
}