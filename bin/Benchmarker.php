<?php

declare(strict_types=1);

namespace UMA\Uuid\Performance;

use UMA\Uuid\UuidGenerator;

/**
 * Simple test bench to see the approximate
 * throughput of a given UuidGenerator.
 */
class Benchmarker
{
    /**
     * @var UuidGenerator
     */
    private $benchmarked;

    public function __construct(UuidGenerator $generator)
    {
        $this->benchmarked = $generator;
    }

    /**
     * Calls $this->benchmarked->generate() continuously
     * for the supplied amount of seconds.
     *
     * Then reports total number of calls and avg throughput.
     */
    public function runFor(int $runTime)
    {
        $count = 0;
        $threshold = $runTime * 1000000;
        $start = self::currentTimeMicros();

        do {
            $count++;
            $this->benchmarked->generate('foo');
        } while (self::currentTimeMicros() - $start < $threshold);

        echo \sprintf(
            "%s: %s UUIDs in %s seconds (%s op/s)\n",
            \get_class($this->benchmarked),
            $count,
            $runTime,
            \number_format($count/$runTime, 1)
        );
    }

    private static function currentTimeMicros(): int
    {
        return (int)(\microtime(true) * 1000000);
    }
}
