<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Domain;

use Dba\DddSkeleton\Shared\Domain\DomainError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DomainErrorTest extends TestCase
{
    #[Test]
    public function it_should_use_error_message_as_exception_message(): void
    {
        $error = new class extends DomainError
        {
            public function errorCode(): string
            {
                return 'test_error';
            }

            protected function errorMessage(): string
            {
                return 'Something went wrong';
            }
        };

        $this->assertSame('Something went wrong', $error->getMessage());
    }

    #[Test]
    public function it_should_expose_error_code(): void
    {
        $error = new class extends DomainError
        {
            public function errorCode(): string
            {
                return 'my_code';
            }

            protected function errorMessage(): string
            {
                return 'msg';
            }
        };

        $this->assertSame('my_code', $error->errorCode());
    }
}
