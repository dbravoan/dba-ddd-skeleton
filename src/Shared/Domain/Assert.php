<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain;

use InvalidArgumentException;

final class Assert
{
    /**
     * @param array<int, string> $classes
     * @param array<int, mixed> $items
     */
    public static function arrayOf(array $classes, array $items): void
    {
        foreach ($items as $item) {
            self::instanceOf($classes, $item);
        }
    }

    /**
     * @param array<int, string> $classes
     */
    public static function instanceOf(array $classes, mixed $item): void
    {
        $is_instance = false;

        foreach ($classes as $class) {
            if ($item instanceof $class) {
                $is_instance = true;
                break;
            }
        }

        // Si no es instancia de ninguno de los tipos permitidos, lanzamos una excepción
        if (! $is_instance) {
            throw new InvalidArgumentException(
                sprintf(
                    'The object <%s> is not an instance of any of the allowed types: [%s]',
                    is_object($item) ? $item::class : gettype($item),
                    implode(', ', $classes)
                )
            );
        }
    }
}
