<?php

namespace API_Inventory_Service;

use API_Administration_Contract\ILanguageService;
use API_DTOEntities_Factory\CollectableFactory;
use API_Inventory_Contract\IPackagingService;
use API_InventoryEntities_Collection\Packagings;
use API_InventoryEntities_Model\Packaging;
use API_InventoryRepositories\PackagingRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories_Model\LanguageRelation;
use Exception;
use ReflectionException;

class PackagingService implements IPackagingService
{
    protected Packagings $packagings;
    protected PackagingRepository $packagingRepository;
    protected LanguageRelationRepository $relationRepository;
    protected ILanguageService $languageService;

    /**
     * @throws ReflectionException
     */
    public function __construct(PackagingRepository $_packagings, LanguageRelationRepository $_relationRepository, ILanguageService $_languageService)
    {
        $factory = new CollectableFactory($_packagings, $_relationRepository);
        $factory->Create();
        $this->packagings = $factory->Collectable();
        $this->packagingRepository = $_packagings;
        $this->relationRepository = $_relationRepository;
        $this->languageService = $_languageService;
    }

    public function GetPackagings(callable $predicate = null): Packaging|Packagings|null
    {
        if (is_null($predicate))
            return $this->packagings;

        $collection = $this->packagings->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    /**
     * @throws ReflectionException
     */
    public function SetPackaging(object $model): void
    {
        $this->packagingRepository->Add(\API_InventoryRepositories_Model\Packaging::class, array($model->packagingname, $model->packagingdesc));
        $factory = new CollectableFactory($this->packagingRepository, $this->relationRepository);
        $factory->Create();
        $this->packagings = $factory->Collectable();
        $id = $this->packagingRepository->FirstOrDefault(fn($n) => $n->Name == $model->packagingname)->Id;
        //
        $languages = $this->languageService->GetLanguages();
        foreach ($languages as $language){
            $lang = $language->It()->Label;
            $this->relationRepository->Add(LanguageRelation::class, array($language->It()->Id, $id, $model->packaginglocale[$lang]));
        }
        //
        $factory = new CollectableFactory($this->packagingRepository, $this->relationRepository);
        $factory->Create();
        $this->packagings = $factory->Collectable();
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function PutPackaging(object $model): void
    {
        $this->packagingRepository->Update(\API_InventoryRepositories_Model\Packaging::class, array($model->packagingid, $model->packagingname, $model->packagingdesc));
        //
        $languages = $this->languageService->GetLanguages();
        $relations = $this->packagings->FirstOrDefault(fn($n) => $n->It()->Id == $model->packagingid)->LanguageRelations();
        foreach ($relations as $relation){
            $id = $relation->LangId;
            $lang = $languages->FirstOrDefault(fn($n) => $n->It()->Id == $id)->It()->Label;
            if (key_exists($lang, $model->packaginglocale))
                $this->relationRepository->Update(LanguageRelation::class, array($relation->Id, $model->packaginglocale[$lang]));
            else
                $this->relationRepository->Remove(LanguageRelation::class, array($relation->Id));
        }
        //
        $factory = new CollectableFactory($this->packagingRepository, $this->relationRepository);
        $factory->Create();
        $this->packagings = $factory->Collectable();
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function DeletePackaging(string $id): void
    {
        $relations = $this->packagings->FirstOrDefault(fn($n) => $n->It()->Id == $id)->LanguageRelations();
        foreach ($relations as $relation)
            $this->relationRepository->Remove(LanguageRelation::class, array($relation->Id));
        //
        $this->packagingRepository->Remove(\API_InventoryRepositories_Model\Packaging::class, array($id));
        $factory = new CollectableFactory($this->packagingRepository, $this->relationRepository);
        $factory->Create();
        $this->packagings = $factory->Collectable();
    }
}