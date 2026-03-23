<?php

namespace Suth\Merits;

use Illuminate\Database\Eloquent\Model;
use Suth\Merits\Contracts\Badgeable;

readonly class BadgeContext
{
    public function __construct(
        public Badgeable $recipient,
        public ?object   $trigger = null,
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

    public static function retroactive(Badgeable $recipient): static
    {
        return new static(recipient: $recipient);
    }

    public function triggerIs(string $class): bool
    {
        return $this->trigger instanceof $class;
    }
}
