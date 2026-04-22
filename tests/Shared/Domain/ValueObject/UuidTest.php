<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Domain\ValueObject;

use Dba\DddSkeleton\Shared\Domain\ValueObject\Uuid;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class UuidTest extends TestCase
{
    /** @test */
    public function it_should_create_a_valid_uuid(): void
    {
        $uuidValue = '00000000-0000-0000-0000-000000000000';
        $uuid = new Uuid($uuidValue);

        $this->assertEquals($uuidValue, $uuid->value());
        $this->assertEquals($uuidValue, (string) $uuid);
    }

    /** @test */
    public function it_should_throw_exception_for_invalid_uuid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Uuid('invalid-uuid');
    }

    /** @test */
    public function it_should_generate_a_random_uuid(): void
    {
        $uuid = Uuid::random();
        $this->assertInstanceOf(Uuid::class, $uuid);
    }

    /** @test */
    public function it_should_be_equal_to_another_uuid_with_same_value(): void
    {
        $uuidValue = '00000000-0000-0000-0000-000000000000';
        $uuid1 = new Uuid($uuidValue);
        $uuid2 = new Uuid($uuidValue);

        $this->assertTrue($uuid1->equals($uuid2));
    }
}
