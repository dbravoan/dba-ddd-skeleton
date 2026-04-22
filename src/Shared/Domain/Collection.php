<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain;

use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * @template T
 * @implements IteratorAggregate<int, T>
 */
abstract class Collection implements Countable, IteratorAggregate
{
    /**
     * @param array<int, T> $items
     */
    public function __construct(private readonly array $items)
    {
        Assert::arrayOf($this->type(), $items);
    }

    /**
     * @return array<int, string>
     */
    abstract protected function type(): array;

    /**
     * @return ArrayIterator<int, T>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items());
    }

    public function count(): int
    {
        return count($this->items());
    }

    /**
     * @return array<int, T>
     */
    public function items(): array
    {
        return $this->items;
    }
}
