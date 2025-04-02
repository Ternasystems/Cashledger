<?php

namespace API_ProfilingRepositories_Model;

use API_DTORepositories_Model\DTOBase;
use DateTime;

class Profile extends DTOBase
{
    public ?string $FirstName;
    public string $LastName;
    public ?string $MaidenName;
    public DateTime $BirthDate;
    public DateTime $StartDate;
    public ?DateTime $EndDate;
    public ?string $Photo;
}