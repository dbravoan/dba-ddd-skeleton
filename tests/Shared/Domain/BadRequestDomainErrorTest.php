<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Domain;

use Dba\DddSkeleton\Shared\Domain\BadRequestDomainError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class BadRequestDomainErrorTest extends TestCase
{
    #[Test]
    public function it_should_return_bad_request_error_code(): void
    {
        $error = new class extends BadRequestDomainError
        {
            protected function errorMessage(): string
            {
                return 'Invalid input';
            }
        };

        $this->assertSame('bad_request', $error->errorCode());
    }

    #[Test]
    public function it_should_use_error_message_as_exception_message(): void
    {
        $error = new class extends BadRequestDomainError
        {
            protected function errorMessage(): string
            {
                return 'Invalid input';
            }
        };

        $this->assertSame('Invalid input', $error->getMessage());
    }
}
