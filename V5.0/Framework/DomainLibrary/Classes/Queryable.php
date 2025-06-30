<?php

declare(strict_types=1);

namespace TS_Domain\Classes;

use Closure;
use TS_Domain\Interfaces\IEnumerable;
use TS_Domain\Interfaces\IQueryable;
use TS_Exception\Classes\DomainException;

/**
 * Extends Enumerable to add complex querying and transformation capabilities
 * like where, select, distinct, join, and groupBy.
 *
 * @template TKey
 * @template TValue
 * @extends Enumerable<TKey, TValue>
 * @implements IQueryable<TKey, TValue>
 */
class Queryable extends Enumerable implements IQueryable
{
    /**
     * @param array<TKey, TValue> $items The initial items for the collection.
     */
    public function __construct(array $items = [])
    {
        parent::__construct($items);
    }

    /**
     * Creates a generic, consistent hash for any given value (object, array, scalar, etc.).
     * This is used to create a "fingerprint" to check for value-based uniqueness.
     *
     * @param mixed $value The value to hash.
     * @return string The resulting SHA256 hash.
     */
    protected function hash(mixed $value): string
    {
        $serializedValue = serialize($value);
        return hash('sha256', $serializedValue);
    }

    /**
     * Filters the collection based on a given callback.
     *
     * @param Closure $callback The callback function to use for filtering.
     * @return static A new collection containing only the elements that pass the callback test.
     */
    public function where(Closure $callback): static
    {
        $filtered = array_filter($this->items, $callback);
        return new static($filtered);
    }

    /**
     * Projects each element of the collection into a new form.
     *
     * @param Closure $callback The function to apply to each element.
     * @return IEnumerable A new Enumerable containing the transformed elements.
     */
    public function select(Closure $callback): IEnumerable
    {
        $items = array_map($callback, $this->items);
        return new Enumerable($items);
    }

    /**
     * Projects each element to a collection and flattens the resulting collections into one.
     *
     * @param Closure $collectionSelector A function to extract a collection from each element.
     * @param ?Closure $resultSelector (Optional) A function to create a result from a source element and a sub-collection element.
     * @return IEnumerable A new, flat Enumerable.
     */
    public function selectMany(Closure $collectionSelector, ?Closure $resultSelector = null): IEnumerable
    {
        $finalResults = [];
        foreach ($this->items as $sourceItem) {
            $intermediateCollection = $collectionSelector($sourceItem);
            foreach ($intermediateCollection as $collectionItem) {
                if ($resultSelector !== null) {
                    $finalResults[] = $resultSelector($sourceItem, $collectionItem);
                } else {
                    $finalResults[] = $collectionItem;
                }
            }
        }
        return new Enumerable($finalResults);
    }

    /**
     * Returns distinct elements from the collection, optionally by a key.
     *
     * @param Closure|null $keySelector A function to return a unique identifier.
     * @return static A new collection with distinct items.
     */
    public function distinct(?Closure $keySelector = null): static
    {
        $seenHashes = [];
        $distinctItems = [];
        foreach ($this->items as $item) {
            $key = $keySelector !== null ? $keySelector($item) : $item;
            $hash = $this->hash($key);
            if (!isset($seenHashes[$hash])) {
                $distinctItems[] = $item;
                $seenHashes[$hash] = true;
            }
        }
        return new static($distinctItems);
    }

    /**
     * Returns a slice of the collection, used for pagination.
     *
     * @param int $count The maximum number of items to return.
     * @param int $offset The starting position.
     * @throws DomainException if the collection is not sorted first.
     * @return static
     */
    public function limit(int $count, int $offset = 0): static
    {
        if (empty($this->sortCriteria)) {
            throw new DomainException('sort_before_limit');
        }
        $items = array_slice($this->items, $offset, $count);
        return new static($items);
    }

    /**
     * Filters the collection by keeping items for which a matching key exists in another collection.
     *
     * @param IEnumerable $innerCollection The collection to check against.
     * @param Closure|string $outerKeySelector The key selector for the current (outer) collection.
     * @param Closure|string $innerKeySelector The key selector for the inner collection.
     * @return static
     */
    public function join(IEnumerable $innerCollection, Closure|string $outerKeySelector, Closure|string $innerKeySelector): static
    {
        $innerKeyMap = [];
        foreach ($innerCollection as $innerItem) {
            $key = is_callable($innerKeySelector) ? $innerKeySelector($innerItem) : $innerItem->{$innerKeySelector};
            $innerKeyMap[$key] = true;
        }

        $resultElements = [];
        if (!empty($innerKeyMap)) {
            foreach ($this->items as $outerItem) {
                $key = is_callable($outerKeySelector) ? $outerKeySelector($outerItem) : $outerItem->{$outerKeySelector};
                if (isset($innerKeyMap[$key])) {
                    $resultElements[] = $outerItem;
                }
            }
        }
        return new static($resultElements);
    }

    /**
     * Merges this collection with another, overwriting values with matching string keys.
     *
     * @param IEnumerable $enumerable The collection to merge.
     * @return static
     */
    public function merge(IEnumerable $enumerable): static
    {
        $elements = array_merge($this->items, $enumerable->toArray());
        return new static($elements);
    }

    /**
     * Groups the collection into a collection of collections based on a key.
     *
     * @param string|Closure $keySelector A function to extract the key for each element.
     * @return static A new collection where each item is a group (another Queryable instance).
     */
    public function groupBy(string|Closure $keySelector): static
    {
        $groups = [];
        foreach ($this->items as $item) {
            $key = is_string($keySelector) ? ($item->{$keySelector} ?? null) : $keySelector($item);
            $groups[$key][] = $item;
        }

        foreach ($groups as $key => $groupedItems) {
            $groups[$key] = new static($groupedItems);
        }

        return new static($groups);
    }
}
