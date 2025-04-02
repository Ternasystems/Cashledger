<?php

namespace API_Administration_Service;

use API_Administration_Contract\ILanguageService;
use API_DTOEntities_Collection\Languages;
use API_DTOEntities_Factory\CollectableFactory;
use API_DTOEntities_Model\Language;
use API_DTORepositories\LanguageRepository;
use ReflectionException;
use API_RelationRepositories\LanguageRelationRepository;

class LanguageService implements ILanguageService
{
    protected Languages $languages;

    /**
     * @throws ReflectionException
     */
    public function __construct(LanguageRepository $repository, LanguageRelationRepository $relationRepository)
    {
        $factory = new CollectableFactory($repository, $relationRepository);
        $factory->Create();
        $this->languages = $factory->Collectable();
    }

    public function GetLanguages(callable $predicate = null): Language|Languages|null
    {
        if (is_null($predicate))
            return $this->languages;

        $collection = $this->languages->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }
}