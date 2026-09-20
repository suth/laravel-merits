<?php

namespace Suth\Merits\Contracts;

use Suth\Merits\BadgeContext;

interface AutoRevocable
{
    /**
     * Whether this badge's award should be removed now that qualify()
     * has returned false. Only ever consulted once qualify() has already
     * failed — override (or skip the RevokesImmediately trait) to add a
     * grace period or threshold before acting on that failure.
     */
    public function shouldRevoke(BadgeContext $context): bool;
}
