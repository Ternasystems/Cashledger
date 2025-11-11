<?php

namespace API_Profiling_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\EntityException;
use API_Assets\Classes\ProfilingException;
use API_Profiling_Contract\ITitleService;
use API_ProfilingEntities_Collection\Titles;
use API_ProfilingEntities_Factory\TitleFactory;
use API_ProfilingEntities_Model\Title;
use API_RelationRepositories\TitleRelationRepository;
use API_RelationRepositories_Model\TitleRelation;
use Throwable;
use TS_Exception\Classes\DomainException;

class TitleService implements ITitleService
{
    protected TitleFactory $titleFactory;
    protected Titles $titles;
    protected TitleRelationRepository $titleRelationRepository;

    public function __construct(TitleFactory $titleFactory, TitleRelationRepository $titleRelationRepository)
    {
        $this->titleFactory = $titleFactory;
        $this->titleRelationRepository = $titleRelationRepository;
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getTitles(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Title|Titles|null
    {
        if (!isset($this->titles) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->titleFactory->filter($filter, $pageSize, $offset);
            $this->titleFactory->Create();
            $this->titles = $this->titleFactory->collectable();
        }

        if (count($this->titles) === 0)
            return null;

        return $this->titles->count() > 1 ? $this->titles : $this->titles->first();
    }

    /**
     * @throws DomainException
     * @throws ProfilingException
     * @throws Throwable
     */
    public function setTitle(array $data): Title
    {
        $context = $this->titleFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main title Profiling
            $title = new \API_ProfilingRepositories_Model\Title($data['titleData']);
            $this->titleFactory->repository()->add($title);

            // 2. Get the newly created title
            $title = $this->titleFactory->repository()->first([['Name', '=', $data['titleData']['Name']]]);
            if (!$title)
                throw new ProfilingException('title_creation_failed');

            if (isset($data['titleRelations'])){
                foreach ($data['titleRelations'] as $titleRelation){
                    $titleRelation['TitleId'] = $title->Id;
                    $relation = new TitleRelation($titleRelation);
                    $this->titleRelationRepository->add($relation);
                }
            }

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getTitles([['Id', '=', $title->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     * @throws DomainException
     * @throws EntityException
     * @throws ProfilingException
     */
    public function putTitle(string $id, array $data): ?Title
    {
        $context = $this->titleFactory->repository()->context;
        $context->beginTransaction();

        try{
            $title = $this->getTitles([['Id', '=', $id]])?->first();
            if (!$title)
                throw new ProfilingException('entity_not_found', ["Id" => $id]);

            // 1. Update the main title record
            foreach ($data as $field => $value)
                $title->it()->{$field} = $value ?? $title->it()->{$field};

            $this->titleFactory->repository()->update($title->it());

            // Delete the title relations
            if ($title->titleRelations()){
                $titleRelations = $title->titleRelations();
                foreach ($titleRelations as $relation)
                    $this->titleRelationRepository->remove($relation);
            }

            // Update the title relations
            if ($data['titleRelations']){
                foreach ($data['titleRelations'] as $titleRelation){
                    $titleRelation['TitleId'] = $id;
                    $relation = new TitleRelation($titleRelation);
                    $this->titleRelationRepository->add($relation);
                }
            }

            $context->commit();

            return $this->getTitles([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     * @throws DomainException
     */
    public function deleteTitle(string $id): bool
    {
        $context = $this->titleFactory->repository()->context;
        $context->beginTransaction();

        try{
            $title = $this->getTitles([['Id', '=', $id]])?->first();
            if (!$title){
                $context->commit();
                return true;
            }

            // Deactivate the title relations
            if ($title->titleRelations()){
                $titleRelations = $title->titleRelations();
                foreach ($titleRelations as $relation)
                    $this->titleRelationRepository->remove($relation->Id);
            }

            $this->titleFactory->repository()->remove($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}