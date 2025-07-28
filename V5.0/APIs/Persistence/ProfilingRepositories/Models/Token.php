<?php

namespace API_ProfilingRepositories_Model;

use API_DTORepositories_Model\DTOBase;

class Token extends DTOBase
{
    public string $RoleId;
    public string $Controller;
    public string $Action;
    public string $Permissions;
}