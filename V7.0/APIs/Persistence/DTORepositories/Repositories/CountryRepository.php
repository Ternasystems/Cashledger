<?php

namespace API_DTORepositories;

use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\Country;

/**
 * @extends Repository<Country>
 */
class CountryRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context, Country::class);
    }
}