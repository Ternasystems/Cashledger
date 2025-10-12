<?php

namespace API_DTORepositories;

use API_DTORepositories_Collection\Apps;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\App;

/**
 * @extends Repository<App, Apps>
 */
class AppRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context, App::class, Apps::class);
    }
}