<?php

namespace API_DTORepositories;

use API_DTORepositories_Collection\Languages;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\Language;

/**
 * @extends Repository<Language, Languages>
 */
class LanguageRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context, Language::class, Languages::class);
    }
}