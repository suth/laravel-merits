<?php

namespace Suth\Merits\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Suth\Merits\Tests\Database\Factories\PostFactory;

/**
 * @method static PostFactory|Post factory(...$parameters)
 */
class Post extends Model
{
    use HasFactory;

    protected static function newFactory(): PostFactory
    {
        return PostFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
