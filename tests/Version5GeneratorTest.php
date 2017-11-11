<?php

declare(strict_types=1);

namespace UMA\Uuid\Tests;

use PHPUnit\Framework\TestCase;
use UMA\Uuid\Uuid;
use UMA\Uuid\Version5Generator;

class Version5GeneratorTest extends TestCase
{
    public function testIt()
    {
        $v5 = new Version5Generator(Uuid::fromString(Version5Generator::NS_DNS));

        self::assertSame(
            'c4a760a8-dbcf-5254-a0d9-6a4474bd1b62',
            $v5->generate('php.net')->asString()
        );
    }

    public function testMissingName()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$name is mandatory. Got: NULL');

        $v5 = new Version5Generator(Uuid::fromString(Version5Generator::NS_DNS));
        $v5->generate();
    }
}
