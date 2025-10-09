<?php

declare(strict_types=1);

namespace TS_Controller\Classes;
use Attribute;

/**
 * An attribute to specify authorization requirements for a controller action.
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
final class Authorize
{
    /**
     * @param string|null $role The specific role required to access this action.
     * @param string|null $policy A more complex authorization policy to check.
     */
    public function __construct(public ?string $role = null, public ?string $policy = null)
    {
    }
}