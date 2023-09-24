<?php

declare(strict_types=1);

namespace UMA\Uuid\Tests;

use PHPUnit\Framework\TestCase;
use UMA\Uuid\Uuid;
use UMA\Uuid\Version4Generator;

final class UuidTest extends TestCase
{
    public function testNilFactory(): void
    {
        self::assertSame('00000000-0000-0000-0000-000000000000', Uuid::nil()->asString());
    }

    public function testAlias(): void
    {
        $uuid = (new Version4Generator)->generate();

        self::assertSame($uuid->asString(), (string) $uuid);
    }

    public function testEquivalence(): void
    {
        $uuid1 = (new Version4Generator)->generate();
        $uuid2 = Uuid::fromString($uuid1->asString());

        self::assertEquals($uuid1, $uuid2);
    }

    public function testCaseInsensitivity(): void
    {
        $uuid1 = Uuid::fromString('00112233-4455-6677-8899-aabbccddeeff');
        $uuid2 = Uuid::fromString('00112233-4455-6677-8899-AABBCCDDEEFF');

        self::assertEquals($uuid1, $uuid2);
    }

    public function testInvalidBytesInput(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Length of $bytes for new Uuid is not 16. Got: 0x61626364');

        Uuid::fromBytes('abcd');
    }

    public function testInvalidTextInput(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$text is not a valid Uuid. Got: a-b-cd-');

        Uuid::fromString('a-b-cd-');
    }
}
