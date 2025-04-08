<?php

namespace TS_Utility\Classes;

use ArrayAccess;
use Countable;
use Exception;
use Iterator;
use Stringable;
use TS_Configuration\Classes\AbstractCls;
use TS_Exception\Classes\CollectableException;
use TS_Utility\Enums\ArrayEnum;
use TS_Utility\Enums\OrderEnum;
use TS_Utility\Interfaces\IQueryable;
use TS_Utility\Traits\TraitQueryable;

abstract class AbstractCollectable extends AbstractCls implements ArrayAccess, Stringable, Countable, Iterator, IQueryable
{
    // Protected properties
    protected array $keyArray = array();
    protected array $indexArray = array();
    protected string $objectType;
    protected int $offset = 0;

    /* Inherited methods from AbstractCls */

    /**
     * @inheritDoc
     */
    protected function SetException(): void
    {
        $this->exception = new CollectableException();
    }

    protected function GetException(): void
    {
        throw $this->exception;
    }

    /* In-class methods */

    /**
     * @throws Exception
     */
    protected function setKeyName(string|int $key): string
    {
        // Check if key is unique
        if (key_exists($key, $this->keyArray) || key_exists($key, $this->indexArray))
            throw new Exception();

        return is_string($key) ? $key : $this->objectType.'_'.$key;
    }

    protected function hash(object $object): string
    {
        return hash('sha256', json_encode(get_object_vars($object)));
    }

    /**
     * @throws Exception
     */
    public function __construct(array $collection, string $_objectType, string $_keySet = null)
    {
        $index = 0;
        $_key = '';
        foreach ($collection as $key => $value) {
            // Set object type
            if (!isset($this->objectType))
                $this->objectType = $_objectType;

            $_key = is_null($_keySet) ? $key : $value->{$_keySet};

            // Build the key
            $keyValue = $this->setKeyName($_key);

            // Add element to keyArray
            $this->keyArray[$keyValue] = $value;

            // Add key to indexArray
            $this->indexArray[$index++] = $keyValue;
        }
    }

    public function toArray(ArrayEnum $arrayType = ArrayEnum::ASSOCIATIVE): array
    {
        return match ($arrayType) {
            ArrayEnum::ASSOCIATIVE => $this->keyArray,
            ArrayEnum::INDEXED => $this->indexArray,
            ArrayEnum::BOTH => [$this->keyArray, $this->indexArray],
        };
    }

    public function index(): int
    {
        return $this->offset;
    }

    public function isType(object $object): bool
    {
        return get_class($object) === $this->objectType;
    }

    public function getType(): string
    {
        return $this->objectType;
    }

    /**
     * @throws Exception
     */
    public function seek(string|int $offset): bool
    {
        if (!$this->offsetExists($offset))
            return false;

        $this->offset = is_int($offset) ? $offset : array_search($offset, $this->indexArray);
        return true;
    }

    public function previous(): void
    {
        --$this->offset;
    }

    public function first(): object
    {
        $key = $this->indexArray[0];
        return $this->keyArray[$key];
    }

    public function last(): object
    {
        $key = $this->indexArray[$this->count() - 1];
        return $this->keyArray[$key];
    }

    public function end(): void
    {
        $this->offset = $this->count() - 1;
    }

    /* ArrayAccess */

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function offsetExists(mixed $offset): bool
    {
        // Check the type of offset
        if (!is_int($offset) && !is_string($offset))
            throw new Exception();

        return key_exists($offset, $this->keyArray) || key_exists($offset, $this->indexArray);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function offsetGet(mixed $offset): object
    {
        // Check the type of offset
        if (!is_int($offset) && !is_string($offset))
            throw new Exception();

        if (!$this->seek($offset))
            throw new Exception();

        $key = is_string($offset) ? $offset : $this->indexArray[$offset];

        return $this->keyArray[$key];
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        // Check the type of offset
        if (!is_int($offset) && !is_string($offset))
            throw new Exception();

        // Check the type of $value
        if (!$this->isType($value))
            throw new Exception();

        // Check if offset already exists
        if ($this->offsetExists($offset)){
            if (is_int($offset)){
                $key = $this->indexArray[$offset];
                $this->keyArray[$key] = $value;
            }else
                $this->keyArray[$offset] = $value;
        }else{
            $key = $this->setKeyName($offset);
            $this->offset = $this->count();
            $this->indexArray[$this->offset] = $key;
            $this->keyArray[$key] = $value;
        }
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function offsetUnset(mixed $offset): void
    {
        // Check the type of offset
        if (!is_int($offset) && !is_string($offset))
            throw new Exception();

        // Check if offset exists
        if (!$this->offsetExists($offset))
            throw new Exception();

        $key = is_string($offset) ? $offset : $this->indexArray[$offset];
        $index = is_int($offset) ? $offset : array_keys($this->indexArray, $offset, true);

        unset($this->indexArray[$index]);
        unset($this->keyArray[$key]);

        $this->rewind();
    }

    /* Stringable */

    public function __toString(): string
    {
        $str = '('.$this->objectType.')';
        $str .= '<br>';
        $str .= 'Array('.$this->count().') { ';
        $str .= '<br>';

        foreach ($this->toArray() as $key => $value) {
            $str .= '['.$key.' => '.$value->__toString().']';
            $str .= '<br>';
        }

        $str .= ' }';
        $str .= '<br>';

        $str .= 'Array('.$this->count().') { ';
        $str .= '<br>';

        foreach ($this->toArray(ArrayEnum::INDEXED) as $key => $value) {
            $str .= '['.$key.' => '.$value.']';
            $str .= '<br>';
        }

        $str .= ' }';

        return $str;
    }

    /* Countable */

    public function count(): int
    {
        return count($this->keyArray);
    }

    /* Iterator */

    public function current(): object
    {
        $key = $this->indexArray[$this->offset];
        return $this->keyArray[$key];
    }

    public function key(): string
    {
        return $this->indexArray[$this->offset];
    }

    public function next(): void
    {
        ++$this->offset;
    }

    public function rewind(): void
    {
        $this->offset = 0;
    }

    /**
     * @throws Exception
     */
    public function valid(): bool
    {
        return $this->offsetExists($this->offset);
    }

    /* Queryable */

    use TraitQueryable
    {
        count as Count;
        toArray as ToArray;
    }

    public function Where(callable $predicate): self
    {
        $derivedClass = get_called_class();
        return new $derivedClass(array_filter($this->keyArray, $predicate));
    }

    public function Select(callable $predicate): self
    {
        $derivedClass = get_called_class();
        return new $derivedClass(array_map($predicate, $this->keyArray));
    }

    public function SortBy(callable $predicate, OrderEnum $orderBy = OrderEnum::ASC): self
    {
        $arr = $this->keyArray;
        usort($arr, fn($a, $b) => $orderBy == OrderEnum::ASC ? $predicate($a) <=> $predicate($b) : $predicate($b) <=> $predicate($a));
        $derivedClass = get_called_class();
        return new $derivedClass($arr);
    }

    public function GroupBy(callable $predicate): array
    {
        $collection = [];

        foreach ($this->keyArray as $item){
            $key = $predicate($item);
            $collection[$key][] = $item;
        }

        $derivedClass = get_called_class();
        $dCollection = [];
        foreach ($collection as $key => $value)
            $dCollection[$key] = new $derivedClass($value);

        return $dCollection;
    }

    public function Distinct(): self
    {
        $arr = array_map('$this->hash', $this->keyArray);
        $derivedClass = get_called_class();
        return new $derivedClass(array_unique($arr, SORT_REGULAR));
    }

    public function Skip(int $count): self
    {
        $derivedClass = get_called_class();
        return new $derivedClass(array_slice($this->keyArray, $count));
    }

    public function Take(int $count): self
    {
        $derivedClass = get_called_class();
        return new $derivedClass(array_slice($this->keyArray, 0, $count));
    }

    public function Limit(int $limit, int $offset = 0): self
    {
        $derivedClass = get_called_class();
        return new $derivedClass(array_slice($this->keyArray, $offset, $limit));
    }

    public function Join(AbstractCollectable $collectable, callable $outerPredicate, callable $innerPredicate): self
    {
        $derivedClass = get_called_class();
        $elements = [];

        foreach ($this->keyArray as $key => $value){
            foreach ($collectable as $item){
                if ($outerPredicate($value) === $innerPredicate($item))
                    $elements[$key] = $value;
            }
        }

        return new $derivedClass($elements);
    }

    /**
     * @throws CollectableException
     */
    public function Sum(callable $predicate): float
    {
        $arr = array_map($predicate, $this->keyArray);
        $num = array_filter($arr, fn($n) => !is_numeric($n));

        if (count($num))
            throw new CollectableException('Sum cannot be performed on non-numeric values');

        return array_sum($arr);
    }

    /**
     * @throws CollectableException
     */
    public function Average(callable $predicate): float
    {
        return $this->Count() === 0 ? 0 : $this->Sum($predicate) / $this->Count();
    }

    public function FirstOrDefault(?callable $predicate = null): ?object
    {
        if (is_null($predicate))
            return $this->first();

        foreach ($this->keyArray as $item){
            if ($predicate($item))
                return $item;
        }

        return null;
    }

    public function LastOrDefault(?callable $predicate = null): ?object
    {
        if (is_null($predicate))
            return $this->last();

        $collection = [];
        foreach ($this->keyArray as $item){
            if ($predicate($item))
                $collection[] = $item;
        }

        return count($collection) > 0 ? $collection[count($collection) - 1] : null;
    }

    public function Any(?callable $predicate = null): bool
    {
        if (is_null($predicate))
            return $this->count() > 0;

        foreach ($this->keyArray as $item){
            if ($predicate($item))
                return true;
        }

        return false;
    }
}