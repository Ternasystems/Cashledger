<?php

namespace API_Profiling_Contract;

use API_ProfilingEntities_Collection\Profiles;
use API_ProfilingEntities_Model\Profile;

interface IProfileService
{
    public function GetProfiles(callable $predicate = null): Profile|Profiles|null;
    public function SetProfile(object $model): void;
    public function PutProfile(object $model): void;
    public function DeleteProfile(string $id): void;
}