<?php

declare(strict_types=1);

namespace UMA\Uuid;

/**
 * This generator implements the version 1 UUID generation algorithm
 * described in RFC 4122.
 *
 * @see https://tools.ietf.org/html/rfc4122#section-4.2
 */
class Version1Generator implements UuidGenerator
{
    /**
     * This is the number of 100-nanosecond intervals elapsed from
     *  1582-10-15 00:00:00 UTC -- introduction of the Gregorian calendar
     * to
     *  1970-01-01 00:00:00 UTC -- Unix epoch, used by microtime()
     */
    const GREGORIAN_OFFSET = 122192928000000000;

    /**
     * Regular expression for matching MAC addresses.
     *
     * @example 01:23:45:67:89:ab
     */
    const MAC_ADDR_FORMAT = '/^[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}$/i';

    /**
     * @var string
     */
    private $nodeID;

    /**
     * @var int
     */
    private $clockSeq;

    /**
     * @param string $nodeID A valid MAC address.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $nodeID)
    {
        if (0 === preg_match(self::MAC_ADDR_FORMAT, $nodeID)) {
            throw new \InvalidArgumentException('$nodeID is not a valid MAC address. Got: ' . $nodeID);
        }

        $this->nodeID = str_replace(':', '', $nodeID);
        $this->clockSeq = random_int(0, 0x3fff) | 0x8000;
    }

    public function generate(string $name = null): Uuid
    {
        $t = $this->timestamp();

        $bytes = pack(
            'NnnnH12',
            $t       & 0xffffffff,
            $t >> 32 & 0xffff,
            $t >> 48 & 0x0fff | 0x1000,
            $this->clockSeq,
            $this->nodeID
        );

        return Uuid::fromBytes($bytes);
    }

    /**
     * Returns the number of 100-nanosecond intervals elapsed since the
     * introduction of the Gregorian calendar (1582-10-15 00:00:00 UTC).
     */
    private function timestamp(): int
    {
        return self::GREGORIAN_OFFSET + (int)(10000000 * microtime(true));
    }
}
