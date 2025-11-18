<?php declare(strict_types=1);

namespace Ewald\MagoTest;

class RedundantLogicalOperation
{
    /**
     * @param array<array{name?: string}> $knownModels
     */
    public function fromArrayRight(array $knownModels): array
    {
        $collection = [];
        foreach ($knownModels as $model) {
            if (!is_array($model) || !array_key_exists('name', $model)) {
                continue;
            }

            $collection[] = $model;
        }

        return $collection;
    }

    /**
     * @param array<array{name?: string}> $knownModels
     */
    public function fromArrayLeft(array $knownModels): array
    {
        $collection = [];
        foreach ($knownModels as $model) {
            if (!array_key_exists('name', $model) || !is_array($model)) {
                continue;
            }

            $collection[] = $model;
        }

        return $collection;
    }
}
