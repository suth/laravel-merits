<?php

namespace Suth\Merits\Tests\Fixtures\Badges;

use Suth\Merits\Badge;
use Suth\Merits\BadgeContext;
use Suth\Merits\Contracts\Badgeable;
use Suth\Merits\Contracts\ListensToEloquentEvents;
use Suth\Merits\Contracts\ManuallyRegistered;
use Suth\Merits\Tests\Fixtures\Models\Post;

class ParameterizedBadge extends Badge implements ListensToEloquentEvents, ManuallyRegistered
{
    public function __construct(protected int $threshold) {}

    public function key(): string
    {
        return "parameterized-badge-{$this->threshold}";
    }

    public function qualify(BadgeContext $context): bool
    {
        return $context->recipient->posts()->count() >= $this->threshold;
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
