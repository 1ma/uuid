<?php

declare(strict_types=1);

namespace UMA\Uuid;

/**
 * Value object that encapsulates the 128 bits of an UUID.
 *
 * @example Uuid::isUUid('96aaab69-7b76-4461-b008-cbb9cfcb6fdf');       // bool(true)
 * @example Uuid::fromString('96aaab69-7b76-4461-b008-cbb9cfcb6fdf');   // object(UMA\Uuid\Uuid)
 */
class Uuid
{
    /**
     * The regular expression of what the value object considers to be a valid Uuid in textual form.
     *
     * It does not try to enforce any particular version.
     */
    const TEXTUAL_FORMAT = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

    /**
     * Ordered sequence of 16 bytes.
     *
     * @example 0x96aaab697b764461b008cbb9cfcb6fdf
     *
     * @var string
     */
    private $raw;

    /**
     * Textual Uuid representation of the $raw byte sequence.
     *
     * @example '96aaab69-7b76-4461-b008-cbb9cfcb6fdf'
     *
     * @var string
     */
    private $textual;

    /**
     * Usage of the regular constructor is only allowed inside the own class.
     */
    private function __construct()
    {
    }

    /**
     * Alias of asString().
     */
    public function __toString(): string
    {
        return $this->asString();
    }

    /**
     * Returns the raw 16-byte sequence of the Uuid.
     */
    public function asBytes(): string
    {
        return $this->raw;
    }

    /**
     * Returns the textual representation of the Uuid.
     */
    public function asString(): string
    {
        return $this->textual;
    }

    /**
     * Factory to create a new Uuid instance from a raw byte sequence.
     *
     * Most of the time this method should be used by UuidGenerator
     * implementations, not the end user.
     *
     * @throws \InvalidArgumentException If $bytes is not exactly 16 bytes long.
     */
    public static function fromBytes(string $bytes): Uuid
    {
        if (16 !== strlen($bytes)) {
            throw new \InvalidArgumentException('Length of $bytes for new Uuid is not 16. Got: 0x' . bin2hex($bytes));
        }

        $uuid = new self;
        $uuid->raw = $bytes;
        $uuid->textual = self::bin2str($uuid->raw);

        return $uuid;
    }

    /**
     * Factory to create a new Uuid instance from a valid Uuid in string form.
     *
     * As opposed to the fromBytes() method, this one is meant to
     * be used by the end user of the library.
     *
     * @throws \InvalidArgumentException If $text is not actually a valid Uuid.
     */
    public static function fromString(string $text): Uuid
    {
        if (false === self::isUuid($text)) {
            throw new \InvalidArgumentException('$text is not a valid Uuid. Got: ' . $text);
        }

        $uuid = new self;
        $uuid->textual = strtolower($text);
        $uuid->raw = self::str2bin($uuid->textual);

        return $uuid;
    }

    /**
     * Helper method to validate if a given string can
     * be considered a valid Uuid.
     */
    public static function isUuid(string $candidate): bool
    {
        return 1 === preg_match(self::TEXTUAL_FORMAT, $candidate);
    }

    /**
     * Turns the textual form of an Uuid to its equivalent raw bytes.
     *
     * Precondition: $uuid is a valid Uuid.
     *
     * @example '96aaab69-7b76-4461-b008-cbb9cfcb6fdf' => 0x96aaab697b764461b008cbb9cfcb6fdf
     */
    private static function str2bin(string $uuid): string
    {
        return pack('H32', str_replace('-', '', $uuid));
    }

    /**
     * Turns the 16 raw bytes of an Uuid to its textual form.
     *
     * Precondition: $bytes is exactly 16 bytes long.
     *
     * @example 0x96aaab697b764461b008cbb9cfcb6fdf => '96aaab69-7b76-4461-b008-cbb9cfcb6fdf'
     */
    private static function bin2str(string $bytes): string
    {
        return implode('-', unpack('H8a/H4b/H4c/H4d/H12e', $bytes));
    }
}
