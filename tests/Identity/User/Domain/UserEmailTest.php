<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Identity\User\Domain;

use Dba\DddSkeleton\Identity\User\Domain\UserEmail;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UserEmailTest extends TestCase
{
    #[Test]
    public function it_should_store_a_valid_email(): void
    {
        $email = new UserEmail('john@example.com');

        $this->assertSame('john@example.com', $email->value());
    }

    #[Test]
    public function it_should_throw_on_invalid_email(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new UserEmail('not-an-email');
    }

    #[Test]
    public function it_should_throw_on_missing_domain(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new UserEmail('user@');
    }
}
