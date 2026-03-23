<?php

namespace Suth\Merits\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Suth\Merits\Contracts\Badgeable;
use Suth\Merits\Tests\Database\Factories\UserFactory;
use Suth\Merits\Traits\HasBadges;

/**
 * @method static UserFactory|User factory(...$parameters)
 */
class User extends Model implements Badgeable
{
    use HasFactory, HasBadges;

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
