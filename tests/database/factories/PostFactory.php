<?php

namespace Suth\Merits\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Suth\Merits\Tests\Fixtures\Models\Post;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Suth\Merits\Tests\Fixtures\Models\Post>
 */
class PostFactory extends Factory {
    protected $model = Post::class;

    public function definition(): array
    {
        return [
//                    'title'     => $this->faker->title(),
//                    'body'    => $this->faker->unique()->safeEmail(),
        ];
    }
};
