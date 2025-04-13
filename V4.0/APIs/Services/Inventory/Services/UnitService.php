<?php

namespace API_Inventory_Service;

use API_Administration_Contract\ILanguageService;
use API_DTOEntities_Factory\CollectableFactory;
use API_Inventory_Contract\IUnitService;
use API_InventoryEntities_Collection\Units;
use API_InventoryEntities_Model\Unit;
use API_InventoryRepositories\UnitRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories_Model\LanguageRelation;
use Exception;
use ReflectionException;

class UnitService implements IUnitService
{
    protected ?Units $units;
    protected UnitRepository $unitRepository;
    protected LanguageRelationRepository $relationRepository;
    protected ILanguageService $languageService;

    /**
     * @throws ReflectionException
     */
    public function __construct(UnitRepository $_units, LanguageRelationRepository $_relationRepository, ILanguageService $_languageService)
    {
        $factory = new CollectableFactory($_units, $_relationRepository);
        $factory->Create();
        $this->units = $factory->Collectable();
        $this->unitRepository = $_units;
        $this->relationRepository = $_relationRepository;
        $this->languageService = $_languageService;
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
        $this->unitRepository->Add(\API_InventoryRepositories_Model\Unit::class, array($model->unitname, $model->unitlabel, $model->unitdesc));
        $factory = new CollectableFactory($this->unitRepository, $this->relationRepository);
        $factory->Create();
        $this->units = $factory->Collectable();
        $id = $this->unitRepository->FirstOrDefault(fn($n) => $n->Name == $model->unitname)->Id;
        //
        $languages = $this->languageService->GetLanguages();
        foreach ($languages as $language){
            $lang = $language->It()->Label;
            $this->relationRepository->Add(LanguageRelation::class, array($language->It()->Id, $id, $model->unitlocale[$lang]));
        }
        //
        $factory = new CollectableFactory($this->unitRepository, $this->relationRepository);
        $factory->Create();
        $this->units = $factory->Collectable();
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function PutUnit(object $model): void
    {
        $this->unitRepository->Update(\API_InventoryRepositories_Model\Unit::class, array($model->unitid, $model->unitname, $model->unitlabel, $model->unitdesc));
        //
        $languages = $this->languageService->GetLanguages();
        $relations = $this->units->FirstOrDefault(fn($n) => $n->It()->Id == $model->unitid)->LanguageRelations();
        foreach ($relations as $relation){
            $id = $relation->LangId;
            $lang = $languages->FirstOrDefault(fn($n) => $n->It()->Id == $id)->It()->Label;
            if (key_exists($lang, $model->unitlocale))
                $this->relationRepository->Update(LanguageRelation::class, array($relation->Id, $model->unitlocale[$lang]));
            else
                $this->relationRepository->Remove(LanguageRelation::class, array($relation->Id));
        }
        //
        $factory = new CollectableFactory($this->unitRepository, $this->relationRepository);
        $factory->Create();
        $this->units = $factory->Collectable();
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function DeleteUnit(string $id): void
    {
        $relations = $this->units->FirstOrDefault(fn($n) => $n->It()->Id == $id)->LanguageRelations();
        foreach ($relations as $relation)
            $this->relationRepository->Remove(LanguageRelation::class, array($relation->Id));
        //
        $this->unitRepository->Remove(\API_InventoryRepositories_Model\Unit::class, array($id));
        $factory = new CollectableFactory($this->unitRepository, $this->relationRepository);
        $factory->Create();
        $this->units = $factory->Collectable();
    }
}