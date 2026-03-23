<?php

namespace Suth\Merits\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Suth\Merits\Badge;
use Suth\Merits\BadgeContext;

class BadgeAwarded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Badge $badge,
        public BadgeContext $context,
    ) {}
}
