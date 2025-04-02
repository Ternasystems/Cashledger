<?php

namespace API_ProfilingRepositories_Model;

use API_DTORepositories_Model\DTOBase;
use DateTime;

class ProfileBase extends DTOBase
{
    public DateTime $StartDate;
    public ?DateTime $EndDate;
    public string $ProfileId;
}