<?php

namespace API_TaxesRepositories;

use API_DTORepositories\Repository;
use API_TaxesRepositories_Collection\Taxes;
use API_TaxesRepositories_Context\TaxesContext;
use API_TaxesRepositories_Model\Tax;

/**
 * @extends Repository<Tax, Taxes>
 */
class TaxRepository extends Repository
{
    public function __construct(TaxesContext $context)
    {
        parent::__construct($context, Tax::class, Taxes::class);
    }
}