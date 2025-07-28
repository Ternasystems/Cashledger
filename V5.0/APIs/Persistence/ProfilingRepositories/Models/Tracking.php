<?php

namespace API_ProfilingRepositories_Model;

use API_DTORepositories_Model\DTOBase;
use DateTime;

class Tracking extends DTOBase
{
    public string $CredentialId;
    public string $Action;
    public string $IP;
    public DateTime $ActionDate;
}