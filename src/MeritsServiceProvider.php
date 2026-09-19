<?php

namespace Suth\Merits;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Suth\Merits\Commands\MeritsCommand;
use Suth\Merits\Contracts\BadgeRegistrationRepository;
use Suth\Merits\Repositories\CachedBadgeRegistrationRepository;

class MeritsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-merits')
            ->hasConfigFile()
            ->hasMigrations(['create_badge_awards_table', 'create_badge_registrations_table'])
            ->hasCommand(MeritsCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(BadgeRegistrationRepository::class, CachedBadgeRegistrationRepository::class);
    }

    public function packageBooted(): void
    {
        $this->app->make(BadgeService::class)->initialize();
    }
}
