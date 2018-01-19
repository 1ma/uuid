<?php

declare(strict_types=1);

namespace UMA\Uuid;

/**
 * An UuidGenerator that returns sequential UUIDs, starting
 * from the NIL Uuid and going up. This can be useful in a unit testing
 * context, where deterministic and easily readable UUIDs might be a boon.
 *
 * Note that only the lowest 64 bits of the UUID are incremented. That should
 * be enough, though.
 *
 * Don't use this one in production.
 */
class SequentialGenerator implements UuidGenerator
{
    /**
     * @var int
     */
    private $counter;

    /**
     * @var int
     */
    private $head;

    /**
     * @example $mark = 15 and $start = 10 will generate:
     *  '00000000-0000-000f-0000-00000000000a'
     *  '00000000-0000-000f-0000-00000000000b'
     *  '00000000-0000-000f-0000-00000000000c'
     *  ...
     */
    public function __construct(int $mark = 0, int $start = 0)
    {
        $this->head = \pack('J', $mark);
        $this->counter = $start;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(string $name = null): Uuid
    {
        return Uuid::fromBytes($this->head . \pack('J', $this->counter++));
    }
}
