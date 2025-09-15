<?php

namespace API_DTORepositories;

use API_Assets\DTOException;
use API_DTORepositories_Collection\AppCategories;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\AppCategory;
use API_DTORepositories_Model\DTOBase;
use Closure;

class AppCategoryRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function first(?Closure $predicate = null): ?AppCategory
    {
        $entity = parent::first($predicate);
        return $entity instanceof AppCategory ? $entity : null;
    }

    public function getAll(): ?AppCategories
    {
        $collection = parent::getAll();
        return $collection instanceof AppCategories ? $collection : null;
    }

    public function getById(string $id): ?AppCategory
    {
        $entity = parent::getById($id);
        return $entity instanceof AppCategory ? $entity : null;
    }

    public function getBy(Closure $predicate): ?AppCategories
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof AppCategories ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?AppCategory
    {
        $entity = parent::last($predicate);
        return $entity instanceof AppCategory ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof AppCategory)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof AppCategory)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}