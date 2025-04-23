<?php

namespace API_Profiling_Contract;

use API_ProfilingEntities_Collection\Civilities;
use API_ProfilingEntities_Collection\Genders;
use API_ProfilingEntities_Collection\Occupations;
use API_ProfilingEntities_Collection\Statuses;
use API_ProfilingEntities_Collection\Titles;
use API_ProfilingEntities_Model\Civility;
use API_ProfilingEntities_Model\Gender;
use API_ProfilingEntities_Model\Occupation;
use API_ProfilingEntities_Model\Status;
use API_ProfilingEntities_Model\Title;

interface ICivilityService
{
    public function GetCivilities(callable $predicate = null): Civility|Civilities|null;
    public function GetGenders(callable $predicate = null): Gender|Genders|null;
    public function GetOccupations(callable $predicate = null): Occupation|Occupations|null;
    public function GetTitles(callable $predicate = null): Title|Titles|null;
    public function GetStatuses(callable $predicate = null): Status|Statuses|null;
    public function GetRelationRepositories(): array;
}