<?php

namespace Suth\Merits\Traits;

use Suth\Merits\BadgeContext;

trait RevokesImmediately
{
    public function shouldRevoke(BadgeContext $context): bool
    {
        return true;
    }
}
