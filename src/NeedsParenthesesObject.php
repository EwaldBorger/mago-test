<?php declare(strict_types=1);

namespace Ewald\MagoTest;

class NeedsParenthesesObject
{
    private string $name;

    public function __construct(array $fields)
    {
        $this->name = $fields['name'];
    }
}
