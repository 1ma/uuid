<?php

declare(strict_types=1);

namespace UMA\Uuid;

/**
 * This generator implements the version 1 UUID generation algorithm
 * described in RFC 4122.
 *
 * @see https://tools.ietf.org/html/rfc4122#section-4.2
 */
final readonly class Version1Generator implements UuidGenerator
{
    /**
     * This is the number of 100-nanosecond intervals elapsed from
     *  1582-10-15 00:00:00 UTC -- introduction of the Gregorian calendar
     * to
     *  1970-01-01 00:00:00 UTC -- Unix epoch, reference point of PHP
     *
     * Check:
     *   $g = new \DateTimeImmutable('1582-10-15 00:00:00 UTC');
     *   var_dump($g->getTimestamp());
     *   var_dump($g->getTimestamp() * -10000000);
     */
    private const GREGORIAN_OFFSET = 122192928000000000;

    /**
     * Regular expression for matching MAC addresses.
     *
     * @example 01:23:45:67:89:ab
     */
    private const MAC_ADDR_FORMAT = '/^[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}$/i';

    private string $nodeID;

    /**
     * @param string $nodeID A valid MAC address.
     *
     * @throws \InvalidArgumentException When $nodeID is not a MAC address.
     * @throws \Exception                When PHP cannot gather enough entropy
     *                                   to generate a random clockSeq.
     */
    public function __construct(string $nodeID)
    {
        if (0 === \preg_match(self::MAC_ADDR_FORMAT, $nodeID)) {
            throw new \InvalidArgumentException('$nodeID is not a valid MAC address. Got: ' . $nodeID);
        }

        $this->nodeID = \str_replace(':', '', $nodeID);
    }

    public function generate(string $name = null): Uuid
    {
        $t = self::timestamp();

        $bytes = \pack(
            'NnnnH12',
            $t       & 0xffffffff,
            $t >> 32 & 0xffff,
            $t >> 48 & 0x0fff | 0x1000,
            \random_int(0, 0x3fff) | 0x8000,
            $this->nodeID
        );

        return Uuid::fromBytes($bytes);
    }

    /**
     * Returns the number of 100-nanosecond intervals elapsed since the
     * introduction of the Gregorian calendar (1582-10-15 00:00:00 UTC).
     *
     * Assuming the code runs on a 64bit build of PHP this method will not
     * break down until 30810-06-28 02:48:05 UTC. On that date PHP_INT_MAX
     * will be reached.
     *
     * Check:
     *  $maxT = (int)((PHP_INT_MAX - self::GREGORIAN_OFFSET)/10000000);
     *  var_dump(new \DateTimeImmutable("@$maxT UTC"));
     */
    private static function timestamp(): int
    {
        return self::GREGORIAN_OFFSET + (int)(10000000 * \microtime(true));
    }
}
