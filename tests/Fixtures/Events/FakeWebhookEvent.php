<?php

namespace Suth\Merits\Tests\Fixtures\Events;

use Suth\Merits\Tests\Fixtures\Models\User;

class FakeWebhookEvent
{
    public function __construct(public ?User $user = null) {}
}
