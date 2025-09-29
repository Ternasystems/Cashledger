<?php

namespace API_Profiling_Contract;

use API_Administration_Service\ReloadMode;
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
    /**
     * Gets a paginated and filterable list of Civility entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Civility|Civilities|null An associative array containing 'data' and 'total'.
     */
    public function GetCivilities(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Civility|Civilities|null;

    /**
     * Gets a paginated and filterable list of Gender entities.
     */
    public function getGenders(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Gender|Genders|null;

    /**
     * Gets a paginated and filterable list of Occupation entities.
     */
    public function getOccupations(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Occupation|Occupations|null;

    /**
     * Gets a paginated and filterable list of Title entities.
     */
    public function getTitles(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Title|Titles|null;

    /**
     * Gets a paginated and filterable list of Status entities.
     */
    public function getStatuses(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Status|Statuses|null;
}