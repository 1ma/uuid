<?php

declare(strict_types=1);

namespace UMA\Uuid;

/**
 * @see http://www.informit.com/articles/article.aspx?p=25862
 */
class CombGenerator extends CustomCombGenerator
{
    /**
     * @param int $granularity Precision of the timestamps, ranging from second up to microsecond.
     *
     * @throws \InvalidArgumentException When $granularity is outside the [0, 6] range.
     */
    public function __construct(int $granularity = 6)
    {
        parent::__construct(
            new Version4Generator,
            new \DateTimeImmutable('1970-01-01 00:00:00 UTC'),
            6, $granularity
        );
    }
}
