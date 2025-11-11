<?php

namespace API_Profiling_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\EntityException;
use API_Assets\Classes\ProfilingException;
use API_Profiling_Contract\IOccupationService;
use API_ProfilingEntities_Collection\Occupations;
use API_ProfilingEntities_Factory\OccupationFactory;
use API_ProfilingEntities_Model\Occupation;
use API_RelationRepositories\OccupationRelationRepository;
use API_RelationRepositories_Model\OccupationRelation;
use Throwable;
use TS_Exception\Classes\DomainException;

class OccupationService implements IOccupationService
{
    protected OccupationFactory $occupationFactory;
    protected Occupations $occupations;
    protected OccupationRelationRepository $occupationRelationRepository;

    public function __construct(OccupationFactory $occupationFactory, OccupationRelationRepository $occupationRelationRepository)
    {
        $this->occupationFactory = $occupationFactory;
        $this->occupationRelationRepository = $occupationRelationRepository;
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getOccupations(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Occupation|Occupations|null
    {
        if (!isset($this->occupations) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->occupationFactory->filter($filter, $pageSize, $offset);
            $this->occupationFactory->Create();
            $this->occupations = $this->occupationFactory->collectable();
        }

        if (count($this->occupations) === 0)
            return null;

        return $this->occupations->count() > 1 ? $this->occupations : $this->occupations->first();
    }

    /**
     * @throws DomainException
     * @throws ProfilingException
     * @throws Throwable
     */ function setOccupation(array $data): Occupation
    {
        $context = $this->occupationFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main occupation Profiling
            $occupation = new \API_ProfilingRepositories_Model\Occupation($data['occupationData']);
            $this->occupationFactory->repository()->add($occupation);

            // 2. Get the newly created occupation
            $occupation = $this->occupationFactory->repository()->first([['Name', '=', $data['occupationData']['Name']]]);
            if (!$occupation)
                throw new ProfilingException('occupation_creation_failed');

            if (isset($data['occupationRelations'])){
                foreach ($data['occupationRelations'] as $occupationRelation){
                    $occupationRelation['OccupationId'] = $occupation->Id;
                    $relation = new OccupationRelation($occupationRelation);
                    $this->occupationRelationRepository->add($relation);
                }
            }

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getOccupations([['Id', '=', $occupation->Id]], 1, 1, ReloadMode::YES);

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
    public function putOccupation(string $id, array $data): ?Occupation
    {
        $context = $this->occupationFactory->repository()->context;
        $context->beginTransaction();

        try{
            $occupation = $this->getOccupations([['Id', '=', $id]])?->first();
            if (!$occupation)
                throw new ProfilingException('entity_not_found', ["Id" => $id]);

            // 1. Update the main occupation record
            foreach ($data as $field => $value)
                $occupation->it()->{$field} = $value ?? $occupation->it()->{$field};

            $this->occupationFactory->repository()->update($occupation->it());

            // Delete the occupation relations
            if ($occupation->occupationRelations()){
                $occupationRelations = $occupation->occupationRelations();
                foreach ($occupationRelations as $relation)
                    $this->occupationRelationRepository->remove($relation);
            }

            // Update the occupation relations
            if ($data['occupationRelations']){
                foreach ($data['occupationRelations'] as $occupationRelation){
                    $occupationRelation['OccupationId'] = $id;
                    $relation = new OccupationRelation($occupationRelation);
                    $this->occupationRelationRepository->add($relation);
                }
            }

            $context->commit();

            return $this->getOccupations([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     * @throws DomainException
     */
    public function deleteOccupation(string $id): bool
    {
        $context = $this->occupationFactory->repository()->context;
        $context->beginTransaction();

        try{
            $occupation = $this->getOccupations([['Id', '=', $id]])?->first();
            if (!$occupation){
                $context->commit();
                return true;
            }

            // Deactivate the occupation relations
            if ($occupation->occupationRelations()){
                $occupationRelations = $occupation->occupationRelations();
                foreach ($occupationRelations as $relation)
                    $this->occupationRelationRepository->remove($relation->Id);
            }

            $this->occupationFactory->repository()->remove($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}