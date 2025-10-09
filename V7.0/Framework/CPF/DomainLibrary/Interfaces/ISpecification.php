<?php

declare(strict_types=1);

namespace TS_Domain\Interfaces;

/**
 * Defines the contract for the Specification pattern.
 * A specification encapsulates a business rule that can be checked against a candidate object.
 *
 * @template T of object
 */
interface ISpecification
{
    /**
     * Checks if a given candidate object satisfies the specification.
     *
     * @param T $candidate The object to check.
     * @return bool True if the rule is satisfied, false otherwise.
     */
    public function isSatisfiedBy(object $candidate): bool;
}