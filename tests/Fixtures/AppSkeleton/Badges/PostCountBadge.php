<?php

namespace App\Badges;

use Suth\Merits\Badge;
use Suth\Merits\BadgeContext;
use Suth\Merits\Contracts\Badgeable;
use Suth\Merits\Contracts\ListensToEloquentEvents;
use Suth\Merits\Tests\Fixtures\Models\Post;

class PostCountBadge extends Badge implements ListensToEloquentEvents
{
    public function key(): string
    {
        return 'post-count-badge';
    }

    public function qualify(BadgeContext $context): bool
    {
        return $context->recipient->posts()->count() >= 3;
    }

    public function resolveRecipient(object $trigger): ?Badgeable
    {
        return $trigger->user;
    }

    public function eloquentEvents(): array
    {
        return [Post::class => 'created'];
    }
}
