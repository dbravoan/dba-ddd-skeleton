<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Domain\ValueObject;

use Dba\DddSkeleton\Shared\Domain\ValueObject\UrlValueObject;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final readonly class TestUrlValueObject extends UrlValueObject {}

final class UrlValueObjectTest extends TestCase
{
    #[Test]
    public function it_should_store_a_valid_url(): void
    {
        $url = new TestUrlValueObject('https://example.com/path');

        $this->assertSame('https://example.com/path', $url->value());
    }

    #[Test]
    public function it_should_throw_on_invalid_url(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new TestUrlValueObject('not a url');
    }

    #[Test]
    public function it_should_throw_on_missing_scheme(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new TestUrlValueObject('example.com');
    }
}
