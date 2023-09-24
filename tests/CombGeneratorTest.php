<?php

declare(strict_types=1);

namespace UMA\Uuid\Tests;

use PHPUnit\Framework\TestCase;
use UMA\Uuid\CombGenerator;

class CombGeneratorTest extends TestCase
{
    public function testInvalidGranularity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$granularity must be in the [0, 6] range. Got: 10');

        new CombGenerator(10);
    }

    /**
     * @dataProvider overflowDatesProvider
     */
    public function testOverflowDates(int $granularity, string $expectedOverflowDate): void
    {
        $overflowDate = (new CombGenerator($granularity))->getOverflowDate();

        self::assertSame($expectedOverflowDate, $overflowDate->format('Y-m-d H:i:s'));
    }

    public static function overflowDatesProvider(): array
    {
        // Yes, 5 overflows sooner than 6 because 6 already overflowed over 48-bits
        // in the past (on 1978-12-02 19:29:36 UTC) and 5 still hasn't overflowed
        // yet as of 2017. Look up the examples of the procrust() method on the
        // CombGenerator class to see how it happens.

        // These test cases will start breaking once their dates are reached but
        // I hope PHP is not even used anymore by 2059. The new dates returned by
        // the getOverflowDate() method will still be correct, though.

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
