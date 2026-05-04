<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Domain;

use DateTimeImmutable;
use DateTimeInterface;
use Dba\DddSkeleton\Shared\Domain\Utils;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class UtilsTest extends TestCase
{
    #[Test]
    public function it_should_convert_to_snake_case(): void
    {
        $this->assertSame('my_class_name', Utils::toSnakeCase('MyClassName'));
        $this->assertSame('already_snake', Utils::toSnakeCase('already_snake'));
    }

    #[Test]
    public function it_should_convert_to_camel_case(): void
    {
        $this->assertSame('myClassName', Utils::toCamelCase('my_class_name'));
    }

    #[Test]
    public function it_should_encode_and_decode_json(): void
    {
        $data = ['key' => 'value', 'num' => 42];
        $json = Utils::jsonEncode($data);
        $decoded = Utils::jsonDecode($json);

        $this->assertSame($data, $decoded);
    }

    #[Test]
    public function it_should_throw_on_invalid_json_decode(): void
    {
        $this->expectException(RuntimeException::class);

        Utils::jsonDecode('{invalid json}');
    }

    #[Test]
    public function it_should_convert_date_to_string_and_back(): void
    {
        $date = new DateTimeImmutable('2024-01-15T10:30:00+00:00');
        $string = Utils::dateToString($date);
        $back = Utils::stringToDate($string);

        $this->assertSame(
            $date->format(DateTimeInterface::ATOM),
            $back->format(DateTimeInterface::ATOM)
        );
    }

    #[Test]
    public function it_should_flatten_array_with_dot_notation(): void
    {
        $array = ['a' => ['b' => ['c' => 'value']]];
        $flat = Utils::dot($array);

        $this->assertSame(['a.b.c' => 'value'], $flat);
    }

    #[Test]
    public function it_should_check_ends_with(): void
    {
        $this->assertTrue(Utils::endsWith('Handler', 'CreateUserCommandHandler'));
        $this->assertFalse(Utils::endsWith('Handler', 'CreateUserCommand'));
        $this->assertTrue(Utils::endsWith('', 'anything'));
    }

    #[Test]
    public function it_should_extract_class_name(): void
    {
        $object = new \stdClass;
        $this->assertSame('stdClass', Utils::extractClassName($object));
    }

    #[Test]
    public function it_should_convert_iterable_to_array(): void
    {
        $generator = (function () {
            yield 1;
            yield 2;
        })();

        $this->assertSame([1, 2], Utils::iterableToArray($generator));
        $this->assertSame([3, 4], Utils::iterableToArray([3, 4]));
    }
}
