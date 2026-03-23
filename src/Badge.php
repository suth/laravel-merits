<?php

namespace Suth\Merits;

use Illuminate\Database\Eloquent\Model;
use Suth\Merits\Contracts\Badgeable;

abstract class Badge
{
    abstract public function key(): string;
//
//    abstract public function name(): string;
//
//    abstract public function description(): string;

    /**
     * Map of Model class => event name(s) that should trigger evaluation.
     * e.g. [Post::class => ['created', 'deleted'], Comment::class => 'created']
     */
//    abstract public function listeners(): array;

    abstract public function qualify(BadgeContext $context): bool;

    abstract public function resolveRecipient(object $trigger): ?Badgeable;

    /**
     * Can this badge be removed if qualify() later returns false?
     */
//    public function revocable(): bool
//    {
//        return false;
//    }

    /**
     * Optional icon path or identifier.
     */
//    public function icon(): string
//    {
//        return 'badges/default.svg';
//    }
}
