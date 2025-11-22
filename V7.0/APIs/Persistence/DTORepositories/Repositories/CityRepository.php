<?php

namespace API_DTORepositories;

use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\City;

/**
 * @extends Repository<City>
 */
class CityRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context, City::class);
    }
}