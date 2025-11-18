<?php declare(strict_types=1);

namespace Ewald\MagoTest;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<string, NeedsParenthesesObject>
 * @implements ArrayAccess<array-key, NeedsParenthesesObject>
 *
 * @template T of NeedsParenthesesObject
 */
class NeedsParentheses implements IteratorAggregate, ArrayAccess
{
    protected const string COMPONENT_CLASS = NeedsParenthesesObject::class;

    protected array $collection;

    public function __construct(null|array $items)
    {
        $this->collection = [];

        if (empty($items) || !array_is_list($items)) {
            return;
        }

        foreach ($items as $item) {
            if ($item instanceof (static::COMPONENT_CLASS)) {
                $this->collection[] = $item;
            } elseif (is_array($item)) {
                $this->collection[] = new (static::COMPONENT_CLASS)($item);
            }
        }
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->collection);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->collection[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->collection[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->collection[] = $value;
        } else {
            $this->collection[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->collection[$offset]);
    }
}
