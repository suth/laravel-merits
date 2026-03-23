<?php

namespace Suth\Merits\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Suth\Merits\Tests\Fixtures\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Suth\Merits\Tests\Fixtures\Models\User>
 */
class UserFactory extends Factory {
    protected $model = User::class;

    public function definition(): array
    {
        return [
//                    'name'     => $this->faker->name(),
//                    'email'    => $this->faker->unique()->safeEmail(),
//                    'password' => bcrypt('password'),
        ];
    }
};
