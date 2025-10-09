<?php

declare(strict_types=1);

namespace TS_Domain\Classes;

use JsonSerializable;
use ReflectionClass;
use ReflectionProperty;
use TS_Configuration\Classes\AbstractCls;

/**
 * The abstract base class for all Value Objects in the domain.
 * A DomainModel's equality is determined by its values, not its identity.
 * It is best practice for these objects to be immutable.
 */
class DomainModel extends AbstractCls implements JsonSerializable
{
    /**
     * Populates the readonly properties of the child class from an associative array.
     *
     * @param array<string, mixed> $properties
     */
    public function __construct(array $properties = [])
    {
        $reflector = new ReflectionClass($this);
        foreach ($properties as $key => $value) {
            if ($reflector->hasProperty($key)) {
                $prop = $reflector->getProperty($key);
                // This is a common way to set readonly properties from a constructor.
                $prop->setValue($this, $value);
            }
        }
    }

    /**
     * Determines if this DomainModel is equal to another by comparing their values.
     *
     * @param self $other The other DomainModel to compare against.
     * @return bool True if they are value-equal, false otherwise.
     */
    final public function equals(self $other): bool
    {
        // A simple and effective way to check for value equality
        // is to compare the serialized representation of the objects.
        return serialize($this) === serialize($other);
    }

    /**
     * Specifies the data which should be serialized to JSON.
     * This method is automatically called by json_encode().
     */
    public function jsonSerialize(): array
    {
        $data = [];
        $reflector = new ReflectionClass($this);
        $properties = $reflector->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $data[$property->getName()] = $property->getValue($this);
        }
        return $data;
    }
}