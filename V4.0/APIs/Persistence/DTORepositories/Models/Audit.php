<?php

namespace API_DTORepositories_Model;

use DateTime;

class Audit extends DTOBase
{
    public string $Action;
    public string $TableName;
    public string $RecordId;
    public DateTime $ActionDate;
}