<?php

declare(strict_types=1);

namespace TS_Domain\Interfaces;

use ArrayAccess;
use JsonSerializable;
use TS_Domain\Enums\ArrayEnum;

/**
 * The primary, user-facing interface for a fully-featured collection object.
 * @template TKey of array-key
 * @template TValue of object
 * @extends IQueryable<TKey, TValue>
 * @extends ArrayAccess<TKey, TValue>
 */
interface ICollectable extends IQueryable, ArrayAccess, JsonSerializable
{
    public static function from(array $items): static;
    public function toArray(ArrayEnum $format = ArrayEnum::Associative): array;
    public function toJson(int $flags = 0): string;
}
