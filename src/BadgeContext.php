<?php

namespace Suth\Merits;

use Illuminate\Database\Eloquent\Model;
use Suth\Merits\Contracts\Badgeable;
use Suth\Merits\Triggers\ManualTrigger;
use Suth\Merits\Triggers\RetroactiveTrigger;

readonly class BadgeContext
{
    public function __construct(
        public Badgeable $recipient,
        public object    $trigger,
        public array     $meta = [],
    ) {}

    public static function fromModel(Model $model, Badgeable $recipient): static
    {
        return new static(recipient: $recipient, trigger: $model);
    }

    public static function fromEvent(object $event, Badgeable $recipient): static
    {
        return new static(recipient: $recipient, trigger: $event);
    }

    public static function retroactive(Badgeable $recipient, array $meta = []): static
    {
        return new static(recipient: $recipient, trigger: new RetroactiveTrigger(), meta: $meta);
    }

    public static function manual(Badgeable $recipient, array $meta = []): static
    {
        return new static(recipient: $recipient, trigger: new ManualTrigger(), meta: $meta);
    }

    public function triggerIs(string $class): bool
    {
        return $this->trigger instanceof $class;
    }
}
