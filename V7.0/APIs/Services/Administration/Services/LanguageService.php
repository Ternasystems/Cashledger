<?php

namespace API_Administration_Service;

use API_Administration_Contract\ILanguageService;
use API_Assets\Classes\AdministrationException;
use API_DTOEntities_Collection\Languages;
use API_DTOEntities_Factory\CollectableFactory;
use API_DTOEntities_Model\Language;
use API_DTORepositories\LanguageRepository;
use API_RelationRepositories\LanguageRelationRepository;
use ReflectionException;
use Throwable;
use TS_Exception\Classes\DomainException;

class LanguageService implements ILanguageService
{
    protected CollectableFactory $factory;
    protected Languages $languages;

    /**
     * @throws ReflectionException
     */
    public function __construct(LanguageRepository $languageRepository, LanguageRelationRepository $relationRepository)
    {
        $this->factory = new CollectableFactory($languageRepository, $relationRepository);
    }

    /**
     * @throws DomainException
     */
    public function getLanguages(?array $filter = null, ?int $page = 1, ?int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Language|Languages|null
    {
        if (!isset($this->languages) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->factory->filter($filter, $pageSize, $offset);
            $this->factory->Create();
            $this->languages = $this->factory->collectable();
        }

        if (count($this->languages) === 0)
            return null;

        return $this->languages->count() > 1 ? $this->languages : $this->languages->first();
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws Throwable
     */
    public function setLanguage(array $data): Language
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main language DTO
            $language = new \API_DTORepositories_Model\Language($data['languageData']);
            $this->factory->repository()->add($language);

            // 2. Get the newly created language
            $language = $this->factory->repository()->first([['Label', '=', $data['languageData']['Label']]]);
            if (!$language)
                throw new AdministrationException('language_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getLanguages([['Id', '=', $language->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws Throwable
     */
    public function putLanguage(string $id, array $data): ?Language
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            $language = $this->getLanguages([['Id', '=', $id]])?->first();
            if (!$language)
                throw new AdministrationException('entity_not_found', ["Id" => $id]);

            // 1. Update the main language record
            foreach ($data as $field => $value)
                $language->it()->{$field} = $value ?? $language->it()->{$field};

            $this->factory->repository()->update($language->it());
            $context->commit();

            return $this->getLanguages([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws Throwable
     */
    public function deleteLanguage(string $id): bool
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            $appCategory = $this->getlanguages([['Id', '=', $id]])?->first();
            if (!$appCategory){
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