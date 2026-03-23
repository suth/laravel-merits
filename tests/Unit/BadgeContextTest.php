<?php

use Suth\Merits\BadgeContext;
use Suth\Merits\Tests\Fixtures\Events\FakeWebhookEvent;
use Suth\Merits\Tests\Fixtures\Models\Post;
use Suth\Merits\Tests\Fixtures\Models\User;

it('can be created retroactively with just a recipient', function () {
    $user = new User();

    $context = BadgeContext::retroactive($user);

    expect($context->recipient)->toBe($user)
        ->and($context->trigger)->toBeNull()
        ->and($context->meta)->toBe([]);
});

//it('can be created manually with just a recipient', function () {
//    $user = new User();
//
//    $context = BadgeContext::manual($user);
//
//    expect($context->recipient)->toBe($user)
//        ->and($context->trigger)->toBeNull()
//        ->and($context->meta)->toBe([]);
//});

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

it('carries metadata', function () {
    $user = new User();

    $context = new BadgeContext(
        recipient: $user,
        meta: ['source' => 'webhook', 'dry_run' => true],
    );

    expect($context->meta)->toBe(['source' => 'webhook', 'dry_run' => true]);
});
