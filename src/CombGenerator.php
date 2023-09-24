<?php

declare(strict_types=1);

namespace UMA\Uuid;

/**
 * @see http://www.informit.com/articles/article.aspx?p=25862
 */
final readonly class CombGenerator implements UuidGenerator
{
    private int $exponent;
    private Version4Generator $v4;

    /**
     * @param int $granularity precision of the timestamps, ranging from second up to microsecond
     *
     * @throws \InvalidArgumentException when $granularity is outside the [0, 6] range
     */
    public function __construct(int $granularity = 6)
    {
        if (!\in_array($granularity, range(0, 6), true)) {
            throw new \InvalidArgumentException('$granularity must be in the [0, 6] range. Got: '.$granularity);
        }

        $this->exponent = 10 ** $granularity;
        $this->v4 = new Version4Generator();
    }

    /**
     * @throws \Exception if $v4->generate() fails due to exhausted entropy
     */
    public function generate(string $name = null): Uuid
    {
        $head = self::procrust($this->timestamp());
        $tail = substr($this->v4->generate()->asBytes(), -10);

        return Uuid::fromBytes($head.$tail);
    }

    /**
     * Returns the exact date on which the 48 most significant bits of
     * the UUIDs will overflow for the chosen $granularity.
     *
     * The higher the granularity the better is the output of the
     * generator, but the overflow date also looms sooner.
     */
    public function getOverflowDate(): \DateTimeImmutable
    {
        $fullTimestampLength = \strlen($this->timestamp());
        $choppedDigits = $fullTimestampLength < 12 ? 0 : $fullTimestampLength - 12;
        $maxTimestamp = (int) (hexdec(str_pad('', 12 + $choppedDigits, 'f')) / $this->exponent);

        return new \DateTimeImmutable("@$maxTimestamp UTC");
    }

    /**
     * Returns the current unix timestamp as a hex-encoded string (that is, each character
     * encodes 4 bits) with variable precision, ranging from second to microsecond.
     *
     * The length of the string varies depending on the $granularity chosen. This is how
     * the exact same reading from microtime() looks like for all 7 possible granularity
     * levels (0 through 6):
     *
     * @example '59b7d71f'
     * @example '3812e6738'
     * @example '230bd00838'
     * @example '15e76205236'
     * @example 'db09d433621'
     * @example '88e624a01d4c'
     * @example '558fd6e4124fb'
     */
    private function timestamp(): string
    {
        return dechex((int) (microtime(true) * $this->exponent));
    }

    /**
     * Returns $timestamp "procrusted" to 6 bytes.
     *
     * If the timestamp is smaller than 6 bytes, leading 0 bits are appended.
     * If the timestamp is larger than 6 bytes, its least significant bits are chopped off.
     *
     * The returned string is raw binary (each character encodes 8 bits)
     * and has always the same size -- 6 bytes.
     *
     * @example '59b7d71f'      => 0x000059b7d71f
     * @example '3812e6738'     => 0x0003812e6738
     * @example '230bd00838'    => 0x00230bd00838
     * @example '15e76205236'   => 0x015e76205236
     * @example 'db09d433621'   => 0x0db09d433621
     * @example '88e624a01d4c'  => 0x88e624a01d4c
     * @example '558fd6e4124fb' => 0x558fd6e4124f
     */
    private static function procrust(string $timestamp): string
    {
        return pack('H12', str_pad(substr($timestamp, 0, 12), 12, '0', \STR_PAD_LEFT));
    }
}
