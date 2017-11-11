<?php

declare(strict_types=1);

namespace UMA\Uuid;

/**
 * This generator implements the version 4 UUID generation algorithm
 * described in RFC 4122.
 *
 * @see https://tools.ietf.org/html/rfc4122#section-4.4
 */
class Version4Generator implements UuidGenerator
{
    public function generate(string $name = null): Uuid
    {
        // Set all the other bits to randomly chosen values.
        $bytes = \unpack('C16', \random_bytes(16));

        // Set the four most significant bits (bits 12 through 15) of
        // the time_hi_and_version field to the 4-bit version number from
        // Section 4.1.3. (these are 0100 for v4)
        $bytes[7] &= 0b01001111;
        $bytes[7] |= 0b01000000;

        // Set the two most significant bits (bits 6 and 7) of the
        // clock_seq_hi_and_reserved to zero and one, respectively.
        $bytes[9] &= 0b10111111;
        $bytes[9] |= 0b10000000;

        return Uuid::fromBytes(\pack('C16', ...$bytes));
    }
}
