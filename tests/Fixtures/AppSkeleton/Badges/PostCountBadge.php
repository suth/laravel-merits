<?php

namespace App\Badges;

use Suth\Merits\Badge;
use Suth\Merits\BadgeContext;
use Suth\Merits\Contracts\Badgeable;
use Suth\Merits\Contracts\EvaluatesEloquentEvents;
use Suth\Merits\Tests\Fixtures\Models\Post;

class PostCountBadge extends Badge implements EvaluatesEloquentEvents
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

    public function eloquentListeners(): array
    {
        return [Post::class => 'created'];
    }
}
