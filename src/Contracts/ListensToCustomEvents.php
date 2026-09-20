<?php

namespace Suth\Merits\Contracts;

interface ListensToCustomEvents
{
    /**
     * List of event classes that should trigger evaluation.
     * e.g. [StripeWebhookReceived::class]
     */
    public function customEvents(): array;
}
