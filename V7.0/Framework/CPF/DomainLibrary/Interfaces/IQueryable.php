<?php

declare(strict_types=1);

namespace TS_Domain\Interfaces;

use Closure;

/**
 * Contract for collections that can be queried and transformed.
 * @template TKey
 * @template TValue
 * @extends IEnumerable<TKey, TValue>
 */
interface IQueryable extends IEnumerable
{
    public function where(Closure $callback): static;

    /**
     * @template TNewValue
     * @param Closure(TValue, TKey): TNewValue $callback
     * @return IEnumerable<array-key, TNewValue>
     */
    public function select(Closure $callback): IEnumerable;

    /**
     * @template TNewValue
     * @param Closure(TValue, TKey): array<TNewValue> $callback
     * @return IEnumerable<array-key, TNewValue>
     */
    public function selectMany(Closure $callback): IEnumerable;
    public function distinct(): static;
    public function limit(int $count): static;
    public function join(ICollectable $collectable, string|Closure $innerKeySelector, string|Closure $outerKeySelector): static;
    public function merge(ICollectable $collectable): static;
    public function groupBy(string|Closure $keySelector): static;
}
