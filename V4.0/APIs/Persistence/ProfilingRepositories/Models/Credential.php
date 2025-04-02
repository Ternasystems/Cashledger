<?php

namespace API_ProfilingRepositories_Model;

use API_ProfilingRepositories_Contract\IProfile;

class Credential extends ProfileBase implements IProfile
{
    public string $UserName;
    public string $UserPassword;
    public ?string $SessionId;
    public bool $ConnectionStatus;
    public string $LoginStatus;
    public bool $CurrentThread;
    public int $Threads;
    public ?string $Ip;
    public string $Action;
}