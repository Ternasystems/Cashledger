<?php

namespace API_Profiling_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\EntityException;
use API_Assets\Classes\ProfilingException;
use API_Profiling_Contract\IStatusService;
use API_ProfilingEntities_Collection\Statuses;
use API_ProfilingEntities_Factory\StatusFactory;
use API_ProfilingEntities_Model\Status;
use API_RelationRepositories\StatusRelationRepository;
use API_RelationRepositories_Model\StatusRelation;
use Throwable;
use TS_Exception\Classes\DomainException;

class StatusService implements IStatusService
{
    protected StatusFactory $statusFactory;
    protected Statuses $statuses;
    protected StatusRelationRepository $statusRelationRepository;

    public function __construct(StatusFactory $statusFactory, StatusRelationRepository $statusRelationRepository)
    {
        $this->statusFactory = $statusFactory;
        $this->statusRelationRepository = $statusRelationRepository;
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getStatuses(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Status|Statuses|null
    {
        if (!isset($this->statuses) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->statusFactory->filter($filter, $pageSize, $offset);
            $this->statusFactory->Create();
            $this->statuses = $this->statusFactory->collectable();
        }

        if (count($this->statuses) === 0)
            return null;

        return $this->statuses->count() > 1 ? $this->statuses : $this->statuses->first();
    }

    /**
     * @throws DomainException
     * @throws ProfilingException
     * @throws Throwable
     */
    public function setStatus(array $data): Status
    {
        $context = $this->statusFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main status Profiling
            $status = new \API_ProfilingRepositories_Model\Status($data['statusData']);
            $this->statusFactory->repository()->add($status);

            // 2. Get the newly created status
            $status = $this->statusFactory->repository()->first([['Name', '=', $data['statusData']['Name']]]);
            if (!$status)
                throw new ProfilingException('status_creation_failed');

            if (isset($data['statusRelations'])){
                foreach ($data['statusRelations'] as $statusRelation){
                    $statusRelation['StatusId'] = $status->Id;
                    $relation = new StatusRelation($statusRelation);
                    $this->statusRelationRepository->add($relation);
                }
            }

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getStatuses([['Id', '=', $status->Id]], 1, 1, ReloadMode::YES);

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
    public function putStatus(string $id, array $data): ?Status
    {
        $context = $this->statusFactory->repository()->context;
        $context->beginTransaction();

        try{
            $status = $this->getStatuses([['Id', '=', $id]])?->first();
            if (!$status)
                throw new ProfilingException('entity_not_found', ["Id" => $id]);

            // 1. Update the main status record
            foreach ($data as $field => $value)
                $status->it()->{$field} = $value ?? $status->it()->{$field};

            $this->statusFactory->repository()->update($status->it());

            // Delete the status relations
            if ($status->statusRelations()){
                $statusRelations = $status->statusRelations();
                foreach ($statusRelations as $relation)
                    $this->statusRelationRepository->remove($relation);
            }

            // Update the status relations
            if ($data['statusRelations']){
                foreach ($data['statusRelations'] as $statusRelation){
                    $statusRelation['StatusId'] = $id;
                    $relation = new StatusRelation($statusRelation);
                    $this->statusRelationRepository->add($relation);
                }
            }

            $context->commit();

            return $this->getStatuses([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     * @throws DomainException
     */
    public function deleteStatus(string $id): bool
    {
        $context = $this->statusFactory->repository()->context;
        $context->beginTransaction();

        try{
            $status = $this->getStatuses([['Id', '=', $id]])?->first();
            if (!$status){
                $context->commit();
                return true;
            }

            // Deactivate the status relations
            if ($status->statusRelations()){
                $statusRelations = $status->statusRelations();
                foreach ($statusRelations as $relation)
                    $this->statusRelationRepository->remove($relation->Id);
            }

            $this->statusFactory->repository()->remove($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}