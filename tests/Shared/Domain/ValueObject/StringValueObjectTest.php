<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Domain\ValueObject;

use Dba\DddSkeleton\Shared\Domain\ValueObject\StringValueObject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class StringValueObjectTest extends TestCase
{
    #[Test]
    public function it_should_store_a_string_value(): void
    {
        $value = 'test-value';
        $stringValueObject = new StubAnonymousStringValueObject($value);

        $this->assertEquals($value, $stringValueObject->value());
    }

    #[Test]
    public function it_should_be_creatable_via_from_method(): void
    {
        $value = 'test-value';
        $stringValueObject = StubStringValueObject::from($value);

        $this->assertInstanceOf(StubStringValueObject::class, $stringValueObject);
        $this->assertEquals($value, $stringValueObject->value());
    }
}

final readonly class StubAnonymousStringValueObject extends StringValueObject {}

final readonly class StubStringValueObject extends StringValueObject {}
