<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Infrastructure\Laravel;

use Dba\DddSkeleton\Shared\Domain\NotFoundDomainError;
use Dba\DddSkeleton\Shared\Infrastructure\Laravel\ApiController;
use Dba\DddSkeleton\Tests\DbaTestCase;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;

final class ApiControllerTest extends DbaTestCase
{
    private ApiController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new class extends ApiController {};
    }

    #[Test]
    public function it_should_send_a_success_response(): void
    {
        $response = $this->controller->sendResponse(['key' => 'value'], 'OK');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());

        /** @var array<string, mixed> $data */
        $data = $response->getData(true);
        $this->assertTrue($data['success']);
        $this->assertSame('OK', $data['message']);
    }

    #[Test]
    public function it_should_send_an_error_response(): void
    {
        $response = $this->controller->sendError('Not Found', [], 404);

        $this->assertSame(404, $response->getStatusCode());

        /** @var array<string, mixed> $data */
        $data = $response->getData(true);
        $this->assertFalse($data['success']);
        $this->assertSame('Not Found', $data['message']);
    }

    #[Test]
    public function it_should_catch_not_found_domain_error_and_return_404(): void
    {
        $error = new class extends NotFoundDomainError
        {
            protected function errorMessage(): string
            {
                return 'Resource not found';
            }
        };

        $response = $this->controller->run(fn () => throw $error);

        $this->assertNotNull($response);
        $this->assertSame(404, $response->getStatusCode());
    }

    #[Test]
    public function it_should_catch_invalid_argument_exception_and_return_422(): void
    {
        $response = $this->controller->run(fn () => throw new InvalidArgumentException('Bad input'));

        $this->assertNotNull($response);
        $this->assertSame(422, $response->getStatusCode());
    }

    #[Test]
    public function it_should_return_null_when_action_succeeds(): void
    {
        $result = $this->controller->run(fn () => null);

        $this->assertNull($result);
    }

    #[Test]
    public function it_should_rethrow_unmapped_exceptions(): void
    {
        $this->expectException(RuntimeException::class);

        $this->controller->run(fn () => throw new RuntimeException('Unexpected error'));
    }
}
