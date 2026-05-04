<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Domain\ValueObject;

use Dba\DddSkeleton\Shared\Domain\ValueObject\EmailValueObject;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final readonly class TestEmailValueObject extends EmailValueObject {}

final class EmailValueObjectTest extends TestCase
{
    #[Test]
    public function it_should_store_a_valid_email(): void
    {
        $email = new TestEmailValueObject('user@example.com');

        $this->assertSame('user@example.com', $email->value());
    }

    #[Test]
    public function it_should_throw_on_invalid_email(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new TestEmailValueObject('not-valid');
    }

    #[Test]
    public function it_should_throw_on_empty_email(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new TestEmailValueObject('');
    }
}
