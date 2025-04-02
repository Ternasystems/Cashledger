<?php

namespace API_DTORepositories_Model;

class DTOBase
{
    public string $Id = '';
    public int $Code = 0;
    public string $Name = '';
    public ?\DateTime $IsActive = null;
    public ?string $Description = null;
}