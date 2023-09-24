<?php

declare(strict_types=1);

namespace UMA\Uuid\Tests;

use PHPUnit\Framework\TestCase;
use UMA\Uuid\Uuid;
use UMA\Uuid\Version5Generator;

class Version5GeneratorTest extends TestCase
{
    public function testIt(): void
    {
        $sut = new Version5Generator(Uuid::fromString(Version5Generator::NS_DNS));

        $uuid = $sut->generate('php.net');

        self::assertSame(
            'c4a760a8-dbcf-5254-a0d9-6a4474bd1b62', (string) $uuid,
            "\$str is not the expected 'c4a760a8-dbcf-5254-a0d9-6a4474bd1b62'"
        );
    }

    public function testMissingName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$name is mandatory. Got: NULL');

        $sut = new Version5Generator(Uuid::fromString(Version5Generator::NS_DNS));
        $sut->generate();
    }
}
