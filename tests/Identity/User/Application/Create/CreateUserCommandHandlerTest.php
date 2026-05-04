<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Identity\User\Application\Create;

use Dba\DddSkeleton\Identity\User\Application\Create\CreateUserCommand;
use Dba\DddSkeleton\Identity\User\Application\Create\CreateUserCommandHandler;
use Dba\DddSkeleton\Identity\User\Domain\User;
use Dba\DddSkeleton\Identity\User\Domain\UserCreatedDomainEvent;
use Dba\DddSkeleton\Identity\User\Domain\UserRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateUserCommandHandlerTest extends TestCase
{
    /** @var UserRepository&MockObject */
    private UserRepository $repository;

    private CreateUserCommandHandler $handler;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->handler = new CreateUserCommandHandler($this->repository);
    }

    #[Test]
    public function it_should_create_and_save_a_user(): void
    {
        $command = new CreateUserCommand(
            id: '550e8400-e29b-41d4-a716-446655440000',
            name: 'John Doe',
            email: 'john@example.com'
        );

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(User::class));

        ($this->handler)($command);
    }

    #[Test]
    public function it_should_record_a_user_created_domain_event(): void
    {
        $command = new CreateUserCommand(
            id: '550e8400-e29b-41d4-a716-446655440000',
            name: 'John Doe',
            email: 'john@example.com'
        );

        $savedUser = null;

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(function (User $user) use (&$savedUser): void {
                $savedUser = $user;
            });

        ($this->handler)($command);

        $this->assertNotNull($savedUser);
        $events = $savedUser->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(UserCreatedDomainEvent::class, $events[0]);
        $this->assertSame('john@example.com', $events[0]->email());
        $this->assertSame('John Doe', $events[0]->name());
    }
}
