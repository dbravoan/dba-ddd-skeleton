<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Identity\User\Domain;

use Dba\DddSkeleton\Identity\User\Domain\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UserIdTest extends TestCase
{
    #[Test]
    public function it_should_store_a_uuid_value(): void
    {
        $id = new UserId('550e8400-e29b-41d4-a716-446655440000');

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $id->value());
    }

    #[Test]
    public function it_should_generate_a_random_uuid(): void
    {
        $id = UserId::random();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $id->value()
        );
    }

    #[Test]
    public function it_should_be_equal_to_another_with_same_value(): void
    {
        $a = new UserId('550e8400-e29b-41d4-a716-446655440000');
        $b = new UserId('550e8400-e29b-41d4-a716-446655440000');

        $this->assertTrue($a->equals($b));
    }

    #[Test]
    public function it_should_not_equal_a_different_id(): void
    {
        $a = UserId::random();
        $b = UserId::random();

        $this->assertFalse($a->equals($b));
    }
}
