<?php

namespace Suth\Merits\Exceptions;

use RuntimeException;

class DuplicateBadgeKeysException extends RuntimeException
{
    /**
     * @param  array<string, array<int, string>>  $duplicates  badge key => colliding class names
     */
    public static function forKeys(array $duplicates): self
    {
        $summary = collect($duplicates)
            ->map(fn (array $classes, string $key) => sprintf('"%s" (%s)', $key, implode(', ', $classes)))
            ->implode('; ');

        return new self(sprintf(
            'Multiple badges are registered with the same key: %s. Badge keys must be unique.',
            $summary
        ));
    }
}
