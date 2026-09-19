<?php

namespace Suth\Merits;

use Suth\Merits\Contracts\Badgeable;
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

        $context->recipient->attachBadge($badge, $context->triggerType(), $context->meta);
        BadgeAwarded::dispatch($badge, $context);
    }

    public function manuallyAward(Badge $badge, Badgeable $recipient): void
    {
        $context = BadgeContext::manual($recipient);
        $this->award($badge, $context);
    }
}
