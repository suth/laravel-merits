<?php

namespace Suth\Merits\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Suth\Merits\MeritsServiceProvider;
use Suth\Merits\Tests\Database\Migrations\CreatePostsTable;
use Suth\Merits\Tests\Database\Migrations\CreateUsersTable;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Suth\\Merits\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            MeritsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        new CreateUsersTable()->up();
        new CreatePostsTable()->up();

        foreach (\Illuminate\Support\Facades\File::allFiles(__DIR__ . '/../database/migrations') as $migration) {
            (include $migration->getRealPath())->up();
        }
    }
}
