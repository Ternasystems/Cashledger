<?php

declare(strict_types=1);

namespace TS_Domain\Classes;

use Closure;
use TS_Configuration\Classes\AbstractCls;
use TS_Domain\Enums\ArrayEnum;
use TS_Domain\Enums\OrderEnum;
use TS_Domain\Interfaces\ICollectable;
use TS_Domain\Interfaces\IEnumerable;
use TS_Exception\Classes\DomainException;

/**
 * An abstract, strongly-typed, queryable collection class that provides a rich,
 * array-like interface for handling collections of objects.
 *
 * It manages two internal arrays: one for associative lookups ($keyArray) and
 * one to maintain the current order ($indexArray), which is crucial for sorting.
 *
 * @template TKey of array-key
 * @template TValue of object
 * @implements ICollectable<TKey, TValue>
 */
abstract class AbstractCollectable extends AbstractCls implements ICollectable
{
    /**
     * An ordered list of keys. This array dictates the iteration and sort order.
     * @var array<int, TKey>
     */
    protected array $indexArray = [];

    /**
     * The main data store. An associative array mapping unique keys to their object values.
     * @var array<TKey, TValue>
     */
    protected array $keyArray = [];

    /**
     * The fully qualified class name of the objects this collection is allowed to hold.
     * @var class-string<TValue>|null
     */
    protected ?string $itemType = null;

    /**
     * The current position of the iterator pointer, relative to the $indexArray.
     * @var int
     */
    protected int $offset = 0;

    /**
     * An array of sorting criteria to be applied by executeSort().
     * @var array<array{selector: string|Closure, direction: OrderEnum}>
     */
    protected array $sortCriteria = [];

    /**
     * Constructor enforces strong typing on all initial items.
     * @param array<TKey, TValue> $collection The initial items.
     * @throws DomainException if items are not objects or not of the same type.
     */
    public function __construct(array $collection = [])
    {
        // If the collection is empty, there's nothing to initialize.
        if (empty($collection)) {
            return;
        }

        // The first item determines the required type for the whole collection.
        $firstItem = reset($collection);
        if (!is_object($firstItem)) {
            throw new DomainException('item_not_object');
        }

        $this->itemType($firstItem);

        // Process each item to ensure type safety and generate keys.
        foreach ($collection as $key => $item) {
            if (!($item instanceof $this->itemType)) {
                throw new DomainException('item_type_mismatch', [':type' => $this->itemType]);
            }

            // If the key is numeric, it's likely an indexed array, so we generate a unique hash key.
            // Otherwise, we trust the provided string key.
            $finalKey = is_int($key) ? $this->hash($item) : $key;
            $this->keyArray[$finalKey] = $item;
        }

        // Initialize the index with the keys from the data store.
        $this->indexArray = array_keys($this->keyArray);
    }

    /**
     * Sets the required object type for the collection.
     */
    protected function itemType(object $item): void
    {
        $this->itemType = get_class($item);
    }

    /**
     * Creates a generic, consistent hash for any given value.
     * @param mixed $value The value to hash.
     * @return string The resulting SHA256 hash.
     */
    protected function hash(mixed $value): string
    {
        // serialize() creates a byte-stream representation of any value.
        $serializedValue = serialize($value);
        return hash('sha256', $serializedValue);
    }

    /**
     * A static factory method to create a new instance of the collection.
     * @throws DomainException
     */
    public static function from(array $items): static
    {
        return new static($items);
    }

    // --- Iterator Implementation ---

    /**
     * Returns the current element in the collection based on the iterator's position.
     * @return TValue|null
     */
    public function current(): mixed
    {
        $key = $this->key();
        return $key !== null ? $this->keyArray[$key] : null;
    }

    /**
     * Returns the key of the current element.
     * @return TKey|null
     */
    public function key(): string|int|null
    {
        return $this->indexArray[$this->offset] ?? null;
    }

    /**
     * Moves the iterator to the next element.
     */
    public function next(): void
    {
        ++$this->offset;
    }

    /**
     * Rewinds the iterator to the first element.
     */
    public function rewind(): void
    {
        $this->offset = 0;
    }

    /**
     * Checks if the current iterator position is valid.
     */
    public function valid(): bool
    {
        return isset($this->indexArray[$this->offset]);
    }

    /**
     * Returns the number of items in the collection.
     */
    public function count(): int
    {
        return count($this->keyArray);
    }

    // --- IEnumerable Methods ---

    /**
     * Moves the iterator to the previous element.
     */
    public function prev(): void
    {
        if ($this->offset > 0) {
            --$this->offset;
        }
    }

    /**
     * Moves the iterator to the last element.
     */
    public function end(): void
    {
        $this->offset = $this->count() > 0 ? $this->count() - 1 : 0;
    }

    /**
     * Sorts the collection by a given key. This is the primary sort.
     * @param string|Closure $keySelector The property name or function to get the sort key.
     * @param OrderEnum $direction The sort direction (ASC or DESC).
     * @return static
     */
    public function sortBy(string|Closure $keySelector, OrderEnum $direction = OrderEnum::ASC): static
    {
        $this->sortCriteria = [['selector' => $keySelector, 'direction' => $direction]];
        $this->executeSort();
        return $this;
    }

    /**
     * Applies a subsequent sorting criterion. Must be called after sortBy().
     * @throws DomainException
     */
    public function thenBy(string|Closure $keySelector, OrderEnum $direction = OrderEnum::ASC): static
    {
        if (empty($this->sortCriteria)) {
            throw new DomainException('sort_before_then');
        }
        $this->sortCriteria[] = ['selector' => $keySelector, 'direction' => $direction];
        $this->executeSort();
        return $this;
    }

    /**
     * A placeholder for a potential future method to modify the last sort criterion.
     */
    public function orderBy(OrderEnum $direction = OrderEnum::ASC, ?Closure $keySelector = null): static
    {
        // This method's logic would depend on specific requirements, currently a placeholder.
        return $this;
    }

    /**
     * Calculates the sum of a value from each item in the collection.
     */
    public function sum(Closure $selector): float|int
    {
        return array_sum(array_map($selector, $this->keyArray));
    }

    /**
     * Calculates the average of a value from each item in the collection.
     */
    public function average(Closure $selector): float|int
    {
        $count = $this->count();
        return $count === 0 ? 0 : $this->sum($selector) / $count;
    }

    /**
     * A placeholder for a potential future method for more complex statistics.
     * @throws DomainException
     */
    public function statistics(string $operator, Closure $selector): float|int|array
    {
        return match (strtolower($operator)) {
            'sum' => $this->sum($selector),
            'average', 'avg' => $this->average($selector),
            'count' => $this->count(),
            default => throw new DomainException('unsupported_statistic', [':operator' => $operator])
        };
    }

    /**
     * Returns the first element in the collection, optionally filtered by a callback.
     * Respects the current sort order.
     */
    public function first(?Closure $callback = null): ?object
    {
        // Iterate through the collection in its defined order (respecting sort).
        foreach ($this as $key => $item) {
            // If no filter, or if the item matches the filter, return it immediately.
            if ($callback === null || $callback($item, $key)) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Returns the last element in the collection, optionally filtered by a callback.
     */
    public function last(?Closure $callback = null): ?object
    {
        // For efficiency, we can reverse the ordered index and find the first match.
        $reversedKeys = array_reverse($this->indexArray);
        foreach ($reversedKeys as $key) {
            $item = $this->keyArray[$key];
            if ($callback === null || $callback($item, $key)) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Sorts the keyArray based on the defined criteria and then rebuilds the
     * indexArray to reflect the new order, ensuring consistency.
     */
    private function executeSort(): void
    {
        // uasort sorts the array by values but maintains key-value association.
        uasort($this->keyArray, function ($a, $b) {
            foreach ($this->sortCriteria as $criterion) {
                $selector = $criterion['selector'];
                $direction = $criterion['direction'];
                $keyA = is_string($selector) ? ($a->{$selector} ?? null) : $selector($a);
                $keyB = is_string($selector) ? ($b->{$selector} ?? null) : $selector($b);
                $result = $keyA <=> $keyB;
                if ($result !== 0) {
                    return ($direction === OrderEnum::DESC) ? -$result : $result;
                }
            }
            return 0;
        });

        // CRITICAL FIX: Resynchronize the indexArray to match the new order of keyArray.
        $this->indexArray = array_keys($this->keyArray);

        // Reset the internal pointer for future iterations.
        $this->rewind();
    }

    // --- IQueryable Implementation ---

    /**
     * Filters the collection based on a given callback.
     */
    public function where(Closure $callback): static
    {
        // array_filter preserves keys, allowing the new collection to be built correctly.
        $filteredItems = array_filter($this->keyArray, $callback, ARRAY_FILTER_USE_BOTH);
        return new static($filteredItems);
    }

    /**
     * Projects each element of the collection into a new form.
     */
    public function select(Closure $callback): IEnumerable
    {
        $projectedItems = array_map($callback, $this->keyArray);
        // Returns a base Enumerable, as the type may have changed.
        return new Enumerable($projectedItems);
    }

    /**
     * Projects each element to a collection and flattens the resulting collections into one.
     */
    public function selectMany(Closure $collectionSelector, ?Closure $resultSelector = null): IEnumerable
    {
        $finalResults = [];
        foreach ($this->keyArray as $sourceItem) {
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
     */
    public function distinct(?Closure $keySelector = null): static
    {
        $seenKeys = [];
        $distinctItems = [];
        foreach ($this->keyArray as $originalKey => $item) {
            // Determine the value to check for uniqueness.
            $key = $keySelector !== null ? $keySelector($item) : $item;
            $hash = $this->hash($key);
            if (!isset($seenKeys[$hash])) {
                // If this value is new, add the item to the results, preserving its original key.
                $distinctItems[$originalKey] = $item;
                $seenKeys[$hash] = true;
            }
        }
        return new static($distinctItems);
    }

    /**
     * Returns a slice of the collection, used for pagination.
     * @throws DomainException if the collection is not sorted first.
     */
    public function limit(int $count, int $offset = 0): static
    {
        if (empty($this->sortCriteria)) {
            throw new DomainException('sort_before_limit');
        }
        // Slice the ordered index to get the keys for the desired page.
        $limitedKeys = array_slice($this->indexArray, $offset, $count);
        // Get only the items corresponding to those keys.
        $limitedItems = array_intersect_key($this->keyArray, array_flip($limitedKeys));
        return new static($limitedItems);
    }

    /**
     * Filters the collection by keeping items for which a matching key exists in another collection.
     */
    public function join(ICollectable $innerCollection, Closure|string $outerKeySelector, Closure|string $innerKeySelector): static
    {
        // Build a fast lookup map from the inner collection's keys.
        $innerKeyMap = [];
        foreach ($innerCollection as $innerItem) {
            $key = is_callable($innerKeySelector) ? $innerKeySelector($innerItem) : $innerItem->{$innerKeySelector};
            $innerKeyMap[$key] = true;
        }
        if (empty($innerKeyMap)) {
            return new static([]);
        }

        // Iterate through the outer collection once, checking against the map.
        $resultElements = [];
        foreach ($this->keyArray as $originalKey => $outerItem) {
            $key = is_callable($outerKeySelector) ? $outerKeySelector($outerItem) : $outerItem->{$outerKeySelector};
            if (isset($innerKeyMap[$key])) {
                $resultElements[$originalKey] = $outerItem;
            }
        }
        return new static($resultElements);
    }

    /**
     * Merges this collection with another, overwriting values with matching string keys.
     */
    public function merge(ICollectable $collectable): static
    {
        $itemsToMerge = iterator_to_array($collectable);
        $mergedItems = array_merge($this->keyArray, $itemsToMerge);
        return new static($mergedItems);
    }

    /**
     * Groups the collection into a collection of collections based on a key.
     */
    public function groupBy(string|Closure $keySelector): static
    {
        $groups = [];
        foreach ($this->keyArray as $item) {
            $key = is_string($keySelector) ? ($item->{$keySelector} ?? null) : $keySelector($item);
            $groups[$key][] = $item;
        }
        // Wrap each group in a new instance of the class to make them queryable.
        foreach ($groups as $key => $groupedItems) {
            $groups[$key] = new static($groupedItems);
        }
        return new static($groups);
    }

    // --- ArrayAccess Implementation ---
    /**
     * Checks if an offset exists.
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->offsetGet($offset) !== null;
    }

    /**
     * Gets an item by its string key or numeric index.
     */
    public function offsetGet(mixed $offset): mixed
    {
        if (is_string($offset)) {
            return $this->keyArray[$offset] ?? null;
        }
        if (is_int($offset)) {
            $key = $this->indexArray[$offset] ?? null;
            return ($key !== null) ? ($this->keyArray[$key] ?? null) : null;
        }
        return null;
    }

    /**
     * Sets an item at a given offset.
     * @throws DomainException
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($this->itemType && !($value instanceof $this->itemType)) {
            throw new DomainException('item_type_mismatch', [':type' => $this->itemType]);
        }
        if (is_null($offset)) { // Append
            $key = $this->hash($value);
            $this->keyArray[$key] = $value;
            $this->indexArray[] = $key;
        } else { // Set by key
            $this->keyArray[$offset] = $value;
            if (!in_array($offset, $this->indexArray, true)) {
                $this->indexArray[] = $offset;
            }
        }
    }

    /**
     * Unsets an item at a given offset.
     */
    public function offsetUnset(mixed $offset): void
    {
        $keyToRemove = null;
        if (is_string($offset) && isset($this->keyArray[$offset])) {
            $keyToRemove = $offset;
        } elseif (is_int($offset) && isset($this->indexArray[$offset])) {
            $keyToRemove = $this->indexArray[$offset];
        }

        if ($keyToRemove !== null) {
            unset($this->keyArray[$keyToRemove]);
            // Filter the key out of the index and re-index the array to prevent gaps.
            $this->indexArray = array_values(array_filter($this->indexArray, fn($key) => $key !== $keyToRemove));
        }
    }

    // --- Output & Conversion ---

    /**
     * Converts the collection to a structured array.
     */
    public function toArray(ArrayEnum $format = ArrayEnum::Associative): array
    {
        return match ($format) {
            ArrayEnum::Indexed => array_values($this->keyArray),
            ArrayEnum::Associative => $this->keyArray,
            ArrayEnum::Complex => (function () {
                $collection = [
                    'indexArray' => $this->indexArray,
                    'keyArray' => []
                ];
                foreach ($this->keyArray as $key => $item) {
                    if (is_object($item) && method_exists($item, 'toArray')) {
                        $collection['keyArray'][$key] = $item->toArray();
                    } else {
                        $collection['keyArray'][$key] = $item;
                    }
                }
                return $collection;
            })(),
        };
    }

    /**
     * Converts the collection to a JSON string.
     */
    public function toJson(int $flags = 0): string
    {
        return json_encode($this, $flags);
    }

    /**
     * Specifies the data which should be serialized to JSON.
     */
    public function jsonSerialize(): array
    {
        // Delegates to toArray, returning the key-value pairs.
        return $this->toArray();
    }

    /**
     * Returns a string representation of the collection object.
     */
    public function __toString(): string
    {
        return static::class . ' (' . $this->count() . ' item(s))';
    }
}
