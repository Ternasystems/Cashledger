<?php

namespace API_TaxesRepositories;

use API_DTORepositories\Repository;
use API_TaxesRepositories_Context\TaxesContext;
use API_TaxesRepositories_Model\Tax;

/**
 * @extends Repository<Tax>
 */
class TaxRepository extends Repository
{
    public function __construct(TaxesContext $context)
    {
        parent::__construct($context, Tax::class);
    }
}