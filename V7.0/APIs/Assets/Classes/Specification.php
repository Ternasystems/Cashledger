<?php

namespace API_Assets\Classes;

use TS_Domain\Interfaces\ISpecification;

/**
 * An abstract base class for specifications that provides composite functionality.
 *
 * @template T of object
 * @implements ISpecification<T>
 */
abstract class Specification implements ISpecification
{
    /**
     * The core method that must be implemented by all concrete specifications.
     */
    abstract public function isSatisfiedBy(object $candidate): bool;

    /**
     * Creates a new specification that is the logical AND of this and another.
     */
    public function and(ISpecification $other): ISpecification
    {
        return new AndSpecification($this, $other);
    }

    /**
     * Creates a new specification that is the logical OR of this and another.
     */
    public function or(ISpecification $other): ISpecification
    {
        return new OrSpecification($this, $other);
    }

    /**
     * Creates a new specification that is the logical NOT of this one.
     */
    public function not(): ISpecification
    {
        return new NotSpecification($this);
    }
}