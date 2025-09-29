<?php

namespace API_Administration_Service;

use API_Administration_Contract\ILanguageService;
use API_DTOEntities_Collection\Languages;
use API_DTOEntities_Factory\CollectableFactory;
use API_DTOEntities_Model\Language;
use API_DTORepositories\LanguageRepository;
use API_RelationRepositories\LanguageRelationRepository;
use ReflectionException;
use TS_Exception\Classes\DomainException;

class LanguageService implements ILanguageService
{
    protected LanguageRepository $languageRepository;
    protected CollectableFactory $factory;
    protected Languages $languages;
    protected LanguageRelationRepository $relationRepository;

    public function __construct(LanguageRepository $_languageRepository, LanguageRelationRepository $_relationRepository)
    {
        $this->languageRepository = $_languageRepository;
        $this->relationRepository = $_relationRepository;
    }

    /**
     * @throws ReflectionException
     * @throws DomainException
     */
    public function GetLanguages(?array $filter = null, ?int $page = 1, ?int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Language|Languages|null
    {
        if (!isset($this->languages) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->factory = new CollectableFactory($this->languageRepository, $this->relationRepository);
            $this->factory->filter($filter, $pageSize, $offset);
            $this->factory->Create();
            $this->languages = $this->factory->collectable();
        }

        if (count($this->languages) === 0)
            return null;

        return $this->languages->count() > 1 ? $this->languages : $this->languages->first();
    }
}