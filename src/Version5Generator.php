<?php

declare(strict_types=1);

namespace UMA\Uuid;

/**
 * This generator implements the version 5 UUID generation algorithm
 * described in RFC 4122.
 *
 * @see https://tools.ietf.org/html/rfc4122#section-4.3
 */
final readonly class Version5Generator implements UuidGenerator
{
    /**
     * These are a few well known Uuids listed in Appendix C
     * of RFC 4122 to be used as namespace identifiers.
     */
    public const NS_DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';
    public const NS_URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';
    public const NS_OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';
    public const NS_X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';

    private string $nsBytes;

    public function __construct(Uuid $namespace)
    {
        $this->nsBytes = $namespace->asBytes();
    }

    public function generate(string $name = null): Uuid
    {
        if (null === $name) {
            throw new \InvalidArgumentException('$name is mandatory. Got: NULL');
        }

        // Use the 16 most significant octets of the hash as the basis for the new Uuid.
        $bytes = \unpack('C16', \sha1($this->nsBytes . $name, true));

        // Set the four most significant bits (bits 12 through 15) of the
        // time_hi_and_version field to the appropriate 4-bit version number
        // from Section 4.1.3 (these are 0101 for v5)
        $bytes[7] &= 0b01011111;
        $bytes[7] |= 0b01010000;

        // Set the two most significant bits (bits 6 and 7) of the
        // clock_seq_hi_and_reserved to zero and one, respectively.
        $bytes[9] &= 0b10111111;
        $bytes[9] |= 0b10000000;

        return Uuid::fromBytes(\pack('C16', ...$bytes));
    }
}
