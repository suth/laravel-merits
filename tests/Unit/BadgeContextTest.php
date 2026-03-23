<?php

use Suth\Merits\BadgeContext;
use Suth\Merits\Tests\Fixtures\Events\FakeWebhookEvent;
use Suth\Merits\Tests\Fixtures\Models\Post;
use Suth\Merits\Tests\Fixtures\Models\User;
use Suth\Merits\Triggers\ManualTrigger;
use Suth\Merits\Triggers\RetroactiveTrigger;

it('can be created retroactively with just a recipient', function () {
    $user = new User();

    $context = BadgeContext::retroactive($user);

    expect($context->recipient)->toBe($user)
        ->and($context->trigger)->toBeInstanceOf(RetroactiveTrigger::class)
        ->and($context->meta)->toBe([]);
});

it('can be created manually with just a recipient', function () {
    $user = new User();

    $context = BadgeContext::manual($user);

    expect($context->recipient)->toBe($user)
        ->and($context->trigger)->toBeInstanceOf(ManualTrigger::class)
        ->and($context->meta)->toBe([]);
});

it('can be created from a model event', function () {
    $user = new User();
    $post = new Post();

    $context = BadgeContext::fromModel($post, $user);

    expect($context->recipient)->toBe($user)
        ->and($context->trigger)->toBe($post);
});

it('can be created from a laravel event', function () {
    $user = new User();
    $event = new FakeWebhookEvent();

    $context = BadgeContext::fromEvent($event, $user);

    expect($context->recipient)->toBe($user)
        ->and($context->trigger)->toBe($event);
});

it('can check the trigger type', function () {
    $user = new User();
    $post = new Post();

    $context = BadgeContext::fromModel($post, $user);

    expect($context->triggerIs(Post::class))->toBeTrue()
        ->and($context->triggerIs(User::class))->toBeFalse();
});

it('returns the correct trigger type string', function (Closure $contextFactory, string $expected) {
    $context = $contextFactory();

    $triggerTypeString = $context->triggerType();

    expect($triggerTypeString)->toBe($expected);
})->with([
    'manual'      => [fn () => BadgeContext::manual(new User()), 'manual'],
    'retroactive' => [fn () => BadgeContext::retroactive(new User()), 'retroactive'],
    'model'       => [fn () => BadgeContext::fromModel(new Post(), new User()), 'model'],
    'event'       => [fn () => BadgeContext::fromEvent(new FakeWebhookEvent(), new User()), 'event'],
]);

it('carries metadata', function () {
    $user = new User();

    $context = new BadgeContext(
        recipient: $user,
        trigger: new ManualTrigger(),
        meta: ['source' => 'webhook', 'dry_run' => true],
    );

    expect($context->meta)->toBe(['source' => 'webhook', 'dry_run' => true]);
});
