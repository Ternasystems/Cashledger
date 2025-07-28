<?php

namespace API_ProfilingRepositories_Model;

use API_DTORepositories_Model\DTOBase;

class Contact extends DTOBase
{
    public string $ContactTypeId;
    public string $ProfileId;
    public int $ContactNo;
}