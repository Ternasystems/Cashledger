<?php

namespace TS_Utility\Interfaces;

use TS_Utility\Classes\AbstractCollectable;
use TS_Utility\Enums\ArrayEnum;
use TS_Utility\Enums\OrderEnum;

interface IQueryable
{
    public function Where(callable $predicate): self;
    public function Select(callable $predicate): self;
    public function SortBy(callable $predicate, OrderEnum $orderBy = OrderEnum::ASC): self;
    public function GroupBy(callable $predicate): array;
    public function Distinct(): self;
    public function Skip(int $count): self;
    public function Take(int $count): self;
    public function Limit(int $limit, int $offset = 0): self;
    public function Join(AbstractCollectable $collectable, callable $outerPredicate, callable $innerPredicate): self;
    public function Count(): int;
    public function Sum(callable $predicate): float;
    public function Average(callable $predicate): float;
    public function ToArray(ArrayEnum $arrayType = ArrayEnum::ASSOCIATIVE): array;
    public function FirstOrDefault(?callable $predicate = null): ?object;
    public function LastOrDefault(?callable $predicate = null): ?object;
    public function Any(): bool;
}