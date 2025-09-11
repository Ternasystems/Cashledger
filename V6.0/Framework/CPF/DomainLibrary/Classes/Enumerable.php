<?php

declare(strict_types=1);

namespace TS_Domain\Classes;

use Closure;
use JsonSerializable;
use TS_Configuration\Classes\AbstractCls;
use TS_Domain\Enums\OrderEnum;
use TS_Domain\Interfaces\IEnumerable;
use TS_Exception\Classes\DomainException;

/**
 * A concrete implementation providing base iteration, sorting, and aggregation features.
 * This class is designed to be a simple, fluent wrapper around a PHP array.
 *
 * @template TKey of array-key
 * @template TValue
 * @implements IEnumerable<TKey, TValue>
 */
class Enumerable extends AbstractCls implements IEnumerable, JsonSerializable
{
    /** * The underlying array holding the collection's items.
     * @var array<TKey, TValue>
     */
    protected array $items = [];

    /**
     * The current position of the iterator.
     * @var int
     */
    protected int $position = 0;

    /** * Stores the sorting criteria for multi-level sorting.
     * @var array<array{selector: string|Closure, direction: OrderEnum}>
     */
    protected array $sortCriteria = [];

    /**
     * @param array<TKey, TValue> $items The initial items for the collection.
     */
    public function __construct(array $items = [])
    {
        // array_values is used to ensure the internal array is a zero-indexed list
        // for consistent iteration behavior.
        $this->items = array_values($items);
    }

    // --- Iterator Implementation ---

    /**
     * Returns the current element in the collection.
     * @return TValue|null
     */
    public function current(): mixed
    {
        return $this->items[$this->position] ?? null;
    }

    /**
     * Returns the key of the current element.
     * @return string|int|null
     */
    public function key(): string|int|null
    {
        return $this->position;
    }

    /**
     * Moves the iterator to the next element.
     */
    public function next(): void
    {
        ++$this->position;
    }

    /**
     * Moves the iterator to the previous element.
     */
    public function prev(): void
    {
        if ($this->position > 0) {
            --$this->position;
        }
    }

    /**
     * Rewinds the iterator to the first element.
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * Moves the iterator to the last element.
     */
    public function end(): void
    {
        $this->position = $this->count() > 0 ? $this->count() - 1 : 0;
    }

    /**
     * Checks if the current iterator position is valid.
     */
    public function valid(): bool
    {
        return isset($this->items[$this->position]);
    }

    // --- Countable Implementation ---

    /**
     * Returns the number of items in the collection.
     */
    public function count(): int
    {
        return count($this->items);
    }

    // --- IEnumerable Methods ---

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
     * Calculates the sum of a value from each item in the collection.
     */
    public function sum(Closure $selector): float|int
    {
        return array_sum(array_map($selector, $this->items));
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
     * Performs a basic statistical operation on the collection.
     * @throws DomainException for unsupported operators.
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
     */
    public function first(?Closure $callback = null): ?object
    {
        if ($callback === null) {
            return $this->items[0] ?? null;
        }
        foreach ($this->items as $item) {
            if ($callback($item)) {
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
        $items = $callback ? array_filter($this->items, $callback) : $this->items;
        return end($items) ?: null;
    }

    /**
     * Executes the sorting logic based on the defined sort criteria.
     * Sorts the internal items array in place.
     */
    private function executeSort(): void
    {
        usort($this->items, function ($a, $b) {
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
    }

    // --- Output & Conversion Methods ---

    /**
     * Converts the collection to a plain PHP array.
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * Converts the collection to a JSON string.
     *
     * @param int $flags Bitmask of JSON encode options (e.g., JSON_PRETTY_PRINT).
     */
    public function toJson(int $flags = 0): string
    {
        // This works because the class implements JsonSerializable
        return json_encode($this, $flags);
    }

    /**
     * Specifies the data which should be serialized to JSON.
     * This method is automatically called by json_encode().
     * @return array
     */
    public function jsonSerialize(): array
    {
        // Delegates to toArray() to return the underlying items.
        return $this->toArray();
    }

    /**
     * Returns a string representation of the collection object.
     * This is automatically called when the object is treated as a string.
     */
    public function __toString(): string
    {
        return static::class . ' (' . $this->count() . ' item(s))';
    }
}
