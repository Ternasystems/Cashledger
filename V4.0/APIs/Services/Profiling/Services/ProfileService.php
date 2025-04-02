<?php

namespace API_Profiling_Service;

use API_Profiling_Contract\IProfileService;
use API_ProfilingEntities_Collection\Profiles;
use API_ProfilingEntities_Factory\ProfileFactory;
use API_ProfilingEntities_Model\Profile;
use Exception;

class ProfileService implements IProfileService
{
    protected Profiles $profiles;

    /**
     * @throws Exception
     */
    public function __construct(ProfileFactory $profileFactory)
    {
        $profileFactory->Create();
        $this->profiles = $profileFactory->Collectable();
    }

    public function GetProfiles(callable $predicate = null): Profile|Profiles|null
    {
        if (is_null($predicate))
            return $this->profiles;

        $collection = $this->profiles->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }
}