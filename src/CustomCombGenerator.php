<?php

declare(strict_types=1);

namespace UMA\Uuid;

class CustomCombGenerator implements UuidGenerator
{
    /**
     * @var UuidGenerator
     */
    private $generator;

    /**
     * @var int
     */
    private $exponent;

    /**
     * @var int
     */
    private $epoch;

    /**
     * @var int
     */
    private $span;

    /**
     * @param UuidGenerator      $generator
     * @param \DateTimeImmutable $epoch
     * @param int                $span
     * @param int                $granularity
     *
     * @throws \InvalidArgumentException When $epoch is a date in the future.
     * @throws \InvalidArgumentException When $span is outside the [1, 16] range.
     * @throws \InvalidArgumentException When $granularity is outside the [0, 6] range.
     */
    public function __construct(UuidGenerator $generator, \DateTimeImmutable $epoch, int $span, int $granularity)
    {
        if (new \DateTimeImmutable('now') < $epoch) {
            throw new \InvalidArgumentException('$epoch must be in the past. Got timestamp: ' . $epoch->getTimestamp());
        }

        if (!\in_array($span, \range(1, 16), true)) {
            throw new \InvalidArgumentException('$span must be in the [1, 16] range. Got: ' . $span);
        }

        if (!\in_array($granularity, \range(0, 6), true)) {
            throw new \InvalidArgumentException('$granularity must be in the [0, 6] range. Got: ' . $granularity);
        }

        $this->generator = $generator;
        $this->exponent = 10 ** $granularity;
        $this->epoch = $epoch->getTimestamp() * $this->exponent;
        $this->span = 2 * $span;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(string $name = null): Uuid
    {
        $head = $this->procrust($this->timestamp());
        $tail = \substr($this->generator->generate()->asBytes(), -16 + ($this->span / 2));

        return Uuid::fromBytes($head . $tail);
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
        $maxTimestamp = (int)(($this->maxTimestamp() - $this->epoch)/$this->exponent);

        return new \DateTimeImmutable("@$maxTimestamp UTC");
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
    private function procrust(string $timestamp): string
    {
        return \pack("H{$this->span}", \str_pad(\substr($timestamp, 0, $this->span), $this->span, '0', STR_PAD_LEFT));
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
        return \dechex((int)(\microtime(true) * $this->exponent) - $this->epoch);
    }

    private function maxTimestamp(): int
    {
        return \hexdec(\str_repeat('f', \max($this->span, \strlen($this->timestamp()))));
    }
}
