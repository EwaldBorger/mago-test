<?php declare(strict_types=1);

// Disclaimer: no this class is not very sensible, it is a piece of legacy code somewhere doing some XML handling

namespace Ewald\MagoTest;

class ImpossibleCondition
{
    public function example($array)
    {
        if (!is_array($array)) {
            return $array;
        }
        foreach ($array as $key => $value) {
            if (is_numeric($key) && is_array($value) && count($value) === 1) {
                return true;
            }
        }

        return false;
    }
}
