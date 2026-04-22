<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Helpers;

final class ArrayHelper
{
    /**
     * @param array<string, mixed> $array
     * @return array<string, mixed>
     */
    public static function formatDecimals(array $array, int $decimals = 2): array
    {
        foreach ($array as $key => $value) {
            if (is_numeric($value)) {
                $array[$key] = number_format((float) $value, $decimals, '.', '');
            }
        }

        return $array;
    }
}
