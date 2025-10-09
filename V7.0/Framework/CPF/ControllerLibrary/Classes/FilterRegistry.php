<?php

declare(strict_types=1);

namespace TS_Controller\Classes;

use TS_Configuration\Classes\AbstractCls;

/**
 * Maps attributes to their corresponding IActionFilter handler classes.
 */
class FilterRegistry extends AbstractCls
{
    /** @var array<string, string> */
    private array $attributeMap = [];

    /**
     * Maps an attribute class to a filter class.
     *
     * @param string $attributeClass The fully qualified name of the attribute.
     * @param string $filterClass The fully qualified name of the IActionFilter implementation.
     */
    public function map(string $attributeClass, string $filterClass): void
    {
        $this->attributeMap[$attributeClass] = $filterClass;
    }

    /**
     * Gets the filter class associated with a given attribute.
     */
    public function getFilterClass(string $attributeClass): ?string
    {
        return $this->attributeMap[$attributeClass] ?? null;
    }
}