<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Laravel;

use Dba\DddSkeleton\Shared\Domain\BadRequestDomainError;
use Dba\DddSkeleton\Shared\Domain\NotFoundDomainError;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use Illuminate\Routing\Controller as BaseController;

abstract class ApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function sendResponse(mixed $result, string $message): JsonResponse
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return new JsonResponse($response, 200);
    }

    /**
     * @param array<string, mixed> $errorMessages
     */
    public function sendError(string $error, array $errorMessages = [], int $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (! empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return new JsonResponse($response, $code);
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @param mixed $value
     * @return T|null
     */
    public function getValueObject(string $class, mixed $value): ?object
    {
        return empty($value) ? null : new $class($value);
    }

    /** @return array<class-string, int> */
    protected function exceptionHandler(): array
    {
        return [
            NotFoundDomainError::class   => 404,
            InvalidArgumentException::class => 422,
            BadRequestDomainError::class => 400,
        ];
    }
}
