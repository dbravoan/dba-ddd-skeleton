<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Helpers;

class ArrayHelper
{

    /**
     * Formatea todos los valores numéricos con decimales a dos decimales en un array.
     *
     * @param array $array
     * @return void
     */
    public static function formatDecimals(&$array)
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                self::formatDecimals($value);
            } else {
                if (is_numeric($value)) {
                    $stringValue = (string) $value;
                    if (strpos($stringValue, '.') !== false) {
                        $value = number_format((float)$value, 2, '.', '');
                    }
                }
            }
        }
    }
}
