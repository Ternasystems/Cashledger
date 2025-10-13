<?php

namespace API_DTOEntities_Collection;

use API_DTOEntities_Model\Entity;
use TS_Domain\Classes\AbstractCollectable;
use Closure;
use TS_Exception\Classes\DomainException;

class EntityCollectable extends AbstractCollectable
{
    /**
     * The constructor processes an array of Entity objects, converting it
     * into an associative array keyed by the object's Id for efficient lookups.
     *
     * @param array<Entity> $collection An array of DTO model instances.
     * @throws DomainException
     */
    public function __construct(array $collection = [])
    {
        $keyedCollection = [];
        foreach ($collection as $item) {
            // Use the 'Id' property of the model as the key.
            if (isset($item->Id)) {
                $keyedCollection[$item->Id] = $item;
            }
        }
        parent::__construct($keyedCollection);
    }

    /**
     * Returns the first element in the collection, optionally filtered by a callback.
     * Ensures the returned object is an instance of Entity.
     *
     * @param Closure|null $callback
     * @return Entity|null
     */
    public function first(?Closure $callback = null): ?Entity
    {
        $entity = parent::first($callback);
        return $entity instanceof Entity ? $entity : null;
    }

    /**
     * Returns the last element in the collection, optionally filtered by a callback.
     * Ensures the returned object is an instance of Entity.
     *
     * @param Closure|null $callback
     * @return Entity|null
     */
    public function last(?Closure $callback = null): ?Entity
    {
        $entity = parent::last($callback);
        return $entity instanceof Entity ? $entity : null;
    }
}