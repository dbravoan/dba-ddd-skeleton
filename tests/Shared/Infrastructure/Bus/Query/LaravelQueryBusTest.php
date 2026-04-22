<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Infrastructure\Bus\Query;

use Dba\DddSkeleton\Shared\Domain\Bus\Query\Query;
use Dba\DddSkeleton\Shared\Domain\Bus\Query\Response;
use Dba\DddSkeleton\Shared\Infrastructure\Bus\Query\LaravelQueryBus;
use Dba\DddSkeleton\Shared\Infrastructure\Bus\Query\QueryNotRegisteredError;
use PHPUnit\Framework\TestCase;

final class LaravelQueryBusTest extends TestCase
{
    /** @test */
    public function it_should_dispatch_a_query_to_its_handler_and_return_response(): void
    {
        $query = new StubQuery;
        $response = new StubResponse;
        $handler = new StubQueryHandler($response);
        $bus = new LaravelQueryBus([$handler]);

        $result = $bus->ask($query);

        $this->assertSame($response, $result);
    }

    /** @test */
    public function it_should_throw_exception_if_handler_is_not_registered(): void
    {
        $this->expectException(QueryNotRegisteredError::class);

        $query = new StubQuery;
        $bus = new LaravelQueryBus([]);

        $bus->ask($query);
    }
}

final class StubQuery implements Query {}

final class StubResponse implements Response {}

final class StubQueryHandler
{
    public function __construct(private Response $response) {}

    public function __invoke(StubQuery $query): Response
    {
        return $this->response;
    }
}
