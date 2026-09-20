<?php

namespace Suth\Merits;

use Illuminate\Database\Eloquent\Model;
use Suth\Merits\Contracts\Badgeable;
use Suth\Merits\Enums\TriggerCategory;
use Suth\Merits\Triggers\ManualTrigger;
use Suth\Merits\Triggers\RetroactiveTrigger;

final readonly class BadgeContext
{
    public function __construct(
        public Badgeable $recipient,
        public object $trigger,
        public array $meta = [],
    ) {}

    public static function fromModel(Model $model, Badgeable $recipient): self
    {
        return new self(recipient: $recipient, trigger: $model);
    }

    public static function fromCustomEvent(object $event, Badgeable $recipient): self
    {
        return new self(recipient: $recipient, trigger: $event);
    }

    public static function retroactive(Badgeable $recipient, array $meta = []): self
    {
        return new self(recipient: $recipient, trigger: new RetroactiveTrigger, meta: $meta);
    }

    public static function manual(Badgeable $recipient, array $meta = []): self
    {
        return new self(recipient: $recipient, trigger: new ManualTrigger, meta: $meta);
    }

    /**
     * @param  class-string  $class
     */
    public function triggerIs(string $class): bool
    {
        return $this->trigger instanceof $class;
    }

    public function triggerCategory(): TriggerCategory
    {
        return match (true) {
            $this->trigger instanceof ManualTrigger => TriggerCategory::Manual,
            $this->trigger instanceof RetroactiveTrigger => TriggerCategory::Retroactive,
            $this->trigger instanceof Model => TriggerCategory::EloquentEvent,
            default => TriggerCategory::CustomEvent,
        };
    }
}
