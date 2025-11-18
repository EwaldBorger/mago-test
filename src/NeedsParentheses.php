<?php declare(strict_types=1);

namespace Ewald\MagoTest;

/**
 * @template T of NeedsParenthesesObject
 */
class NeedsParentheses
{
    protected const string COMPONENT_CLASS = NeedsParenthesesObject::class;

    public array $collection;

    public function __construct(null|array $items)
    {
        $this->collection = [];

        if ($items === null || !array_is_list($items)) {
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
}
