<?php

namespace API_Administration_Service;

use API_Administration_Contract\IAppService;
use API_Assets\Classes\AdministrationException;
use API_Assets\Classes\EntityException;
use API_DTOEntities_Collection\Apps;
use API_DTOEntities_Factory\AppFactory;
use API_DTOEntities_Model\App;
use API_RelationRepositories\AppRelationRepository;
use API_RelationRepositories_Model\AppRelation;
use Throwable;
use TS_Exception\Classes\DomainException;

class AppService implements IAppService
{
    protected AppFactory $appFactory;
    protected Apps $apps;
    protected AppRelationRepository $appRelationRepository;

    public function __construct(AppFactory $_appFactory, AppRelationRepository $_appRelationRepository)
    {
        $this->appFactory = $_appFactory;
        $this->appRelationRepository = $_appRelationRepository;
    }

    /**
     * @throws DomainException
     */
    public function getApps(?array $filter = null, ?int $page = 1, ?int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): App|Apps|null
    {
        if (!isset($this->apps) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->appFactory->filter($filter, $pageSize, $offset);
            $this->appFactory->Create();
            $this->apps = $this->appFactory->collectable();
        }

        if (count($this->apps) === 0)
            return null;

        return $this->apps->count() > 1 ? $this->apps : $this->apps->first();
    }

    /**
     * @throws DomainException
     * @throws AdministrationException
     * @throws Throwable
     */
    public function setApp(array $data): App
    {
        $context = $this->appFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main app DTO
            $app = new \API_DTORepositories_Model\App($data['appData']);
            $this->appFactory->repository()->add($app);

            // 2. Get the newly created app
            $app = $this->appFactory->repository()->first([['Name', '=', $data['appData']['Name']]]);
            if (!$app)
                throw new AdministrationException('app_creation_failed');

            if (isset($data['appRelations'])){
                foreach ($data['appRelations'] as $appRelation){
                    $appRelation['AppId'] = $app->Id;
                    $relation = new AppRelation($appRelation);
                    $this->appRelationRepository->add($relation);
                }
            }

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getApps([['Id', '=', $app->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     * @throws DomainException
     * @throws EntityException
     * @throws AdministrationException
     */
    public function putApp(string $id, array $data): ?App
    {
        $context = $this->appFactory->repository()->context;
        $context->beginTransaction();

        try{
            $app = $this->getApps([['Id', '=', $id]])?->first();
            if (!$app)
                throw new AdministrationException('entity_not_found', ["Id" => $id]);

            // 1. Update the main app record
            foreach ($data as $field => $value)
                $app->it()->{$field} = $value ?? $app->it()->{$field};

            $this->appFactory->repository()->update($app->it());

            // Delete the app relations
            if ($app->appRelations()){
                $appRelations = $app->appRelations();
                foreach ($appRelations as $relation)
                    $this->appRelationRepository->remove($relation->Id);
            }

            // Update the app relations
            if (isset($data['appRelations'])){
                foreach ($data['appRelations'] as $appRelation){
                    $appRelation['AppId'] = $id;
                    $relation = new AppRelation($appRelation);
                    $this->appRelationRepository->add($relation);
                }
            }

            $context->commit();

            return $this->getApps([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     * @throws DomainException
     */
    public function deleteApp(string $id): bool
    {
        $context = $this->appFactory->repository()->context;
        $context->beginTransaction();

        try{
            // Retrieve the app entity
            $app = $this->getApps([['Id', '=', $id]])?->first();
            if (!$app){
                $context->commit();
                return true;
            }

            // Deactivate the app relations
            if ($app->appRelations()){
                $appRelations = $app->appRelations();
                foreach ($appRelations as $relation)
                    $this->appRelationRepository->remove($relation->Id);
            }

            $this->appFactory->repository()->remove($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}