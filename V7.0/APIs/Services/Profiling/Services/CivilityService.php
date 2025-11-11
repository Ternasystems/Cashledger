<?php

namespace API_Profiling_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\EntityException;
use API_Assets\Classes\ProfilingException;
use API_Profiling_Contract\ICivilityService;
use API_ProfilingEntities_Collection\Civilities;
use API_ProfilingEntities_Factory\CivilityFactory;
use API_ProfilingEntities_Model\Civility;
use API_RelationRepositories\CivilityRelationRepository;
use API_RelationRepositories_Model\CivilityRelation;
use Throwable;
use TS_Exception\Classes\DomainException;

class CivilityService implements ICivilityService
{
    protected CivilityFactory $civilityFactory;
    protected Civilities $civilities;
    protected CivilityRelationRepository $civilityRelationRepository;

    public function __construct(CivilityFactory $civilityFactory, CivilityRelationRepository $civilityRelationRepository)
    {
        $this->civilityFactory = $civilityFactory;
        $this->civilityRelationRepository = $civilityRelationRepository;
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getCivilities(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Civility|Civilities|null
    {
        if (!isset($this->civilities) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->civilityFactory->filter($filter, $pageSize, $offset);
            $this->civilityFactory->Create();
            $this->civilities = $this->civilityFactory->collectable();
        }

        if (count($this->civilities) === 0)
            return null;

        return $this->civilities->count() > 1 ? $this->civilities : $this->civilities->first();
    }

    /**
     * @throws DomainException
     * @throws ProfilingException
     * @throws Throwable
     */
    public function setCivility(array $data): Civility
    {
        $context = $this->civilityFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main civility Profiling
            $civility = new \API_ProfilingRepositories_Model\Civility($data['civilityData']);
            $this->civilityFactory->repository()->add($civility);

            // 2. Get the newly created civility
            $civility = $this->civilityFactory->repository()->first([['Name', '=', $data['civilityData']['Name']]]);
            if (!$civility)
                throw new ProfilingException('civility_creation_failed');

            if (isset($data['civilityRelations'])){
                foreach ($data['civilityRelations'] as $civilityRelation){
                    $civilityRelation['CivilityId'] = $civility->Id;
                    $relation = new CivilityRelation($civilityRelation);
                    $this->civilityRelationRepository->add($relation);
                }
            }

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getCivilities([['Id', '=', $civility->Id]], 1, 1, ReloadMode::YES);

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
    public function putCivility(string $id, array $data): ?Civility
    {
        $context = $this->civilityFactory->repository()->context;
        $context->beginTransaction();

        try{
            $civility = $this->getCivilities([['Id', '=', $id]])?->first();
            if (!$civility)
                throw new ProfilingException('entity_not_found', ["Id" => $id]);

            // 1. Update the main civility record
            foreach ($data as $field => $value)
                $civility->it()->{$field} = $value ?? $civility->it()->{$field};

            $this->civilityFactory->repository()->update($civility->it());

            // Delete the civility relations
            if ($civility->civilityRelations()){
                $civilityRelations = $civility->civilityRelations();
                foreach ($civilityRelations as $relation)
                    $this->civilityRelationRepository->remove($relation);
            }

            // Update the civility relations
            if ($data['civilityRelations']){
                foreach ($data['civilityRelations'] as $civilityRelation) {
                    $civilityRelation['CivilityId'] = $id;
                    $relation = new CivilityRelation($civilityRelation);
                    $this->civilityRelationRepository->add($relation);
                }
            }

            $context->commit();

            return $this->getCivilities([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     * @throws DomainException
     */
    public function deleteCivility(string $id): bool
    {
        $context = $this->civilityFactory->repository()->context;
        $context->beginTransaction();

        try{
            $civility = $this->getCivilities([['Id', '=', $id]])?->first();
            if (!$civility){
                $context->commit();
                return true;
            }

            // Deactivate the civility relations
            if ($civility->civilityRelations()){
                $civilityRelations = $civility->civilityRelations();
                foreach ($civilityRelations as $relation)
                    $this->civilityRelationRepository->remove($relation->Id);
            }

            $this->civilityFactory->repository()->remove($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}