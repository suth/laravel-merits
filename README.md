> [!WARNING]
> This project is a work in progress and is not functional yet.

# A self-contained user achievement badge system for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/suth/laravel-merits.svg?style=flat-square)](https://packagist.org/packages/suth/laravel-merits)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/suth/laravel-merits/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/suth/laravel-merits/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/suth/laravel-merits/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/suth/laravel-merits/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/suth/laravel-merits.svg?style=flat-square)](https://packagist.org/packages/suth/laravel-merits)

Laravel Merits allows you to define achievement badges for your users without the need to modify any of your business logic.

Badges are self-contained, so they define their own criteria for when they should be awarded and which events should trigger an evaluation for the award. These triggers can be any type of event in your Laravel application, including Eloquent model events.

Here's a simple example of a badge that will be awarded to a user when they post 100 comments:

```php
namespace App\Badges\Definitions;

use Suth\Merits\Badge;
use Suth\Merits\BadgeContext;
use Suth\Merits\Contracts\Meritable;
use Suth\Merits\Contracts\EvaluatesRetroactively;
use Suth\Merits\Contracts\EvaluatesEloquentEvents;
use App\Models\Comment;
use App\Models\User;

class ProlificCommenter extends Badge implements EvaluatesEloquentEvents, EvaluateRetroactively
{
    public function eloquentListeners(): array
    {
        return [Comment::class => 'created'];
    }

    public function resolveRecipient(object $trigger): ?Meritable
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

// TODO: Show usage instructions

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
