<?php

namespace API_DTORepositories;

use API_Assets\DTOException;
use API_DTORepositories_Collection\Languages;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\DTOBase;
use API_DTORepositories_Model\Language;
use Closure;

class LanguageRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function first(?Closure $predicate = null): ?Language
    {
        $entity = parent::first($predicate);
        return $entity instanceof Language ? $entity : null;
    }

    public function getAll(): ?Languages
    {
        $collection = parent::getAll();
        return $collection instanceof Languages ? $collection : null;
    }

    public function getById(string $id): ?Language
    {
        $entity = parent::getById($id);
        return $entity instanceof Language ? $entity : null;
    }

    public function getBy(Closure $predicate): ?Languages
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof Languages ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?Language
    {
        $entity = parent::last($predicate);
        return $entity instanceof Language ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof Language)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof Language)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}