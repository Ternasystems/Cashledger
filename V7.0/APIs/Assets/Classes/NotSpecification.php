<?php

namespace API_Assets\Classes;

use TS_Domain\Interfaces\ISpecification;

/**
 * @template T of object
 * @extends Specification<T>
 */
class NotSpecification extends Specification
{
    public function __construct(private readonly ISpecification $spec) {}

    public function isSatisfiedBy(object $candidate): bool
    {
        return !$this->spec->isSatisfiedBy($candidate);
    }
}