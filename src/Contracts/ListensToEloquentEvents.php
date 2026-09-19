<?php

namespace Suth\Merits\Contracts;

interface ListensToEloquentEvents
{
    /**
     * Map of Model class => event name(s) that should trigger evaluation.
     * e.g. [Post::class => 'created'] or [Post::class => ['created', 'deleted']]
     */
    public function eloquentEvents(): array;
}
