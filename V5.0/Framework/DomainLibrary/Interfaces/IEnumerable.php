<?php

namespace TS_Domain\Interfaces;

use Closure;
use Countable;
use Iterator;
use TS_Domain\Enums\OrderEnum;

/**
 * Base contract for sortable, iterable collections with basic element access.
 * @template TKey
 * @template TValue
 */
interface IEnumerable extends Iterator, Countable
{
    public function toArray(): array;
    public function prev(): void;
    public function end(): void;
    public function sortBy(string|Closure $keySelector, OrderEnum $direction = OrderEnum::ASC): static;
    public function thenBy(string|Closure $keySelector, OrderEnum $direction = OrderEnum::ASC): static;
    public function orderBy(OrderEnum $direction = OrderEnum::ASC, ?Closure $keySelector = null): static;
    public function sum(Closure $selector): float|int;
    public function average(Closure $selector): float|int;
    public function statistics(string $operator, Closure $selector): float|int|array;
    public function first(?Closure $callback = null): ?object;
    public function last(?Closure $callback = null): ?object;
}
