<?php

namespace Suth\Merits;

use Suth\Merits\Contracts\Badgeable;

abstract class Badge
{
    abstract public function key(): string;
    //
    //    abstract public function name(): string;
    //
    //    abstract public function description(): string;

    abstract public function qualify(BadgeContext $context): bool;

    abstract public function resolveRecipient(object $trigger): ?Badgeable;

    /**
     * Optional icon path or identifier.
     */
    //    public function icon(): string
    //    {
    //        return 'badges/default.svg';
    //    }
}
