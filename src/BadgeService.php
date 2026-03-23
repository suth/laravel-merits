<?php

namespace Suth\Merits;

use Suth\Merits\Events\BadgeAwarded;

class BadgeService
{
    public function evaluate(Badge $badge, BadgeContext $context): void
    {
        if ($badge->qualify($context)) {
            $this->award($badge, $context);
        }
    }

    public function award(Badge $badge, BadgeContext $context): void
    {
        if ($context->recipient->hasBadge($badge)) {
            return;
        }

        $context->recipient->attachBadge($badge);
        BadgeAwarded::dispatch($badge, $context);
    }
}
