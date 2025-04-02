<?php

namespace TS_Utility\Traits;

use TS_Utility\Enums\ArrayEnum;

trait TraitQueryable
{
    public function count(): int
    {
        return $this->count();
    }

    public function toArray(ArrayEnum $arrayType = ArrayEnum::ASSOCIATIVE): array
    {
        return $this->toArray($arrayType);
    }
}