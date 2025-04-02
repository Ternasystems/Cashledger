<?php

namespace API_DTORepositories;

use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Collection\Languages;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\Language;
use Exception;
use TS_Utility\Enums\OrderEnum;

class LanguageRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Language
    {
        $entity = parent::FirstOrDefault($predicate);
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

    public function GetBy(callable $predicate): ?Languages
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Languages ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Language
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Language ? $entity : null;
    }

    public function OrderBy(Collectable $languages, array $properties, array $orderBy = [OrderEnum::ASC]): ?Languages
    {
        if (!$languages instanceof Languages)
            throw new Exception("Languages must be instance of Languages");

        $collection = parent::OrderBy($languages, $properties, $orderBy);
        return $collection instanceof Languages ? $collection : null;
    }
}