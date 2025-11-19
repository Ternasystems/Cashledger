<?php

namespace API_Profiling_Facade;

use API_Administration_Contract\IFacade;
use API_Administration_Service\ReloadMode;
use API_Profiling_Contract\ICivilityService;
use API_Profiling_Contract\IGenderService;
use API_Profiling_Contract\IOccupationService;
use API_Profiling_Contract\IProfileService;
use API_Profiling_Contract\IStatusService;
use API_Profiling_Contract\ITitleService;
use API_ProfilingEntities_Collection\Civilities;
use API_ProfilingEntities_Collection\Genders;
use API_ProfilingEntities_Collection\Occupations;
use API_ProfilingEntities_Collection\Profiles;
use API_ProfilingEntities_Collection\Statuses;
use API_ProfilingEntities_Collection\Titles;
use API_ProfilingEntities_Model\Civility;
use API_ProfilingEntities_Model\Gender;
use API_ProfilingEntities_Model\Occupation;
use API_ProfilingEntities_Model\Profile;
use API_ProfilingEntities_Model\Status;
use API_ProfilingEntities_Model\Title;
use Exception;

/**
 * This is the Facade class for profile management.
 * It implements the generic IFacade interface directly.
 * It injects the individual services so controllers don't have to.
 */
class ProfileFacade implements IFacade
{
    /**
     * The constructor injects all the individual services
     * this facade will orchestrate.
     */
    public function __construct(protected IProfileService $profileService, protected ICivilityService $civilityService, protected IGenderService $genderService,
                                protected IOccupationService $occupationService, protected IStatusService $statusService, protected ITitleService $titleService){}

    /**
     * Gets a resource from the appropriate service.
     * @throws Exception
     */
    public function get(string $resourceType, ?array $filter, int $page, int $pageSize, ReloadMode $reloadMode): null|Profiles|Profile|Civilities|Civility|Genders|Gender|
    Occupations|Occupation|Statuses|Status|Titles|Title
    {
        return match ($resourceType) {
            'Profile' => $this->profileService->getProfiles($filter, $page, $pageSize, $reloadMode),
            'Civility' => $this->civilityService->getCivilities($filter, $page, $pageSize, $reloadMode),
            'Gender' => $this->genderService->getGenders($filter, $page, $pageSize, $reloadMode),
            'Occupation' => $this->occupationService->getOccupations($filter, $page, $pageSize, $reloadMode),
            'Status' => $this->statusService->getStatuses($filter, $page, $pageSize, $reloadMode),
            'Title' => $this->titleService->getTitles($filter, $page, $pageSize, $reloadMode),
            default => throw new Exception("Invalid resource type for AppFacade 'get': $resourceType"),
        };
    }

    /**
     * Creates a new resource using the appropriate service.
     * @throws Exception
     */
    public function set(string $resourceType, array $data): Profile|Civility|Gender|Occupation|Status|Title
    {
        return match ($resourceType) {
            'Profile' => $this->profileService->setProfile($data),
            'Civility' => $this->civilityService->setCivility($data),
            'Gender' => $this->genderService->setGender($data),
            'Occupation' => $this->occupationService->setOccupation($data),
            'Status' => $this->statusService->setStatus($data),
            'Title' => $this->titleService->setTitles($data),
            default => throw new Exception("Invalid resource type for AppFacade 'set': $resourceType"),
        };
    }

    /**
     * Updates an existing resource using the appropriate service.
     * @throws Exception
     */
    public function put(string $resourceType, string $id, array $data): null|Profile|Civility|Gender|Occupation|Status|Title
    {
        return match ($resourceType) {
            'Profile' => $this->profileService->putProfile($id, $data),
            'Civility' => $this->civilityService->putCivility($id, $data),
            'Gender' => $this->genderService->putGender($id, $data),
            'Occupation' => $this->occupationService->putOccupation($id, $data),
            'Status' => $this->statusService->putStatus($id, $data),
            'Title' => $this->titleService->putTitle($id, $data),
            default => throw new Exception("Invalid resource type for AppFacade 'put': $resourceType"),
        };
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function delete(string $resourceType, string $id): bool
    {
        return match ($resourceType) {
            'Profile' => $this->profileService->deleteProfile($id),
            'Civility' => $this->civilityService->deleteCivility($id),
            'Gender' => $this->genderService->deleteGender($id),
            'Occupation' => $this->occupationService->deleteOccupation($id),
            'Status' => $this->statusService->deleteStatus($id),
            'Title' => $this->titleService->deleteTitle($id),
            default => throw new Exception("Invalid resource type for AppFacade 'delete': $resourceType"),
        };
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function disable(string $resourceType, string $id): bool
    {
        return $resourceType == 'Profile' ? $this->profileService->disableProfile($id) :
            throw new Exception("Invalid or unsupported resource type for ProfileFacade 'disable': $resourceType");
    }
}