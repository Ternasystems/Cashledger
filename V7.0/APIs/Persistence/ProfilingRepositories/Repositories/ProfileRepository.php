<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Profiles;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Profile;

/**
 * @extends Repository<Profile, Profiles>
 */
class ProfileRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context, Profile::class, Profiles::class);
    }
}