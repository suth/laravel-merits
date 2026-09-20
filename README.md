> [!WARNING]
> This project is a work in progress and is rapidly evolving. APIs may change without notice.

# A self-contained user achievement badge system for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/suth/laravel-merits.svg?style=flat-square)](https://packagist.org/packages/suth/laravel-merits)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/suth/laravel-merits/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/suth/laravel-merits/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/suth/laravel-merits/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/suth/laravel-merits/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/suth/laravel-merits.svg?style=flat-square)](https://packagist.org/packages/suth/laravel-merits)

Laravel Merits allows you to define achievement badges for your users without the need to modify any of your business logic.

Badges are self-contained, so they define their own criteria for when they should be awarded and which events should trigger an evaluation for the award. These triggers can be any type of event in your Laravel application, including Eloquent model events.

Here's a simple example of a badge that will be awarded to a user when they post 100 comments:

```php
namespace App\Badges;

use Suth\Merits\Badge;
use Suth\Merits\BadgeContext;
use Suth\Merits\Contracts\Badgeable;
use Suth\Merits\Contracts\ListensToEloquentEvents;
use App\Models\Comment;
use App\Models\User;

class ProlificCommenter extends Badge implements ListensToEloquentEvents
{
    public function key(): string
    {
        return 'prolific-commenter';
    }

    public function eloquentEvents(): array
    {
        return [Comment::class => 'created'];
    }

    public function resolveRecipient(object $trigger): ?Badgeable
    {
        return $trigger->user;
    }

    public function qualify(BadgeContext $context): bool
    {
        return $context->recipient->comments()->count() >= 100;
    }
}
```

## Installation

You can install the package via composer:

```bash
composer require suth/laravel-merits
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-merits-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-merits-config"
```

## Usage

### Making a model badgeable

Any model that can receive badges (typically your `User` model) must implement the `Badgeable` contract. The easiest way to do this is with the included `HasBadges` trait:

```php
use Illuminate\Database\Eloquent\Model;
use Suth\Merits\Contracts\Badgeable;
use Suth\Merits\Traits\HasBadges;

class User extends Model implements Badgeable
{
    use HasBadges;
}
```

### Defining a badge

Badges live in `app/Badges` by default (configurable via `badges_path` in `config/merits.php`) and are discovered/registered automatically. The first things we need to give a badge are a unique `key()` and a `qualify()` method that can determine whether a given recipient has earned it, plus a `resolveRecipient()` method that maps whatever triggered the evaluation back to a `Badgeable`:

```php
namespace App\Badges;

use App\Models\Post;
use Suth\Merits\Badge;
use Suth\Merits\BadgeContext;
use Suth\Merits\Contracts\Badgeable;

class PostCountBadge extends Badge
{
    public function key(): string
    {
        return 'post-count-badge';
    }

    public function qualify(BadgeContext $context): bool
    {
        return $context->recipient->posts()->count() >= 3;
    }

    public function resolveRecipient(object $trigger): ?Badgeable
    {
        return $trigger->user;
    }
}
```

On its own, this badge is only ever evaluated when you [award it manually](#awarding-badges-manually) or [evaluate it retroactively](#evaluating-badges-retroactively). To have it evaluated automatically, implement one of the trigger contracts below.

### Triggering on Eloquent events

Implement `ListensToEloquentEvents` and return a map of model classes to the Eloquent event(s) that should trigger an evaluation. Every time a listened-to event fires, `resolveRecipient()` is called with the model, and `qualify()` is run against the resolved recipient:

```php
use App\Models\Post;
use Suth\Merits\Contracts\ListensToEloquentEvents;

class PostCountBadge extends Badge implements ListensToEloquentEvents
{
    // ...

    public function eloquentEvents(): array
    {
        return [Post::class => 'created'];
    }
}
```

`eloquentEvents()` also accepts multiple events for a model, e.g. `[Post::class => ['created', 'deleted']]`.

### Triggering on custom events

Badges aren't limited to Eloquent events — implement `ListensToCustomEvents` to trigger evaluation off of any event your application dispatches:

```php
use App\Events\StripeWebhookReceived;
use Suth\Merits\Contracts\ListensToCustomEvents;

class WebhookBadge extends Badge implements ListensToCustomEvents
{
    // ...

    public function resolveRecipient(object $trigger): ?Badgeable
    {
        return $trigger->user;
    }

    public function customEvents(): array
    {
        return [StripeWebhookReceived::class];
    }
}
```

### Awarding badges manually

You can award a badge to a recipient directly, bypassing `qualify()` entirely. This is idempotent, so awarding a badge a recipient already has is a no-op:

```php
$user->awardBadge(new PostCountBadge);
```

### Checking if a user has a badge

```php
$user->hasBadge(new PostCountBadge);
```

### Evaluating badges retroactively

In some cases (for example, after introducing a new badge) you may want to evaluate existing data against a badge. To accomplish this, build a retroactive `BadgeContext` for each recipient and hand it to the `BadgeService`:

```php
use App\Models\User;
use Suth\Merits\BadgeContext;
use Suth\Merits\BadgeService;

$service = app(BadgeService::class);
$badge = new PostCountBadge;

User::each(function (User $user) use ($service, $badge) {
    $service->evaluate($badge, BadgeContext::retroactive($user));
});
```

`evaluate()` runs `qualify()` and awards the badge if it passes, just like an automatic trigger would.

### Listening for badge awards

Whenever a badge is awarded (manually, retroactively, or via a trigger) a `Suth\Merits\Events\BadgeAwarded` event is dispatched with the `Badge` and its `BadgeContext`. Listen for it like any other Laravel event to, for example, notify the recipient:

```php
use Suth\Merits\Events\BadgeAwarded;

Event::listen(function (BadgeAwarded $event) {
    $event->context->recipient->notify(new BadgeEarned($event->badge));
});
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Sutherland Boswell](https://github.com/suth)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
