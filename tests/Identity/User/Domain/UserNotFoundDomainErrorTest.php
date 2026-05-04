<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Identity\User\Domain;

use Dba\DddSkeleton\Identity\User\Domain\UserNotFoundDomainError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UserNotFoundDomainErrorTest extends TestCase
{
    #[Test]
    public function it_should_have_not_found_error_code(): void
    {
        $error = new UserNotFoundDomainError('some-uuid');

        $this->assertSame('not_found', $error->errorCode());
    }

    #[Test]
    public function it_should_include_user_id_in_message(): void
    {
        $userId = 'abc-123';
        $error = new UserNotFoundDomainError($userId);

        $this->assertStringContainsString($userId, $error->getMessage());
    }
}
