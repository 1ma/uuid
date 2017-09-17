<?php

declare(strict_types=1);

namespace UMA\Uuid\Tests\Unit;

use PHPUnit\Framework\TestCase;
use UMA\Uuid\CombGenerator;
use UMA\Uuid\Uuid;

class CombGeneratorTest extends TestCase
{
    public function testIt()
    {
        $seen = [];
        $comb = new CombGenerator;

        for ($i = 0; $i < 1000; $i++) {
            $str = $comb->generate()->asString();

            self::assertTrue(Uuid::isUuid($str));
            self::assertSame('4', $str[14], "Uuid version is not the expected '4': $str");
            self::assertContains($str[19], ['8', '9', 'a', 'b'], "Uuid variant is not the expected '10xx': $str");
            self::assertNotContains($str, $seen, "OMG FOUND A COLLISION: $str");

            $seen[] = $str;

            self::assertSame($str, max($seen));

            usleep(1);
        }
    }

    public function testInvalidGranularity()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$granularity must be in the [0, 6] range. Got: 10');

        new CombGenerator(10);
    }

    /**
     * @dataProvider overflowDatesProvider
     */
    public function testOverflowDates(int $granularity, string $correctOfDate)
    {
        $ofDate = (new CombGenerator($granularity))->getOverflowDate();

        self::assertSame($correctOfDate, $ofDate->format('Y-m-d H:i:s'));
    }

    public function overflowDatesProvider(): array
    {
        // Yes, 5 overflows sooner than 6 because 6 already overflowed over 48-bits
        // in the past (on 1978-12-02 19:29:36 UTC) and 5 still hasn't overflowed
        // as of 2017. Look up the last two examples of the procrust() method.

        // These test cases will start breaking once their dates are reached but
        // I'll be mush by then, and I don't care. The new dates returned by the
        // getOverflowDate() method will still be correct, though.

        return [
            [0, '8921556-12-07 10:44:15'],
            [1, '893928-09-11 01:04:25'],
            [2, '91165-11-14 07:18:26'],
            [3, '10889-08-02 05:31:50'],
            [4, '2861-12-16 05:21:11'],
            [5, '2059-03-13 02:56:07'],
            [6, '2112-09-17 23:53:47']
        ];
    }
}
