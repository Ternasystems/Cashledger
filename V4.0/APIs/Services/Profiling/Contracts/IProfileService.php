<?php

namespace API_Profiling_Contract;

use API_ProfilingEntities_Collection\Profiles;
use API_ProfilingEntities_Model\Profile;

interface IProfileService
{
    public function GetProfiles(callable $predicate = null): Profile|Profiles|null;
}