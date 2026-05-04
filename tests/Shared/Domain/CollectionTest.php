<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Domain;

use Dba\DddSkeleton\Shared\Domain\Collection;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

final class CollectionTest extends TestCase
{
    #[Test]
    public function it_should_store_and_return_items(): void
    {
        $a = new stdClass;
        $b = new stdClass;

        $collection = new class([$a, $b]) extends Collection
        {
            protected function type(): array
            {
                return [stdClass::class];
            }
        };

        $this->assertSame([$a, $b], $collection->items());
    }

    #[Test]
    public function it_should_count_items(): void
    {
        $collection = new class([new stdClass, new stdClass, new stdClass]) extends Collection
        {
            protected function type(): array
            {
                return [stdClass::class];
            }
        };

        $this->assertSame(3, $collection->count());
    }

    #[Test]
    public function it_should_be_iterable(): void
    {
        $items = [new stdClass, new stdClass];
        $collection = new class($items) extends Collection
        {
            protected function type(): array
            {
                return [stdClass::class];
            }
        };

        $iterated = [];
        foreach ($collection as $item) {
            $iterated[] = $item;
        }

        $this->assertSame($items, $iterated);
    }

    #[Test]
    public function it_should_reject_wrong_type(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new class(['not-an-object']) extends Collection
        {
            protected function type(): array
            {
                return [stdClass::class];
            }
        };
    }
}
