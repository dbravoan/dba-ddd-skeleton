<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Persistence\QueryBuilder;

use BadMethodCallException;
use Closure;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * @mixin QueryBuilder
 *
 * @phpstan-consistent-constructor
 */
class QueryBuilderCriteria
{
    /** @var array<int, class-string> */
    protected array $proxies = [
        QueryBuilder::class,
    ];

    /**
     * @var Method[]
     */
    protected array $methods = [];

    /** @var array<string, array<string, int>> */
    private static array $cache = [];

    /**
     * Handle dynamic method calls into the method.
     *
     * @param  string  $method
     * @param  array<mixed>  $parameters
     * @return $this
     *
     * @throws BadMethodCallException
     * @throws ReflectionException
     */
    public function __call($method, $parameters)
    {
        $hasMethod = $this->hasMethod($method);
        if ($hasMethod) {
            $this->methods[] = new Method($method, $parameters);

            return $this;
        }

        if (Str::startsWith($method, 'where')) {
            $this->methods[] = new Method($method, $parameters);

            return $this;
        }

        $className = static::class;

        throw new BadMethodCallException("Call to undefined method {$className}::{$method}()");
    }

    /**
     * create.
     */
    public static function create(): static
    {
        return new static;
    }

    /**
     * alias raw.
     */
    public static function expr(float|int|string|\Stringable $value): Expression
    {
        return static::raw($value);
    }

    public static function raw(float|int|string|\Stringable $value): Expression
    {
        return new Expression(is_string($value) ? $value : (string) $value);
    }

    /**
     * each.
     */
    public function each(Closure $callback): void
    {
        foreach ($this->methods as $method) {
            $callback($method);
        }
    }

    /**
     * toArray.
     *
     * @return array<int, array{method: string, parameters: array<mixed>}>
     */
    public function toArray(): array
    {
        return array_map(static function ($method) {
            /** @var Method $method */
            return [
                'method' => $method->name,
                'parameters' => (array) $method->parameters,
            ];
        }, $this->methods);
    }

    /**
     * Begin querying the model on the write connection.
     *
     * @return $this
     */
    public function onWriteConnection(): self
    {
        /** @var callable $callable */
        $callable = [$this, 'useWritePdo'];
        $callable();

        return $this;
    }

    /**
     * @param  class-string|object  $class
     * @return array<string, int>
     *
     * @throws ReflectionException
     */
    private function findMethods(string|object $class): array
    {
        $cacheKey = is_object($class) ? $class::class : $class;
        if (array_key_exists($cacheKey, self::$cache)) {
            return self::$cache[$cacheKey];
        }

        $ref = new ReflectionClass($class);

        /** @var array<string, int> $methods */
        $methods = array_flip(array_map(static function (ReflectionMethod $method) {
            return $method->getName();
        }, $ref->getMethods(ReflectionMethod::IS_PUBLIC)));

        return self::$cache[$cacheKey] = $methods;
    }

    /**
     * @throws ReflectionException
     */
    private function hasMethod(string $method): bool
    {
        foreach ($this->proxies as $proxy) {
            $methods = $this->findMethods($proxy);
            if (array_key_exists($method, $methods)) {
                return true;
            }
        }

        return false;
    }
}
