<?php

namespace API_Assets\Classes;

use TS_Domain\Interfaces\ISpecification;

/**
 * @template T of object
 * @extends Specification<T>
 */
class AndSpecification extends Specification
{
    public function __construct(private readonly ISpecification $left, private readonly ISpecification $right) {}

    public function isSatisfiedBy(object $candidate): bool
    {
        return $this->left->isSatisfiedBy($candidate) && $this->right->isSatisfiedBy($candidate);
    }
}