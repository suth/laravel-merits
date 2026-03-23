<?php

use Suth\Merits\BadgeContext;
use Suth\Merits\Tests\Fixtures\Badges\SimpleBadge;
use Suth\Merits\Tests\Fixtures\Models\Post;
use Suth\Merits\Tests\Fixtures\Models\User;

it('does not qualify a user below the threshold', function () {
    $user = User::factory()->create();
    Post::factory()->count(2)->for($user)->create();
    $badge = new SimpleBadge();
    $context = BadgeContext::retroactive($user);

    $result = $badge->qualify($context);

    expect($result)->toBeFalse();
});

it('qualifies a user at the threshold', function () {
    $user = User::factory()->create();
    Post::factory()->count(3)->for($user)->create();
    $badge = new SimpleBadge();
    $context = BadgeContext::retroactive($user);

    $result = $badge->qualify($context);

    expect($result)->toBeTrue();
});

it('resolves the recipient from a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();
    $badge = new SimpleBadge();

    $recipient = $badge->resolveRecipient($post);

    expect($user->is($recipient))->toBeTrue();
});
