<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Domain;

use Dba\DddSkeleton\Shared\Domain\NotFoundDomainError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NotFoundDomainErrorTest extends TestCase
{
    #[Test]
    public function it_should_return_not_found_error_code(): void
    {
        $error = new class extends NotFoundDomainError
        {
            protected function errorMessage(): string
            {
                return 'Resource not found';
            }
        };

        $this->assertSame('not_found', $error->errorCode());
    }

    #[Test]
    public function it_should_use_error_message_as_exception_message(): void
    {
        $error = new class extends NotFoundDomainError
        {
            protected function errorMessage(): string
            {
                return 'Resource not found';
            }
        };

        $this->assertSame('Resource not found', $error->getMessage());
    }
}
