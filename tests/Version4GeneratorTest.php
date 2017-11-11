<?php

declare(strict_types=1);

namespace UMA\Uuid\Tests;

use PHPUnit\Framework\TestCase;
use UMA\Uuid\Uuid;
use UMA\Uuid\Version4Generator;

class Version4GeneratorTest extends TestCase
{
    public function testIt()
    {
        $seen = [];
        $v4 = new Version4Generator;

        for ($i = 0; $i < 1000; $i++) {
            $str = $v4->generate()->asString();

            self::assertTrue(Uuid::isUuid($str));
            self::assertSame('4', $str[14], "Uuid version is not the expected '4': $str");
            self::assertContains($str[19], ['8', '9', 'a', 'b'], "Uuid variant is not the expected '10xx': $str");
            self::assertNotContains($str, $seen, "OMG FOUND A COLLISION: $str");

            $seen[] = $str;
        }
    }
}
