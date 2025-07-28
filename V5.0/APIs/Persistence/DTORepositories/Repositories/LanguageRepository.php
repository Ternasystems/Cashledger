<?php

namespace API_DTORepositories;

use API_DTORepositories_Collection\Languages;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\Language;
use Closure;

class LanguageRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Language
    {
        $entity = parent::first($predicate);
        return $entity instanceof Language ? $entity : null;
    }

    public function GetAll(): ?Languages
    {
        $collection = parent::GetAll();
        return $collection instanceof Languages ? $collection : null;
    }

    public function GetById(string $id): ?Language
    {
        $entity = parent::GetById($id);
        return $entity instanceof Language ? $entity : null;
    }

    public function GetBy(Closure $predicate): ?Languages
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Languages ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Language
    {
        $entity = parent::last($predicate);
        return $entity instanceof Language ? $entity : null;
    }
}