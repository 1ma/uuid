<?php

declare(strict_types=1);

namespace UMA\Uuid\Tests;

use PHPUnit\Framework\TestCase;
use UMA\Uuid\SequentialGenerator;

class SequentialGeneratorTest extends TestCase
{
    public function testVanillaUsage()
    {
        $sut = new SequentialGenerator();

        self::assertSame('00000000-0000-0000-0000-000000000000', (string) $sut->generate());
        self::assertSame('00000000-0000-0000-0000-000000000001', (string) $sut->generate());
        self::assertSame('00000000-0000-0000-0000-000000000002', (string) $sut->generate());
        self::assertSame('00000000-0000-0000-0000-000000000003', (string) $sut->generate());
    }

    public function testCustomMarksAndStarts()
    {
        $sut = new SequentialGenerator(15);

        self::assertSame('00000000-0000-000f-0000-000000000000', (string) $sut->generate());
        self::assertSame('00000000-0000-000f-0000-000000000001', (string) $sut->generate());
        self::assertSame('00000000-0000-000f-0000-000000000002', (string) $sut->generate());
        self::assertSame('00000000-0000-000f-0000-000000000003', (string) $sut->generate());

        $sut = new SequentialGenerator(0, 255);

        self::assertSame('00000000-0000-0000-0000-0000000000ff', (string) $sut->generate());
        self::assertSame('00000000-0000-0000-0000-000000000100', (string) $sut->generate());
        self::assertSame('00000000-0000-0000-0000-000000000101', (string) $sut->generate());
        self::assertSame('00000000-0000-0000-0000-000000000102', (string) $sut->generate());

        $sut = new SequentialGenerator(10, 10);

        self::assertSame('00000000-0000-000a-0000-00000000000a', (string) $sut->generate());
        self::assertSame('00000000-0000-000a-0000-00000000000b', (string) $sut->generate());
        self::assertSame('00000000-0000-000a-0000-00000000000c', (string) $sut->generate());
        self::assertSame('00000000-0000-000a-0000-00000000000d', (string) $sut->generate());
    }

    public function testOverflows()
    {
        $sut = new SequentialGenerator(0, PHP_INT_MAX);

        // After reaching PHP_INT_MAX the internal counter does not overflow.
        // Instead, it converts to a floating point number and the ticking stops.
        self::assertSame('00000000-0000-0000-7fff-ffffffffffff', (string) $sut->generate());
        self::assertSame('00000000-0000-0000-8000-000000000000', (string) $sut->generate());
        self::assertSame('00000000-0000-0000-8000-000000000000', (string) $sut->generate());
        self::assertSame('00000000-0000-0000-8000-000000000000', (string) $sut->generate());

        $sut = new SequentialGenerator(0, -2);

        self::assertSame('00000000-0000-0000-ffff-fffffffffffe', (string) $sut->generate());
        self::assertSame('00000000-0000-0000-ffff-ffffffffffff', (string) $sut->generate());
        self::assertSame('00000000-0000-0000-0000-000000000000', (string) $sut->generate());
        self::assertSame('00000000-0000-0000-0000-000000000001', (string) $sut->generate());
    }
}
