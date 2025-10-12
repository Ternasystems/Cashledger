<?php

namespace API_DTORepositories;

use API_DTORepositories_Collection\AppCategories;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\AppCategory;

/**
 * @extends Repository<AppCategory, AppCategories>
 */
class AppCategoryRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context, AppCategory::class, AppCategories::class);
    }
}