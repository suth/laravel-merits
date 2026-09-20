<?php

namespace App\Badges;

use Suth\Merits\Badge;
use Suth\Merits\BadgeContext;
use Suth\Merits\Contracts\Badgeable;
use Suth\Merits\Contracts\ListensToCustomEvents;
use Suth\Merits\Tests\Fixtures\Events\FakeWebhookEvent;

class WebhookBadge extends Badge implements ListensToCustomEvents
{
    public function key(): string
    {
        return 'webhook-badge';
    }

    public function qualify(BadgeContext $context): bool
    {
        return true;
    }

    public function resolveRecipient(object $trigger): ?Badgeable
    {
        return $trigger->user;
    }

    public function customEvents(): array
    {
        return [FakeWebhookEvent::class];
    }
}
