<?php

namespace API_DTORepositories;

use API_DTORepositories_Collection\Continents;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\Continent;

/**
 * @extends Repository<Continent, Continents>
 */
class ContinentRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context, Continent::class, Continents::class);
    }
}