<?php

declare(strict_types=1);

namespace UMA\Uuid\Tests;

use PHPUnit\Framework\TestCase;
use UMA\Uuid\Uuid;
use UMA\Uuid\Version1Generator;

class Version1GeneratorTest extends TestCase
{
    public function testIt()
    {
        $seen = [];
        $v1 = new Version1Generator('01:23:45:67:89:ab');

        for ($i = 0; $i < 1000; $i++) {
            $str = $v1->generate()->asString();

            self::assertTrue(Uuid::isUuid($str));
            self::assertSame('1', $str[14], "Uuid version is not the expected '1': $str");
            self::assertContains($str[19], ['8', '9', 'a', 'b'], "Uuid variant is not the expected '10xx': $str");
            self::assertNotContains($str, $seen, "OMG FOUND A COLLISION: $str");

            $seen[] = $str;
        }
    }

    public function testInvalidMACAddress()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$nodeID is not a valid MAC address. Got: wh:at:is:th:is:?!');

        new Version1Generator('wh:at:is:th:is:?!');
    }
}
