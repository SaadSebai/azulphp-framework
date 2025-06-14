<?php

namespace Azulphp\Collections;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @template T
 * @implements IteratorAggregate<mixed, T>
 */
class Collection implements ArrayAccess, Countable, IteratorAggregate, Arrayable
{
    /**
     * @param  array<mixed, T>  $items
     */
    public function __construct(protected array $items = [])
    {
        //
    }
    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->items[$offset] ?? false;
    }

    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->items[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }

    /**
     * @return Traversable<mixed, T>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Do custom logic through a callable and return a new instance.
     *
     * @param  callable  $callback
     * @return self
     */
    public function map(callable $callback): self
    {
        return new self(array_map($callback, $this->items));
    }

    /**
     * Get the data as array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * Get the first element.
     *
     * @return mixed
     */
    public function first(): mixed
    {
        return reset($this->items);
    }

    /**
     * Is the collection empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function toArrayTree(): array
    {
        $result = [];

        foreach ($this->items as $element)
        {
            if ($element instanceof Collection)
            {
                $result[] = $element->toArrayTree();
            }
            else
            {
                if ($element instanceof Arrayable)
                    $result[] = $element->toArray();
                else
                    $result[] = $element;
            }
        }

        return $result;
    }
}