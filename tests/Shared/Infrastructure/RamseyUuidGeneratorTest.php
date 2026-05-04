<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Infrastructure;

use Dba\DddSkeleton\Shared\Infrastructure\RamseyUuidGenerator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RamseyUuidGeneratorTest extends TestCase
{
    #[Test]
    public function it_should_generate_a_valid_uuid_v4(): void
    {
        $generator = new RamseyUuidGenerator;
        $uuid = $generator->generate();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $uuid
        );
    }

    #[Test]
    public function it_should_generate_unique_uuids(): void
    {
        $generator = new RamseyUuidGenerator;

        $this->assertNotSame($generator->generate(), $generator->generate());
    }
}
