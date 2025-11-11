<?php

namespace API_Profiling_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\EntityException;
use API_Assets\Classes\ProfilingException;
use API_Profiling_Contract\IGenderService;
use API_ProfilingEntities_Collection\Genders;
use API_ProfilingEntities_Factory\GenderFactory;
use API_ProfilingEntities_Model\Gender;
use API_RelationRepositories\GenderRelationRepository;
use API_RelationRepositories_Model\GenderRelation;
use Throwable;
use TS_Exception\Classes\DomainException;

class GenderService implements IGenderService
{
    protected GenderFactory $genderFactory;
    protected Genders $genders;
    protected GenderRelationRepository $genderRelationRepository;

    public function __construct(GenderFactory $genderFactory, GenderRelationRepository $genderRelationRepository)
    {
        $this->genderFactory = $genderFactory;
        $this->genderRelationRepository = $genderRelationRepository;
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getGenders(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Gender|Genders|null
    {
        if (!isset($this->genders) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->genderFactory->filter($filter, $pageSize, $offset);
            $this->genderFactory->Create();
            $this->genders = $this->genderFactory->collectable();
        }

        if (count($this->genders) === 0)
            return null;

        return $this->genders->count() > 1 ? $this->genders : $this->genders->first();
    }

    /**
     * @throws DomainException
     * @throws ProfilingException
     * @throws Throwable
     */
    public function setGender(array $data): Gender
    {
        $context = $this->genderFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main gender Profiling
            $gender = new \API_ProfilingRepositories_Model\Gender($data['genderData']);
            $this->genderFactory->repository()->add($gender);

            // 2. Get the newly created gender
            $gender = $this->genderFactory->repository()->first([['Name', '=', $data['genderData']['Name']]]);
            if (!$gender)
                throw new ProfilingException('gender_creation_failed');

            if (isset($data['genderRelations'])){
                foreach ($data['genderRelations'] as $genderRelation){
                    $genderRelation['GenderId'] = $gender->Id;
                    $relation = new GenderRelation($genderRelation);
                    $this->genderRelationRepository->add($relation);
                }
            }

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getGenders([['Id', '=', $gender->Id]], 1, 1, ReloadMode::YES);

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
    public function putGender(string $id, array $data): ?Gender
    {
        $context = $this->genderFactory->repository()->context;
        $context->beginTransaction();

        try{
            $gender = $this->getGenders([['Id', '=', $id]])?->first();
            if (!$gender)
                throw new ProfilingException('entity_not_found', ["Id" => $id]);

            // 1. Update the main gender record
            foreach ($data as $field => $value)
                $gender->it()->{$field} = $value ?? $gender->it()->{$field};

            $this->genderFactory->repository()->update($gender->it());

            // Delete the gender relations
            if ($gender->genderRelations()){
                $genderRelations = $gender->genderRelations();
                foreach ($genderRelations as $relation)
                    $this->genderRelationRepository->remove($relation);
            }

            // Update the gender relations
            if ($data['genderRelations']){
                foreach ($data['genderRelations'] as $genderRelation){
                    $genderRelation['GenderId'] = $id;
                    $relation = new GenderRelation($genderRelation);
                    $this->genderRelationRepository->add($relation);
                }
            }

            $context->commit();

            return $this->getGenders([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     * @throws DomainException
     */
    public function deleteGender(string $id): bool
    {
        $context = $this->genderFactory->repository()->context;
        $context->beginTransaction();

        try{
            $gender = $this->getGenders([['Id', '=', $id]])?->first();
            if (!$gender){
                $context->commit();
                return true;
            }

            // Deactivate the gender relations
            if ($gender->genderRelations()){
                $genderRelations = $gender->genderRelations();
                foreach ($genderRelations as $relation)
                    $this->genderRelationRepository->remove($relation->Id);
            }

            $this->genderFactory->repository()->remove($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}