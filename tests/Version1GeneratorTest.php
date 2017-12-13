<?php

declare(strict_types=1);

namespace UMA\Uuid\Tests;

use PHPUnit\Framework\TestCase;
use UMA\Uuid\Uuid;
use UMA\Uuid\Version1Generator;

class Version1GeneratorTest extends TestCase
{
    public function testValidMACAddress()
    {
        $sut = new Version1Generator('01:23:45:67:89:ab');

        self::assertTrue(Uuid::isUuid((string) $sut->generate()));
    }

    public function testInvalidMACAddress()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$nodeID is not a valid MAC address. Got: wh:at:is:th:is:?!');

        new Version1Generator('wh:at:is:th:is:?!');
    }
}
