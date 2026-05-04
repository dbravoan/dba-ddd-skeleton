<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Domain;

use Dba\DddSkeleton\Shared\Domain\Assert;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

final class AssertTest extends TestCase
{
    #[Test]
    public function it_should_pass_when_all_items_match_the_allowed_type(): void
    {
        $items = [new stdClass, new stdClass];

        // No exception thrown
        Assert::arrayOf([stdClass::class], $items);
        $this->assertTrue(true);
    }

    #[Test]
    public function it_should_throw_when_an_item_does_not_match(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Assert::arrayOf([stdClass::class], [new stdClass, 'not-an-object']);
    }

    #[Test]
    public function it_should_pass_with_multiple_allowed_types(): void
    {
        $items = [new stdClass, new \DateTime];

        Assert::arrayOf([stdClass::class, \DateTime::class], $items);
        $this->assertTrue(true);
    }

    #[Test]
    public function it_should_pass_with_empty_array(): void
    {
        Assert::arrayOf([stdClass::class], []);
        $this->assertTrue(true);
    }

    #[Test]
    public function it_should_throw_instance_of_for_wrong_type(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Assert::instanceOf([stdClass::class], 'a string');
    }
}
