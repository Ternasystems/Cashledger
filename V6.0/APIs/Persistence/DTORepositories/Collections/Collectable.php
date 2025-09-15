<?php

namespace API_DTORepositories_Collection;

use API_DTORepositories_Model\DTOBase;
use TS_Domain\Classes\AbstractCollectable;
use Closure;
use TS_Exception\Classes\DomainException;

/**
 * The abstract base class for all strongly-typed DTO collections.
 * It inherits a rich set of querying and manipulation methods from the framework.
 */
abstract class Collectable extends AbstractCollectable
{
    /**
     * The constructor processes an array of DTOBase objects, converting it
     * into an associative array keyed by the object's Id for efficient lookups.
     *
     * @param array<DTOBase> $collection An array of DTO model instances.
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
     * Ensures the returned object is an instance of DTOBase.
     *
     * @param Closure|null $callback
     * @return DTOBase|null
     */
    public function first(?Closure $callback = null): ?DTOBase
    {
        $entity = parent::first($callback);
        return $entity instanceof DTOBase ? $entity : null;
    }

    /**
     * Returns the last element in the collection, optionally filtered by a callback.
     * Ensures the returned object is an instance of DTOBase.
     *
     * @param Closure|null $callback
     * @return DTOBase|null
     */
    public function last(?Closure $callback = null): ?DTOBase
    {
        $entity = parent::last($callback);
        return $entity instanceof DTOBase ? $entity : null;
    }
}