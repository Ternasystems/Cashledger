<?php

namespace API_TellerRepositories;

use API_DTORepositories\Repository;
use API_TellerRepositories_Context\TellerContext;
use API_TellerRepositories_Model\CashFigure;

/**
 * @extends Repository<CashFigure>
 */
class CashFigureRepository extends Repository
{
    public function __construct(TellerContext $context)
    {
        parent::__construct($context, CashFigure::class);
    }
}