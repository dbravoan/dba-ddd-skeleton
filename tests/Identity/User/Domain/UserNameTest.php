<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Identity\User\Domain;

use Dba\DddSkeleton\Identity\User\Domain\UserName;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UserNameTest extends TestCase
{
    #[Test]
    public function it_should_store_the_name_value(): void
    {
        $name = new UserName('John Doe');

        $this->assertSame('John Doe', $name->value());
    }

    #[Test]
    public function it_should_create_from_static_factory(): void
    {
        $name = UserName::from('Jane');

        $this->assertSame('Jane', $name->value());
    }
}
