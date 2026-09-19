<?php

namespace Suth\Merits\Contracts;

interface EvaluatesEloquentEvents
{
    /**
     * Map of Model class => event name(s) that should trigger evaluation.
     * e.g. [Post::class => 'created'] or [Post::class => ['created', 'deleted']]
     */
    public function eloquentListeners(): array;
}
