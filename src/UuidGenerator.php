<?php

declare(strict_types=1);

namespace UMA\Uuid;

/**
 * Contract for Uuid generators.
 */
interface UuidGenerator
{
    /**
     * Returns a brand new Uuid value object.
     *
     * The optional $name parameter is required by Name-Based Uuid
     * generators (e.g. Version 5). Other generators simply ignore it.
     */
    public function generate(string $name = null): Uuid;
}
