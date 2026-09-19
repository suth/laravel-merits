<?php

namespace Suth\Merits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Suth\Merits\Contracts\Badgeable;
use Suth\Merits\Contracts\BadgeRegistrationRepository;
use Suth\Merits\Contracts\EvaluatesEloquentEvents;
use Suth\Merits\Events\BadgeAwarded;

class BadgeService
{
    public function __construct(
        protected BadgeRegistrationRepository $registrations,
    ) {}

    public function initialize(): void
    {
        $path = $this->badgesPath();
        $namespace = $this->badgesNamespace($path);

        /** @var Collection<int, Badge> $badges */
        $badges = BadgeDiscovery::within($path, $namespace)
            ->map(fn (string $class) => app($class));

        $badges->each(fn (Badge $badge) => $this->registrations->register($badge));

        foreach ($badges as $badge) {
            if ($badge instanceof EvaluatesEloquentEvents) {
                $this->registerEloquentListeners($badge);
            }
        }
    }

    public function badgesPath(): string
    {
        return config('merits.badges_path') ?? app_path('Badges');
    }

    public function badgesNamespace(string $path): string
    {
        return rtrim(app()->getNamespace().str_replace(
            ['/', DIRECTORY_SEPARATOR],
            '\\',
            trim(Str::after(realpath($path) ?: $path, realpath(app_path())), '/\\')
        ), '\\');
    }

    protected function registerEloquentListeners(Badge&EvaluatesEloquentEvents $badge): void
    {
        foreach ($badge->eloquentListeners() as $modelClass => $events) {
            foreach ((array) $events as $event) {
                Event::listen("eloquent.{$event}: {$modelClass}", function ($model) use ($badge) {
                    $recipient = $badge->resolveRecipient($model);

                    if ($recipient !== null) {
                        $this->evaluate($badge, BadgeContext::fromModel($model, $recipient));
                    }
                });
            }
        }
    }

    public function evaluate(Badge $badge, BadgeContext $context): void
    {
        if ($badge->qualify($context)) {
            $this->award($badge, $context);
        }
    }

    public function award(Badge $badge, BadgeContext $context): void
    {
        if ($context->recipient->hasBadge($badge)) {
            return;
        }

        $context->recipient->attachBadge($badge, $context->triggerType(), $context->meta);
        BadgeAwarded::dispatch($badge, $context);
    }

    public function manuallyAward(Badge $badge, Badgeable $recipient): void
    {
        $context = BadgeContext::manual($recipient);
        $this->award($badge, $context);
    }
}
