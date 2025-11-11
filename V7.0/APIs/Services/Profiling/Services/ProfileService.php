<?php

namespace API_Profiling_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\EntityException;
use API_Assets\Classes\ProfilingException;
use API_Profiling_Contract\IProfileService;
use API_ProfilingEntities_Collection\Profiles;
use API_ProfilingEntities_Factory\ProfileFactory;
use API_ProfilingEntities_Model\Profile;
use Throwable;
use TS_Exception\Classes\DomainException;

class ProfileService implements IProfileService
{
    protected ProfileFactory $profileFactory;
    protected Profiles $profiles;

    public function __construct(ProfileFactory $profileFactory)
    {
        $this->profileFactory = $profileFactory;
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getProfiles(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Profile|Profiles|null
    {
        if (!isset($this->profiles) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->profileFactory->filter($filter, $pageSize, $offset);
            $this->profileFactory->Create();
            $this->profiles = $this->profileFactory->collectable();
        }

        if (count($this->profiles) === 0)
            return null;

        return $this->profiles->count() > 1 ? $this->profiles : $this->profiles->first();
    }

    /**
     * @throws DomainException
     * @throws ProfilingException
     * @throws Throwable
     */
    public function setProfile(array $data): Profile
    {
        $context = $this->profileFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main profile Profiling
            $profile = new \API_ProfilingRepositories_Model\Profile($data['profileData']);
            $this->profileFactory->repository()->add($profile);

            // 2. Get the newly created profile
            $profile = $this->profileFactory->repository()->first([['Name', '=', $data['profileData']['Name']]]);
            if (!$profile)
                throw new ProfilingException('profile_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getProfiles([['Id', '=', $profile->Id]], 1, 1, ReloadMode::YES);

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
    public function putProfile(string $id, array $data): ?Profile
    {
        $context = $this->profileFactory->repository()->context;
        $context->beginTransaction();

        try{
            $profile = $this->getProfiles([['Id', '=', $id]])?->first();
            if (!$profile)
                throw new ProfilingException('entity_not_found', ["Id" => $id]);

            // 1. Update the main profile record
            foreach ($data as $field => $value)
                $profile->it()->{$field} = $value ?? $profile->it()->{$field};

            $this->profileFactory->repository()->update($profile->it());
            $context->commit();

            return $this->getProfiles([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     * @throws DomainException
     */
    public function deleteProfile(string $id): bool
    {
        $context = $this->profileFactory->repository()->context;
        $context->beginTransaction();

        try{
            $profile = $this->getProfiles([['Id', '=', $id]])?->first();
            if (!$profile){
                $context->commit();
                return true;
            }

            $this->profileFactory->repository()->remove($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     * @throws DomainException
     */
    public function disableProfile(string $id): bool
    {
        $context = $this->profileFactory->repository()->context;
        $context->beginTransaction();

        try{
            $profile = $this->getProfiles([['Id', '=', $id]])?->first();
            if (!$profile){
                $context->commit();
                return true;
            }

            $this->profileFactory->repository()->deactivate($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}